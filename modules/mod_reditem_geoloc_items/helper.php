<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_geoloc_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('reditem.library');
require_once JPATH_SITE . '/modules/mod_reditem_geoloc_items/helper.php';

/**
 * Categories helper
 *
 * @since  1.0
 */
class ModredITEMGeolocItemsHelper
{
	/**
	 * Get list of categories
	 *
	 * @param   array  &$params  Module parameters
	 *
	 * @return  array
	 */
	public static function getList(&$params)
	{
		$paramCategories    = $params->get('categoriesIds', array());
		$paramSubCat        = $params->get('include_sub', 0);
		$paramOrdering      = $params->get('items_ordering', 'i.alias');
		$paramDirection     = $params->get('items_direction', 'asc');
		$paramLimit         = $params->get('limit', '10');
		$location           = $params->get('location', null);
		$paramFeaturedItems = (int) $params->get('featured_items', '0');
		$db                 = JFactory::getDbo();
		$query              = $db->getQuery(true);

		if ($paramSubCat)
		{
			$categories = array_unique(array_merge(ReditemHelper::getSubCategories($paramCategories), $paramCategories));
		}
		else
		{
			$categories = $paramCategories;
		}

		$query->select(
			array (
				$db->qn('i') . '.*',
				$db->qn('t.table_name', 'table')
			)
		)
			->from($db->qn('#__reditem_items', 'i'))
			->innerJoin($db->qn('#__reditem_item_category_xref', 'icx') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('icx.item_id'))
			->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('i.type_id'))
			->where($db->qn('icx.category_id') . ' IN (' . implode(',', $categories) . ')')
			->where($db->qn('i.featured') . ' = ' . $paramFeaturedItems)
			->order($db->qn($paramOrdering) . ' ' . $paramDirection);

		if (!empty($location))
		{
			$db->setQuery($query);
			$items = $db->loadObjectList();
			$items = self::findClosest($location->lat, $location->lng, $items, $paramLimit);
		}
		else
		{
			$db->setQuery($query, 0, $paramLimit);
			$items = $db->loadObjectList();
		}

		$groups = array();
		$keys   = array();

		foreach ($items as $key => $item)
		{
			if (!isset($groups[$item->table]))
			{
				$groups[$item->table] = array($item->id);
			}
			else
			{
				$groups[$item->table][] = $item->id;
			}

			$keys[$item->id] = $key;
		}

		foreach ($groups as $table => $ids)
		{
			$cfValues = self::getCustomFieldValues($ids, $table);

			foreach ($cfValues as $id => $values)
			{
				foreach ($values as $key => $val)
				{
					$items[$keys[$id]]->$key = $val;
				}
			}
		}

		return $items;
	}

	/**
	 * Find closest items.
	 *
	 * @param   float  $lat           Location latitude.
	 * @param   float  $lng           Location longitude.
	 * @param   array  $items         List of items.
	 * @param   int    $closestCount  Number of closest items to get.
	 *
	 * @return  array  Array of closest items.
	 */
	public static function findClosest($lat, $lng, $items, $closestCount = 3)
	{
		// R - Radius of the earth in km
		$R         = 6371;
		$distances = array();
		$closest   = array();
		$temp      = null;

		for ($i = 0; $i < count($items); $i++)
		{
			$params   = new JRegistry($items[$i]->params);
			$location = $params->get('itemLatLng', '');

			if (!empty($location))
			{
				$tmp         = explode(',', $location);
				$mlat        = $tmp[0];
				$mlng        = $tmp[1];
				$dLat        = ($mlat - $lat) * M_PI / 180;
				$dLong       = ($mlng - $lng) * M_PI / 180;
				$a           = sin($dLat / 2) * sin($dLat / 2) + cos($lat * M_PI / 180) * cos($lat * M_PI / 180) * sin($dLong / 2) * sin($dLong / 2);
				$c           = 2 * atan2(sqrt($a), sqrt(1 - $a));
				$d           = $R * $c;
				$distances[] = array(
					'index'    => $i,
					'distance' => $d
				);
			}
		}

		if ($closestCount > count($distances))
		{
			$closestCount = count($distances);
		}

		for ($i = 0; $i < $closestCount; $i++)
		{
			for ($j = $i + 1; $j < count($distances); $j++)
			{
				if ($distances[$i]['distance'] > $distances[$j]['distance'])
				{
					$temp          = $distances[$i];
					$distances[$i] = $distances[$j];
					$distances[$j] = $temp;
				}
			}

			$closest[] = $items[$distances[$i]['index']];
		}

		return $closest;
	}

	/**
	 * Get item custom field values.
	 *
	 * @param   array   $ids    Array of item ids.
	 * @param   string  $table  Item table.
	 *
	 * @return  array  Array of custom field values.
	 */
	public static function getCustomFieldValues($ids, $table)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__reditem_types_' . $table))
			->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);

		return $db->loadAssocList('id');
	}
}
