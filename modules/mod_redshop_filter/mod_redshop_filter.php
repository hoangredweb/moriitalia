<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_filter
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';
JLoader::import('redshop.library');

$productHelper      = productHelper::getInstance();
$input              = JFactory::getApplication()->input;
$cid                = $input->getInt('cid', 0);
$mid                = $input->getInt('mid', 0);
$moduleClassSfx     = $params->get("moduleclass_sfx");
$rootCategory       = $params->get('root_category', 0);
$enableCategory     = $params->get('category');
$enableManufacturer = $params->get('manufacturer');
$enablePrice        = $params->get('price');
$enableCustomField  = $params->get('custom_field');
$productFields      = $params->get('product_fields');
$enableKeyword      = $params->get('keyword');
$template           = $params->get('template_id');
$limit              = $params->get('limit', 0);
$option             = $input->getCmd('option', '');
$view               = $input->getCmd('view', '');
$layout             = $input->getCmd('layout', '');
$itemId             = $input->getInt('Itemid', 0);
$keyword            = $input->getString('keyword', '');
$action             = JRoute::_("index.php?option=com_redshop&view=search");
$getData            = $input->getArray();
$productOnSale      = $input->getInt('product_on_sale', 0);
$categoryForSale    = $params->get('category_for_sale', 0);
$pids       		= array();

if (!empty($cid))
{
	$list = RedshopHelperCategory::getCategoryListArray($categoryForSale);
	$childCat = array($categoryForSale);

	foreach ($list as $key => $value)
	{
		$childCat[] = $value->id;
	}

	foreach ($list as $key => $value)
	{
		$childCat[] = $value->id;
	}

	if (in_array($cid, $childCat))
	{
		$productList = array();
		$catList     = array();
		$manuList    = array();
		$pids        = array();

		if ($cid == $categoryForSale)
		{
			foreach ($childCat as $k => $value)
			{
				$productCats = $productHelper->getProductCategory($value);

				foreach ($productCats as $key => $value)
				{
					$productList[] = $productHelper->getProductById($value->product_id);
				}
			}
		}
		else
		{
			$productCats = $productHelper->getProductCategory($cid);

			foreach ($productCats as $key => $value)
			{
				$productList[$key] = $productHelper->getProductById($value->product_id);
			}
		}

		foreach ($productList as $k => $value)
		{
			$tmpCategories = is_array($value->categories) ? $value->categories : explode(',', $value->categories);
			$catList = array_merge($catList, $tmpCategories);
			$manuList[] = $value->manufacturer_id;
			$pids[]     = $value->product_id;
		}

		$catList = array_unique($catList);
		$manufacturers = ModRedshopFilter::getManufacturerOnSale(array_unique($manuList));
		$categories    = ModRedshopFilter::getParentCategoryOnSale($catList, $rootCategory, $categoryForSale);
		$rangePrice    = ModRedshopFilter::getRange($pids);
		}
	else
	{
		$categories    = ModRedshopFilter::getParentCategory($cid);
		$rangePrice    = ModRedshopFilter::getRangeMaxMin($cid);
		$manufacturers = ModRedshopFilter::getManufacturers($cid);
	}
}
elseif (!empty($mid))
{
	$manufacturerModel = JModelLegacy::getInstance('Manufacturers', 'RedshopModel');
	$manufacturerModel->setId($mid);
	$products = $manufacturerModel->getManufacturerProducts();
	$productList = array();

	foreach ($products as $key => $product)
	{
		$detail = RedshopHelperProduct::getProductById($product->product_id);
		$productList[] = $detail;
	}

	$manufacturers = array();
	$pids          = ModRedshopFilter::getProductByManufacturer($mid);
	$categories    = ModRedshopFilter::getCategorybyPids($pids, $rootCategory, $categoryForSale);
	$rangePrice    = ModRedshopFilter::getRange($pids);
}
elseif ($view == 'search')
{
	$modelSearch = JModelLegacy::getInstance("Search", "RedshopModel");
	$productList = $modelSearch->getData();
	$manuList    = array();
	$catList     = array();
	$pids        = array();

	foreach ($productList as $k => $value)
	{
		$tmpCategories = is_array($value->categories) ? $value->categories : explode(',', $value->categories);
		$catList = array_merge($catList, $tmpCategories);
		$pids[]  = $value->product_id;

		if ($value->manufacturer_id && $value->manufacturer_id != $mid)
		{
			$manuList[] = $value->manufacturer_id;
		}
	}

	$manufacturers = ModRedshopFilter::getManufacturers(array_unique($manuList));
	$categories    = ModRedshopFilter::getSearchCategories(array_unique($catList));
	$rangePrice    = ModRedshopFilter::getRange($pids);
}

$rangeMin = isset($getData['filterprice']['min']) ?: $rangePrice['min'];
$rangeMax = isset($getData['filterprice']['max']) ?: $rangePrice['max'];

if ($enablePrice)
{
	JHtml::stylesheet('mod_redshop_filter/jqui.css', false, true);
	JHtml::script('mod_redshop_filter/jquery-ui.min.js', false, true);
}

require JModuleHelper::getLayoutPath('mod_redshop_filter', $params->get('layout', 'default'));
