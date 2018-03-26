<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  RedITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Category RSS view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Feed
 * @since       2.1
 */
class ReditemViewCategoryDetail extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		// Parameters
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$redItemConfig = JComponentHelper::getParams('com_reditem');

		// Get category ID
		$categoryId = $app->input->getInt('id', 0);

		if (!$categoryId)
		{
			JError::raiseError(500, JText::_('COM_REDITEM_CATEGORYDETAIL_FEED_ERROR_CATEGORY_ID_NOT_FOUND'));
		}

		// Get category data
		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		$category = $categoryModel->getItem($categoryId);

		if (empty($category))
		{
			JError::raiseError(500, JText::_('COM_REDITEM_CATEGORYDETAIL_FEED_ERROR_CATEGORY_NOT_FOUND'));
		}

		// Set page title by Category title
		$doc->title = $category->title;

		// Prepare link for feed
		$doc->link = JRoute::_(ReditemRouterHelper::getCategoryRoute($category->id));

		// Get limit item from component config
		$limit = (int) $redItemConfig->get('categoryFeedLimit', 5);

		// Get "Include in RSS content" fields
		$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsModel->setState('list.select', 'f.fieldcode');
		$fieldsModel->setState('filter.published', 1);
		$fieldsModel->setState('filter.params', array('includeInRSSContent' => 1));
		$fieldsModel->setState('list.ordering', 'f.ordering');
		$fieldsModel->setState('list.direction', 'asc');
		$rssFields = $fieldsModel->getItems();

		// Get items data
		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('filter.published', 1);
		$itemsModel->setState('list.select', 'DISTINCT (i.id), i.*');
		$itemsModel->setState('list.ordering', 'i.publish_up');
		$itemsModel->setState('list.direction', 'desc');
		$itemsModel->setState('list.limit', $limit);

		// Check if include items in sub-categories
		$includeItemsSubCategories = (boolean) $redItemConfig->get('categoryFeedIncludeSub', true);

		if ($includeItemsSubCategories)
		{
			// Get sub-categories of this category
			$subCategories = ReditemHelper::getSubCategories($category->id);
			array_unshift($subCategories, $category->id);
			$itemsModel->setState('filter.catid', $subCategories);
		}

		$items = $itemsModel->getItems();
		$itemIds = ReditemHelperItem::getItemIds($items);
		$cfValues = ReditemHelperItem::getCustomFieldValues($itemIds);

		// Prepare item categories
		$categories = ReditemHelperItem::getCategories($itemIds, false);

		// Process on item
		foreach ($items as $item)
		{
			if (isset($cfValues[$item->type_id][$item->id]))
			{
				$item->customfield_values = $cfValues[$item->type_id][$item->id];
			}

			// Get item link
			$itemLink = JRoute::_(ReditemHelperRouter::getItemRoute($item->id));

			// Prepare description
			$description = '';

			foreach ($rssFields as $field)
			{
				if (isset($item->customfield_values[$field->fieldcode]))
				{
					$description .= '<p>' . strip_tags($item->customfield_values[$field->fieldcode], array('<strong>', '<b>', '<i>')) . '</p>';
				}
			}

			$description .= '<p class="feed-readmore"><a target="_blank" href ="' . $itemLink . '">';
			$description .= JText::_('COM_REDITEM_CATEGORYDETAIL_FEED_ITEM_READ_MORE');
			$description .= '</a></p>';

			if (isset($categories[$item->id]))
			{
				$itemCategories = $categories[$item->id];
			}

			// Prepare user detail
			$userDetail = ReditemHelperSystem::getUser($item->created_user_id);

			// Prepare feed item object
			$feedItem = new JFeedItem;
			$feedItem->title       = html_entity_decode($this->escape($item->title), ENT_COMPAT, 'UTF-8');
			$feedItem->link        = $itemLink;
			$feedItem->date        = $item->publish_up;
			$feedItem->description = '<div class="feed-description">' . $description . '</div>';
			$feedItem->author      = $userDetail->name;
			$feedItem->authorEmail = $userDetail->email;
			$feedItem->category    = array();

			if (!empty($itemCategories))
			{
				foreach ($itemCategories as $itemCategory)
				{
					$feedItem->category[] = $itemCategory->title;
				}
			}

			// Clean up memory
			unset($itemCategories);

			// Loads item info into rss array
			$doc->addItem($feedItem);
		}
	}
}
