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
 * Category Gmap helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.SearchEngine
 * @since       2.1.15
 *
 */
class ReditemHelperSearchengine
{
	/**
	 * Method for get menu of search engine page.
	 *
	 * @return  string  Menu link of search engine page.
	 */
	public static function getSearchEngineManagePage()
	{
		$db     = JFactory::getDbo();
		$itemId = JFactory::getApplication()->input->getInt('Itemid', 0);
		$link   = 'index.php?option=com_reditem&view=searchengine';

		$query = $db->getQuery(true)
			->select('id')
			->from($db->qn('#__menu'))
			->where($db->qn('link') . ' = ' . $db->quote($link));
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result)
		{
			$itemId = $result->id;
		}

		return $link . '&Itemid=' . $itemId;
	}

	/**
	 * Method for get Search Engines list of current user
	 *
	 * @param   boolean  $sendMail  Option for get only Send Mail enabled or get all.
	 * @param   int      $userId    ID of user. Default is get current user.
	 *
	 * @return  array/boolean       Array of search engines. False otherwise.
	 */
	public static function getUserSearchEngines($sendMail = true, $userId = 0)
	{
		$db   = JFactory::getDbo();
		$user = ReditemHelperSystem::getUser();

		if ($userId)
		{
			$user = ReditemHelperSystem::getUser($userId);
		}

		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			return false;
		}

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__reditem_search_engine'))
			->where($db->qn('user_id') . ' = ' . (int) $user->id)
			->where($db->qn('send_mail') . ' = ' . (int) $sendMail);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
