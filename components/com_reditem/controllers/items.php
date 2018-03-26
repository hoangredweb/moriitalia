<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The templates controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.Items
 * @since       2.0
 */
class ReditemControllerItems extends RControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}

	/**
	 * display the add and the edit form
	 *
	 * @return void
	 */
	public function edit()
	{
		$jInput = JFactory::getApplication()->input;
		$jInput->set('view', 'item');
		$jInput->set('layout', 'default');
		$jInput->set('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		$cat = $this->input->getInt('cat', 0);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorderprod($pks, $order, $cat);

		if ($return)
		{
			echo '1';
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Method to set item to "Featured" item.
	 *
	 * @return  void
	 */
	public function setFeatured()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true));
		$cids      = $input->get('cid', array(), 'array');
		$return    = $input->getBase64('return', null);

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$itemModel->featured($cid, 1))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_FEATURED_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_FEATURED_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=items');
		}

		$this->redirect();
	}

	/**
	 * Method to set "Featured" item to item.
	 *
	 * @return  void
	 */
	public function setUnFeatured()
	{
		$app       = JFactory::getApplication();
		$input     = $app->input;
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true));
		$cids      = $input->get('cid', array(), 'array');
		$return    = $input->getBase64('return', null);

		if (!empty($cids))
		{
			$cid = $cids[0];

			if (!$itemModel->featured($cid, 0))
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_UN_FEATURED_ERROR'), 'error');
			}
			else
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ITEMS_SET_UN_FEATURED_SUCCESS'));
			}
		}

		if ($return)
		{
			$this->setRedirect(base64_decode($return));
		}
		else
		{
			$this->setRedirect(JURI::base() . 'index.php?option=com_reditem&view=items');
		}

		$this->redirect();
	}

	/**
	 * Copy items function
	 *
	 * @return  void
	 */
	public function copy()
	{
		$this->copyItemsProcess(false);
		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false));
	}

	/**
	 * Copy items using batch option function
	 *
	 * @return  void
	 */
	public function batch()
	{
		$input = JFactory::getApplication()->input;
		$isMove = $input->getString('copyMove', 'copy');

		if ($isMove === 'copy')
		{
			$this->copyItemsProcess(true);
		}
		elseif ($isMove === 'move')
		{
			$this->moveItemsProcess();
		}

		$this->setRedirect(JRoute::_('index.php?option=com_reditem&view=items', false));
	}

	/**
	 * Move items process function
	 *
	 * @return  void
	 */
	public function moveItemsProcess()
	{
		$input           = JFactory::getApplication()->input;
		$cids            = $input->get('cid', array(), 'array');
		$batchCategories = $input->get('batchCategories', array(), 'array');
		$removeOrigin    = $input->getString('removeOrigin', 'yes');

		if (count($cids))
		{
			$i = 0;

			foreach ($cids as $cid)
			{
				$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
				$item      = $itemModel->getItem($cid);

				// Move item to chosen categories of batch process
				$itemTable = RTable::getAdminInstance('Item', array('ignore_request' => true));
				$itemTable->bind((array) $item);

				if ($removeOrigin === 'yes')
				{
					$itemTable->deleteCategoriesXref($cid);
				}

				$itemTable->createCategoriesXref($cid, $batchCategories);

				// Update access level of moved item
				$accessLevel = $input->getString('access', '');

				if ($accessLevel !== '')
				{
					$itemTable->updateAccessLevel($cid, $input->getString('access', '1'));
				}

				$i++;
			}

			JFactory::getApplication()->enqueueMessage($i . " " . JText::_('COM_REDITEM_ITEMS_MOVE_SUCCESSFUL'));
		}
	}

	/**
	 * Copy items process function
	 *
	 * @param   bool  $isBatch  if batch copy, it is true value
	 *
	 * @return  void
	 */
	public function copyItemsProcess($isBatch=false)
	{
		$input = JFactory::getApplication()->input;
		$cids  = $input->get('cid', array(), 'array');

		if (count($cids))
		{
			$i = 0;

			foreach ($cids as $cid)
			{
				$itemModel   = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
				$item        = $itemModel->getItem($cid);
				$item->id    = null;
				$item->alias = '';
				$item->title = JString::increment($item->title);

				if ($isBatch)
				{
					$accessLevel = $input->getString('access', '');

					if ($accessLevel !== '')
					{
						$item->access = $accessLevel;
					}
				}

				$itemTable = RTable::getAdminInstance('Item', array('ignore_request' => true));
				$itemTable->bind((array) $item);

				if (!$itemTable->check())
				{
				}

				if (!$itemTable->store(true))
				{
				}
				else
				{
					// Copy item's image
					$imageFolder = JPATH_ROOT . '/media/com_reditem/images/item/';

					if (JFile::exists($imageFolder . $cid . "/" . $item->item_image))
					{
						JFile::copy($imageFolder . $cid . "/" . $item->item_image, $imageFolder . $itemTable->id . "/" . $item->item_image);
					}

					// Copy customfields
					$itemTable->copyCustomfields($itemTable->type_id, $cid, $itemTable->id);

					// Copy categories Xref
					$removeOrigin = $input->getString('removeOrigin', 'yes');

					if (!$isBatch)
					{
						$itemTable->copyCategoriesXref($cid, $itemTable->id);
					}
					elseif ($isBatch && $removeOrigin === 'no')
					{
						$itemTable->copyCategoriesXref($cid, $itemTable->id);
					}

					// Add batch categories for copied items
					if ($isBatch)
					{
						$batchCategories = $input->get('batchCategories', array(), 'array');
						$itemTable->createCategoriesXref($itemTable->id, $batchCategories);
					}

					$i++;
				}
			}

			JFactory::getApplication()->enqueueMessage($i . " " . JText::_('COM_REDITEM_ITEMS_COPY_SUCCESSFUL'));
		}
	}

	/**
	 * Method for search items base on item's title and all types
	 *
	 * @return  void
	 */
	public function ajaxSearchItem()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$result = new JRegistry;

		$search       = $app->input->getHtml('search', '');
		$categories   = $app->input->getString('categories', '');
		$customfields = $app->input->getInt('customfields', 1);

		if (empty($search))
		{
			$result->loadArray(array('items' => array()));
			echo $result->toString();

			$app->close();
		}

		// Process for search on all types
		$typesModel = RModel::getAdminInstance('Types', array('ignore_request'), 'com_reditem');
		$typesModel->setState('filter.published', 1);
		$typesModel->setState('list.select', $db->qn(array('id', 'table_name')));
		$types = $typesModel->getItems();

		if (empty($types))
		{
			$result->loadArray(array('items' => array()));
			echo $result->toString();

			$app->close();
		}

		$items = array();

		foreach ($types as $type)
		{
			$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
			$itemsModel->setState('filter.filter_types', (int) $type->id);

			if (!empty($categories))
			{
				if (!is_array($categories))
				{
					$categories = explode(",", $categories);
				}

				$itemsModel->setState('filter.catid', $categories);
			}

			$itemsModel->setState('filter.published', 1);
			$itemsModel->setState('filter.search', $search);

			if ($customfields)
			{
				$search = new JRegistry(array('value' => $search, 'table' => '#__reditem_types_' . $type->table_name));

				$itemsModel->setState('filter.cfSearch', $search->toString());
			}

			$itemsResult = $itemsModel->getItems();

			// Process check view permission for sub-categories list.
			ReditemHelperACL::processItemACL($itemsResult);

			if (!empty($itemsResult))
			{
				$itemIds = ReditemHelperItem::getItemIds($itemsResult);
				$cfValues = ReditemHelperItem::getCustomFieldValues($itemIds);
				$categories = ReditemHelperItem::getCategories($itemIds, false);

				foreach ($itemsResult as $item)
				{
					if (isset($cfValues[$item->type_id][$item->id]))
					{
						$item->customfield_values = $cfValues[$item->type_id][$item->id];
					}

					if (isset($categories[$item->id]))
					{
						$item->categories = $categories[$item->id];
					}

					$item->link = JRoute::_(ReditemRouterHelper::getItemRoute($item->id), false);

					if (!empty($item->categories))
					{
						$item->categoryFirstId = $categories[0];
					}
				}
			}

			$items = array_merge($items, $itemsResult);
		}

		$result->loadArray(array('items' => $items));
		echo $result->toString();

		$app->close();
	}
}
