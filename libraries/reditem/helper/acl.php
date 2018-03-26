<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM ACL Helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers.ACL
 * @since       2.0
 *
 */
class ReditemHelperACL
{
	/**
	 * Method for check permission of user on type
	 *
	 * @param   string  $permission  Permission string
	 * @param   int     $typeId      ID of type
	 * @param   object  $user        User object
	 *
	 * @return  boolean              True if have permission. False otherwise.
	 */
	public static function checkPermission($permission, $typeId, $user = null)
	{
		$typeId = (int) $typeId;

		if (empty($permission) || (!$typeId))
		{
			return false;
		}

		$assetName = 'com_reditem.type.' . $typeId;

		// If $user is null, get current user
		if (!$user)
		{
			$user = ReditemHelperSystem::getUser();
		}

		return $user->authorise($permission, $assetName);
	}

	/**
	 * Method for check permission of user on category
	 *
	 * @param   string  $permission  Permission string
	 * @param   int     $categoryId  ID of category
	 * @param   object  $user        User object
	 *
	 * @return  boolean              True if have permission. False otherwise.
	 */
	public static function checkCategoryPermission($permission, $categoryId, $user = null)
	{
		$categoryId = (int) $categoryId;

		if (empty($permission) || (!$categoryId))
		{
			return false;
		}

		$assetName = 'com_reditem.category.' . $categoryId;

		// If $user is null, get current user
		if (!$user)
		{
			$user = ReditemHelperSystem::getUser();
		}

		return $user->authorise($permission, $assetName);
	}

	/**
	 * Method for check permission of user on item
	 *
	 * @param   string  $permission  Permission string
	 * @param   int     $itemId      ID of item
	 * @param   object  $user        User object
	 *
	 * @return  boolean              True if have permission. False otherwise.
	 */
	public static function checkItemPermission($permission, $itemId, $user = null)
	{
		$itemId = (int) $itemId;

		if (empty($permission) || (!$itemId))
		{
			return false;
		}

		$assetName = 'com_reditem.item.' . $itemId;

		// If $user is null, get current user
		if (!$user)
		{
			$user = ReditemHelperSystem::getUser();
		}

		return $user->authorise($permission, $assetName);
	}

	/**
	 * Method for check view permission each of category in list
	 *
	 * @param   array   &$categories  Array of Category object
	 * @param   object  $user         User object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function processCategoryACL(&$categories, $user = null)
	{
		if (empty($categories))
		{
			return true;
		}

		if (!isset($user))
		{
			$user = ReditemHelperSystem::getUser();
		}

		foreach ($categories as $key => $category)
		{
			$canView = false;

			if (is_object($category) && !empty($category->id))
			{
				$canView = (boolean) self::checkCategoryPermission('category.view', $category->id, $user);
			}
			elseif (is_numeric($category))
			{
				$canView = (boolean) self::checkCategoryPermission('category.view', $category, $user);
			}

			if (!$canView)
			{
				unset($categories[$key]);
			}
		}

		return true;
	}

	/**
	 * Method for check view permission each of item in list
	 *
	 * @param   array   &$items  Array of Item object
	 * @param   object  $user    User object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function processItemACL(&$items, $user = null)
	{
		if (empty($items))
		{
			return true;
		}

		if (!isset($user))
		{
			$user = ReditemHelperSystem::getUser();
		}

		foreach ($items as $key => $item)
		{
			$canView = (boolean) self::checkItemPermission('item.view', $item->id, $user);

			if (!$canView)
			{
				unset($items[$key]);
			}
		}

		return true;
	}
}
