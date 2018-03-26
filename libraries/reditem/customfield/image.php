<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  CustomField
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Renders a Image Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Image
 * @since       2.1.13
 *
 */
class ReditemCustomfieldImage extends ReditemCustomfieldGeneric
{
	/**
	 * returns the html code for the form element
	 *
	 * @param   array   $attributes  HTML element attributes array.
	 * @param   string  $basePath    Base path for render image
	 *
	 * @return string
	 */
	public function render($attributes = array(), $basePath = 'customfield')
	{
		$imageConfig = new JRegistry($this->params);
		$required    = (boolean) $imageConfig->get('required');
		$type        = null;

		if ($required)
		{
			$attributes['class'] .= 'required';
		}

		$layoutData = array(
			'id'           => $this->id,
			'fieldcode'    => $this->fieldcode,
			'basepath'     => $basePath,
			'value'        => $this->value,
			'attributes'   => $attributes,
			'imagePreview' => '',
			'config'       => $imageConfig,
			'default'      => $this->default,
			'alt'          => ''
		);

		if (!empty($this->value))
		{
			$imageJSON = json_decode($this->value, true);

			if (!empty($imageJSON))
			{
				$tmp  = explode('|', $imageJSON[0]);
				$path = $tmp[0];
				$alt  = !empty($tmp[1]) ? (string) $tmp[1] : '';

				$layoutData['imagePreview'] = JURI::root() . 'media/com_reditem/images/' . $basePath . '/' . $path;
				$layoutData['alt']          = $alt;
			}
		}

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.image.edit', $layoutData, array('component' => 'com_reditem'));
	}

	/**
	 * Method for replace value tag of customfield
	 *
	 * @param   string  &$content  HTML content
	 * @param   object  $field     Field object of customfield
	 * @param   object  $item      Item object
	 *
	 * @return  boolean            True on success. False otherwise.
	 */
	public function replaceValueTag(&$content, $field, $item)
	{
		if (empty($content) || empty($field) || !is_object($field) || empty($item))
		{
			return false;
		}

		$imageConfig = new JRegistry($this->params);
		$width       = (int) $imageConfig->get('preview_image_width', 0);
		$height      = (int) $imageConfig->get('preview_image_height', 0);
		$hasLinkTag  = strpos($content, '{' . $field->fieldcode . '_link}');
		$hasValueTag = preg_match_all('/{' . $field->fieldcode . '_value[^}]*}/i', $content, $matches);

		if ($hasLinkTag === false && $hasValueTag <= 0)
		{
			return false;
		}

		if (empty($this->value))
		{
			// Prepare data for this item
			$this->prepareData($item);

			$customFieldValues = $item->customfield_values;
			$value             = '';

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = json_decode($customFieldValues[$field->fieldcode], true);
			}
		}
		else
		{
			$value = json_decode($this->value, true);
		}

		$imageValue      = !empty($value[0]) ? $value[0] : '';
		$tmp             = explode('|', $imageValue);
		$imageValue      = $tmp[0];
		$alternativeText = !empty($tmp[1]) ? (string) $tmp[1] : '';

		$base      = !empty($item->type_id) ? 'customfield' : 'categoryfield';
		$imagePath = !empty($imageValue) ? JUri::root() . 'media/com_reditem/images/' . $base . '/' . $imageValue : '';

		if ($hasLinkTag !== false)
		{
			// Replace {_link} tag
			$content = str_replace('{' . $field->fieldcode . '_link}', $imagePath, $content);
		}

		if ($hasValueTag <= 0)
		{
			return true;
		}

		foreach ($matches[0] as $match)
		{
			$tagParams = explode('|', $match);

			// Get "Width" parameter
			$width = !empty($tagParams[1]) ? (int) $tagParams[1] : $width;

			// Get "Height" parameter
			$height = !empty($tagParams[2]) ? (int) $tagParams[2] : $height;

			$thumbnailPath = '';

			if (($width) || ($height))
			{
				// Auto create thumbnail file
				$tmp = explode('/', $imageValue);
				$fileName = array_pop($tmp);
				$thumbnailPath = ReditemHelperImage::getImageLink($item, 'customfield', $fileName, 'thumbnail', $width, $height, true);
			}

			$layoutData    = array('tag' => $field, 'value' => $imagePath, 'thumb' => $thumbnailPath, 'item' => $item, 'alt' => $alternativeText);
			$layoutFile    = 'customfields.image.view';
			$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

			if (isset($item->type) && is_object($item->type))
			{
				$contentHtml = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);
			}
			else
			{
				$contentHtml = ReditemHelperLayout::render(null, $layoutFile, $layoutData, $layoutOptions);
			}

			$content = str_replace($match, $contentHtml, $content);
		}

		return true;
	}
}
