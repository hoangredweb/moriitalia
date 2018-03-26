<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('reditem.library');

/**
 * Categorydetail items gmap module helper.
 * NOTE: This is applus specific module atm. Idea is to generalize this module later on.
 *
 * @since  1.0
 */
class ModredITEMCategoryDetailItemsGmapHelper
{
	/**
	 * Module params.
	 *
	 * @var Object
	 */
	private static $params = null;

	/**
	 * Array of map items per category.
	 *
	 * @var array
	 */
	private static $items = array();

	/**
	 * Params setter function.
	 *
	 * @param   Object  $params  Params object.
	 *
	 * @return  void
	 */
	public static function setParams($params)
	{
		self::$params = $params;
	}

	/**
	 * Init module function.
	 *
	 * @return  array
	 */
	public static function init()
	{
		$input  = JFactory::getApplication()->input;
		$result = array('items' => array(), 'filters' => '');

		if ($input->getString('option', '') == 'com_reditem' && $input->getString('view', '') == 'categorydetail')
		{
			$category = $input->getInt('id', self::$params->get('category', 0));
		}
		else
		{
			$category = self::$params->get('category', 0);
		}

		// Prepare items list
		$result['items'] = self::getData($category);

		foreach ($result['items'] as $item)
		{
			$params                 = new JRegistry($item->params);
			$item->itemLatLng       = $params->get('itemLatLng');
			$item->location_address = $params->get('itemAddress');
			$item->link             = JRoute::_(ReditemHelperRouter::getItemRoute($item->id));
			$item->html             = self::getItemHtml($item);
			//$item->types            = array($item->type);
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('options'))
			->from($db->qn('#__reditem_fields'))
			->where($db->qn('fieldcode') . ' = ' . $db->q('type'));
		$db->setQuery($query);
		$options = $db->loadResult();
		$tmp     = explode("\n", $options);
		$filters = array();

		$all         = new stdClass;
		$all->active = false;
		$all->text   = JText::_('JALL');
		$all->value  = '';
		$filters[]   = $all;

		foreach ($tmp as $t)
		{
			$exp            = explode('|', $t);
			$filter         = new stdClass;
			$filter->value  = trim($exp[0]);
			$filter->text   = $exp[0];
			$filter->active = false;

			if (isset($exp[1]))
			{
				$filter->text = $exp[1];
			}

			$filters[] = $filter;
		}

		$result['filters'] = $filters;
		$ip    = self::ip2Long($_SERVER['REMOTE_ADDR']);
		$query = $db->getQuery(true);
		$query->select(
			array (
				$db->qn('latitude', 'lat'),
				$db->qn('longitude', 'lng'),
				$db->qn('city_name', 'city'),
				$db->qn('country_name', 'country'),
				'(' . $ip . ' - CAST(' . $db->qn('ip_from') . ' AS SIGNED)) AS ' . $db->qn('close_from'),
				'(CAST(' . $db->qn('ip_to') . ' AS SIGNED) - ' . $ip . ') AS ' . $db->qn('close_to')
			)
		)
			->from($db->qn('#__ipligence2'))
			->where($db->q($ip) . ' BETWEEN ' . $db->qn('ip_from') . ' AND ' . $db->qn('ip_to'))
			->order($db->qn('close_from') . ' ASC, ' . $db->qn('close_to') . ' ASC');
		$location = $db->setQuery($query)->loadObject();

		if (!empty($location) && !empty($location->city) && !empty($location->country))
		{
			$result['lat'] = $location->lat;
			$result['lng'] = $location->lng;
		}
		else
		{
			$result['lat'] = 0.0;
			$result['lng'] = 0.0;
		}

		// Default location: HCM
		$result['lat'] = 10.7782501;
		$result['lng'] = 106.70202790000008;

		// Default location: HN
		// $result['lat'] = 21.016313;
		// $result['lng'] = 105.82850200000007;

		return $result;
	}

	/**
	 * Function for converting ip address to a long number.
	 *
	 * @param   string  $ip  IP address for converting.
	 *
	 * @return  float  IP as long number.
	 */
	private static function ip2Long($ip)
	{
		$d = 0.0;
		$b = explode(".", $ip, 4);

		for ($i = 0; $i < 4; $i++)
		{
			$d *= 256.0;
			$d += $b[$i];
		};

		return $d;
	}

	/**
	 * Ajax function for getting results shown on map atm.
	 *
	 * @return  object  Json object with results.
	 */
	public static function getOnMapResultsAjax()
	{
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$ids    = $input->get('ids', array(0), 'array');
		$cat    = $input->getInt('category', 0);
		$search = mb_strtolower($input->getString('search', ''), 'UTF-8');
		$items  = self::getData($cat);
		$html   = '';
		$res1   = array();
		$res2   = array();
		$data   = array();

		foreach ($items as $item)
		{
			if (in_array($item->id, $ids))
			{
				$params                 = new JRegistry($item->params);
				$item->location_address = $params->get('itemAddress');
				$item->link             = JRoute::_(ReditemHelperRouter::getItemRoute($item->id));

				if (!empty($search))
				{
					if (strpos(mb_strtolower($item->title, 'UTF-8'), $search) !== false
						|| strpos(mb_strtolower($item->location_address, 'UTF-8'), $search) !== false)
					{
						$res1[] = $item;
					}
					else
					{
						$res2[] = $item;
					}
				}
				else
				{
					$data[] = $item;
				}
			}
		}

		if (!empty($search))
		{
			$data = array_merge($res1, $res2);
		}

		foreach ($data as $item)
		{
			$html .= self::getItemHtml($item);
		}

		$return = new stdClass;
		$return->html = $html;

		return $return;
	}

	/**
	 * Get items data.
	 *
	 * @param   int  $category  Category id.
	 *
	 * @return  array  Array of items object data.
	 */
	public static function getData($category = 0)
	{
		if (!isset(self::$items[$category]) || empty(self::$items[$category]))
		{
			require_once JPATH_LIBRARIES . '/reditem/helper/type.php';
			require_once JPATH_LIBRARIES . '/reditem/helper/category.php';

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$table = ReditemHelperType::getTableName(2);

			$query->select(
				array (
					$db->qn('i.id'),
					$db->qn('i.title'),
					$db->qn('i.params'),
					$db->qn('ri') . '.*'
				)
			)
				->from($db->qn('#__reditem_items', 'i'))
				->innerJoin($db->qn($table, 'ri') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('ri.id'))
				->where($db->qn('i.blocked') . ' = 0')
				->where($db->qn('i.published') . ' = 1')
				// Hard coding type id, for applus.dk needs
				->where($db->qn('i.type_id') . ' = 2');

			if ($category)
			{
				$cats = ReditemHelperCategory::getChildrenCategories(array($category));
				$query->innerJoin($db->qn('#__reditem_item_category_xref', 'icx') . ' ON ' . $db->qn('icx.item_id') . ' = ' . $db->qn('i.id'))
					->where($db->qn('icx.category_id') . ' IN (' . implode(',', $cats) . ')');
			}

			$query->group($db->qn('i.id'));
			//$query->order($db->qn('ri.post_nr') . ' ASC');
			$db->setQuery($query);

			self::$items[$category] = $db->loadObjectList();
		}

		return self::$items[$category];
	}

	/**
	 * Get single item template.
	 *
	 * @param   object  $item  Item object.
	 *
	 * @return  string  Item html string.
	 */
	private static function getItemHtml($item)
	{
		$iconBase = JUri::base() . 'templates/redcomponent/images/';

		$html = '<div class="infoBoxItem">
					<div class="icon"></div>
					<div class="content">
						<div class="title"><a href="'.$item->link.'">' . $item->title . '</a></div>
						<div class="info">';

		/*if (!empty($item->adresse) && !empty($item->post_nr) && !empty($item->by))
		{
			$html .= '<div class="location"><img src="' . $iconBase . '/address.svg"><div class="address">' . $item->adresse . '<br />' . $item->post_nr . ' ' . $item->by . '</div></div>';
		}

		if (!empty($item->telefon))
		{
			$html .= '<div class="phone"><img src="' . $iconBase . '/phone.svg">' . $item->telefon . '</div>';
		}*/

		/*if (!empty($item->website))
		{
			$website = json_decode($item->website);

			if (!empty($website[0]))
			{
				$html .= '<div class="website"><a href="' . $website[0] . '"><img src="' . $iconBase . '/link.svg"></a></div>';
				$item->website = $website[0];
			}
		}*/

		$html .= '</div></div></div>';

		return $html;
	}
}
