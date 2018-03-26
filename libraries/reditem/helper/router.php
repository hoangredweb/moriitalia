<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Router helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Router
 * @since       2.1
 *
 */
class ReditemHelperRouter
{
	/**
	 * Router lookup
	 *
	 * @var  array
	 */
	protected static $lookup;

	/**
	 * Get Item route link
	 *
	 * @param   int  $id          The route of the content item
	 * @param   int  $categoryId  Category ID for look up menu item id
	 *
	 * @return  string            Link
	 */
	public static function getItemRoute($id, $categoryId = 0)
	{
		$needles = array(
			'itemdetail' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_reditem&view=itemdetail&id=' . $id;

		if ($item = self::findItem($needles, $id, 'item'))
		{
			$link .= '&Itemid=' . $item;
		}
		// Find item id of parent category if neccessary
		elseif (!empty($categoryId) && $categoryId != 0)
		{
			if ($item = self::findItem(array('categorydetail' => array($categoryId)), $categoryId, 'category'))
			{
				$link .= '&Itemid=' . $item;
			}
			elseif ($item = self::findItem(null, $categoryId, 'category'))
			{
				$link .= '&Itemid=' . $item;
			}
		}
		// Find menu item follow parent deeply
		elseif ($item = self::findItem(null, $id, 'item'))
		{
			$link .= '&Itemid=' . $item;
		}
		else
		{
			$link .= '&Itemid=' . JFactory::getApplication()->input->getInt('Itemid', 0);
		}

		return $link;
	}

	/**
	 * Get Category route link
	 *
	 * @param   int  $id      The route of the content item
	 * @param   int  $itemId  Custom itemId pass by user
	 *
	 * @return  string  Link
	 */
	public static function getCategoryRoute($id, $itemId = null)
	{
		$needles = array(
			'categorydetail'  => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_reditem&view=categorydetail&id=' . $id;

		if (isset($itemId))
		{
			$link .= '&Itemid=' . $itemId;
		}
		elseif ($item = self::findItem($needles, $id, 'category'))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::findItem(null, $id, 'category'))
		{
			$link .= '&Itemid=' . $item;
		}
		else
		{
			$link .= '&Itemid=' . JFactory::getApplication()->input->getInt('Itemid', 0);
		}

		return $link;
	}

	/**
	 * Find items
	 *
	 * @param   array   $needles  Array of require.
	 * @param   int     $typeId   Type id (In case item is item ID, in case category is category ID)
	 * @param   string  $type     Type to search parents for.
	 *
	 * @return  string
	 */
	protected static function findItem($needles, $typeId, $type)
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (is_null(self::$lookup))
		{
			self::$lookup = array();
			$component    = JComponentHelper::getComponent('com_reditem');
			$items        = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view]))
					{
						self::$lookup[$view] = array();
					}

					if (isset($item->query['id']))
					{
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$view][(int) $id]))
						{
							return self::$lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			if ($type == 'item')
			{
				$parents = self::getItemParents($typeId);
			}
			else
			{
				$parents = self::getCategoryParents($typeId);
			}

			if (empty($parents) || !isset(self::$lookup['categorydetail']))
			{
				return null;
			}

			if (isset(self::$lookup['categorydetail']))
			{
				foreach ($parents as $parent)
				{
					$keys = array_keys(self::$lookup['categorydetail']);

					if (in_array($parent, $keys))
					{
						return self::$lookup['categorydetail'][$parent];
					}
				}
			}
			else
			{
				$active = $menus->getActive();

				if ($active)
				{
					return $active->id;
				}
			}
		}

		return null;
	}

	/**
	 * Get item parent categories, ordered by level.
	 *
	 * @param   int  $id  Item id.
	 *
	 * @return  array  Categories ids.
	 */
	public static function getItemParents($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('c2.id'))
			->from($db->qn('#__reditem_item_category_xref', 'icx'))
			->innerJoin(
				$db->qn('#__reditem_categories', 'c1') . ' ON ' . $db->qn('icx.category_id') . ' = ' . $db->qn('c1.id') .
				' AND ' . $db->qn('icx.item_id') . ' = ' . (int) $id
			)
			->innerJoin(
				$db->qn('#__reditem_categories', 'c2') . ' ON ' . $db->qn('c2.lft') . ' <= ' . $db->qn('c1.lft') .
				' AND ' . $db->qn('c2.rgt') . ' >= ' . $db->qn('c1.rgt')
			)
			->order($db->qn('c2.lft') . ' DESC');
		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Get category parent categories, ordered by level.
	 *
	 * @param   int  $id  Item id.
	 *
	 * @return  array  Categories ids.
	 */
	public static function getCategoryParents($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('c2.id'))
			->from($db->qn('#__reditem_categories', 'c1'))
			->innerJoin(
				$db->qn('#__reditem_categories', 'c2') . ' ON ' . $db->qn('c2.lft') . ' < ' . $db->qn('c1.lft') .
				' AND ' . $db->qn('c2.rgt') . ' > ' . $db->qn('c1.rgt')
			)
			->where($db->qn('c1.id') . ' = ' . (int) $id)
			->order($db->qn('c2.lft') . ' ASC');
		$db->setQuery($query);

		return $db->loadColumn();
	}
}
