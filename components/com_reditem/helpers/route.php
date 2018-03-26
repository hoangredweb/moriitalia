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

/**
 * Custom field tags
 *
 * @package     RedITEM.Frontend
 * @subpackage  Helper.Helper
 * @since       2.0
 *
 */
abstract class ReditemRouterHelper
{
	protected static $lookup;

	/**
	 * Get Item route link
	 *
	 * @param   int  $id  The route of the content item
	 *
	 * @return  link
	 */
	public static function getItemRoute($id)
	{
		$needles = array(
			'itemdetail'  => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_reditem&view=itemdetail&id=' . $id;

		if ($item = self::findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::findItem())
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
	 * @return  link
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
		elseif ($item = self::findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::findItem())
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
	 * @param   array  $needles  array of require
	 *
	 * @return  string
	 */
	protected static function findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_reditem');

			$items		= $menus->getItems('component_id', $component->id);

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
			$active = $menus->getActive();

			if ($active)
			{
				return $active->id;
			}
		}

		return null;
	}
}
