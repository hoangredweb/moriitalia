<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Forms View.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  View
 * @since       2.0
 */
class RedproductfinderViewForms extends RViewSite
{
	/**
	 * Display the template list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 */
	function display($tpl = null)
	{
		$app        = JFactory::getApplication();
		$user       = JFactory::getUser();
		$dispatcher = JEventDispatcher::getInstance();

		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->user  = $user;

		parent::display($tpl);
	}
}
