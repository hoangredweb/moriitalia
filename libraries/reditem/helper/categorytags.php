<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Category tags helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.CategoryTags
 * @since       2.1
 *
 */
class ReditemHelperCategorytags
{
	/**
	 * Replace tag on content of template -> Category TAG
	 * Replace for ReditemTagsHelper::tagReplaceCategory() function
	 *
	 * @param   string  &$content     Content template
	 * @param   object  $category     Category data array
	 * @param   string  $subCategory  prefix string for sub Category tag "sub_"
	 *
	 * @return  boolean  True if success. False otherwise
	 */
	public static function replaceTag(&$content, $category, $subCategory = '')
	{
		// Check if content is empty or category is not available
		if (empty($content) || empty($category))
		{
			return false;
		}

		// Items count
		if (strpos($content, '{' . $subCategory . 'items_count}') !== false)
		{
			if (!isset($category->items_count))
			{
				$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
				$itemsModel->setState('filter.catid', $category->id);
				$itemsModel->setState('filter.published', 1);
				$category->items_count = $itemsModel->getTotal();
			}

			$itemCountHtml = ReditemHelperLayout::render(
				null,
				'features.items_count',
				array('count' => (int) $category->items_count, 'category' => $category, 'prefix' => $subCategory),
				array('component' => 'com_reditem')
			);

			$content = str_replace('{' . $subCategory . 'items_count}', $itemCountHtml, $content);
		}

		// Category link
		$categoryLink = JRoute::_(ReditemRouterHelper::getCategoryRoute($category->id), false);

		$content = str_replace('{' . $subCategory . 'category_link}', $categoryLink, $content);

		// Category Id
		$content = str_replace('{' . $subCategory . 'category_id}', $category->id, $content);

		// Category Title
		$content = str_replace('{' . $subCategory . 'category_title}', $category->title, $content);

		// Introtext tag
		$matches = array();

		if (preg_match_all('/{' . $subCategory . 'category_introtext[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			foreach ($matches as $match)
			{
				$textParams = explode('|', $match);

				$textContent = $category->introtext;

				if (isset($textParams[1]))
				{
					// Have param limit string
					$textContent = JHTML::_('string.truncate', $textContent, (int) $textParams[1], true, false);
				}

				$content = str_replace($match, $textContent, $content);
			}
		}

		// Fulltext tag
		$matches = array();

		if (preg_match_all('/{' . $subCategory . 'category_fulltext[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			foreach ($matches as $match)
			{
				$textParams = explode('|', $match);

				$textContent = $category->fulltext;

				if (isset($textParams[1]))
				{
					// Have param limit string
					$textContent = JHTML::_('string.truncate', $textContent, (int) $textParams[1], true, false);
				}

				$content = str_replace($match, $textContent, $content);
			}
		}

		// Original Image tag
		$imageOriginalLink = ReditemHelperImage::getImageLink($category, 'category', $category->category_image, '', 300, 300, true);
		$img = ReditemHelperImage::getImageLink($category, 'category', $category->category_image);
		$content = str_replace('{' . $subCategory . 'category_image}', $img, $content);
		$content = str_replace('{' . $subCategory . 'category_image_link}', $imageOriginalLink, $content);

		// Image Thumbnail Large tag
		$imageLarge = ReditemHelperImage::getImageLink($category, 'category', $category->category_image, 'large');
		$content = str_replace('{' . $subCategory . 'category_image_large}', $imageLarge, $content);

		// Image Thumbnail Large tag
		$imageMedium = ReditemHelperImage::getImageLink($category, 'category', $category->category_image, 'medium');
		$content = str_replace('{' . $subCategory . 'category_image_medium}', $imageMedium, $content);

		// Image Thumbnail Large tag
		$imageSmall = ReditemHelperImage::getImageLink($category, 'category', $category->category_image, 'small');
		$content = str_replace('{' . $subCategory . 'category_image_small}', $imageSmall, $content);

		// Print icon
		if (empty($subCategory))
		{
			$url  = '#';
			$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			$attribs['title']   = JText::_('JGLOBAL_PRINT');
			$attribs['onclick'] = "window.print(); return false;";
			$attribs['rel']     = 'nofollow';
			$printHtml = JHtml::_('link', JRoute::_($url), $text, $attribs);
			$content = str_replace('{print_icon}', $printHtml, $content);
		}

		// Next category stuff
		if ((strpos($content, '{next_category_link}') == true)
			|| (strpos($content, '{next_category_title}') == true)
			|| (strpos($content, '{prev_category_title}') == true)
			|| (strpos($content, '{prev_category_title}') == true))
		{
			$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
			$categoriesModel->setState('filter.published', 1);
			$categoriesModel->setState('list.select', 'c.id, c.title');
			$categoriesModel->setState('filter.parent_id', $category->parent_id);
			$categoriesModel->setState('filter.level', $category->level);
			$categoriesArray = $categoriesModel->getItems();

			$nextCategory = null;
			$previousCategory = null;

			foreach ($categoriesArray as $key => $sequeneCategory)
			{
				if ($sequeneCategory->id == $category->id)
				{
					$previousKey = $key - 1;

					if (isset($categoriesArray[$previousKey]))
					{
						$previousCategory = $categoriesArray[$previousKey];
					}

					$nextKey = $key + 1;

					if (isset($categoriesArray[$nextKey]))
					{
						$nextCategory = $categoriesArray[$nextKey];
					}

					// Break the for loop if found category
					break;
				}
			}

			// Process for Next category link
			if (strpos($content, '{next_category_link}') == true)
			{
				$nextCategoryLink = '';

				if (!empty($nextCategory) && ReditemHelperACL::checkCategoryPermission('category.view', $nextCategory->id))
				{
					$nextCategoryLink = JRoute::_(ReditemRouterHelper::getCategoryRoute($nextCategory->id), false);
				}

				$content = str_replace('{next_category_link}', $nextCategoryLink, $content);
			}

			// Process for Next category title
			if (strpos($content, '{next_category_title}') == true)
			{
				$nextCategoryTitle = '';

				if (!empty($nextCategory) && ReditemHelperACL::checkCategoryPermission('category.view', $nextCategory->id))
				{
					$nextCategoryTitle = $nextCategory->title;
				}

				$content = str_replace('{next_category_title}', $nextCategoryTitle, $content);
			}

			// Process for {if_next_category} category tag
			if (strpos($content, '{if_next_category}') == true)
			{
				$tempContent = explode('{if_next_category}', $content);
				$preContent = (count($tempContent) > 1) ? $tempContent[0] : '';

				$tempContent = $tempContent[count($tempContent) - 1];
				$tempContent = explode('{end_if_next_category}', $tempContent);
				$subTemplate = $tempContent[0];

				$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

				if (empty($nextCategory))
				{
					$subTemplate = '';
				}

				$content = $preContent . $subTemplate . $postContent;
			}

			// Process for Previous category link
			if (strpos($content, '{prev_category_link}') == true)
			{
				$prevCategoryLink = '';

				if (!empty($previousCategory) && ReditemHelperACL::checkCategoryPermission('category.view', $previousCategory->id))
				{
					$prevCategoryLink = JRoute::_(ReditemRouterHelper::getCategoryRoute($previousCategory->id), false);
				}

				$content = str_replace('{prev_category_link}', $prevCategoryLink, $content);
			}

			// Process for Previous category title
			if (strpos($content, '{prev_category_title}') == true)
			{
				$prevCategoryTitle = '';

				if (!empty($previousCategory) && ReditemHelperACL::checkCategoryPermission('category.view', $previousCategory->id))
				{
					$prevCategoryTitle = $previousCategory->title;
				}

				$content = str_replace('{prev_category_title}', $prevCategoryTitle, $content);
			}

			// Process for {if_prev_category} category tag
			if (strpos($content, '{if_prev_category}') == true)
			{
				$tempContent = explode('{if_prev_category}', $content);
				$preContent = (count($tempContent) > 1) ? $tempContent[0] : '';

				$tempContent = $tempContent[count($tempContent) - 1];
				$tempContent = explode('{end_if_prev_category}', $tempContent);
				$subTemplate = $tempContent[0];

				$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

				if (empty($previousCategory))
				{
					$subTemplate = '';
				}

				$content = $preContent . $subTemplate . $postContent;
			}

			// Clean up memory
			unset($categoriesModel);
			unset($categoriesArray);
			unset($nextCategory);
			unset($prevCategory);
		}

		// Replace category custom fields
		if (!isset($customFieldTags))
		{
			$customFieldTags = ReditemHelperCategory::getCustomFieldTags($category->id);
		}

		foreach ($customFieldTags As $tag)
		{
			$fieldClass = ReditemHelperCustomfield::getCustomField($tag->type);
			$fieldClass->bind($tag);

			// Replace the title tag
			$fieldClass->replaceLabelTag($content, $tag);

			// Replace the value tag
			$fieldClass->replaceValueTag($content, $tag, $category);
		}

		// Replace {category_gmap} tag
		self::replaceCategoryGmapTag($content, $category, $subCategory);

		return true;
	}

	/**
	 * Replace Category Filter tag on content of template
	 * Replace for ReditemTagsHelper::tagReplaceCategoryFilter() function
	 *
	 * @param   string  &$content            Content template
	 * @param   array   $category            Category data
	 * @param   string  $javascriptCallback  Javascript callback function
	 *
	 * @return  boolean  True if success. False otherwise.
	 */
	public static function createCategoryFilter(&$content, $category, $javascriptCallback = 'reditemCatExtraFilterAjax')
	{
		// Check if content is empty or category object is null
		if (empty($content) || empty($category))
		{
			return false;
		}

		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_categories');

		// {filter_subcatitemsavaiable} render filter
		if (strrpos($content, '{filter_subcatitemsavaiable}') !== false)
		{
			$options = array();
			$options[] = JHTML::_('select.option', '', JText::_('COM_REDITEM_TEMPLATE_TAG_SUB_CATEGORY_ITEMS_AVAIABLE_SELECT'));
			$options[] = JHTML::_('select.option', '1', JText::_('COM_REDITEM_TEMPLATE_TAG_SUB_CATEGORY_ITEMS_AVAIABLE_ONLY_ITEMS_AVAIABLE'));
			$options[] = JHTML::_('select.option', '0', JText::_('COM_REDITEM_TEMPLATE_TAG_SUB_CATEGORY_ITEMS_AVAIABLE_ALL'));

			$attribs = ' class="chosen" onChange="javascript:' . $javascriptCallback . '();"';
			$value = JFactory::getApplication()->input->getInt('filter_subcatitemsavaiable', '');
			$selectHTML = JHTML::_('select.genericlist', $options, 'filter_subcatitemsavaiable', $attribs, 'value', 'text', $value);
			$content = str_replace('{filter_subcatitemsavaiable}', $selectHTML, $content);
		}

		// {filter_subcat_title | autocomplete | hint}
		$matches = array();

		if (preg_match_all('/{filter_subcat_title[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			$filterValue = JFactory::getApplication()->input->get('filter_subcat_title', null, 'raw');

			foreach ($matches as $match)
			{
				// Default filter config
				$filterConfig = array('autocomplete' => false, 'hint' => '');

				// Prepare filter config from tag
				$params = str_replace('{', '', $match);
				$params = str_replace('}', '', $params);
				$params = explode('|', $params);

				if (isset($params[1]))
				{
					$filterConfig['autocomplete'] = (boolean) $params[1];
				}

				if (isset($params[2]))
				{
				$filterConfig['hint'] = $params[2];
				}

				// Prepare data for layout
				$layoutData = array(
					'config'             => $filterConfig,
					'javascriptCallback' => 'reditemCatExtraFilterAjax',
					'value'              => $filterValue
				);

				$generatedFilterHTML = RLayoutHelper::render('filters.filter_subcat_title', $layoutData, null, array('component' => 'com_reditem'));
				$content = str_replace($match, $generatedFilterHTML, $content);
			}
		}

		$dispatcher->trigger('onReplaceCategoryFilterExtrasFieldTag', array(&$content, $category));

		return true;
	}

	/**
	 * Method for replace category gmap tag.
	 *
	 * @param   string  &$content     HTML content of template
	 * @param   object  $category     Data object of category
	 * @param   string  $subCategory  Prefix of category
	 *
	 * @return  bool    True on success, false otherwise.
	 */
	public static function replaceCategoryGmapTag(&$content, $category, $subCategory = '')
	{
		// Check if content is empty or category is not available
		if (empty($content) || empty($category) || strpos($content, '{' . $subCategory . 'category_gmap}') === false)
		{
			return false;
		}

		$categoryParams = new JRegistry($category->params);
		$categoryLatLng = explode(',', $categoryParams->get('categoryLatLng', ''));

		if (count($categoryLatLng) < 2)
		{
			$content = str_replace('{' . $subCategory . 'category_gmap}', '', $content);

			return true;
		}

		$layoutData    = array('category' => $category, 'latitude' => (float) $categoryLatLng[0], 'longitude' => (float) $categoryLatLng[1]);
		$layoutFile    = 'category.gmap';
		$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

		$contentHtml = ReditemHelperLayout::render($category->type, $layoutFile, $layoutData, $layoutOptions);
		$content = str_replace('{' . $subCategory . 'category_gmap}', $contentHtml, $content);

		return true;
	}
}
