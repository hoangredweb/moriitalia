<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die;

JLoader::import('reditem.library');
require_once JPATH_SITE . '/components/com_reditem/helpers/route.php';

/**
 * Reditem Helper for Site
 *
 * @package     RedITEM.Frontend
 * @subpackage  Helper.Helper
 * @since       2.0
 *
 */
class ReditemHelper
{
	/**
	 * Get menu item ID of category
	 *
	 * @param   int      $id        Category ID
	 * @param   boolean  $recusive  Recusive find on it's parent
	 *
	 * @return  boolean/int
	 */
	public static function getCategoryMenuItem($id = 0, $recusive = true)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select($db->qn('c.parent_id'))
			->from($db->qn('#__reditem_categories', 'c'))
			->where($db->qn('c.id') . ' = ' . (int) $id);

		$db->setQuery($query);

		$category = $db->loadObject();

		if ($category)
		{
			$link = 'index.php?option=com_reditem&view=categorydetail&id=' . $id;

			$query = $db->getQuery(true);
			$query->select($db->qn('id'))
				->from($db->qn('#__menu'))
				->where($db->qn('published') . ' = ' . $db->quote('1'))
				->where($db->qn('link') . ' = ' . $db->quote($link));

			$db->setQuery($query);
			$menu = $db->loadObject();

			if ($menu)
			{
				return $menu->id;
			}
			elseif ($recusive)
			{
				// No menu item, get of this parent
				return self::getCategoryMenuItem($category->parent_id);
			}
		}

		return false;
	}

	/**
	 * Get menu item ID of category but optimized performance
	 *
	 * @param   object  &$categories    Category instance
	 * @param   object  $property       Name of property to assign link
	 * @param   object  $categoryModel  Category model instance
	 * @param   object  $db             Database object
	 * @param   object  $app            Application object
	 * @param   array   &$menus         Array of menus object
	 *
	 * @return  void
	 */
	public static function getCategoryMenuItemOptimized(&$categories, $property = 'link', $categoryModel = null, $db = null, $app = null,
		&$menus = array())
	{
		$link = 'index.php?option=com_reditem&view=categorydetail&id=';

		if (!isset($categoryModel))
		{
			$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		}

		if (!isset($db))
		{
			$db = JFactory::getDBO();
		}

		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}

		if (!isset($menus))
		{
			$query = $db->getQuery(true);
			$query->select($db->qn('id') . ', ' . $db->qn('link'))
				->from($db->qn('#__menu'))
				->where(
					$db->qn('type') . ' = ' . $db->q('component')
					. ' AND ' . $db->qn('link') . ' LIKE ' . $db->q('%option=com_reditem&view=categorydetail%')
					. ' AND ' . $db->qn('published') . ' = 1'
				);
			$db->setQuery($query);
			$menus = $db->loadObjectList();
		}

		// Heuristic
		if (count($menus))
		{
			$startKey = '&id=';

			foreach ($menus as &$menu)
			{
				$linkLen	= JString::strlen($menu->link);
				$startPos	= JString::strpos($menu->link, $startKey);
				$startPos	+= JString::strlen($startKey);
				$findId 	= '';

				for ($i = $startPos; $i <= $linkLen; $i++)
				{
					$nextNumber = JString::substr($menu->link, $i, 1);

					if (!is_numeric($nextNumber))
					{
						break;
					}
					else
					{
						$findId .= $nextNumber;
					}
				}

				if (is_numeric($findId) && ($findId !== ""))
				{
					$menu->categoryInstance = $categoryModel->getItem((int) $findId);

					// Child links
					if (!empty($categories) && $menu->categoryInstance)
					{
						foreach ($categories as &$category)
						{
							if (($category->lft >= $menu->categoryInstance->lft) && ($category->rgt <= $menu->categoryInstance->rgt))
							{
								if (!isset($category->ancient) || ($menu->categoryInstance->id > $category->ancient))
								{
									$category->$property = JRoute::_($link . $category->id . '&Itemid=' . $menu->id, false);
									$category->ancient = $findId;
								}
							}
							else
							{
								if (!isset($category->ancient))
								{
									$itemId = $app->input->getInt('Itemid', 0);
									$category->$property = JRoute::_($link . $category->id . '&Itemid=' . $itemId, false);
								}
							}
						}
					}
				}
				else
				{
					if (!empty($categories))
					{
						foreach ($categories as &$category)
						{
							if (!isset($category->ancient))
							{
								$itemId = $app->input->getInt('Itemid', 0);
								$category->$property = JRoute::_($link . $category->id . '&Itemid=' . $itemId, false);
							}
						}
					}
				}
			}
		}
		else
		{
			if (!empty($categories))
			{
				foreach ($categories as &$category)
				{
					$itemId = $app->input->getInt('Itemid', 0);
					$category->$property = JRoute::_($link . $category->id . '&Itemid=' . $itemId, false);
				}
			}
		}
	}

	/**
	 * Get menu item ID of item
	 *
	 * @param   int  $id  Item ID
	 *
	 * @return  boolean/int
	 */
	public function getItemMenuItem($id = 0)
	{
		$db = JFactory::getDBO();
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
		$item = $itemModel->getItem($id);

		if ($item)
		{
			$link = 'index.php?option=com_reditem&view=itemdetail&id=' . $item->id;

			$query = $db->getQuery(true);
			$query->select($db->qn('id'))
				->from($db->qn('#__menu'))
				->where($db->qn('published') . ' = ' . $db->quote('1'))
				->where($db->qn('link') . ' = ' . $db->quote($link));

			$db->setQuery($query);
			$menu = $db->loadObject();

			if ($menu)
			{
				return $menu->id;
			}
		}

		return false;
	}

	/**
	 * Get deeper subCategories
	 *
	 * @param   mixed   $categoryId       array or int
	 * @param   object  $categoriesModel  Model of categories
	 *
	 * @return  array
	 */
	public static function getSubCategories($categoryId, $categoriesModel = null)
	{
		$db = JFactory::getDBO();

		if (!$categoryId)
		{
			return array();
		}

		if (!isset($categoriesModel))
		{
			$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
		}

		if (!is_array($categoryId))
		{
			$categoryId = array($categoryId);
		}

		$categoriesModel->setState('filter.published', 1);
		$categoriesModel->setState('filter.ids', $categoryId);
		$categoriesModel->setState('list.select', 'c.id, c.lft, c.rgt');
		$query = $categoriesModel->getListQuery();
		$db->setQuery($query);
		$parents = $db->loadObjectList();

		if (!isset($parents) && !count($parents))
		{
			return array();
		}

		$categoriesModel->setState('filter.ids', null);
		$categoriesModel->setState('filter.isOrder', false);
		$categoriesModel->setState('list.select', 'DISTINCT ' . $db->qn('c.id'));

		$query->clear();

		for ($i = 0; $i < count($parents); $i++)
		{
			$categoriesModel->setState('filter.lft', (int) $parents[$i]->lft + 1);
			$categoriesModel->setState('filter.rgt', (int) $parents[$i]->rgt - 1);

			if ($i == 0)
			{
				$query = $categoriesModel->getListQuery();
			}
			else
			{
				$query->union($categoriesModel->getListQuery());
			}
		}

		$db->setQuery($query);
		$subCategories = $db->loadColumn();

		return $subCategories;
	}

	/**
	 * Get all related categories
	 *
	 * @param   int     $categoryId  Category Id
	 * @param   object  $db          Database object
	 * @param   object  $query       Query object
	 *
	 * @return  array
	 */
	public static function getRelatedCategories($categoryId, $db = null, $query = null)
	{
		if (!isset($db))
		{
			$db = JFactory::getDBO();
		}

		if (!isset($query))
		{
			$query = $db->getQuery(true);
		}

		$relatedCatIds = array();
		$relatedCategories = array();

		$categoryId = (int) $categoryId;

		if (!$categoryId)
		{
			return false;
		}

		$query->select($db->qn('parent_id'))
			->from($db->qn('#__reditem_category_related'))
			->where($db->qn('related_id') . ' = ' . $db->quote($categoryId));
		$db->setQuery($query);
		$relatedCatIds = $db->loadColumn();

		if (!$relatedCatIds)
		{
			return false;
		}

		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

		foreach ($relatedCatIds as $relatedCatId)
		{
			$relatedCategories[] = $categoryModel->getItem($relatedCatId);
		}

		return $relatedCategories;
	}
}
