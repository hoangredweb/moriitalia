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
 * RedPRODUCTFINDER Types View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewTypes extends JViewLegacy
{
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
		RedproductfinderHelper::addSubmenu("types");

		/* Get the pagination */
		$pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');

		/* Get the fields list */
		$types = $this->get('Types');
		$items = $this->get('Items');
		$state = $this->get("State");
		$model  = $this->getModel();
		$count  = $model->countForms();

		/* Set variabels */
		$this->assignRef('pagination', $pagination);
		$this->assignRef('types', $types);
		$this->assignRef('items', $items);
		$this->assignRef('state', $state);
		$this->assignRef('count', $count);

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
		JToolBarHelper::title(JText::_('Types List'), 'address contact');
		JToolbarHelper::addNew('type.add');
		JToolbarHelper::editList('type.edit');
		JToolbarHelper::publish('types.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('types.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::deleteList('Are you sure you want to delete items', 'types.delete');
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
			't.type_name' => JText::_('JGLOBAL_TITLE'),
			't.published' => JText::_('JSTATUS'),
			't.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
