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
 * Items List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class ReditemViewItems extends ReditemView
{
	/**
	 * @var  boolean
	 */
	protected $items;

	/**
	 * @var  object
	 */
	public $state;

	/**
	 * @var  JPagination
	 */
	public $pagination;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * @var array
	 */
	public $activeFilters;

	/**
	 * @var array
	 */
	public $stoolsOptions = array();

	/**
	 * Display the template list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();

		$this->items			= $this->get('Items');
		$this->state			= $this->get('State');
		$this->pagination		= $this->get('Pagination');
		$this->filterForm		= $this->get('Form');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->items			= $this->getModel()->getPrepareItems($this->items);

		// Load fields for batch template
		$this->filterForm->loadFile('items_batch', false);

		// Items ordering
		$this->ordering = array();

		if ($this->items)
		{
			foreach ($this->items as &$item)
			{
				$this->ordering[0][] = $item->id;
			}
		}

		/*
		 * Get displayable fields
		 */
		$displayableFields = array();
		$filterType = $this->getModel()->getState('filter.filter_types', 0);

		// Make sure user has choose filter by type
		if (is_numeric($filterType) && ($filterType > 0))
		{
			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('filter.searchableInBackend', 1);
			$fieldsModel->setState('filter.types', $filterType);

			$displayableFields = $fieldsModel->getItems();
		}

		$this->displayableFields = $displayableFields;

		// Edit permission
		$this->canEdit = false;

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$this->canEdit = true;
		}

		// Edit state permission
		$this->canEditState = false;

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->canEditState = true;
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   0.9.1
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_ITEM_ITEMS');
	}

	/**
	 * Get the tool-bar to render.
	 *
	 * @todo	The commented lines are going to be implemented once we have setup ACL requirements for redITEM
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = ReditemHelperSystem::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$new = RToolbarBuilder::createNewButton('item.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit = RToolbarBuilder::createEditButton('item.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('items.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem'))
		{
			$copy = RToolbarBuilder::createCopyButton('items.copy');
			$secondGroup->addButton($copy);
		}

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$publish = RToolbarBuilder::createPublishButton('items.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('items.unpublish');
			$thirdGroup->addButton($unPublish);

			$archive = RToolbarBuilder::createArchiveButton('items.archive');
			$thirdGroup->addButton($archive);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('items.delete');
			$secondGroup->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
