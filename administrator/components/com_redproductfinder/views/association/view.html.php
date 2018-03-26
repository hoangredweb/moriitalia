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
 * RedPRODUCTFINDER Association View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewAssociation extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew			= ($this->item->id == 0);

		if ($isNew)
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_ASSOCIATION_NEW_TITLE'), 'address contact');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_ASSOCIATION_EDIT_TITLE'), 'address contact');
		}

		JToolbarHelper::apply('association.apply');
		JToolbarHelper::save('association.save');
		JToolbarHelper::save2new('association.save2new');
		JToolbarHelper::cancel('association.cancel');

		JToolbarHelper::divider();
	}
}
