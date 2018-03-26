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

	public function replaceCompareProductsButton($product_id = 0, $category_id = 0, $data_add = "", $is_relatedproduct = 0)
	{
		$app = JFactory::getApplication();
		$Itemid = JRequest::getInt('Itemid');
		$prefix = ($is_relatedproduct == 1) ? "related" : "";
		// for compare product div...
		if (PRODUCT_COMPARISON_TYPE != "")
		{
			if (strpos($data_add, '{' . $prefix . 'compare_product_div}') !== false)
			{
				$menu = $app->getMenu();
				$menuItem = $menu->getItems('link', 'index.php?option=com_redshop&view=product&layout=compare', true);
				
				$div                 = $this->makeCompareProductDiv();
				$compare_product_div = "<div id='divCompareProduct'>" . $div . "</div>";
				$compare_product_div .= "<a href='" . JRoute::_('index.php?option=com_redshop&view=product&layout=compare&Itemid=' . $menuItem->id) . "' >" . JText::_('COM_REDSHOP_COMPARE')
					. "</a><br />";
				$data_add = str_replace("{compare_product_div}", $compare_product_div, $data_add);
			}

			if (strpos($data_add, '{' . $prefix . 'compare_products_button}') !== false)
			{
				if ($category_id == 0)
				{
					$category_id = $this->getCategoryProduct($product_id);
				}

				$compareButton = new stdClass;
				$compareButton->text = JText::_("COM_REDSHOP_ADD_TO_COMPARE");
				$compareButton->value = $product_id . '.' . $category_id;

				$compareButtonAttributes = array(
					'cssClassSuffix' => ' no-group'
				);

				$compare_product = JHTML::_(
						'redshopselect.checklist',
						array($compareButton),
						'rsProductCompareChk',
						$compareButtonAttributes,
						'value',
						'text',
						(new RedshopProductCompare)->getItemKey($product_id)
					);
				$data_add = str_replace("{" . $prefix . "compare_products_button}", $compare_product, $data_add);
			}
		}
		else
		{
			$data_add = str_replace("{" . $prefix . "compare_product_div}", "", $data_add);
			$data_add = str_replace("{" . $prefix . "compare_products_button}", "", $data_add);
		}

		return $data_add;
	}
}