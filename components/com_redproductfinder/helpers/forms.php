<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('form', JPATH_SITE . '/components/com_redproductfinder/models/');

/**
 * Redproductfinder Component Form Helper
 *
 * @since  3.0
 */
class RedproductfinderForms
{
	/**
	 * This method will filter type and tag to array
	 *
	 * @param   array  $data  value is array type
	 *
	 * @return array
	 */
	static function filterForm($data)
	{
		$types = array();
		$forms = array();

		$model = JModelLegacy::getInstance("forms", "RedproductfinderModel");

		foreach ($data as $key => $record)
		{
			// Get Type data
			$types[] = $record->typeid;
		}

		// Get unique types
		$types = array_unique($types);

		// Find tag and add them to form
		foreach ($data as $key => $record)
		{
			foreach ($types as $k => $r)
			{
				if (!isset($forms[$r]))
				{
					$forms[$r] = array(
						"typeid"	=> $r
					);
				}

				if ($r === $record->typeid)
				{
					$forms[$r]["typename"] = $record->type_name;
					$forms[$r]["typeselect"] = $record->type_select;
					$forms[$r]["class_name"] = $record->class_name;
					$forms[$r]["tags"][] = array(
						"tagid" 	=> $record->tagid,
						"tagname" 	=> $record->tag_name,
						"ordering"	=> $record->ordering,
						"aliases"	=> $record->aliases
					);

					unset($data[$key]);
				}
			}
		}

		// Remove duplicate types value
		return $forms;
	}

	/**
	 * This method will get form from model
	 *
	 * @return void
	 */
	public static function getModelForm()
	{
		return JModelLegacy::getInstance('forms', 'RedproducfinderModel');
	}

	/**
	 * This method will get parent category redshop by category id
	 *
	 * @return array
	 */
	public function getParentCategoryById($cid)
	{
/*		$checkParentCat = self::checkParentCat($cid);

		if ($checkParentCat == 0)
		{
			$cid = self::getParentBySub($cid);
		}*/

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn('c.category_id'))
			->select($db->qn('c.category_name'))
			->from($db->qn("#__redshop_category", "c"))
			->join("LEFT", $db->qn("#__redshop_category_xref", "cx") . " ON " . $db->qn('c.category_id') . ' = ' . $db->qn('cx.category_child_id'))
			->where($db->qn("c.category_id") . ' = ' . $db->q((int) $cid))
			->where("c.published = 1");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		foreach ($data as $key => $value)
		{
			if ($value->category_id != 0)
			{
				$child = $this->getChildCategory($value->category_id);
				$data[$key]->child = $child;

				foreach ($child as $k => $subChild)
				{
					$sub = $this->getChildCategory($subChild->category_id);
					$data[$key]->child[$k]->sub = $sub;
				}
			}
		}

		return $data;
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @return array
	 */
	public function getParentCategory()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn('c.category_id'))
			->select($db->qn('c.category_name'))
			->from($db->qn("#__redshop_category", "c"))
			->join("LEFT", $db->qn("#__redshop_category_xref", "cx") . " ON " . $db->qn('c.category_id') . ' = ' . $db->qn('cx.category_child_id'))
			->where($db->qn("cx.category_parent_id") . ' = 15526')
			->where("c.published = 1");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		foreach ($data as $key => $value)
		{
			if (!empty($value) && $value->category_id != 0)
			{
				$child = $this->getChildCategory($value->category_id);
				$data[$key]->child = $child;

				foreach ($child as $k => $subChild)
				{
					$sub = $this->getChildCategory($subChild->category_id);
					$data[$key]->child[$k]->sub = $sub;
				}
			}
		}

		return $data;
	}

	/**
	 * This method will get child category redshop
	 *
	 * @return array
	 */
	public function getChildCategory($parent_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn('c.category_id'))
			->select($db->qn('c.category_name'))
			->from($db->qn("#__redshop_category", "c"))
			->join("LEFT", $db->qn("#__redshop_category_xref", "cx") . " ON " . $db->qn('c.category_id') . ' = ' . $db->qn('cx.category_child_id'))
			->where($db->qn("cx.category_parent_id") . ' = ' . $db->q((int) $parent_id))
			->where("c.published = 1");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	public function checkParentCat($cat_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->qn("#__redshop_category_xref"))
			->where($db->qn("category_child_id") . ' = ' . $db->q((int) $cat_id))
			->where($db->qn("category_parent_id") . " = 15526");

		return $db->setQuery($query)->loadResult();
	}

	public function getParentBySub($cat_id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn('category_parent_id'))
			->from($db->qn("#__redshop_category_xref"))
			->where($db->qn("category_child_id") . ' = ' . $db->q((int) $cat_id));

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @return array
	 */
	public function getParentCategoryOnSale($catList = NULL)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn('c.category_id'))
			->select($db->qn('c.category_name'))
			->from($db->qn("#__redshop_category", "c"))
			->join("LEFT", $db->qn("#__redshop_category_xref", "cx") . " ON " . $db->qn('c.category_id') . ' = ' . $db->qn('cx.category_child_id'))
			->where($db->qn("cx.category_parent_id") . ' = 15526');

		if (!empty($catList))
		{
			$query->where($db->qn('c.category_id') . ' IN (' . implode(',', $catList) . ')')
				->where($db->qn('c.category_id') . ' != 15527');
		}

		$db->setQuery($query);
		$data = $db->loadObjectList();

/*
		foreach ($data as $key => $value)
		{
			if (!empty($value) && $value->category_id != 0)
			{
				$child = $this->getChildCategory($value->category_id);
				$data[$key]->child = $child;

				foreach ($child as $k => $subChild)
				{
					$sub = $this->getChildCategory($subChild->category_id);
					$data[$key]->child[$k]->sub = $sub;
				}
			}
		}*/

		return $data;
	}

	/**
	 * This method will get parent category redshop
	 *
	 * @return array
	 */
	public function getCategorybyPids($pids = array())
	{
		$data = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (!empty($pids))
		{
			$query->select('category_id')
				->from($db->qn('#__redshop_product_category_xref'))
				->where($db->qn('product_id') . ' IN (' . implode(',', $pids) . ')');

			$cids = $db->setQuery($query)->loadColumn();
			$cids = array_merge(array(), array_unique($cids));

			$query = $db->getQuery(true)
				->clear()
				->select($db->qn('c.category_id'))
				->select($db->qn('c.category_name'))
				->from($db->qn("#__redshop_category", "c"))
				->join("LEFT", $db->qn("#__redshop_category_xref", "cx") . " ON " . $db->qn('c.category_id') . ' = ' . $db->qn('cx.category_child_id'))
				->where($db->qn("cx.category_parent_id") . ' = 15526');

			if (!empty($cids))
			{
				$query->where($db->qn('c.category_id') . ' IN (' . implode(',', $cids) . ')')
					->where($db->qn('c.category_id') . ' != 15527');
			}

			$db->setQuery($query);
			$data = $db->loadObjectList();

			foreach ($data as $key => $value)
			{
				if (!empty($value) && $value->category_id != 0)
				{
					$child = $this->getChildCategory($value->category_id);
					$data[$key]->child = $child;

					foreach ($child as $k => $subChild)
					{
						$sub = $this->getChildCategory($subChild->category_id);
						$data[$key]->child[$k]->sub = $sub;
					}
				}
			}

			return $data;
		}

		return $data;
	}
}
