<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  redITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('reditem.library');

/**
 * Component routing class
 *
 * @since  2.5.1
 */
class ReditemRouter extends JComponentRouterBase
{
	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   2.5.1
	 */
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$app  = JFactory::getApplication();
		$menu = $app->getMenu();

		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
		}

		// Check if plugins are able to build the route
		$dispatcher = JEventDispatcher::getInstance();
		$context    = 'com_reditem.router';
		JPluginHelper::importPlugin('reditem_sef');

		$result = $dispatcher->trigger('onReditemBuildRoute', array($context, &$query, &$segments, $menuItem));

		if (in_array(true, $result, true))
		{
			return $segments;
		}

		$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
		$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

		if (isset($query['view']))
		{
			$view = $query['view'];

			if (empty($query['Itemid']))
			{
				$segments[] = $query['view'];
			}

			unset($query['view']);
		}

		if (isset($view) && ($mView == $view) and (isset($query['id'])) and ($mId == intval($query['id'])))
		{
			unset($query['view']);
			unset($query['id']);
			unset($query['cid']);

			return $segments;
		}

		if (isset($view) and ($view == 'categorydetail' || $view == 'itemdetail'))
		{
			if ($mId != intval($query['id']) || $mView != $view)
			{
				$segments[] = 'reditem';
				$segments[] = $view;
				$id = $query['id'];

				if ($view == 'categorydetail')
				{
					$categorymodel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
					$category = $categorymodel->getItem($id);
					$segments[] = $id . ':' . $category->alias;

					if (isset($query['templateId']) && !empty($query['templateId']))
					{
						$segments[] = $query['templateId'];
					}
				}
				else
				{
					$itemmodel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
					$item = $itemmodel->getItem($id);
					$segments[] = $id . ':' . $item->alias;
				}
			}

			unset($query['id']);
			unset($query['view']);
			unset($query['cid']);
			unset($query['templateId']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   2.5.1
	 */
	public function parse(&$segments)
	{
		$vars = array();

		// Get the active menu item.
		$app  = JFactory::getApplication();
		$menu = $app->getMenu();
		$menuItem = $menu->getActive();

		// Check if plugins are able to parse the route
		$dispatcher = JEventDispatcher::getInstance();
		$context    = 'com_reditem.router';
		JPluginHelper::importPlugin('reditem_sef');

		$result = $dispatcher->trigger('onReditemParseRoute', array($context, &$segments, &$vars, $menuItem));

		if (in_array(true, $result, true))
		{
			return $vars;
		}

		$vars['view'] = $segments[1];
		$vars['id']   = $segments[2];

		if (isset($segments[3]))
		{
			$vars['templateId'] = $segments[3];
		}

		return $vars;
	}
}

if (!function_exists('reditemBuildRoute'))
{
	/**
	 * Method for create query
	 *
	 * @param   array  &$query  A named array
	 *
	 * @return	array
	 *
	 * @since   2.5.1
	 */
	function reditemBuildRoute(&$query)
	{
		$router = new ReditemRouter;

		return $router->build($query);
	}
}

if (!function_exists('reditemParseRoute'))
{
	/**
	 * Parse short link to full link
	 *
	 * @param   array  &$segments  A named array
	 *
	 * @return  array  $vars
	 *
	 * @since   2.5.1
	 */
	function reditemParseRoute(&$segments)
	{
		$router = new ReditemRouter;

		return $router->parse($segments);
	}
}
