<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER master controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */

class RedproductfinderController extends JControllerLegacy
{
	/**
	 * Method display sub controller
	 *
	 * @param   string  $cachable   Default variable is false
	 * @param   string  $urlparams  Default variable is false
	 *
	 * @return object
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display();

		return $this;
	}
}
