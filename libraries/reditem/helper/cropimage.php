<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
include_once JPATH_LIBRARIES . '/redcore/joomla/image/image.php';

/**
 * RedITEM Cropping Image Feature Helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers.CropImage
 * @since       2.1
 *
 */
class ReditemHelperCropimage
{
	/**
	 * Crop image function
	 *
	 * @param   string  $imageFile  Image filename
	 * @param   string  $path       Path of image
	 * @param   int     $top        Top position
	 * @param   int     $left       Left position
	 * @param   int     $width      Width of image
	 * @param   int     $height     Height of image
	 *
	 * @return  bool
	 */
	public static function cropImage($imageFile, $path, $top, $left, $width, $height)
	{
		$image  = new JImage($path . $imageFile);
		$result = $image->crop($width, $height, $left, $top, false);

		// Process on image mime
		$imageProperties = JImage::getImageFileProperties($path . $imageFile);
		$imageMime       = strtolower($imageProperties->mime);
		$imageType       = null;
		$imageOptions    = array();

		if (($imageMime == 'image/jpeg') || ($imageMime == 'image/jpg'))
		{
			$imageType = IMAGETYPE_JPEG;
			$imageOptions['quality'] = 100;
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

		return $result->toFile($path . $imageFile, $imageType, $imageOptions);
	}
}
