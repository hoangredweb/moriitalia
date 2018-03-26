<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class producthelper extends producthelperDefault
{
	/**
	 * Get menu detail
	 *
	 * @param   string  $link  Link
	 *
	 * @return mixed|null
	 */
	public function getMenuDetail($link = '')
	{
		// Do not allow queries that load all the items
		if ($link != '')
		{
			$app = JFactory::getApplication();
			$language = JFactory::getLanguage();
			$menu = $app->getMenu();
			$res = $menu->getItems('link', $link, true);
			$items = $menu->getMenu();
			$data = array();

			foreach ($items as $key => $item)
			{
				if (count(array_diff($item->query, $menu->getActive()->query)) === 0)
				{
					$data[$item->language]['id'] = $item->id;
					$data[$item->language]['title'] = $item->title;
				}
			}

			$tag = $language->get('tag');

			if ($tag == 'vi-VN')
			{
				$res->id = $data['*']['id'];
				$res->title = $data['*']['title'];
			}
			else
			{
				$res->id = $data['en-GB']['id'];
				$res->title = $data['en-GB']['title'];
			}

			return $res;
		}

		return null;
	}
}