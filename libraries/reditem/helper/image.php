<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

include_once JPATH_LIBRARIES . '/redcore/joomla/image/image.php';

/**
 * Image generator helper
 *
 * @package     RedITEM.Frontend
 * @subpackage  Helper.Helper
 * @since       2.0
 *
 */
class ReditemHelperImage
{
	/**
	 * Get Image thumbnail link of item
	 *
	 * @param   object   $object              Object (item, category)
	 * @param   string   $prefix              Prefix ('image', 'category', 'categoryfield', 'customfield')
	 * @param   string   $imageFile           Image file path
	 * @param   string   $imageType           Type of image ('' => original image, 'small', 'medium', 'large')
	 * @param   int      $defaultImageWidth   Default width
	 * @param   int      $defaultImageHeight  Default height
	 * @param   boolean  $linkOnly            Return link only or full image tag
	 * @param   string   $attrs               Attributes of img tags
	 *
	 * @return  string  Image link
	 */
	public static function getImageLink(
		$object,
		$prefix             = 'item',
		$imageFile          = '',
		$imageType          = '',
		$defaultImageWidth  = null,
		$defaultImageHeight = null,
		$linkOnly           = false,
		$attrs              = '')
	{
		$url                 = JUri::root();
		$imageWidth          = 300;
		$imageHeight         = 300;
		$typeParams          = null;
		$generateHolderImage = true;

		if (!$object || empty($object))
		{
			return false;
		}

		// Create "Type" object if $type is null
		if (!isset($object->type) && isset($object->type_id))
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');

			if (!isset($object->type_id))
			{
				$object->type_id = $object->template->type_id;
			}

			$object->type = $typeModel->getItem($object->type_id);
		}

		if (isset($object->type))
		{
			$typeParams = new JRegistry($object->type->params);
		}

		// If this is from category
		if ($prefix == 'category')
		{
			$redItemConfig = JComponentHelper::getParams('com_reditem');

			// Empty attributes
			if (empty($attrs))
			{
				$categoryParams = new JRegistry($object->params);
				$attrs          = ' title="' . $categoryParams->get('category_image_title') . '"';
				$attrs         .= ' alt="' . $categoryParams->get('category_image_alt') . '"';
			}

			// Get width & height of type (small, medium, large)
			if (!$defaultImageWidth)
			{
				if (!is_null($redItemConfig))
				{
					$defaultImageWidth = (int) $redItemConfig->get('default_' . $prefix . 'image_' . $imageType . '_width', 300);
				}
				else
				{
					$defaultImageWidth = 300;
				}
			}

			if (!$defaultImageHeight)
			{
				if (!is_null($redItemConfig))
				{
					$defaultImageHeight = (int) $redItemConfig->get('default_' . $prefix . 'image_' . $imageType . '_height', 300);
				}
				else
				{
					$defaultImageHeight = 300;
				}
			}
		}

		if ($defaultImageWidth || $defaultImageHeight)
		{
			$imageWidth  = (int) $defaultImageWidth;
			$imageHeight = (int) $defaultImageHeight;
		}

		$originalImagePath = $url . 'media/com_reditem/images/' . $prefix . '/' . $object->id . '/' . $imageFile;
		$realImagePath     = JPATH_SITE . '/media/com_reditem/images/' . $prefix . '/' . $object->id . '/' . $imageFile;

		if ($prefix == 'category')
		{
			$reditemConfig       = JComponentHelper::getParams('com_reditem');
			$generateHolderImage = (boolean) $reditemConfig->get('generate_holder_category_image', 1);
		}

		if (empty($imageFile) || !JFile::exists($realImagePath))
		{
			if (!$generateHolderImage)
			{
				return '';
			}

			// No image value or original image doesn't exists. Return generated image
			$imagePath  = 'holder.js';
			$imagePath .= '/' . $imageWidth . 'x' . $imageHeight . '/text:' . JFactory::getConfig()->get('sitename') . '/gray';

			if ($linkOnly)
			{
				return $imagePath;
			}
			else
			{
				return '<img data-src="' . $imagePath . '" ' . $attrs . ' />';
			}
		}
		else
		{
			if (empty($imageType))
			{
				$returnThumbImagePath = $originalImagePath;
			}
			else
			{
				$thumbnailImagePath   = 'media/com_reditem/images/' . $prefix . '/' . $object->id;
				$thumbnailImagePath  .= '/' . $imageType . '_' . $imageWidth . 'x' . $imageHeight . '_' . $imageFile;
				$realThumbImagePath   = JPATH_SITE . '/' . $thumbnailImagePath;
				$returnThumbImagePath = $url . $thumbnailImagePath;

				if (!JFile::exists($realThumbImagePath))
				{
					// Get thumbnail quality from config
					if (!is_null($typeParams))
					{
						$thumbQuality = $typeParams->get('thumbnailImageQuality', 90);
					}
					else
					{
						$thumbQuality = 90;
					}

					// Check thumbnail quality has wrong number
					if (($thumbQuality > 100) || ($thumbQuality == 0))
					{
						// Set thumbnail quality to default value: 90%
						$thumbQuality = 90;
					}

					// Get thumbnail method
					if (!is_null($typeParams))
					{
						$thumbnailMethod = $typeParams->get('thumbnailCreateMethod', 0);
					}
					else
					{
						$thumbnailMethod = 0;
					}

					self::makeImage($realImagePath, $realThumbImagePath, $imageWidth, $imageHeight, $thumbQuality, $thumbnailMethod);
				}
			}

			if ($linkOnly)
			{
				return $returnThumbImagePath;
			}
			else
			{
				return '<img src="' . $returnThumbImagePath . '" ' . $attrs . ' />';
			}
		}
	}

	/**
	 * Make thumbnail file
	 *
	 * @param   string  $sourceFile       Source file path
	 * @param   string  $destFile         Destination file path
	 * @param   int     $width            Width of image
	 * @param   int     $height           Height of image
	 * @param   int     $quality          Quality of image
	 * @param   int     $thumbnailMethod  Create thumbnail method (0 => resize, 1 => crop & resize)
	 *
	 * @return  boolean
	 */
	public static function makeImage($sourceFile, $destFile, $width, $height, $quality = 100, $thumbnailMethod = 0)
	{
		$width           = (int) $width;
		$height          = (int) $height;
		$quality         = (int) $quality;
		$thumbnailMethod = (int) $thumbnailMethod;

		// If destination file exist
		if (JFile::exists($destFile))
		{
			return true;
		}

		$imageinfo = getimagesize($sourceFile);

		// Detect corrupted image
		if (empty($imageinfo))
		{
			return false;
		}

		if ($width <= 0 && $height <= 0)
		{
			$width = $imageinfo[0];
			$height = $imageinfo[1];
		}
		elseif ($width <= 0 && $height > 0)
		{
			$width = ($height / $imageinfo[1]) * $imageinfo[0];
		}
		elseif ($width > 0 && $height <= 0)
		{
			$height = ($width / $imageinfo[0]) * $imageinfo[1];
		}

		if (!extension_loaded('gd') && !function_exists('gd_info'))
		{
			JError::raiseError(500, JText::_('COM_REDITEM_CHECK_GD_LIBRARY'));

			return false;
		}

		if ($width == 0 || $height == 0)
		{
			JError::raiseError(500, JText::_('COM_REDITEM_IMAGE_NOT_ZERO'));

			return false;
		}

		jimport('joomla.filesystem.file');

		$image           = new JImage($sourceFile);
		$imageProperties = JImage::getImageFileProperties($sourceFile);
		$imageMime       = strtolower($imageProperties->mime);
		$imageOptions    = array();
		$imageType       = null;

		if ($thumbnailMethod == 1)
		{
			$image = $image->cropResize($width, $height);
		}
		elseif ($thumbnailMethod == 2)
		{
			$image = $image->resize($width, $height);
		}
		else
		{
			$image = $image->resize($width, $height, true, JImage::SCALE_FILL);
		}

		// Process on image mime
		if (($imageMime == 'image/jpeg') || ($imageMime == 'image/jpg'))
		{
			$imageType = IMAGETYPE_JPEG;
			$imageOptions['quality'] = $quality;
		}
		elseif ($imageMime == 'image/png')
		{
			$imageType = IMAGETYPE_PNG;

			// 1-9 for compression. 0 is not compression. 1 is fastest but larger file, 9 is slowest but smaller file
			$imageOptions['quality'] = 0;
		}
		elseif ($imageMime == 'image/gif')
		{
			$imageType = IMAGETYPE_GIF;
		}

		$image->toFile($destFile, $imageType, $imageOptions);

		return true;
	}
}
