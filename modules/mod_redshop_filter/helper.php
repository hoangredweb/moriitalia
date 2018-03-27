<?php
/**
 * @package     RedSHOP.Module
 * @subpackage  mod_redshop_filter
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_redshop_filter
 *
 * @since  2.0.0.4
 */
abstract class ModRedshopFilter
{
	/**
	 * This function will help get max and min value on list product price
	 *
	 * @param   array  $pids  product array list
	 *
	 * @return array
	 */
	public static function getRange($pids = array())
	{
		$max = 0;
		$min = 0;
		$producthelper = producthelper::getInstance();
		$allProductPrices = array();

		if (!empty($pids))
		{
			// Get product price
			foreach ($pids as $k => $id)
			{
				$productprices = $producthelper->getProductNetPrice($id);
				$allProductPrices[] = $productprices['product_price'];
			}

			// Get first value to make sure it won't zero value
			$max = $min = $allProductPrices[0];

			// Loop to check max min
			foreach ($allProductPrices as $k => $value)
			{
				// Check max
				if ($value >= $max)
				{
					$max = $value;
				}

				// Check min
				if ($value <= $min)
				{
					$min = $value;
				}
			}
		}

		return array(
			"max" => $max,
			"min" => $min
		);
	}

	/**
	 * This method will get child category redshop
	 *
	 * @param   $parentId  category parent id
	 *
	 * @return array
	 */
	public static function getChildCategory($parentId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('c.id', 'category_id'))
			->select($db->qn('c.name', 'category_name'))
			->from($db->qn("#__redshop_category", "c"))
			->where($db->qn("c.parent_id") . ' = ' . $db->q((int) $parentId))
			->where($db->qn("c.published") . " = 1");

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Retrieve a list of article
	 *
	 * @param   array  $manuList  manufacturer ids
	 *
	 * @return  mixed
	 */
	public static function getManufacturers($manuList = array())
	{
		if (empty($manuList))
		{
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('name'))
			->select($db->qn('id'))
			->from($db->qn('#__redshop_manufacturer'))
			->where($db->qn('published') . ' = 1');

		if (!empty($manuList))
		{
			$query->where($db->qn('id') . ' IN (' . implode(',', $manuList) . ')');
		}

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @param   array  $catList       category ids
	 * @param   int    $rootCategory  root category ids
	 * @param   array  $cid           category id
	 *
	 * @return array
	 */
	public static function getCategories($catList = array(), $rootCategory = 0, $cid = 0)
	{
		$categories = self::getChildCategory($cid);
		$mainCat = array_merge(array(), array_intersect($categories, $catList));

		if (empty($mainCat))
		{
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->select($db->qn('name'))
			->from($db->qn("#__redshop_category"))
			->where($db->qn('id') . ' IN (' . implode(',', $mainCat) . ')');

		if ($rootCategory != 0)
		{
			$query->where($db->qn('parent_id') . ' = ' . $db->q((int) $rootCategory));
		}

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @param   array  $catList  category ids
	 *
	 * @return array
	 */
	public static function getSearchCategories($catList = array())
	{
		if (empty($catList))
		{
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->select($db->qn('name'))
			->from($db->qn("#__redshop_category"))
			->where($db->qn('id') . ' IN (' . implode(',', $catList) . ')');

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @param   array  $pids          product ids
	 * @param   int    $rootCategory  root category ids
	 *
	 * @return array
	 */
	public static function getCategorybyPids($pids = array(), $rootCategory = 0, $saleCategory = null)
	{
		$data = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$list = RedshopHelperCategory::getCategoryListArray($saleCategory);
		$childCat = array($saleCategory);

		foreach ($list as $key => $value)
		{
			$childCat[] = $value->id;
		}

		if (!empty($pids))
		{
			$query->select('id')
				->from($db->qn('#__redshop_category'))
				->where($db->qn("parent_id") . ' = ' . $db->q((int) $rootCategory));
			$root = $db->setQuery($query)->loadColumn();

			$query = $db->getQuery(true)
				->select('category_id')
				->from($db->qn('#__redshop_product_category_xref'))
				->where($db->qn('product_id') . ' IN (' . implode(',', $pids) . ')');

			$cids = $db->setQuery($query)->loadColumn();
			$cids = array_merge(array(), array_unique($cids));
			$containsAllValues = count(array_diff($cids, $root));
			$query = $db->getQuery(true)
				->clear()
				->select($db->qn('c.id', 'category_id'))
				->select($db->qn('c.name', 'category_name'))
				->from($db->qn("#__redshop_category", "c"));

			if (count($cids) != $containsAllValues)
			{
				$query->where($db->qn("c.parent_id") . ' = ' . $db->q((int) $rootCategory));
			}

			if (!empty($cids))
			{
				$query->where($db->qn('c.id') . ' IN (' . implode(',', $cids) . ')');

				if (!empty($saleCategory))
				{
					$query->where($db->qn('c.id') . ' NOT IN (' . implode(',', $childCat) . ')');
				}
			}

			$data = $db->setQuery($query)->loadObjectList();

			foreach ($data as $key => $value)
			{
				if (!empty($value) && $value->category_id != 0)
				{
					$child = self::getChildCategory($value->category_id);
					$data[$key]->child = $child;

					foreach ($child as $k => $subChild)
					{
						$sub = self::getChildCategory($subChild->category_id);
						$data[$key]->child[$k]->sub = $sub;
					}
				}
			}

			return $data;
		}

		return $data;
	}

	/**
	 * Get products by manufacturer id
	 *
	 * @param   int  $mid  manufacturer id
	 *
	 * @return  array
	 */
	public static function getProductByManufacturer($mid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('product_id'))
			->from($db->qn('#__redshop_product'))
			->where($db->qn('manufacturer_id') . ' = ' . $db->q((int) $mid));

		return $db->setQuery($query)->loadColumn();
	}

	/**
	 * Get products by category id
	 *
	 * @param   int  $cid  category id
	 *
	 * @return  array
	 */
	public static function getProductByCategory($cid = 0)
	{
		if ($cid == 0)
		{
			return array();
		}

		$tmpCategories = RedshopHelperCategory::getCategoryTree($cid);
		$categories = array($cid);

		if (!empty($tmpCategories))
		{
			foreach ($tmpCategories as $child)
			{
				$categories[] = $child->id;
			}
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('product_id'))
			->from($db->qn('#__redshop_product_category_xref'))
			->where($db->qn('category_id') . ' IN (' . implode(',', $categories) . ')')
			->group('product_id');

		return $db->setQuery($query)->loadColumn();
	}

	/**
	 * Get products custom fields
	 *
	 * @param   array  $pids           Product Ids
	 * @param   array  $productFields  Product custom fields
	 *
	 * @return  array
	 */
	public static function getCustomFields($pids = array(), $productFields = array())
	{
		if (empty($pids) || empty($productFields))
		{
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('fv.field_value'))
			->select($db->qn('fv.field_id'))
			->select($db->qn('fv.field_name'))
			->select($db->qn('f.title'))
			->select($db->qn('f.class'))
			->from($db->qn('#__redshop_fields', 'f'))
			->leftJoin($db->qn('#__redshop_fields_value', 'fv') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fv.field_id'))
			->leftJoin($db->qn('#__redshop_fields_data', 'fd') . ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fd.fieldid'))
			->where($db->qn('fd.itemid') . ' IN (' . implode(',', $pids) . ')')
			->where($db->qn('f.name') . ' IN (' . implode(',', $db->q($productFields)) . ')')
			->group($db->qn('fv.field_value'))
			->order($db->qn('fv.value_id'));

		$data   = $db->setQuery($query)->loadObjectList();
		$result = array();

		foreach ($data as $key => $value)
		{
			$result[$value->field_id]['title'] = $value->title;
			$result[$value->field_id]['class'] = $value->class;
			$result[$value->field_id]['value'][$value->field_value] = $value->field_name;
		}

		return $result;
	}
}