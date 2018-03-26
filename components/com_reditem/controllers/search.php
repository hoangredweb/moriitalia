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
class ReditemControllerSearch extends JControllerLegacy
{
	/**
	 * Ajax filter items
	 *
	 * @return void
	 */
	public function ajaxFilter()
	{
		$app         = JFactory::getApplication();
		$model       = RModel::getFrontInstance('Search', array('ignore_request' => true), 'com_reditem');
		$data        = $model->getData();
		$items       = $data->items;
		$type        = $data->type;
		$mainContent = $data->content;
		$groupTag    = false;
		$groupItems  = array();

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

				$availableGroupFields = array('checkbox', 'select', 'radio');

				// Check if this field is checkbox
				if (($groupField) && in_array($groupField->type, $availableGroupFields))
				{
					$groupTag = true;

					// Create list value of group
					$groupValue = trim($groupField->options);

					if ($groupValue)
					{
						$options = explode("\n", $groupValue);
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
								'items' => array()
							);
						}
						else
						{
							foreach ($groupOptions as $text => $value)
							{
								$groupItems[$value] = array(
									'text'  => $text,
									'items' => array()
								);
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

			if (!empty($items))
			{
				$customFieldTags = ReditemHelperItem::getCustomFieldTags($type->id);

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
							$subContent   .= ReditemHelperLayout::render($type, $layoutFile, $layoutData, $layoutOptions);

							foreach ($groupItem['items'] as $item)
							{
								$subContentSub = $subTemplate;
								ReditemHelperItem::replaceTag($subContentSub, $item);
								ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, $customFieldTags);
								$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
							}
						}
					}
				}
				else
				{
					foreach ($items as $item)
					{
						$subContentSub = $subTemplate;
						ReditemHelperItem::replaceTag($subContentSub, $item);
						ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item, $customFieldTags);
						$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
					}
				}
			}

			$return               = array();
			$return['category']   = $subContent;
			$return['pagination'] = $data->items_pagination;

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

			echo json_encode($return, JSON_FORCE_OBJECT);
		}
		else
		{
			echo json_encode(array(), JSON_FORCE_OBJECT);
		}

		JFactory::getApplication()->close();
	}
}
