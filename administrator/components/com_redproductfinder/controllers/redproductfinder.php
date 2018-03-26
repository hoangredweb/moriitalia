<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Redproductfinder controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderControllerRedproductfinder extends RControllerForm
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();

		$input = JFactory::getApplication()->input;
		$input->set('view', 'redproductfinder');
		$input->set('layout', 'redproductfinder');

		parent::display();
	}
}
