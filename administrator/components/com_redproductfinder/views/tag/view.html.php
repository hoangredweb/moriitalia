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
 * RedPRODUCTFINDER Tag View.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderViewTag extends JViewLegacy
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

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_TAG_NEW_TITLE'), 'address contact');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_REDPRODUCTFINDER_VIEWS_TAG_EDIT_TITLE'), 'address contact');
		}

		JToolbarHelper::apply('tag.apply');
		JToolbarHelper::save('tag.save');
		JToolbarHelper::save2new('tag.save2new');
		JToolbarHelper::cancel('tag.cancel');

		JToolbarHelper::divider();
	}
}
