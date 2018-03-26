<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_reditem/helpers/reditem.php';

/**
 * Categories helper
 *
 * @since  1.0
 */
class ModredITEMCategoriesHelper
{
	/**
	 * Get list of categories
	 *
	 * @param   array  &$params  Params
	 *
	 * @return  array
	 */
	public static function getList(&$params)
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$result	= array('parent' => null, 'sub' => array());

		$categoryId   = $params->get('parent', 0);

		// Check permission for this category
		$canView = (boolean) ReditemHelperACL::checkCategoryPermission('category.view', $categoryId);

		if (!$canView)
		{
			return $result;
		}

		$featuredCats = $params->get('featured_categories', 0);
		$imgW         = $params->get('image_width', 100);
		$imgH         = $params->get('image_height', 100);
		$ordering     = 'c.' . $params->get('subcat_ordering', 'title');
		$destination  = $params->get('subcat_destination', 'asc');
		$limit        = $params->get('limit', 0);

		// Get Admin model
		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');

		// Get parent Itemid for module, remove old recursive logic
		$query = $db->getQuery(true)
			->select($db->qn('id') . ', ' . $db->qn('link'))
			->from($db->qn('#__menu'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('link') . ' LIKE ' . $db->q('%option=com_reditem&view=categorydetail%'))
			->where($db->qn('published') . ' = 1');

		$db->setQuery($query);
		$menus = $db->loadObjectList();

		// Get subCategories of current category
		$parentCategory = $categoryModel->getItem($categoryId);

		// Prepare type object
		if (!isset($parentCategory->type))
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$parentCategory->type = $typeModel->getItem($parentCategory->type_id);
		}

		// Get sub-categories
		$categoriesModel->setState('filter.published', 1);
		$categoriesModel->setState('filter.featured', $featuredCats);
		$categoriesModel->setState('filter.lft', $parentCategory->lft + 1);
		$categoriesModel->setState('filter.rgt', $parentCategory->rgt - 1);
		$select = array('DISTINCT (c.id)', 'c.title', 'c.alias', 'c.parent_id', 'c.category_image', 'c.lft', 'c.rgt', 'c.params');
		$categoriesModel->setState('list.select', $select);
		$categoriesModel->setState('list.ordering', $ordering);
		$categoriesModel->setState('list.destination', $destination);

		$query = $categoriesModel->getListQuery();
		$db->setQuery($query, 0, $limit);
		$subCategories = $db->loadObjectList();

		// Process check view permission for sub-categories list.
		ReditemHelperACL::processCategoryACL($subCategories);

		$temp = array($parentCategory);

		// Parent readmore link
		ReditemHelper::getCategoryMenuItemOptimized($temp, 'readmoreLink', $categoryModel, $db, $app, $menus);

		// Image link for parentCategory
		$parentCategory->readmoreImage = ReditemHelperImage::getImageLink(
			$parentCategory,
			'category',
			$parentCategory->category_image,
			'module',
			$imgW,
			$imgH,
			true
		);

		if (!empty($subCategories))
		{
			// Children link
			ReditemHelper::getCategoryMenuItemOptimized($subCategories, 'link', $categoryModel, $db, $app, $menus);

			foreach ($subCategories as &$category)
			{
				$category->type = $parentCategory->type;

				// Image link for subCategories
				$category->category_image = ReditemHelperImage::getImageLink(
					$category,
					'category',
					$category->category_image,
					'module',
					$imgW,
					$imgH,
					true
				);
			}

			$result['sub'] = $subCategories;
		}

		$result['parent'] = $parentCategory;

		return $result;
	}

	/**
	 * Get Category link
	 *
	 * @param   int  $id        Category Id
	 * @param   int  $parentId  Parent Id
	 *
	 * @return  string
	 */
	public static function getCategoryLink($id, $parentId = 0)
	{
		$app = JFactory::getApplication();

		$categoryItemId = ReditemHelper::getCategoryMenuItem($id);

		if (!$categoryItemId)
		{
			$categoryItemId = $app->input->getInt('Itemid', 0);
		}

		$categoryLink = 'index.php?option=com_reditem&amp;view=categorydetail&amp;id=' . $id;

		if ($parentId)
		{
			$categoryLink .= '&amp;cid=' . $parentId;
		}

		$categoryLink .= '&amp;Itemid=' . $categoryItemId;

		return JRoute::_($categoryLink);
	}
}
