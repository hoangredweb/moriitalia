<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Category Controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class ReditemControllerCategorydetail extends JControllerLegacy
{
	/**
	 * Ajax filter items
	 *
	 * @return void
	 */
	public function ajaxFilter()
	{
		$app          = JFactory::getApplication();
		$view         = ucfirst($app->input->getString('view', 'categorydetail'));
		$model        = RModel::getFrontInstance($view, array('ignore_request' => true), 'com_reditem');
		$mainCategory = $model->getData();
		$mainContent  = $mainCategory->template->content;
		$groupTag     = false;
		$groupItems   = array();
		$items        = array();
		JPluginHelper::importPlugin('content');
		$dispatcher   = JEventDispatcher::getInstance();

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
				// Get filter customfield value
				$filterCustomfield = $app->input->get('filter_customfield', array(), 'array');

				// Get field object of groupped field
				$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');
				$groupField = $fieldModel->getItem($groupFieldId);
				$typeId     = $groupField->type_id;

				$avaiableGroupFields = array('checkbox', 'select', 'radio');

				// Check if this field is checkbox
				if (($groupField) && in_array($groupField->type, $avaiableGroupFields))
				{
					$groupTag = true;

					// Create list value of group
					$groupValue = trim($groupField->options);

					if ($groupValue)
					{
						$options      = explode("\n", $groupValue);
						$groupOptions = array();

						foreach ($options as $option)
						{
							$opt                       = explode('|', trim($option));
							$optionValue               = $opt[0];
							$optionText                = (isset($opt[1])) ? $opt[1] : $opt[0];
							$groupOptions[$optionText] = $optionValue;
						}

						if (!empty($filterCustomfield[$groupField->id]))
						{
							$optionValue = base64_decode($filterCustomfield[$groupField->id]);
							$optionText  = $optionValue;
							$key         = array_search($optionValue, $groupOptions);

							if ($key)
							{
								$optionText = $key;
							}

							$groupItems[$optionValue] = array(
								'text'  => $optionText,
								'items' => array(),
								'type'  => $typeId
							);
						}
						else
						{
							foreach ($groupOptions as $text => $value)
							{
								$groupItems[$value] = array(
									'text'  => $text,
									'items' => array(),
									'type'  => $typeId
								);
							}
						}
					}

					// Put items for group
					foreach ($mainCategory->items as $item)
					{
						// This is has value of groupped field
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

		// Items array
		if ((strpos($mainContent, '{items_loop_start}') !== false) && (strpos($mainContent, '{items_loop_end}') !== false))
		{
			$tempContent = explode('{items_loop_start}', $mainContent);
			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{items_loop_end}', $tempContent);
			$subTemplate = $tempContent[0];
			$subContent  = '';

			// Filter distance process
			$filterDistanceFrom  = $app->input->getRaw('gmap_filter_distance_from', '');
			$filterDistanceValue = $app->input->getRaw('gmap_filter_distance', '');

			if (!empty($filterDistanceFrom) && !empty($filterDistanceValue))
			{
				// Get latitude & longtitude from filter
				$filterDistanceFromArray = explode(',', $filterDistanceFrom);
				$latitude                = $filterDistanceFromArray[0];
				$longtitude              = $filterDistanceFromArray[1];

				$distance = (float) $filterDistanceValue;

				foreach ($mainCategory->items as $key => $item)
				{
					if (empty($item->itemLatLng) || (strpos($item->itemLatLng, ',') == false))
					{
						unset($mainCategory->items[$key]);

						continue;
					}

					$itemLatLng     = explode(',', $item->itemLatLng);
					$itemLatitude   = (float) $itemLatLng[0];
					$itemLongtitude = (float) $itemLatLng[1];

					$item->distance = ReditemHelperCategorygmap::calculateDistance($itemLatitude, $itemLongtitude, $latitude, $longtitude, 'K');

					if ($item->distance > $distance)
					{
						unset($mainCategory->items[$key]);
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

			if ($mainCategory->items)
			{
				$itemTypes = array();

				foreach ($mainCategory->items as $item)
				{
					$itemTypes[] = $item->type_id;
					$items[$item->id] = $item;
					$itemParams = new JRegistry($item->params);
					$item->itemLatLng = $itemParams->get('itemLatLng', '');
				}

				$itemTypes = array_unique($itemTypes);
				$db        = JFactory::getDbo();
				$query     = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__reditem_fields'))
					->where($db->qn('published') . ' = 1')
					->where($db->qn('type_id') . ' IN (' . implode(',', $itemTypes) . ')');
				$db->setQuery($query);
				$customFieldTags = $db->loadObjectList();

				if ($groupTag)
				{
					// Replace tag for items inside group
					foreach ($groupItems as $groupItem)
					{
						if (!empty($groupItem['items']))
						{
							$layoutFile    = 'items_group';
							$layoutOptions = array('component' => 'com_reditem');
							$layoutData    = array('value' => $groupItem['text']);

							$subContent .= ReditemHelperLayout::render($groupItem['type'], $layoutFile, $layoutData, $layoutOptions);

							foreach ($groupItem['items'] as $item)
							{
								$subContentSub = $subTemplate;
								ReditemHelperItem::replaceTag($subContentSub, $item, $mainCategory->id);
								ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, $customFieldTags);
								$items[$item->id]->content = $subContentSub;
								$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
							}
						}
					}
				}
				else
				{
					foreach ($mainCategory->items as $item)
					{
						$subContentSub = $subTemplate;
						ReditemHelperItem::replaceTag($subContentSub, $item, $mainCategory->id);
						ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, $customFieldTags);
						$items[$item->id]->content = $subContentSub;

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

			// Trigger Twig plugin
			if (JPluginHelper::isEnabled('system', 'twig'))
			{
				$dispatcher->trigger(
					'onTwigRender',
					array (
						&$subContent,
						'subcontent-' . $mainCategory->id . '.html',
						array (
							'items'  => !empty($mainCategory->items) ? $mainCategory->items : null,
							'fields' => ReditemHelperCustomfield::processValuesForTwig($mainCategory->fields),
							'page'   => $_SERVER
						)
					)
				);
			}

			// Trigger content plugins
			$dispatcher->trigger(
				'onContentPrepare',
				array(
					'com_reditem.categorydetail',
					&$subContent
				)
			);

			$return               = array();
			$return['category']   = $subContent;
			$return['pagination'] = $mainCategory->pagination;
			$return['itemsCount'] = (int) $mainCategory->items_count;
			$return['categoryId'] = $mainCategory->id;
			$return['itemIds']    = implode(',', ReditemHelperItem::getItemIds($mainCategory->items));
			$return['items']      = $items;
			$return['filters']    = $model->getFilterValues($mainCategory->all_items);

			// Related Categories
			$filterCategories  = JFactory::getApplication()->input->get('filter_category', array(), 'array');
			$relatedCategories = array();

			if (!empty($filterCategories))
			{
				if (array_filter($filterCategories))
				{
					foreach ($filterCategories as $filterCatId => $catId)
					{
						if (!isset($relatedCategories[$filterCatId]))
						{
							$relatedCategories[$filterCatId] = array();
						}

						if ($catId)
						{
							$tmpCategories = ReditemHelperCategory::getRelatedCategories($catId);

							if (!empty($tmpCategories))
							{
								foreach ($tmpCategories as $tmpCategory)
								{
									$tmpParentId = $tmpCategory->parent_id;

									if (!isset($relatedCategories[$tmpParentId]))
									{
										$relatedCategories[$tmpParentId] = array();
									}

									$relatedCategories[$tmpParentId][] = $tmpCategory;
								}
							}
						}
					}

					// Make selected if this filter has been choose already
					foreach ($filterCategories as $filterCatId => $catId)
					{
						if (isset($relatedCategories[$filterCatId]))
						{
							foreach ($relatedCategories[$filterCatId] as &$relatedCategory)
							{
								$relatedCategory->filter = false;

								if ($relatedCategory->id == $catId)
								{
									$relatedCategory->filter = true;
								}
							}
						}
					}

					$return['relatedCategories'] = $relatedCategories;
				}
			}

			echo json_encode($return);
		}
		else
		{
			echo json_encode(array());
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Ajax filter base on item's title and other condition. Use for {filter_title} tag
	 *
	 * @return   void
	 */
	public function ajaxFilterItemTitle()
	{
		$model = RModel::getFrontInstance('Categorydetail', array('ignore_request' => true), 'com_reditem');

		$jsonResult = array();

		$mainCategory = $model->getData();
		$itemsList = $mainCategory->items;

		if (!empty($itemsList))
		{
			foreach ($itemsList as $item)
			{
				$jsonResult[] = $item->title;
			}
		}

		echo json_encode($jsonResult);

		JFactory::getApplication()->close();
	}

	/**
	 * Ajax filter sub-categories
	 *
	 * @return void
	 */
	public function ajaxCatExtraFilter()
	{
		$return = array();

		$model = RModel::getFrontInstance('Categorydetail', array('ignore_request' => true), 'com_reditem');

		$mainCategory = $model->getData();

		$mainContent = $mainCategory->template->content;

		// Sub-Categories array
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
					ReditemHelperCategorytags::replaceTag($subContentSub, $subCategory, 'sub_');
					$subContent .= $subContentSub;
				}
			}

			$return['content'] = $subContent;
			$return['pagination'] = $mainCategory->sub_categories_pagination;
		}

		echo json_encode($return, JSON_FORCE_OBJECT);

		JFactory::getApplication()->close();
	}

	/**
	 * Ajax filter base on sub-category's title and other condition. Use for {filter_subcat_title} tag
	 *
	 * @return   void
	 */
	public function ajaxFilterSubCatTitle()
	{
		$model = RModel::getFrontInstance('Categorydetail', array('ignore_request' => true), 'com_reditem');

		$jsonResult = array();

		$mainCategory  = $model->getData();
		$subCategories = $mainCategory->sub_categories;

		if (!empty($subCategories))
		{
			foreach ($subCategories as $category)
			{
				$jsonResult[] = $category->title;
			}
		}

		echo json_encode($jsonResult);

		JFactory::getApplication()->close();
	}
}
