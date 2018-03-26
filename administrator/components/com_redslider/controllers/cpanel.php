<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Control panel view for RedSLIDER.
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 * @since       2.0
 */
class RedsliderControllerCpanel extends RControllerAdmin
{
	/**
	 * function install sample content
	 * 
	 * @return void
	 */
	public function demoContentInsert()
	{
		// Install the demo content
		$model = $this->getModel('cpanel');
		$model->demoContentInsert();

		// Redirect to control panel
		$this->setRedirect('index.php?option=com_redslider&view=cpanel', JText::_('COM_REDSLIDER_CPANEL_DEMO_CONTENT_SUCCESS'), 'message');
	}
}
