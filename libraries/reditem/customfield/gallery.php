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
 * Renders a Gallery Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Gallery
 * @since       2.1.13
 *
 */
class ReditemCustomfieldGallery extends ReditemCustomfieldGeneric
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
		$divId             = 'imgfield_' . $this->fieldcode . '_' . $this->id;
		$value             = $this->value;
		$imageData         = '';
		$index             = 0;
		$defaultImage      = '';
		$layoutOptions     = array('component' => 'com_reditem');
		$config            = new JRegistry($this->params);
		$required          = (boolean) $config->get('required');
		$type              = null;

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		if ($required)
		{
			$attributes['class'] = ' required';
		}

		if ($value)
		{
			$imageArray = json_decode($value, true);

			if (!empty($imageArray))
			{
				foreach ($imageArray as $image)
				{
					if (!empty($image))
					{
						$imagePath = $image;
						$name      = '';

						if (is_array($image))
						{
							$imagePath = $image['path'];

							if ($image['default'])
							{
								$defaultImage = $image['path'];
							}

							if (!empty($image['alt']))
							{
								$name = $image['alt'];
							}
							else
							{
								$tmp  = explode('/', $imagePath);
								$name = array_pop($tmp);
							}
						}

						$imageData .= '
							var filePath = \'' . JUri::root() . 'media/com_reditem/images/' . $basePath . '/' . $imagePath . '\';
							var mockFile = {
								accepted       : true,
								name           : \'' . $name . '\',
								size           : ' . filesize(JPATH_REDITEM_MEDIA . 'images/' . $basePath . '/' . $imagePath) . ',
								status         : \'success\',
								preload        : true,
								previewElement : Dropzone.createElement(instance.options.previewTemplate.trim()),
								default        : ' . $image['default'] . '
							};

							instance.emit(\'addedfile\', mockFile);
							instance.emit(\'thumbnail\', mockFile, filePath);
							instance.emit(\'success\', mockFile, \'' . $imagePath . '\');
							instance.files.push(mockFile);
						';
						$index++;
					}
				}
			}
		}

		$layoutData = array(
			'fieldcode'    => $this->fieldcode,
			'index'        => $index,
			'divId'        => $divId,
			'imageData'    => $imageData,
			'config'       => $config,
			'defaultImage' => $defaultImage,
			'value'        => $value,
			'basepath'     => $basePath
		);

		return ReditemHelperLayout::render($type, 'customfields.gallery.edit', $layoutData, $layoutOptions);
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

		$matches = array();

		if (preg_match_all('/{' . $field->fieldcode . '_value[^}]*}/i', $content, $matches) <= 0)
		{
			return false;
		}

		$matches = $matches[0];
		$index   = 0;

		if (empty($this->value))
		{
			$this->prepareData($item);

			$customFieldValues = $item->customfield_values;

			$value = '';

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = json_decode($customFieldValues[$field->fieldcode], true);
			}
		}
		else
		{
			$value = json_decode($this->value, true);
		}

		foreach ($matches as $match)
		{
			$width       = '';
			$height      = '';
			$displayType = '';
			$tmpMatch    = str_replace('{', '', $match);
			$tmpMatch    = str_replace('}', '', $tmpMatch);
			$params      = explode('|', $tmpMatch);

			// Get "Width" parameter
			if (isset($params[1]))
			{
				$width = (int) $params[1];
			}

			// Get "Height" parameter
			if (isset($params[2]))
			{
				$height = (int) $params[2];
			}

			// Get "Display Type" parameter
			if (isset($params[3]))
			{
				$displayType = $params[3];
			}

			if ($displayType == 'slider')
			{
				$contentHtml = $this->generateSlider($index, $value, $content, $field, $item, $width, $height);
			}
			else
			{
				$contentHtml = $this->generateColorBox($index, $value, $content, $field, $item, $width, $height);
			}

			$content = str_replace($match, $contentHtml, $content);
			$index++;
		}

		return true;
	}

	/**
	 * Method for generate slider for customfield gallery
	 *
	 * @param   int     $index      Index
	 * @param   array   $imageList  Array of images
	 * @param   string  $content    HTML content
	 * @param   object  $field      Field object data
	 * @param   object  $item       Item object data
	 * @param   int     $width      Thumbnail width
	 * @param   int     $height     Thumbnail height
	 *
	 * @return  string              Generated HTML content
	 */
	private function generateSlider($index, $imageList, $content, $field, $item, $width = 0, $height = 0)
	{
		$html = '';

		if (empty($imageList) || empty($content) || !$field || !$item)
		{
			return $html;
		}

		$width  = (int) $width;
		$height = (int) $height;
		$images = array();

		foreach ($imageList as $image)
		{
			if (empty($image) && ($width || $height))
			{
				$imagePath = ReditemHelperImage::getImageLink($item, 'customfield', '', 'thumbnail', $width, $height, false);
			}
			elseif (!empty($image))
			{
				$imagePath = $image;

				if (is_array($image))
				{
					$imagePath = $image['path'];
				}

				$imagePath = JURI::base() . 'media/com_reditem/images/customfield/' . $imagePath;

				if ($width || $height)
				{
					$tmp       = explode('/', $imagePath);
					$fileName  = array_pop($tmp);
					$imagePath = ReditemHelperImage::getImageLink($item, 'customfield', $fileName, 'thumbnail', $width, $height, false);
				}
				else
				{
					$imagePath = '<img src="' . $imagePath . '" />';
				}
			}
			else
			{
				continue;
			}

			$images[] = $imagePath;
		}

		$layoutData = array(
			'tag'       => $field,
			'value'     => $images,
			'reditemId' => $item->id,
			'index'     => $index,
			'width'     => $width,
			'item'      => $item
		);
		$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

		if (isset($item->type) && is_object($item->type))
		{
			$html = ReditemHelperLayout::render($item->type, 'customfields.gallery.slider', $layoutData, $layoutOptions);
		}
		else
		{
			$html = ReditemHelperLayout::render(null, 'customfields.gallery.slider', $layoutData, $layoutOptions);
		}

		return $html;
	}

	/**
	 * Method for generate colorbox for customfield gallery
	 *
	 * @param   int     $index      Index
	 * @param   array   $imageList  Array of images
	 * @param   string  $content    HTML content
	 * @param   object  $field      Field object data
	 * @param   object  $item       Item object data
	 * @param   int     $width      Thumbnail width
	 * @param   int     $height     Thumbnail height
	 *
	 * @return  string              Generated HTML content
	 */
	private function generateColorBox($index, $imageList, $content, $field, $item, $width = 0, $height = 0)
	{
		if (empty($imageList) || empty($content) || !$field || !$item)
		{
			return '';
		}

		$width      = (int) $width;
		$height     = (int) $height;
		$imagesPath = array();
		$firstImage = array();
		$imagePath  = rtrim(JUri::root(), '/') . '/media/com_reditem/images/customfield/';

		foreach ($imageList as $key => $image)
		{
			if (empty($image))
			{
				unset($imageList[$key]);

				continue;
			}

			// Image value is array, since 2.1.3
			if (is_array($image) && !empty($image['path']))
			{
				$image['path'] = $imagePath . $image['path'];

				if ($image['default'])
				{
					$firstImage['original'] = $image;
				}
				else
				{
					$imagesPath[$key] = $image;
				}
			}
			else
			{
				$imagesPath[] = $imagePath . $image;
			}
		}

		// If default image has not been set. Make the first image in gallery become default image.
		if (empty($firstImage))
		{
			$firstImage['original'] = $imagesPath[0];
			unset($imagesPath[0]);
		}

		// Create thumbnail file for first image
		if ($width || $height)
		{
			$tmp      = explode('/', $firstImage['original']);
			$fileName = array_pop($tmp);
			$firstImage['thumbnail'] = ReditemHelperImage::getImageLink($item, 'customfield', $fileName, 'thumbnail', $width, $height, true);
		}
		else
		{
			$firstImage['thumbnail'] = $firstImage['original'];
		}

		$layoutData = array(
			'tag'        => $field,
			'value'      => $imagesPath,
			'firstImage' => $firstImage,
			'reditemId'  => $item->id,
			'index'      => $index,
			'item'       => $item
		);
		$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

		if (isset($item->type) && is_object($item->type))
		{
			$html = ReditemHelperLayout::render($item->type, 'customfields.gallery.view', $layoutData, $layoutOptions);
		}
		else
		{
			$html = ReditemHelperLayout::render(null, 'customfields.gallery.view', $layoutData, $layoutOptions);
		}

		return $html;
	}
}
