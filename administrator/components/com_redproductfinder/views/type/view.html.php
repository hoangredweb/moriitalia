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
 * RedPRODUCTFINDER Tags View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewType extends JViewLegacy
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
		JToolBarHelper::title(JText::_('Type edit'), 'address contact');

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_TYPE_NEW_TITLE'), 'address contact');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_TYPE_EDIT_TITLE'), 'address contact');
		}

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		JToolbarHelper::apply('type.apply');
		JToolbarHelper::save('type.save');
		JToolbarHelper::save2new('type.save2new');
		JToolbarHelper::cancel('type.cancel');

		JToolbarHelper::divider();
	}
}
