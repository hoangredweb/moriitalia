<?php
/**
 * @package     Webservices
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.2
 */
class RApiHalHelperSiteReditemcategories
{
	/**
	 * Method to get the row form.
	 *
	 * @return	mixed	A list object on success, false on failure
	 *
	 * @since	1.4
	 */
	public function getItems()
	{
		// Load redITEM Library
		JLoader::import('reditem.library');

		$input = JFactory::getApplication()->input;

		// Get categories model
		$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');

		// Set states
		$filterSearch = $input->getString('filter_search', '');
		$categoriesModel->setState('filter.search', $filterSearch);

		$filterTypes = $input->get('filter_types', array(), 'ARRAY');
		$categoriesModel->setState('filter.filter_types', $filterTypes);

		$filterIds = $input->get('filter_ids', array(), 'ARRAY');
		$categoriesModel->setState('filter.ids', $filterIds);

		$parent = $input->getInt('filter_parentid', 0);
		$categoriesModel->setState('filter.parentid', $parent);

		$published = $input->getString('filter_published', '');
		$categoriesModel->setState('filter.published', $published);

		$featured = $input->getString('filter_featured', '');
		$categoriesModel->setState('filter.featured', $featured);

		$limit = $input->getInt('list_limit', 0);
		$categoriesModel->setState('list.limit', $limit);

		$value = $input->getInt('list_start', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$categoriesModel->setState('list.start', $limitstart);

		$ordering = $input->getString('ordering', 'title');
		$categoriesModel->setState('list.ordering', $ordering);

		$direction = $input->getString('direction', 'asc');
		$categoriesModel->setState('list.direction', $direction);

		$categories = $categoriesModel->getItems();

		if (!$categories)
		{
			return false;
		}

		if ($input->getBool('includeItems', false))
		{
			// Get items model
			$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');

			foreach ($categories as &$category)
			{
				$itemsModel->setState('filter.catid', $category->id);
				$category->items = $itemsModel->getItems();

				if ($input->getBool('includeItemsFields', false))
				{
					$ids = ReditemHelperItem::getItemIds($category->items);
					$cfValues = ReditemHelperItem::getCustomFieldValues($ids);

					if (!empty($cfValues))
					{
						foreach ($category->items as $item)
						{
							if (isset($cfValues[$item->type_id][$item->id]))
							{
								$item->customfield_values = $cfValues[$item->type_id][$item->id];
							}
						}
					}
				}
			}
		}

		return $categories;
	}
}
