<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require JPATH_SITE . '/modules/mod_redproductforms/helper.php';
JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('redshop.library');

$productHelper = productHelper::getInstance();
$lists = ModRedproductForms::getList($params);
$session = JFactory::getSession();
$countProduct = $session->get('count_product');
$model = JModelLegacy::getInstance("Forms", "RedproductfinderModel");
// $productId = $session->get('product_id_list');
$attributeSubProperties = $model->getAttributeSubProperty();
$templateId = $params->get("template_id");
$view = JFactory::getApplication()->input->get("view");
$option = JFactory::getApplication()->input->get("option");
$formid = $params->get("form_id");
$module_class_sfx = $params->get("moduleclass_sfx");
$app = JFactory::getApplication();
$input = JFactory::getApplication()->input;
$redform = $input->post->get("redform", array(), "filter");
$json = $input->post->get('jsondata', "", "filter");
$formData = $session->get('form_data');
$orderCat = $input->getInt('category');
$cid = $input->getInt('cid', 0);
$mid = $input->getInt('mid', 0);
$Itemid = $app->input->get("Itemid", 0);
$menu = $app->getMenu();
$item = $menu->getParams($Itemid);
$menuItem = $menu->getItems('link', 'index.php?option=com_redproductfinder&view=findproducts', true);
$view = $input->getString('view');
$layout = $input->getString('layout');
$productOnSale = 0;

if (!empty($menuItem))
{
	$itemId = $menuItem->id;
}
else
{
	$itemId = $Itemid;
}

if (!empty($cid))
{
	$formHelper = new RedproductfinderForms;
	$list = RedshopHelperCategory::getCategoryListArray(15527);
	$childCat = array(15527);

	foreach ($list as $key => $value)
	{
		$childCat[] = $value->category_id;
	}

	if (in_array($cid, $childCat))
	{
		$productList = array();

		if ($cid == 15527)
		{
			foreach ($childCat as $k => $value)
			{
				$productCats = $productHelper->getProductCategory($value);

				foreach ($productCats as $key => $value)
				{
					$productDetail = $productHelper->getProductById($value->product_id);
					$productList[] = $productDetail;
				}
			}
		}
		else
		{
			$productCats = $productHelper->getProductCategory($cid);

			foreach ($productCats as $key => $value)
			{
				$productDetail = $productHelper->getProductById($value->product_id);
				$productList[$key] = $productDetail;
			}
		}

		$catList = array();
		$manuList = array();
		$pids = array();

		foreach ($productList as $k => $value)
		{
			$catList[] = $value->categories;
			$manuList[] = $value->manufacturer_id;
			$pids[] = $value->product_id;
		}

		$cats = array();

		foreach ($catList as $key => $value)
		{
			foreach ($value as $val)
			{
				$cats[] = $val;
			}
		}

		$manufacturer = $model->getManufacturerOnSale(array_unique($manuList));
		$categories = $formHelper->getParentCategoryOnSale(array_unique($cats));
		$rangeDefault = ModRedproductForms::getRange($pids);
	}
	else
	{
		$categories = $formHelper->getParentCategoryById($cid);
		$rangeDefault = ModRedproductForms::getRangeMaxMinDefault($cid);
		$manufacturer = $model->getManufacturer($cid);
	}

	$action = JRoute::_("index.php?option=com_redproductfinder&view=findproducts&cid=" . $cid . "&Itemid=" . $itemId);
}
elseif ($view == 'search') 
{
	$formHelper = new RedproductfinderForms;
	$modelSearch = JModelLegacy::getInstance("Search", "RedshopModel");
	$products = $modelSearch->getData();
	$manuList = array();
	$catList = array();
	$pids = array();

	foreach ($products as $key => $value)
	{
		$pids[] = $value->product_id;

		if (!empty($value->manufacturer_id))
		{
			$manuList[] = $value->manufacturer_id;
		}

		if (!empty($value->category_id))
		{
			$catList[] = $value->category_id;
		}
	}

	$manufacturer = $model->getManufacturerOnSale(array_unique($manuList));
	$categories = $formHelper->getParentCategoryOnSale(array_unique($catList));
	$rangeDefault = ModRedproductForms::getRange($pids);
	$action = JRoute::_("index.php?option=com_redproductfinder&view=findproducts&Itemid=" . $itemId);
}
else
{
	$formHelper = new RedproductfinderForms;

	if (!empty($mid))
	{
		$manufacturer = $model->getManufacturerById($mid);
		$pids = $model->getProductByManu($mid);
		$categories = $formHelper->getCategorybyPids($pids);
		$rangeDefault = ModRedproductForms::getRange($pids);
	}
	else
	{
		$categories = $formHelper->getParentCategory();
		$rangeDefault = ModRedproductForms::getRangeMaxMinDefault();
	}

	$action = JRoute::_("index.php?option=com_redproductfinder&view=findproducts&Itemid=" . $itemId);
}


if (!empty($productId))
{
	$attributes = $model->getAttributeSubmit($productId);
	$attributeProperties = $model->getAttributePropertySubmit($productId);
	$manufacturer = $model->getManufacturerSubmit($productId);
}
else
{
	$attributes = $model->getAttribute();
	$attributeProperties = $model->getAttributeProperty();
}

// Get search by tag or type from component
$paramComponent = JComponentHelper::getParams('com_redproductfinder');
$minConfig = $paramComponent->get('filter_price_min_value');
$maxConfig = $paramComponent->get('filter_price_max_value');
$searchBy = $paramComponent->get('search_by');
$searchBox = $paramComponent->get('search_box');
$showMaxMin = $paramComponent->get('show_max_min');
$searchManu = $paramComponent->get('search_manufacturer');
$searchCat = $paramComponent->get('search_categories');
$isAvailable = $paramComponent->get('is_available');
$showAttribute = $paramComponent->get('show_attribute');
$orderBy = $paramComponent->get('order_by');

$catId = 0;
$manufacturer_id = 0;
$objhelper = new redhelper;
$order_data = $objhelper->getOrderByList();
$brand = new stdClass;
$rating = new stdClass;
$review_rate_desc = new stdClass;
$review_rate_asc = new stdClass;
$rating = new stdClass;
$brand->value = 'p.manufacturer_id DESC';
$brand->text = 'Brand';
$rating->value = 'pr.favoured DESC';
$rating->text = 'Rating';
$review_rate_asc->value = 'review_rate ASC';
$review_rate_asc->text = 'Review Rate Ascending';
$review_rate_desc->value = 'review_rate DESC';
$review_rate_desc->text = 'Review Rate Descending';
$order_data[] = $brand;
$order_data[] = $rating;
$order_data[] = $review_rate_asc;
$order_data[] = $review_rate_desc;

$getorderby = JRequest::getString('order_by', DEFAULT_PRODUCT_ORDERING_METHOD);
$lists_order['order_select'] = JHTML::_('select.genericlist', $order_data, 'order_by', 'class="inputbox" size="1"', 'value', 'text', $getorderby);

if ($redform)
{
	$pk = $redform;
}
elseif (!empty($json))
{
	$json = $input->post->get('jsondata', "", "string");

	// Decode from string to array data
	$pk = json_decode($json, true);
}
// else
// {
// 	$pk = json_decode($formData, true);
// }

switch ($option)
{
	case "com_redproductfinder":
			switch ($view)
			{
				case "findproducts":
					$cid = $app->input->get("cid", 0, "INT");
					$manufacturer_id = $app->input->get("manufacturer_id", 0, "INT");
				break;
			}
		break;
	case "com_redshop":
			switch ($view)
			{
				case "category":
					$cid = $app->input->get("cid", 0, "INT");

					if (isset($cid) && $cid != 0)
					{
						$catId = $cid;
					}
					else
					{
						$catId = $item->get('cid');
					}

					$pk['category'] = array($catId);

					$manufacturer_id = $app->input->get("manufacturer_id", 0, "INT");
					break;
				case "manufacturers":
					$params = $app->getParams('com_redshop');
					$manufacturer_id = $params->get("manufacturerid");
					break;
			}
		break;
}

// if (!empty($pk['category']))
// {
// 	$catId = $pk['category'];
// }

if (!empty($pk['manufacturer']))
{
	$manufacturer_id = $pk['manufacturer'];
}

$range = ModRedproductForms::getRangeMaxMin();
$count = 0;

if (!empty($pk))
{
	$count = count($pk);
}

$keyTags = array();
$keyword = $input->post->getString('keyword', '');

if ($count > 0)
{
	if (isset($pk['keyword']))
	{
		$keyword = $pk['keyword'];
	}

	if (!empty($orderCat))
	{
		$pk['category'] = $orderCat;
		unset($pk['filterprice']);
	}
	elseif ($orderCat === 0)
	{
		$pk['category'] = 0;
		unset($pk['filterprice']);
	}

	if (isset($pk['category']))
	{
		$category = $pk['category'];
	}

	if (isset($pk['cid']))
	{
		$catFormId = $pk['cid'];
	}
	else
	{
		$catFormId = 0;
	}

	if (isset($pk['available']))
	{
		$available = $pk['available'];
	}

	if (isset($pk['manufacturer']))
	{
		$manuCheck = $pk['manufacturer'];
	}

	if (isset($pk['manufacturer_id']))
	{
		$manufacturerId = $pk['manufacturer_id'];
	}
	else
	{
		$manufacturerId = 0;
	}

	if (isset($pk['filterprice']))
	{
		$filter = $pk['filterprice'];

		$min = $filter['min'];
		$max = $filter['max'];
	}
	else
	{
		$filter = array();
	}

	if ($searchBy == 1)
	{
		if (isset($pk['properties']))
		{
			$properties = $pk['properties'];
		}
		else
		{
			$properties = array();
		}
	}

	unset($pk["filterprice"]);
	unset($pk["template_id"]);
	unset($pk["manufacturer_id"]);
	unset($pk["cid"]);

	foreach ( $pk as $k => $value )
	{
		$values[] = $value;
	}

	if (isset($pk['attribute']))
	{
		$attributeCheck = $pk['attribute'];

		foreach ($attributeCheck as $pros)
		{
			if (isset($pros["subproperty"]))
			{
				foreach ($pros["subproperty"] as $k_s => $s_n)
				{
					$subName[$k_s] = $s_n;
				}
			}
		}
	}
}

$calendarFormat = '%d-%m-%Y';

if (!empty($filter['min']))
{
	$rangeMin = $min;
}
else
{
	$rangeMin = $rangeDefault['min'];
}


if (!empty($filter['max']))
{
	$rangeMax = $max;
}
else
{
	$rangeMax = $rangeDefault['max'];
}

require JModuleHelper::getLayoutPath('mod_redproductforms');
