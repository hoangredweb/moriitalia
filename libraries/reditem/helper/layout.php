<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Layout helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Layout
 * @since       2.1
 *
 */
class ReditemHelperLayout
{
	/**
	 * Method to render the layout.
	 *
	 * @param   object  $type         ReditemType Object
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   mixed   $options      Optional custom options to load. JRegistry or array format
	 *
	 * @return  string
	 *
	 * @since   2.1
	 */
	public static function render($type, $layoutFile, $displayData = null, $options = null)
	{
		$options['type'] = $type;
		$layout          = new ReditemHelperLayoutFile($layoutFile, null, $options);
		$renderedLayout  = $layout->render($displayData);

		return $renderedLayout;
	}
}
