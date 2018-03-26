<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('findproducts', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('redshop.library');

/**
 * Findproducts Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class RedproductfinderModelFindproducts extends RModelList
{
	protected $limitField = 'limit';

	protected $limitstartField = 'auto';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');
		$param = JComponentHelper::getParams('com_redproductfinder');
		$session 	= JFactory::getSession();
		$input = $app->input;
		$formData = $session->get('form_data');
		$json = $input->post->get('jsondata', "", "filter");

		// Load state from the request.
		$redform = $input->post->get('redform', array(), 'filter');
		$categories = $input->getInt('category');
		$cid = $input->getInt("cid");

		if ($redform)
		{
			$pk = $redform;
		}
		elseif (!empty($json))
		{
			$decode = $json;
			$pk = json_decode($decode, true);
		}
		else
		{
			$pk = json_decode($formData, true);
		}

		if (!empty($categories))
		{
			$pk['category'] = $categories;
			//unset($pk['filterprice']);
		}
		elseif ($categories === 0)
		{
			$pk['category'] = 0;
			//unset($pk['filterprice']);
		}

		if (!empty($pk['cid']))
		{
			$this->setState('catid', $pk['cid']);
		}
		else
		{
			$this->setState('catid', $cid);
		}

		$this->setState('redform.data', $pk);

		$orderBy = $app->input->getString('order_by', '');

		$this->setState('order_by', $orderBy);

		$this->setState('order_category', $categories);

		$params = $app->getParams();

		$this->setState('params', $params);

		$templateId = $param->get('prod_template');
		$templateDesc = RedproductfinderFindProducts::getTemplate($templateId);

		$this->setState('templateDesc', $templateDesc);

		$limit = $input->get("limit", null);

		if ($limit == null)
		{
			if ($pk['cid'] == null)
			{
				$cid = $input->get("cid", 0, "int");

				if ($cid !== 0)
				{
					$cat = RedshopHelperCategory::getCategoryById($cid);
					$limit = $cat->products_per_page;
				}
				else
				{
					$value = $params->get('product_per_page');
					$limit = $value;
				}
			}
			else
			{
				$cat = RedshopHelperCategory::getCategoryById($pk['cid']);

				if ($cat)
				{
					$limit = $cat->products_per_page;
				}
				else
				{
					$value = $params->get('product_per_page');
					$limit = $value;
				}
			}
		}
		else
		{
			$value = $params->get('product_per_page');
			$limit = $value;
		}

		// If limit = 0, set limit by configuration, from redshop, see redshop to get more detail
		if (!$limit)
		{
			$limit = MAXCATEGORY;
		}

		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
		$limitStart = $input->post->get('limitstart');

		if (isset($limitStart))
		{
			$limitstart = $limitStart;
		}
		else
		{
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		}

		$this->setState('list.start', $limitstart);
	}

	/**
	 * Set session
	 *
	 * @return array
	 */
	public function addFilterStateData()
	{
		$input = JFactory::getApplication()->input;
		$act = $input->getString("act");
		$tempType = $input->getInt("tempType");
		$tempTag = $input->getInt("tempTag");

		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		if ($tempTag)
		{
			if (!$saveFilter)
			{
				$saveFilter = array();
			}

			if (!$saveFilter)
			{
				$saveFilter[$tempType] = array();
				$saveFilter[$tempType][$tempTag] = array("typeid" => $tempType, "tagid" => $tempTag);
			}
			else
			{
				$saveFilter[$tempType][$tempTag] = array("typeid" => $tempType, "tagid" => $tempTag);
			}

			$session->set("saveFilter", $saveFilter);
		}

		if ($act == 'delete')
		{
			unset($saveFilter[$tempType][$tempTag]);

			if ($saveFilter[$tempType] == null)
			{
				unset($saveFilter[$tempType]);
			}

			$session->set("saveFilter", $saveFilter);
		}

		if ($act == 'clear')
		{
			$session->clear('saveFilter');
		}
	}

	/**
	 * Get List from product
	 *
	 * @return array
	 */
	function getListQuery()
	{
		// Add filter data for filter state
		$this->addFilterStateData();

		$param = JComponentHelper::getParams('com_redproductfinder');

		$searchBy = $param->get("search_relation");

		switch ($searchBy)
		{
			case "or":
				return $this->getListQueryByOr($param);
			break;
			default:
				return $this->getListQueryByAnd($param);
			break;
		}
	}

	/**
	 * Get List from product search by OR
	 *
	 * @param   int  $param  search relation id
	 *
	 * @return array
	 */
	public function getListQueryByOr($param)
	{
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select("p.product_id")
			->from($db->qn("#__redshop_product", "p"))
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON p.product_id = pc.product_id");

		// Session filter
		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		$searchByComp = $param->get('search_by');
		$searchParentProduct = $param->get('show_main_product');
		$searchChildProduct = $param->get('search_child_product');
		$qualityScore = $param->get('use_quality_score');
		$filterOption = $param->get('redshop_filter_option');
		$considerAllTags = $param->get('consider_all_tags');
		$productOnSale = $pk['product_on_sale'];

		$catList = RedshopHelperCategory::getCategoryListArray(15527);
		$childCat = array(15527);

		foreach ($catList as $key => $value)
		{
			$childCat[] = $value->category_id;
		}

		// Filter by cid
		$cid = $this->getState("catid");

		if (isset($pk['keyword']))
		{
			$keyword = $pk['keyword'];
		}

		if (isset($pk['category']))
		{
			$category = $pk['category'];

			if (in_array(15526, $category))
			{
				$key = array_search(15526, $category);
				unset($category[$key]);
			}
			// $catList = RedshopHelperCategory::getCategoryListArray($category);

			// foreach ($catList as $key => $cat)
			// {
			// 	$list[] = $cat->category_id;
			// }

			// array_push($list, $category);

			$categories = implode(',', $category);
		}
		elseif (isset($pk['cid']))
		{
			$category = $pk['cid'];

			$catList = RedshopHelperCategory::getCategoryListArray($category);

			if (!empty($catList))
			{
				foreach ($catList as $key => $cat)
				{
					$list[] = $cat->category_id;
				}

				array_push($list, $category);

				$categories = implode(',', $list);

			}
			else
			{
				$categories = $category;
			}
		}
		else
		{
			$categories = $cid;
		}

		if (isset($pk['manufacturer']))
		{
			$manufacturer = $pk['manufacturer'];
		}

		if (isset($pk['available']))
		{
			$available = $pk['available'];
		}

		// Filter by manufacturer_id
		$manufacturerId = $pk["manufacturer_id"];

		// Filter by filterprice
		if (isset($pk["filterprice"]))
		{
			$filter = $pk["filterprice"];
			$min = $filter['min'];
			$max = $filter['max'];
		}

		$orderBy = $this->getState('order_by');

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}
		elseif ($orderBy == 'pr.favoured DESC')
		{
			$query->leftjoin($db->qn('#__redshop_product_rating', 'pr') . ' ON ' . $db->qn('p.product_id') . ' = ' . $db->qn('pr.product_id'));
		}

		$attribute = "";

		if (isset($pk["attribute"]))
		{
			$attribute = $pk["attribute"];
		}

		$view = $this->getState("redform.view");

		if (isset($saveFilter))
		{
			$query->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			$i = 0;
			$j = 0;

			// Begin join query
			foreach ($saveFilter as $type_id => $value)
			{
				$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id AND ac.published = 1');

				foreach ($value as $tag_id => $type_tag)
				{
					$tagId[$j][] = $tag_id;
				}

				foreach ($tagId as $k => $tag)
				{
					if ($k == $i)
					{
						$tagString = implode(',', $tag);
						$arrQuery[] = 'ac_t' . $j . '.tag_id IN (' . $tagString . ")";
						$tagString = implode(' OR ', $arrQuery);
					}
				}

				$i++;
				$j++;
			}

			$query->where($tagString);
		}
		elseif ($searchByComp == 1)
		{
			$j = 0;
			$i = 0;
			$arrQuery1 = array();
			$arrQuery2 = array();
			$arrWhere = array();

			if (isset($pk['attribute']))
			{
				foreach ($attribute as $k => $value)
				{
					$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $i) . ' ON pa' . $i . '.product_id = p.product_id')
						->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $i) . ' ON pp' . $i . '.attribute_id = pa' . $i . '.attribute_id')
						->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps" . $i) . ' ON ps' . $i . '.subattribute_id = pp' . $i . '.property_id');

					if (isset($value['subproperty']))
					{
						foreach ($value['subproperty'] as $pro => $subs)
						{
							$property[] = $pro;

							foreach ($subs as $sub)
							{
								$subproperty[] = $sub;
							}
						}

						$proString = implode("','", $property);
						$subString = implode("','", $subproperty);
						$arrQuery1[] = "pp" . $j . ".property_name IN ('" . $proString . "')";
						$arrQuery1[] .= "ps" . $j . ".subattribute_color_name IN ('" . $subString . "')";

						unset($attribute[$k]);
						$where1 = implode(" OR ", $arrQuery1);
						$arrWhere[] = $where1;
					}
					else
					{
						foreach ($value as $pro => $subs)
						{
							$property1[] = $subs;
						}

						$proString1 = implode("','", $value);
						$arrQuery2[] = "pp" . $j . ".property_name IN ('" . $proString1 . "')";
					}

					$i++;
					$j++;
				}

				if (empty($value['subproperty']))
				{
					$where3 = implode(" OR ", $arrQuery2);
					$query->where($where3);
				}
			}
		}
		elseif ($searchByComp == 0)
		{
			$query->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON ac.product_id = p.product_id");

			// Create arrays variable
			$types = array();
			$count = count($pk);
			$j = 0;
			$i = 0;

			if ($pk != null)
			{
				// Get how many type
				$types = array_keys($pk);

				foreach ($types as $k => $type)
				{
					if (isset($pk[$type]['tags']))
					{
						$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

						$typeString = implode(',', $pk[$type]["tags"]);

						if (isset($pk[$type]["tags"]))
						{
							$arrQuery[] = 'ac_t' . $j . '.tag_id IN (' . $typeString . ")";
							$tagString = implode(' OR ', $arrQuery);
						}

						if ($qualityScore == 1)
						{
							$query->order('ac_t' . $i . '.quality_score ASC');
						}

						$j++;
						$i++;
					}
					elseif (isset($pk[$type]['from']) && !empty($pk[$type]['from']) && isset($pk[$type]['to']) && !empty($pk[$type]['to']))
					{
						$from = $pk[$type]['from'];
						$to = $pk[$type]['to'];
						$dateFrom = date_format(date_create($from), "Y-m-d H:i:s");
						$dateTo = date_format(date_create($to), "Y-m-d H:i:s");
						$where = "p.publish_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'";
						$query->where($where);
					}
				}

				if (isset($tagString))
				{
					$query->where($tagString);
				}
			}
		}

		if ($searchParentProduct == 1 && $searchChildProduct == 0)
		{
			$query->where("p.product_parent_id = 0");
		}
		elseif ($searchChildProduct == 1 && $searchParentProduct == 0)
		{
			$query->where("p.product_parent_id <> 0");
		}
		elseif ($searchParentProduct == $searchChildProduct)
		{
			$query->where("p.product_parent_id IS NOT NULL");
		}

		$query->where("p.published = 1")
			->where("p.expired = 0");

		if ($this->getTaxRate())
		{
			$taxRate = $this->getTaxRate();
		}
		else
		{
			$taxRate = 0;
		}

		$productPrice = "(p.product_price * $taxRate)";
		$productDiscountPrice = "(p.discount_price * $taxRate)";

		if (!empty($filter))
		{
			$productPrices = $db->qn("p.product_price") . " + " . $productPrice;
			$productDiscountPrices = $db->qn("p.discount_price") . " + " . $productDiscountPrice;
			$comparePrice = "(" . $productPrices . ' >= ' . $db->q($min) . ' AND ' . $productPrices . ' <= ' . $db->q(($max + 2000000)) . ")";
			$compareDiscountPrice = "(" . $productDiscountPrices . ' >= ' . $db->q($min) . ' AND ' . $productDiscountPrices . ' <= ' . $db->q(($max + 2000000)) . ")";
			$priceNormal = $comparePrice;
			$priceDiscount = $compareDiscountPrice;
			$saleTime = $db->qn('p.discount_stratdate') . ' AND ' . $db->qn('p.discount_enddate');
			$query->where('IF(' . $db->qn('p.product_on_sale') . ' = 1 && UNIX_TIMESTAMP() BETWEEN ' . $saleTime . ', ' . $priceDiscount . ', ' . $priceNormal . ')');
		}

		if (!empty($keyword))
		{
			$search = $db->q('%' . $db->escape(trim($keyword, true) . '%'));

			$query->leftjoin($db->qn('#__redshop_manufacturer', 'm') . ' ON ' . $db->qn('m.id') . ' = ' . $db->qn('p.manufacturer_id'))
				->where('(' . $db->qn('p.product_name') . ' LIKE ' . $search . ' OR ' . $db->qn('m.name') . ' LIKE ' . $search . ')');
		}

		if (!empty($pk['cid']) && in_array($pk['cid'], $childCat))
		{
			if (!empty($pk['category']))
			{
				foreach ($pk['category'] as $key => $value)
				{
					$query->leftjoin(
						$db->qn('#__redshop_product_category_xref', 'pc' . $key) . ' ON '
						. $db->qn('p.product_id') . ' = '
						. $db->qn('pc' . $key . '.product_id')
					)
						->where($db->qn('pc' . $key . '.category_id') . ' = ' . $db->q($value))
						->where($db->qn("pc.category_id") . " IN (" . $pk['cid'] . ')');
				}
			}
			else
			{
				$query->where($db->qn("pc.category_id") . " IN (" . $categories . ')');
			}
		}
		else
		{
			if (!empty($category))
			{
				$query->where($db->qn("pc.category_id") . " IN (" . $categories . ')');
			}
			elseif ($cid)
			{
				$query->where($db->qn("pc.category_id") . " IN (" . $categories . ')');
			}
		}

		if (!empty($manufacturer))
		{
			$manuList = implode(',', $manufacturer);
			$query->where($db->qn("p.manufacturer_id") . " IN (" . $manuList . ')');
		}
		elseif ($manufacturerId)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturerId));
		}

		if (!empty($available))
		{
			$query->leftjoin($db->qn('#__redshop_product_stockroom_xref', 'psx') . ' ON ' . $db->qn('p.product_id') . ' = ' . $db->qn('psx.product_id'));
			$query->where($db->qn('psx.quantity') . '> 0');
		}

		if (!empty($productOnSale))
		{
			$query->where($db->qn('p.product_on_sale') . ' = ' . $db->q((int) $productOnSale));
		}

		if ($orderBy)
		{
			$query->order($db->escape($orderBy));
		}
		elseif ($filterOption == 1)
		{
			$query->order('p.product_id DESC');
		}

		$query->group('p.product_id');

		return $query;
	}

	/**
	 * Get List from product search by AND
	 *
	 * @param   int  $param  search relation id
	 *
	 * @return array
	 */
	public function getListQueryByAnd($param)
	{
		// Session filter
		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		$searchByComp = $param->get('search_by');
		$searchParentProduct = $param->get('show_main_product');
		$searchChildProduct = $param->get('search_child_product');
		$qualityScore = $param->get('use_quality_score');
		$filterOption = $param->get('redshop_filter_option');
		$considerAllTags = $param->get('consider_all_tags');

		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		$productOnSale = $pk['product_on_sale'];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select("p.product_id")
			->from($db->qn("#__redshop_product", "p"))
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON p.product_id = pc.product_id");

		if (isset($pk['keyword']))
		{
			$keyword = $pk['keyword'];
		}

		if (isset($pk['category']))
		{
			$category = $pk['category'];

			if (in_array(15526, $category))
			{
				$key = array_search(15526, $category);
				unset($category[$key]);
			}
			// $catList = RedshopHelperCategory::getCategoryListArray($category);

			// if (!empty($catList))
			// {
			// 	foreach ($catList as $key => $cat)
			// 	{
			// 		$list[] = $cat->category_id;
			// 	}

			// 	array_push($list, $category);

				$categories = implode(',', $category);
			// }
			// else
			// {
			// 	$categories = $category;
			// }
		}
		elseif (isset($pk['cid']))
		{
			$category = $pk['cid'];

			$catList = RedshopHelperCategory::getCategoryListArray($category);

			foreach ($catList as $key => $cat)
			{
				$list[] = $cat->category_id;
			}

			array_push($list, $category);

			$categories = implode(',', $list);
		}
		else
		{
			$categories = $cid;
		}

		if (isset($pk['manufacturer']))
		{
			$manufacturer = $pk['manufacturer'];
		}

		if (isset($pk['available']))
		{
			$available = $pk['available'];
		}

		$orderBy = $this->getState('order_by');

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}
		elseif ($orderBy == 'pr.favoured DESC')
		{
			$query->leftjoin($db->qn('#__redshop_product_rating', 'pr') . ' ON ' . $db->qn('p.product_id') . ' = ' . $db->qn('pr.product_id'));
		}

		// Condition min max price
		$filter = array();

		if (isset($pk["filterprice"]))
		{
			// Filter by filterprice
			$filter = $pk["filterprice"];
			$min = $filter['min'];
			$max = $filter['max'];
		}

		$attribute = "";

		if (isset($pk["attribute"]))
		{
			$attribute = $pk["attribute"];
		}

		$cid = $this->getState("catid");
		$manufacturerId = $pk["manufacturer_id"];

		if (isset($saveFilter))
		{
			$query->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			$i = 0;
			$j = 0;

			// Begin join query
			foreach ($saveFilter as $type_id => $value)
			{
				$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id AND ac.published = 1');

				foreach ($value as $tag_id => $type_tag)
				{
					$tagId[$j][] = $tag_id;
				}

				foreach ($tagId as $k => $tag)
				{
					if ($k == $i)
					{
						$tagString = implode(',', $tag);
						$query->where('ac_t' . $j . '.tag_id IN (' . $tagString . ")");
					}
				}

				$i++;
				$j++;
			}
		}
		elseif ($searchByComp == 1)
		{
			$j = 0;
			$i = 0;
			$arrQuery1 = array();
			$arrQuery2 = array();

			if (isset($pk['attribute']))
			{
				foreach ($attribute as $k => $value)
				{
					$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $i) . ' ON pa' . $i . '.product_id = p.product_id')
						->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $i) . ' ON pp' . $i . '.attribute_id = pa' . $i . '.attribute_id')
						->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps" . $i) . ' ON ps' . $i . '.subattribute_id = pp' . $i . '.property_id');

					if (isset($value['subproperty']))
					{
						foreach ($value['subproperty'] as $pro => $subs)
						{
							$property[] = $pro;

							foreach ($subs as $sub)
							{
								$subproperty[] = $sub;
							}
						}

						$proString = implode("','", $property);
						$subString = implode("','", $subproperty);
						$arrQuery1[] = "pp" . $j . ".property_name IN ('" . $proString . "')";
						$arrQuery1[] .= "ps" . $j . ".subattribute_color_name IN ('" . $subString . "')";

						unset($attribute[$k]);
						$where1 = implode(" OR ", $arrQuery1);
						$query->where($where1);
					}
					else
					{
						foreach ($value as $pro => $subs)
						{
							$property1[] = $subs;
						}

						$proString1 = implode("','", $value);
						$arrQuery2[] = "pp" . $j . ".property_name IN ('" . $proString1 . "')";
					}

					$i++;
					$j++;
				}

				if (empty($value['subproperty']))
				{
					$where2 = implode(" AND ", $arrQuery2);

					$query->where($where2);
				}
			}
		}
		elseif ($searchByComp == 0)
		{
			// Remove some field
			unset($pk["manufacturer"]);
			unset($pk["available"]);
			unset($pk["filterprice"]);
			unset($pk["template_id"]);
			unset($pk["manufacturer_id"]);
			unset($pk["cid"]);
			unset($pk["keyword"]);

			$query->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			// Create arrays variable
			$types = array();
			$count = count($pk);
			$j = 0;
			$i = 0;

			if ($pk != null)
			{
				// Get how many type
				$types = array_keys($pk);

				foreach ($types as $k => $type)
				{
					if (isset($pk[$type]['tags']))
					{
						$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

						$typeString = implode(',', $pk[$type]["tags"]);

						if (isset($pk[$type]["tags"]))
						{
							$query->where('ac_t' . $j . '.tag_id IN (' . $typeString . ")");
						}

						if ($qualityScore == 1)
						{
							$query->order('ac_t' . $i . '.quality_score ASC');
						}

						$j++;
						$i++;
					}
					elseif (isset($pk[$type]['from']) && !empty($pk[$type]['from']) && isset($pk[$type]['to']) && !empty($pk[$type]['to']))
					{
						$from = $pk[$type]['from'];
						$to = $pk[$type]['to'];
						$dateFrom = date_format(date_create($from), "Y-m-d H:i:s");
						$dateTo = date_format(date_create($to), "Y-m-d H:i:s");
						$where = "p.publish_date BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "'";
						$query->where($where);
					}
				}
			}
		}

		if ($searchParentProduct == 1 && $searchChildProduct == 0)
		{
			$query->where("p.product_parent_id = 0");
		}
		elseif ($searchChildProduct == 1 && $searchParentProduct == 0)
		{
			$query->where("p.product_parent_id <> 0");
		}
		elseif ($searchParentProduct == $searchChildProduct)
		{
			$query->where("p.product_parent_id IS NOT NULL");
		}

		$query->where("p.published = 1")
			->where("p.expired = 0");

		if ($this->getTaxRate())
		{
			$taxRate = $this->getTaxRate();
		}
		else
		{
			$taxRate = 0;
		}

		$productPrice = "(p.product_price * $taxRate)";
		$productDiscountPrice = "(p.discount_price * $taxRate)";

		if (!empty($filter))
		{
			$productPrices = $db->qn("p.product_price") . " + " . $productPrice;
			$productDiscountPrices = $db->qn("p.discount_price") . " + " . $productDiscountPrice;
			$comparePrice = "(" . $productPrices . ' >= ' . $db->q($min) . ' AND ' . $productPrices . ' <= ' . $db->q($max) . ")";
			$compareDiscountPrice = "(" . $productDiscountPrices . ' >= ' . $db->q($min) . ' AND ' . $productDiscountPrices . ' <= ' . $db->q($max + 100000) . ")";
			$priceNormal = $comparePrice;
			$priceDiscount = $compareDiscountPrice;
			$saleTime = $db->qn('p.discount_stratdate') . ' AND ' . $db->qn('p.discount_enddate');
			$query->where('IF(' . $db->qn('p.product_on_sale') . ' = 1 && UNIX_TIMESTAMP() BETWEEN ' . $saleTime . ', ' . $priceDiscount . ', ' . $priceNormal . ')');
		}

		if (!empty($keyword))
		{
			$search = $db->q('%' . $db->escape(trim($keyword, true) . '%'));
			$query->where('(' . $db->qn('p.product_name') . ' LIKE ' . $search . ')');
		}

		if (!empty($category))
		{
			$query->where($db->qn("pc.category_id") . " IN (" . $categories . ')');
		}
		elseif ($cid)
		{
			$query->where($db->qn("pc.category_id") . " IN (" . $categories . ')');
		}

		if (!empty($manufacturer))
		{
			$manuList = implode(',', $manufacturer);
			$query->where($db->qn("p.manufacturer_id") . " IN (" . $manuList . ')');
		}
		elseif ($manufacturerId)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturerId));
		}

		if (!empty($available))
		{
			$query->leftjoin($db->qn('#__redshop_product_stockroom_xref', 'psx') . ' ON ' . $db->qn('p.product_id') . ' = ' . $db->qn('psx.product_id'));
			$query->where($db->qn('psx.quantity') . '> 0');
		}

		if (!empty($productOnSale))
		{
			$query->where($db->qn('p.product_on_sale') . ' = ' . $db->q((int) $productOnSale));
		}

		if ($orderBy)
		{
			$query->order($db->escape($orderBy));
		}
		elseif ($filterOption == 1)
		{
			$query->order('p.product_id DESC');
		}

		$query->group('p.product_id');

		return $query;
	}

	/**
	 * Get Item from category view
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getItem($pk = null)
	{
		$query = $this->getListQuery();
		$db = JFactory::getDbo();
		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$templateDesc = $this->getState('templateDesc');

		if ($templateDesc)
		{
			if (strstr($templateDesc, "{pagination}"))
			{
				$db->setQuery($query, $start, $limit);
			}
			else
			{
				$db->setQuery($query);
			}
		}
		else
		{
			$db->setQuery($query);
		}

		$data = $db->loadAssocList();

		$this->getProductId();

		$temp = array();

		foreach ($data as $k => $value)
		{
			$temp[] = $value["product_id"];
		}

		return $temp;
	}

	/**
	 * Get pagination.
	 *
	 * @return pagination
	 */
	public function getPagination()
	{
		$endlimit          = $this->getState('list.limit');
		$limitstart        = $this->getState('list.start');
		$this->pagination = new JPagination($this->getTotal(), $limitstart, $endlimit);

		return $this->pagination;
	}

	/**
	 * Get total.
	 *
	 * @return total
	 */
	public function getTotal()
	{
		$query        = $this->getListQuery();
		$this->total = $this->_getListCount($query);

		return $this->total;
	}

	/**
	 * Get all product id.
	 *
	 * @return object
	 */
	public function getProductId()
	{
		$query = $this->getListQuery();
		$session = JFactory::getSession();
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$productId = array();

		$data = $db->loadAssocList();

		foreach ($data as $k => $value)
		{
			$productId[] = $value["product_id"];
		}

		return $session->set('product_id_list', $productId);
	}

	/**
	 * Method to get tax rate
	 *
	 * @return  object
	 */
	public function getTaxRate()
	{
		// Create a new query object.
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('tr.tax_rate')
			->from($db->qn('#__redshop_tax_rate', 'tr'))
			->leftjoin($db->qn('#__redshop_tax_group', 'tg') . ' ON ' . $db->qn('tr.tax_group_id') . ' = ' . $db->qn('tg.tax_group_id'))
			->where($db->qn('tg.published') . ' = 1');

		$db->setQuery($query);

		$data = $db->loadResult();

		return $data;
	}

	/**
	 * Method to get keyword
	 *
	 * @param   string  $keyword  Default value is null
	 *
	 * @return  object
	 */
	public function checkKeyword($keyword)
	{
		// Create a new query object.
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->qn('#__redproductfinder_keyword', 'k'))
			->where($db->qn('k.keyword') . ' = ' . $db->q($keyword));

		$db->setQuery($query);

		$data = $db->loadResult();

		return $data;
	}

	/**
	 * This function will insert keyword
	 *
	 * @param   string  $keyword  Default value is null
	 *
	 * @return boolean
	 */
	public function insertKeyword($keyword)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->insert($db->quoteName('#__redproductfinder_keyword'))
		->columns($db->quoteName(array('keyword', 'times', 'created_date')))
		->values($db->q($keyword) . ',' . $db->q(1) . ',' . $db->q(date('Y-m:d H:i:s')));

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	 * Method to get times of keyword
	 *
	 * @param   string  $keyword  Default value is null
	 *
	 * @return  object
	 */
	public function getTimes($keyword)
	{
		// Create a new query object.
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('k.times')
			->from($db->qn('#__redproductfinder_keyword', 'k'))
			->where($db->qn('k.keyword') . ' = ' . $db->q($keyword));

		$db->setQuery($query);

		$data = $db->loadResult();

		return $data;
	}

	/**
	 * This function will update times keyword
	 *
	 * @param   string  $keyword  Default value is null
	 *
	 * @return boolean
	 */
	public function updateTimes($keyword)
	{
		$times = $this->getTimes($keyword) + 1;
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$fields = array(
			$db->qn('times') . ' = ' . $db->q((int) $times)
		);

		$conditions = array(
			$db->qn('keyword') . ' = ' . $db->q($keyword)
		);

		$query->update($db->quoteName('#__redproductfinder_keyword'))->set($fields)->where($conditions);

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}
}
