<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Forms View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewForms extends JViewLegacy
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
		/* add submenu here */
		RedproductfinderHelper::addSubmenu("forms");

		$pagination = $this->get('Pagination');

		/* Get the competitions list */
		$forms = $this->get('Forms');
		$items = $this->get("Items");
		$state = $this->get("State");

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('forms', $forms);
		$this->assignRef('items', $items);
		$this->assignRef('state', $state);

		/* Get the toolbar */
		$this->toolbar();

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
		JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_FORMS_TITLE'), 'address contact');

		JToolbarHelper::addNew('form.add');
		JToolbarHelper::editList('form.edit');
		JToolbarHelper::publish('forms.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('forms.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		// JToolbarHelper::deleteList(JText::_('Are you sure you want to delete the form and all related fields and values?'), 'forms.delete', 'JTOOLBAR_EMPTY_TRASH');
		JToolbarHelper::deleteList('Are you sure you want to delete items', 'forms.delete');
		JToolBarHelper::preferences('com_redproductfinder', '300');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.formname' => JText::_('JGLOBAL_TITLE'),
			'a.published' => JText::_('JSTATUS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
