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
 * Renders a Url Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Url
 * @since       2.1.13
 *
 */
class ReditemCustomfieldUrl extends ReditemCustomfieldGeneric
{
	/**
	 * Returns the html code for the form element
	 *
	 * @param   array  $attributes  HTML element attributes array.
	 *
	 * @return string
	 */
	public function render($attributes = array())
	{
		$params        = new JRegistry($this->params);
		$required      = (boolean) $params->get('required');
		$attributeHtml = '';
		$type          = null;

		if ($required)
		{
			$attributes['class'] = 'required';
		}

		$attributes['placeholder'] = $this->name;
		$value = array('link' => '', 'title' => '', 'target' => '_blank');

		if (!empty($this->value))
		{
			$urlJSON = json_decode($this->value, true);

			if (!empty($urlJSON) && is_array($urlJSON))
			{
				$value['link']  = $urlJSON[0];
				$value['title'] = $urlJSON[1];

				// Attributes value
				if (isset($urlJSON[2]))
				{
					$value['target'] = $urlJSON[2];
				}
			}
		}

		// Prepare attributes
		if (!empty($attributes))
		{
			foreach ($attributes as $attribute => $attributeValue)
			{
				$attributeHtml .= ' ' . $attribute . '="' . $attributeValue . '"';
			}
		}

		$layoutData = array(
			'fieldcode'  => $this->fieldcode,
			'value'      => $value,
			'attributes' => $attributeHtml,
			'default'    => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.url.edit', $layoutData, array('component' => 'com_reditem'));
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

		$linkTag  = '{' . $field->fieldcode . '_link}';
		$titleTag = '{' . $field->fieldcode . '_title}';
		$valueTag = '{' . $field->fieldcode . '_value}';

		if ((strpos($content, $linkTag) === false) && (strpos($content, $valueTag) === false)
			&& (strpos($content, $titleTag) === false))
		{
			return false;
		}

		$value = array('link' => '', 'title' => '', 'target' => '_blank');

		if (empty($this->value))
		{
			// Prepare data for this item
			$this->prepareData($item);

			if (isset($item->customfield_values[$field->fieldcode]))
			{
				$urlJSON = json_decode($item->customfield_values[$field->fieldcode], true);

				// Get file path
				if (!empty($urlJSON))
				{
					$value['link']  = $urlJSON[0];
					$value['title'] = $urlJSON[0];
				}

				// Get file custom name
				if (isset($urlJSON[1]))
				{
					$value['title'] = $urlJSON[1];
				}
			}
		}
		else
		{
			$urlJSON = json_decode($this->value, true);

			// Get file path
			if (!empty($urlJSON))
			{
				$value['link']  = $urlJSON[0];
				$value['title'] = $urlJSON[0];
			}

			// Get file custom name
			if (isset($urlJSON[1]))
			{
				$value['title'] = $urlJSON[1];
			}
		}

		// Replace link tag
		if (strpos($content, $linkTag) !== false)
		{
			$content = str_replace($linkTag, $value['link'], $content);
		}

		// Replace title tag
		if (strpos($content, $titleTag) !== false)
		{
			$content = str_replace($titleTag, $value['title'], $content);
		}

		// Replace value tag
		if (strpos($content, $valueTag) !== false)
		{
			$layoutData    = array('tag' => $field, 'value' => $value, 'item' => $item);
			$layoutFile    = 'customfields.url.view';
			$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

			if (isset($item->type) && is_object($item->type))
			{
				$contentHtml = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);
			}
			else
			{
				$contentHtml = ReditemHelperLayout::render(null, $layoutFile, $layoutData, $layoutOptions);
			}

			$content = str_replace($valueTag, $contentHtml, $content);
		}

		return true;
	}
}
