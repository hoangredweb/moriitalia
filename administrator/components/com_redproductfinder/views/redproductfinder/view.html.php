<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/redproductfinder.php';
/**
 * RedPRODUCTFINDER Forms View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewRedproductfinder extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		/* Get the total number of tags */
		$stats = $this->get('Totals');

		$this->assignRef('stats', $stats);

		/* add submenu here */
		RedproductfinderHelper::addSubmenu("redproductfinder");

		/* Get the toolbar */
		$this->toolbar();

		/* Add sidebar */
		$this->sidebar = JHtmlSidebar::render();

		/* Display the page */
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	function toolbar()
	{
		JHtmlSidebar::setAction('index.php?option=com_redproductfinder');

		JToolBarHelper::title(JText::_('REDPRODUCTFINDER'), 'redproductfinder_redproductfinder');
		JToolBarHelper::preferences('com_redproductfinder', '300');
	}
}
