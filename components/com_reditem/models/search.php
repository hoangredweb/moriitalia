<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_reditem/helpers/reditem.php';

/**
 * Search page model
 *
 * @package     RedITEM.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class ReditemModelSearch extends RModel
{
	/**
	 * Get data of item
	 *
	 * @return  boolean/array
	 */
	public function getData()
	{
		$app        = JFactory::getApplication();
		$input      = $app->input;
		$params     = JComponentHelper::getParams('com_reditem');
		$data       = new stdClass;
		$typeId     = $input->getInt('typeId', 0);
		$templateId = $input->getInt('templateId', 0);

		if (!$typeId || !$templateId)
		{
			return false;
		}

		$templateModel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
		$itemsModel    = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$typeModel     = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
		$type          = $typeModel->getItem($typeId);
		$template      = $templateModel->getItem($templateId);

		if (!$type || !$template)
		{
			return false;
		}

		// Items
		if (strrpos($template->content, '{items_loop_start}') !== false)
		{
			// Has tag {items_loop_start}
			$itemsOrdering    = 'i.' . $params->get('items_ordering', 'alias');
			$itemsDestination = $params->get('items_destination', 'asc');
			$start            = $input->getInt($itemsModel->getPagination()->prefix . 'limitstart', 0);
			$limit            = 0;
			$paginationTag    = '';

			// If no value of input, try to find items_sort tag in template
			if (preg_match('/{items_sort\|[^}]*}/i', $template->content, $matches) > 0)
			{
				// Only use first items order tag
				$match    = $matches[0];
				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);
				$tmpMatch = explode('|', $tmpMatch);

				// Get order column name
				if (!empty($tmpMatch[1]))
				{
					$itemsOrdering = $tmpMatch[1];
				}

				// Get order column destination
				if (!empty($tmpMatch[2]))
				{
					$itemsDestination = 'desc';
				}
			}

			// Get pagination limit
			if (preg_match('/{items_pagination[^}]*}/i', $template->content, $matches) > 0)
			{
				// Only use first pagination tag
				$match = $matches[0];
				$tmp = explode('|', $match);

				if (isset($tmp[1]))
				{
					// Have limit number
					$limit = (int) $tmp[1];
				}

				$paginationTag = $match;
			}

			$app->setUserState('com_reditem.items_pagination.limit', $limit);

			$itemsModel->setState('filter.types', $typeId);
			$itemsModel->setState('filter.published', 1);
			$itemsModel->setState('list.select', 'i.*');
			$itemsModel->setState('list.ordering', $itemsOrdering);
			$itemsModel->setState('list.direction', $itemsDestination);
			$itemsModel->setState('list.limit', $limit);
			$itemsModel->setState('list.start', $start);

			/*
			 * Add filter by Categories
			 */
			$filterCategory = $input->get('filter_category', array(), 'array');

			if (!empty($filterCategory) && is_array($filterCategory))
			{
				$filterCategoriesIds = array();

				foreach ($filterCategory as $filterCategoryId => $categoryId)
				{
					if (!$categoryId)
					{
						// If value of this filter is empty. Load all sub-categories of this filter
						$subCategories = ReditemHelper::getSubCategories($filterCategoryId);
						$filterCategoriesIds = array_merge($subCategories);
					}
					else
					{
						// If value of this filter has set.
						$filterCategoriesIds[] = $categoryId;
						$subCategories = ReditemHelper::getSubCategories($categoryId);
						$filterCategoriesIds = array_merge($subCategories);
					}
				}

				$itemsModel->setState('filter.catid', $filterCategoriesIds);
				$itemsModel->setState('filter.item_ids', $this->getItemFilter());
			}

			/*
			 * Add filter by item's title
			 */
			$filterTitle = $input->getRaw('filter_title', null);

			if ($filterTitle)
			{
				$itemsModel->setState('filter.plgSearchItem', $filterTitle);
			}

			/*
			 * Add filter search in frontend
			 */
			$searchInFrontend = $input->get('filter_searchinfrontend', '');

			if ($searchInFrontend != '')
			{
				$itemsModel->setState('filter.searchInFrontend', $searchInFrontend);
			}

			/*
			 * Add filter by custom field value
			 */
			$filterFields = $input->get('filter_customfield', array(), 'array');

			if (!empty($filterFields))
			{
				$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

				// Remove unused filter custom value
				foreach ($filterFields as $fieldId => $value)
				{
					if (empty($value))
					{
						unset($filterFields[$fieldId]);

						continue;
					}
					elseif (is_array($value))
					{
						$encodedValues = array();

						foreach ($value as $tmpValue)
						{
							$encodedValues[] = base64_decode($tmpValue);
						}

						$value = $encodedValues;
					}
					else
					{
						$value = base64_decode($value);
					}

					$field = $fieldModel->getItem($fieldId);
					$filterFields[$fieldId]['value'] = $value;
					$filterFields[$fieldId]['table'] = ReditemHelperType::getTableName($field->type_id);
				}

				$filterFields = new JRegistry($filterFields);
				$itemsModel->setState('filter.cfSearch', $filterFields->toString());
			}

			/*
			 * Add filter by custom field with ranges value
			 */
			$filterFieldsRange = $input->get('filter_ranges', array(), 'array');

			if (!empty($filterFieldsRange))
			{
				$itemsModel->setState('filter.cfTableRanges', '#__reditem_types_' . $type->table_name);

				foreach ($filterFieldsRange as $field => $value)
				{
					if (empty($value))
					{
						unset($filterFieldsRange[$field]);
					}
				}

				$itemsModel->setState('filter.cfSearchRanges', json_encode($filterFieldsRange));
			}

			$items = $itemsModel->getItems();

			// Process check view permission for sub-categories list.
			ReditemHelperACL::processItemACL($items);

			// Replace pagination data for {items_pagination} tag
			$data->paginationPrefix = $itemsModel->getPagination()->prefix;
			$pagination = '<div class="pagination" id="reditemItemsSearchPagination">' . $itemsModel->getPagination()->getPagesLinks() . '</div>';
			$template->content = str_replace($paginationTag, $pagination, $template->content);

			$data->items_pagination = $itemsModel->getPagination()->getPagesLinks();
			$data->items = array();
			$itemIds     = ReditemHelperItem::getItemIds($items);
			$cfValues    = ReditemHelperItem::getCustomFieldValues($itemIds);

			if ($items)
			{
				foreach ($items as $item)
				{
					if (isset($cfValues[$item->type_id][$item->id]))
					{
						$item->customfield_values = $cfValues[$item->type_id][$item->id];
					}

					$item->template         = $templateModel->getItem($item->template_id);
					$data->items[$item->id] = $item;
				}
			}
		}

		// Check jQuery Masonry tags
		if (preg_match('/{items_masonry[^}]*}/i', $template->content, $matches) > 0)
		{
			$doc          = JFactory::getDocument();
			$masonryWidth = 200;
			RHelperAsset::load('masonry.pkgd.min.js', 'com_reditem');

			// Get the width config if exist
			$match = $matches[0];
			$tmp   = explode('|', $match);

			if (isset($tmp[1]))
			{
				$masonryWidth = (int) $tmp[1];
			}

			$jsScript = '(function($){
				$(document).ready(function($){
					$("#reditemsItems").masonry({
						itemSelector: ".reditemItem",
						columnWidth: ' . $masonryWidth . ',
						gutter: 30
					})
				});
			})(jQuery);';

			$doc->addScriptDeclaration($jsScript);
			$template->content = str_replace($match, '', $template->content);
		}

		$data->content  = $template->content;
		$data->type     = $type;
		$data->template = $template;

		return $data;
	}

	/**
	 * Get items data from filter
	 *
	 * @return  array
	 */
	private function getItemFilter()
	{
		$input = JFactory::getApplication()->input;
		$db    = JFactory::getDBO();

		/*Array ( [category] => Array ( [0] => 40 [1] => ) ) */
		$categoryFilters = $input->get('filter_category', array(), 'array');
		$categoryFilters = array_filter($categoryFilters);

		// Return empty array for show all items if the filters is "show all"
		if (empty($categoryFilters))
		{
			return array();
		}

		$query = $db->getQuery(true);
		$index = 0;

		foreach ($categoryFilters as $categoryFilter)
		{
			$categories = array();

			if (is_array($categoryFilter))
			{
				foreach ($categoryFilter as $categoryFilterTmpId)
				{
					// Add it's category Id
					$categories[] = $categoryFilterTmpId;

					$tmpSubCategories = ReditemHelper::getSubCategories($categoryFilterTmpId);
					$categories = array_merge($categories, $tmpSubCategories);
				}
			}
			else
			{
				$categories = ReditemHelper::getSubCategories($categoryFilter);

				// Add it's category Id
				$categories[] = $categoryFilter;
			}

			if ($index == 0)
			{
				$query->select('DISTINCT (x.item_id)')
					->from($db->qn('#__reditem_item_category_xref', 'x'))
					->where($db->qn('x.category_id') . ' IN (' . implode(',', $categories) . ')');
			}
			else
			{
				$table = $db->qn('#__reditem_item_category_xref', 'x' . $index);

				$query->innerjoin($table . ' ON ' . $db->qn('x.item_id') . ' = ' . $db->qn('x' . $index . '.item_id'))
					->where($db->qn('x' . $index . '.category_id') . ' IN (' . implode(',', $categories) . ')');
			}

			$index++;
		}

		$db->setQuery($query);
		$itemIds = $db->loadColumn();

		if (empty($itemIds))
		{
			return array(-1);
		}

		return $itemIds;
	}
}
