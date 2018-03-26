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
 * Category helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Category
 * @since       2.1
 *
 */
class ReditemHelperCategory
{
	/**
	 * Method for replace tag of template
	 *
	 * @param   object  $mainCategory        Category object
	 * @param   string  $javascriptCallback  Javascript callback function
	 *
	 * @return  string  HTML code after replace tag.
	 */
	public static function prepareCategoryDetailTemplate($mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		$mainContent = '';

		if (!$mainCategory)
		{
			return $mainContent;
		}

		$mainContent = $mainCategory->template->content;
		ReditemHelperCategorytags::replaceTag($mainContent, $mainCategory);

		$groupTag = false;
		$groupItems = array();

		// Check if group items exist
		if (preg_match_all('/{group_items[^}]*}/i', $mainContent, $matches) > 0)
		{
			$groupFieldId = 0;

			// Get result matches array
			$match = $matches[0];

			// Get only first result
			if (is_array($match))
			{
				$match = $match[0];
			}

			// Remove tag {group_items}
			$mainContent = str_replace($match, '', $mainContent);

			// Remove the unused chars
			$match = str_replace('{', '', $match);
			$match = str_replace('}', '', $match);

			$params = explode('|', $match);

			if (isset($params[1]))
			{
				$groupFieldId = (int) $params[1];
			}

			if ($groupFieldId)
			{
				// Get field object of groupped field
				$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');
				$groupField = $fieldModel->getItem($groupFieldId);
				$typeId     = $groupField->type_id;

				$avaiableGroupFields = array('checkbox', 'select', 'radio');

				// Check if this field is checkbox
				if (($groupField) && in_array($groupField->type, $avaiableGroupFields))
				{
					// Create list value of group
					$groupValue = trim($groupField->options);

					if ($groupValue)
					{
						$options = explode("\n", $groupValue);

						foreach ($options as $option)
						{
							$opt = explode('|', trim($option));
							$optionValue = $opt[0];
							$optionText = (isset($opt[1])) ? $opt[1] : $opt[0];

							$groupItems[$optionValue] = array(
								'text'  => $optionText,
								'items' => array(),
								'type'  => $typeId
							);
						}
					}

					// Put items for group
					foreach ($mainCategory->items as $item)
					{
						// This is has value of grouped field
						if (isset($item->customfield_values[$groupField->fieldcode]))
						{
							$itemGroupFieldValues = json_decode($item->customfield_values[$groupField->fieldcode], true);

							if (!empty($itemGroupFieldValues))
							{
								foreach ($itemGroupFieldValues as $itemGroupValue)
								{
									if (isset($groupItems[$itemGroupValue]))
									{
										$groupItems[$itemGroupValue]['items'][] = $item;
									}
								}
							}
						}
					}
				}
			}
		}

		// Show char tag
		$showChars = false;

		if (strrpos($mainContent, '{show_char}') !== false)
		{
			$mainContent = str_replace('{show_char}', '', $mainContent);
			$showChars   = true;
		}

		// Items array
		$mainContent = self::replaceItems($mainContent, $mainCategory, $showChars, '{items_loop_start}', '{items_loop_end}', $groupItems);

		// Sub categories (Featured)
		if ((strpos($mainContent, '{sub_featured_category_start}') !== false) && (strpos($mainContent, '{sub_featured_category_end}') !== false))
		{
			$tempContent = explode('{sub_featured_category_start}', $mainContent);
			$preContent = (count($tempContent) > 1) ? $tempContent[0] : '';

			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{sub_featured_category_end}', $tempContent);
			$subTemplate = $tempContent[0];

			$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

			$subContent = '';

			if ($mainCategory->sub_categories_featured)
			{
				// Has sub categories
				foreach ($mainCategory->sub_categories_featured as $subCategory)
				{
					$subContentSub = $subTemplate;
					ReditemHelperCategorytags::replaceTag($subContentSub, $subCategory, 'sub_', $mainCategory->id);

					// Assume sub category have same type as parent
					$subCategory->type = $mainCategory->type;
					$subContentSub = self::replaceItems(
						$subContentSub, $subCategory, $showChars,
						'{sub_category_items_start}', '{sub_category_items_end}', $groupItems
					);

					$subContent .= $subContentSub;
				}
			}

			$mainContent = $preContent . $subContent . $postContent;
		}

		// Sub categories
		if ((strpos($mainContent, '{sub_category_start}') !== false) && (strpos($mainContent, '{sub_category_end}') !== false))
		{
			$tempContent = explode('{sub_category_start}', $mainContent);
			$preContent = (count($tempContent) > 1) ? $tempContent[0] : '';

			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{sub_category_end}', $tempContent);
			$subTemplate = $tempContent[0];

			$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

			$subContent = '';

			if ($mainCategory->sub_categories)
			{
				// Has sub categories
				foreach ($mainCategory->sub_categories as $subCategory)
				{
					$subContentSub = $subTemplate;
					ReditemHelperCategorytags::replaceTag($subContentSub, $subCategory, 'sub_', $mainCategory->id);

					// Check if we have this tag exists before execute
					if (strpos($subContentSub, '{sub_category_recursive}') !== false)
					{
						// Replace sub categories recursive tag on this this case if it's exists
						$subContentSub = str_replace('{sub_category_recursive}', self::replaceRecursiveCategory($subCategory), $subContentSub);
					}

					$subContent .= $subContentSub;
				}
			}

			$mainContent = $preContent . '<div id="reditemCategories">' . $subContent . '</div>' . $postContent;
		}

		// Related categories
		if ((strpos($mainContent, '{related_category_start}') !== false) && (strpos($mainContent, '{related_category_end}') !== false))
		{
			$tempContent = explode('{related_category_start}', $mainContent);
			$preContent = (count($tempContent) > 1) ? $tempContent[0] : '';

			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{related_category_end}', $tempContent);
			$subTemplate = $tempContent[0];

			$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

			$subContent = '';

			$relatedCategories = self::getRelatedCategories($mainCategory->id);

			if ($relatedCategories)
			{
				// Has sub categories
				foreach ($relatedCategories as $subCategory)
				{
					$subContentSub = $subTemplate;
					ReditemHelperCategorytags::replaceTag($subContentSub, $subCategory, 'sub_', $subCategory->parent_id);
					$subContent .= $subContentSub;
				}
			}

			$mainContent = $preContent . $subContent . $postContent;
		}

		// Filter tag
		ReditemHelperTags::tagReplaceFilter($mainContent, $mainCategory, $javascriptCallback);
		ReditemHelperCategorytags::createCategoryFilter($mainContent, $mainCategory);
		ReditemHelperTags::replaceSortTool($mainContent, $mainCategory, $javascriptCallback);

		JPluginHelper::importPlugin('content');
		$mainContent = JHtml::_('content.prepare', $mainContent);

		return $mainContent;
	}

	/**
	 * Get all related categories
	 *
	 * @param   int      $categoryId  Category Id
	 * @param   boolean  $idOnly      Return Id of category or category object
	 *
	 * @return  array
	 */
	public static function getRelatedCategories($categoryId, $idOnly = false)
	{
		$categoryId = (int) $categoryId;

		if (!$categoryId)
		{
			return false;
		}

		$db = JFactory::getDBO();

		$query = $db->getQuery(true)
			->select($db->qn('parent_id'))
			->from($db->qn('#__reditem_category_related'))
			->where($db->qn('related_id') . ' = ' . $db->quote($categoryId));
		$db->setQuery($query);
		$relatedCatIds = $db->loadColumn();

		if (!$relatedCatIds)
		{
			return false;
		}

		// If idOnly param has been set, return the array id
		if ($idOnly)
		{
			return $relatedCatIds;
		}

		$relatedCategories = array();

		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

		foreach ($relatedCatIds as $relatedCatId)
		{
			$relatedCategories[] = $categoryModel->getItem($relatedCatId);
		}

		return $relatedCategories;
	}

	/**
	 * Get children categories for given parent categories.
	 *
	 * @param   array  $categories  Parent category ids.
	 *
	 * @return  array  Array of categories.
	 */
	public static function getChildrenCategories($categories)
	{
		if (empty($categories))
		{
			return array();
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('DISTINCT ' . $db->qn('c2.id'))
			->from($db->qn('#__reditem_categories', 'c1'))
			->innerJoin(
				$db->qn('#__reditem_categories', 'c2') . ' ON ' . $db->qn('c1.lft') . ' <= ' . $db->qn('c2.lft') .
				' AND ' . $db->qn('c1.rgt') . ' >= ' . $db->qn('c2.rgt')
			)
			->where($db->qn('c1.id') . ' IN (' . implode(',', $categories) . ')')
			->where($db->qn('c2.published') . ' = 1');
		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Get categories list.
	 *
	 * @return array Categories list.
	 */
	public static function getCategories()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__reditem_categories'))
			->where($db->qn('level') . ' > 0');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method for get custom fields list of specific category.
	 *
	 * @param   int  $catId  Id of a category
	 *
	 * @return  array  List of fields. False otherwise.
	 */
	public static function getCustomFieldTags($catId)
	{
		if (!$catId)
		{
			return false;
		}

		$fieldsmodel = RModel::getAdminInstance('Category_Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsmodel->setState('filter.catId', $catId);
		$fieldsmodel->setState('filter.published', 1);
		$fields = $fieldsmodel->getItems();

		return $fields;
	}

	/**
	 * Replace items tag. It also used for sub categories' items if needed
	 *
	 * @param   string  $mainContent   Main content which one will return to render
	 * @param   object  $mainCategory  Category object
	 * @param   bool    $showChars     Show chars
	 * @param   string  $beginTag      Begin tag
	 * @param   string  $endTag        Ending tag
	 * @param   array   $groupItems    Group items.
	 *
	 * @return   string
	 */
	public static function replaceItems(
		$mainContent, $mainCategory, $showChars,
		$beginTag = '{items_loop_start}', $endTag= '{items_loop_end}', $groupItems = array())
	{
		// Items array
		if ((strpos($mainContent, $beginTag) !== false) && (strpos($mainContent, $endTag) !== false))
		{
			$tempContent = explode($beginTag, $mainContent);
			$preContent  = (count($tempContent) > 1) ? $tempContent[0] : '';
			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode($endTag, $tempContent);
			$subTemplate = $tempContent[0];
			$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';
			$subContent  = '';

			if (!empty($mainCategory->items))
			{
				if (!empty($groupItems))
				{
					$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');

					// Replace tag for items inside group
					foreach ($groupItems as $groupItem)
					{
						$layoutFile    = 'items_group';
						$layoutOptions = array('component' => 'com_reditem');
						$layoutData    = array('value' => $groupItem['text']);
						$type          = $typeModel->getItem((int) $groupItem['type']);

						$subContent .= ReditemHelperLayout::render($type, $layoutFile, $layoutData, $layoutOptions);

						foreach ($groupItem['items'] as $item)
						{
							$subContentSub = $subTemplate;
							ReditemHelperItem::replaceTag($subContentSub, $item, $mainCategory->id);
							ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, ReditemHelperItem::getCustomFieldTags($item->type_id));
							$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
						}
					}
				}
				else
				{
					$tmpChar = '';

					foreach ($mainCategory->items as $item)
					{
						$subContentSub   = $subTemplate;
						$customFieldTags = ReditemHelperItem::getCustomFieldTags($item->type_id);
						ReditemHelperItem::replaceTag($subContentSub, $item, $mainCategory->id);
						ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, $customFieldTags);

						if ($showChars)
						{
							$itemFirstChar = JString::substr($item->title, 0, 1);

							if (is_numeric($itemFirstChar))
							{
								$itemFirstChar = '#';
							}

							if (empty($tmpChar) || $tmpChar != $itemFirstChar)
							{
								$tmpChar = $itemFirstChar;

								$subContent .= ReditemHelperLayout::render(
									$item->type,
									'features.item_head_char',
									array('char' => $tmpChar),
									array('component' => 'com_reditem')
								);
							}
						}

						$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
					}
				}
			}

			$mainContent = $preContent . '<div id="reditemsItems">' . $subContent . '</div>' . $postContent;
		}

		return $mainContent;
	}

	/**
	 * Render sub categories with recursive
	 *
	 * @param   object  $category  Category object
	 * @param   int     $level     Level
	 *
	 * @return string
	 */
	public static function replaceRecursiveCategory($category, $level = 0)
	{
		$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true));
		$categoriesModel->setState('filter.parentid', $category->id);

		// Get sub categories
		$subCategories = $categoriesModel->getItems();

		// We have subCategories
		if (!empty($subCategories))
		{
			// Wrapped for all sub categories
			$html = '<div class="subCategories">';

			foreach ($subCategories as $category)
			{
				$class = 'subCategory level-' . $level;
				$html .= '<div class="' . $class . '"><span>' . $category->title . '</span></div>';

				// Recursive it again with level +1
				$html .= self::replaceRecursiveCategory($category, $level + 1);
			}

			$html .= '</div>';

			return $html;
		}

		// If no subCategories than of course do nothing but make sure we have returned empty string for replacing
		return '';
	}

	/**
	 * Gets array of item types inside given category.
	 *
	 * @param   int  $catId  Category id.
	 *
	 * @return  array  Type ids array.
	 */
	public static function getItemsTypes($catId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('DISTINCT' . $db->qn('i.type_id'))
			->from($db->qn('#__reditem_item_category_xref', 'icx'))
			->innerJoin($db->qn('#__reditem_items', 'i') . ' ON ' . $db->qn('icx.item_id') . ' = ' . $db->qn('i.id'))
			->where($db->qn('icx.category_id') . ' = ' . (int) $catId);
		$types = $db->setQuery($query)->loadColumn();

		if (!empty($types))
		{
			return $types;
		}

		return array();
	}
}
