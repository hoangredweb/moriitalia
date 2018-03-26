<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

require_once JPATH_LIBRARIES . '/reditem/helper/item.php';
require_once JPATH_LIBRARIES . '/reditem/helper/category.php';

/**
 * Plugin to change item status when user status changed
 *
 * @package  RedITEM.Plugin
 *
 * @since    1.0
 */
class PlgReditemUser_Behaviors extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Event after user deactivated
	 *
	 * @param   int  $userId  User Id
	 *
	 * @return  void
	 */
	public function onAfterUserDeactivated($userId)
	{
		$user = ReditemHelperSystem::getUser($userId);

		// Make sure user exist and not Guest.
		if ($user->guest)
		{
			return;
		}

		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('filter.created_user', (int) $user->id);
		$itemsModel->setState('filter.published', 1);

		$items = $itemsModel->getItems();

		$ids = ReditemHelperItem::getItemIds($items);
		$categories = ReditemHelperItem::getCategories($ids, false);

		foreach ($items as $item)
		{
			// Check category settings if we should unpublish this item or not
			$check = false;
			$categories = array();

			if (isset($categories[$item->id]))
			{
				$categories = $categories[$item->id];
			}

			if ($categories)
			{
				foreach ($categories as $category)
				{
					$params = new JRegistry($category->params);

					if ($params->get('category_item_unpublish_after_user_deactivated'))
					{
						$check = true;
						break;
					}
				}
			}

			// Unpublish item if condition meets
			if ($check)
			{
				// Add include path
				JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_reditem/tables');
				$itemTable = JTable::getInstance('Item', 'ReditemTable', array('ignore_request' => true));

				if (!$itemTable->load($item->id))
				{
					continue;
				}

				// Set item to unpublished state
				$itemTable->published = 0;

				// Try to store item
				if (!$itemTable->store())
				{
					$this->app->enqueueMessage(JText::_('PLG_REDITEM_USER_BEHAVIORS_CANT_STORE_ITEM'), 'warning');
				}
			}
		}
	}

	/**
	 * Event after user deleted
	 *
	 * @param   int  $userId  User Id
	 *
	 * @return  boolean
	 */
	public function onAfterUserDeleted($userId)
	{
		$user = ReditemHelperSystem::getUser($userId);

		// Make sure user exist and not Guest.
		if ($user->guest)
		{
			return;
		}

		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('filter.created_user', (int) $user->id);
		$itemsModel->setState('filter.published', 1);

		$items = $itemsModel->getItems();
		$ids = ReditemHelperItem::getItemIds($items);
		$categories = ReditemHelperItem::getCategories($ids, false);

		foreach ($items as $item)
		{
			$categories = array();
			$check = false;

			if (isset($categories[$item->id]))
			{
				$categories = $categories[$item->id];
			}

			if ($categories)
			{
				foreach ($categories as $category)
				{
					$params = new JRegistry($category->params);

					if ($params->get('category_item_unpublish_after_user_deleted'))
					{
						$check = true;
						break;
					}
				}
			}

			// Unpublish item if condition meets
			if ($check)
			{
				// Add include path
				JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_reditem/tables');
				$itemTable = JTable::getInstance('Item', 'ReditemTable', array('ignore_request' => true));

				if (!$itemTable->load($item->id))
				{
					continue;
				}

				// Set item to unpublished state
				$itemTable->published = 0;

				// Try to store item
				if (!$itemTable->store())
				{
					$this->app->enqueueMessage(JText::_('PLG_USER_REDITEM_USER_BEHAVIORS_CANT_STORE_ITEM'), 'warning');
				}
			}
		}
	}
}
