<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Base Controller.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class RedproductfinderController  extends JControllerLegacy
{
	/**
	 * This method will call display view
	 *
	 * @param   boolean  $cachable   default variable is false
	 * @param   array    $urlparams  default variableis array
	 *
	 * @return RedproductfinderController
	 */
	function display($cachable = false, $urlparams = array())
	{
		$cachable = true;
		$input = JFactory::getApplication()->input;
		$view = $input->get("view", null);

		// Set a default view if none exists
		if ($view == null)
		{
			$input->set('view', 'redproductfinder');
			$input->set('layout', 'redproductfinder');
		}

		parent::display($cachable, $urlparams);

		return $this;
	}
}
