<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Class searchModelsearch
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelSearch extends RedshopModelSearchDefault
{
	/**
	 * Build query
	 *
	 * @param   int|array  $manudata  Post request
	 * @param   bool       $getTotal  Get total product(true) or product data(false)
	 *
	 * @return JDatabaseQuery
	 */
	public function _buildQuery($manudata = 0, $getTotal = false)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		$orderByMethod = $app->input->getString(
							'order_by',
							$app->getParams()->get('order_by', DEFAULT_PRODUCT_ORDERING_METHOD)
						);
		$orderByObj  = redhelper::getInstance()->prepareOrderBy(urldecode($orderByMethod));

		$orderBy = $orderByObj->ordering . ' ' . $orderByObj->direction;

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}

		if ($getTotal)
		{
			$query = $db->getQuery(true)
				->select('COUNT(DISTINCT(p.product_id))')
				->leftJoin($db->qn('#__redshop_manufacturer', 'm') . ' ON m.id = p.manufacturer_id');
		}
		else
		{
			$query = $db->getQuery(true)
				->select('DISTINCT(p.product_id)')
				->leftJoin($db->qn('#__redshop_manufacturer', 'm') . ' ON m.id = p.manufacturer_id')
				->order($db->escape($orderBy));
		}

		$query->from($db->qn('#__redshop_product', 'p'))
			->leftJoin($db->qn('#__redshop_product_category_xref', 'pc') . ' ON pc.product_id = p.product_id')
			->where('p.published = 1');

		$layout = JRequest::getVar('layout', 'default');

		$category_helper = new product_category;
		$manufacture_id = JRequest::getInt('manufacture_id', 0);
		$cat_group = array();

		if ($category_id = $app->input->get('category_id', 0))
		{
			$cat = RedshopHelperCategory::getCategoryListArray(0, $category_id);

			for ($j = 0, $countCat = count($cat); $j < $countCat; $j++)
			{
				$cat_group[$j] = $cat[$j]->category_id;

				if ($j == count($cat) - 1)
				{
					$cat_group[$j + 1] = $category_id;
				}
			}

			JArrayHelper::toInteger($cat_group);

			if ($cat_group)
			{
				$cat_group = join(',', $cat_group);
			}
			else
			{
				$cat_group = $category_id;
			}
		}

		$menu = $app->getMenu();
		$item = $menu->getActive();
		$days        = isset($item->query['newproduct']) ? $item->query['newproduct'] : 0;
		$today       = date('Y-m-d H:i:s', time());
		$days_before = date('Y-m-d H:i:s', time() - ($days * 60 * 60 * 24));
		$aclProducts = producthelper::getInstance()->loadAclProducts();

		// Shopper group - choose from manufactures Start
		$rsUserhelper               = rsUserHelper::getInstance();
		$shopper_group_manufactures = $rsUserhelper->getShopperGroupManufacturers();

		if ($shopper_group_manufactures != "")
		{
			// Sanitize ids
			$manufacturerIds = explode(',', $shopper_group_manufactures);
			JArrayHelper::toInteger($manufacturerIds);

			$query->where('p.manufacturer_id IN (' . implode(',', $manufacturerIds) . ')');
		}

		// Shopper group - choose from manufactures End
		if ($aclProducts != "")
		{
			// Sanitize ids
			$productIds = explode(',', $aclProducts);
			JArrayHelper::toInteger($productIds);

			$query->where('p.product_id IN (' . implode(',', $productIds) . ')');
		}

		if ($layout == 'productonsale')
		{
			$categoryid = $item->params->get('categorytemplate');

			if ($categoryid)
			{
				$cat_main       = $category_helper->getCategoryTree($categoryid);
				$cat_group_main = array();

				for ($j = 0, $countCatMain = count($cat_main); $j < $countCatMain; $j++)
				{
					$cat_group_main[$j] = $cat_main[$j]->category_id;
				}

				$cat_group_main[] = $categoryid;
				JArrayHelper::toInteger($cat_group_main);

				$query->where('pc.category_id IN (' . implode(',', $cat_group_main) . ')');
			}

			$query->where(
				array(
					'p.product_on_sale = 1',
					'p.expired = 0',
					'p.product_parent_id = 0'
				)
			);
		}
		elseif ($layout == 'featuredproduct')
		{
			$query->where('p.product_special = 1');
		}
		elseif ($layout == 'newproduct')
		{
			$catid = $item->query['categorytemplate'];

			$cat_main       = $category_helper->getCategoryTree($catid);
			$cat_group_main = array();

			for ($j = 0, $countCatMain = count($cat_main); $j < $countCatMain; $j++)
			{
				$cat_group_main[$j] = $cat_main[$j]->category_id;
			}

			$cat_group_main[] = $catid;
			JArrayHelper::toInteger($cat_group_main);

			if ($catid)
			{
				$query->where('pc.category_id in (' . implode(',', $cat_group_main) . ')');
			}

			$query->where('p.publish_date BETWEEN ' . $db->quote($days_before) . ' AND ' . $db->quote($today))
				->where('p.expired = 0')
				->where('p.product_parent_id = 0');
		}
		elseif ($layout == 'redfilter')
		{
			$query->where('p.expired = 0');

			// Get products for filtering
			if ($products = $this->getRedFilterProduct())
			{
				// Sanitize ids
				$productIds = explode(',', $products);
				JArrayHelper::toInteger($productIds);

				$query->where('p.product_id IN ( ' . implode(',', $productIds) . ')');
			}
		}
		else
		{
			$keyword = $this->getState('keyword');
			$manuCondition = ' OR (' . $db->qn('m.name') . ' LIKE ' . $db->q('%' . $keyword . '%') . ')';
			$defaultSearchType = $app->input->getCmd('search_type', 'product_name');

			if (!empty($manudata['search_type']))
			{
				$defaultSearchType = $manudata['search_type'];
			}

			switch ($defaultSearchType)
			{
				case 'name_number':
					$query->where($this->getSearchCondition(array('p.product_name', 'p.product_number'), $keyword) . $manuCondition);
					break;
				case 'name_desc':
					$query->where($this->getSearchCondition(array('p.product_name', 'p.product_desc', 'p.product_s_desc'), $keyword) . $manuCondition);
					break;
				case 'virtual_product_num':
					$query->where($this->getSearchCondition(array('pap.property_number', 'ps.subattribute_color_number'), $keyword) . $manuCondition);
					break;
				case 'name_number_desc':
					$query->where(
						$this->getSearchCondition(
							array('p.product_name', 'p.product_number', 'p.product_desc', 'p.product_s_desc', 'pap.property_number', 'ps.subattribute_color_number'),
							$keyword
						) . $manuCondition
					);
					break;
				case 'product_desc':
					$query->where($this->getSearchCondition(array('p.product_s_desc', 'p.product_desc'), $keyword) . $manuCondition);
					break;
				case 'product_name':
					$query->where($this->getSearchCondition('p.product_name', $keyword) . $manuCondition);
					break;
				case 'product_number':
					$query->where($this->getSearchCondition('p.product_number', $keyword) . $manuCondition);
					break;
			}

			if ($manufacture_id == 0)
			{
				if (!empty($manudata['manufacturer_id']))
				{
					$manufacture_id = $manudata['manufacturer_id'];
				}
			}

			if ($defaultSearchType == "name_number_desc" || $defaultSearchType == "virtual_product_num")
			{
				$query->leftJoin($db->qn('#__redshop_product_attribute', 'a') . ' ON a.product_id = p.product_id')
					->leftJoin($db->qn('#__redshop_product_attribute_property', 'pap') . ' ON pap.attribute_id = a.attribute_id')
					->leftJoin($db->qn('#__redshop_product_subattribute_color', 'ps') . ' ON ps.subattribute_id = pap.property_id');
			}

			$query->where('p.expired = 0');

			if ($category_id != 0)
			{
				// Sanitize ids
				$catIds = explode(',', $cat_group);
				JArrayHelper::toInteger($catIds);

				$query->where('pc.category_id IN (' . $cat_group . ')');
			}

			if ($manufacture_id != 0)
			{
				$query->where('p.manufacturer_id = ' . (int) $manufacture_id);
			}
		}

		return $query;
	}
}