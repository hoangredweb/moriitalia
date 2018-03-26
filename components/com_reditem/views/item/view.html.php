<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Category edit view
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewItem extends ReditemView
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the category edit page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @todo Check the extra fields once implemented
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->item = $this->get('Item');

		// Get customfield values if item is in edit mode
		if (isset($this->item->id))
		{
			$cfValues = ReditemHelperItem::getCustomFieldValues($this->item->id);

			if (isset($cfValues[$this->item->type_id][$this->item->id]))
			{
				$this->item->customfield_values = $cfValues[$this->item->type_id][$this->item->id];
			}
		}

		$this->form = $this->get('Form');
		$this->customfields = $this->get('CustomFields');
		$this->typeId = $app->getUserState('com_reditem.global.tid', 0);

		// We need store this session than we can allow for editing at frontend
		$app->setUserState('com_reditem.edit.item.id', $this->item->id);

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		$subTitle = ' <small>' . JText::_('COM_REDITEM_NEW') . '</small>';

		if ($this->item->id)
		{
			$subTitle = ' <small>' . JText::_('COM_REDITEM_EDIT') . '</small>';
		}

		return JText::_('COM_REDITEM_ITEM_ITEM') . $subTitle;
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo	We have setup ACL requirements for redITEM
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('item.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('item.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('item.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('item.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('item.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('item.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
