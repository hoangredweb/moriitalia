<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * RedSLIDER welcome model
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Model.welcome
 * @since       2.0
 */
class RedSliderModelWelcome extends RModelAdmin
{
	/**
	 * Get the current redSLIDER version
	 *
	 * @return  string  The redSLIDER version
	 *
	 * @since   0.9.1
	 */
	public function getVersion()
	{
		$xmlfile = JPATH_SITE . '/administrator/components/com_redslider/redslider.xml';
		$version = JText::_('COM_REDSLIDER_FILE_NOT_FOUND');

		if (file_exists($xmlfile))
		{
			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			$version = $data['version'];
		}

		return $version;
	}
}
