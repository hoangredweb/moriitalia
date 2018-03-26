<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Category detail model
 *
 * @package     RedITEM.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class ReditemModelCategoryDetail extends RModel
{
	/**
	 * Get data of item
	 *
	 * @return  boolean/array
	 */
	public function getData()
	{
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$id     = $input->getInt('id', 0);
		$params = $app->getParams();
		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem_categories');

		if (!$id)
		{
			return false;
		}

		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		$category = $categoryModel->getItem($id);

		if (!$category || empty($category))
		{
			return false;
		}

		// Assigned template
		$templatemodel = RModel::getAdminInstance('Template', array('ignore_request' => true));

		// If menu set a template, get this template Id
		$templateId = $input->getInt('templateId', 0);

		if (!$templateId)
		{
			$templateId = $category->template_id;
		}

		$category->template = $templatemodel->getItem($templateId);

		// Getting subcategories
		$subCatOrdering    = 'c.' . $params->get('subcat_ordering', 'lft');
		$subCatDestination = $params->get('subcat_destination', 'asc');

		$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true));

		$limitstart    = $input->getInt($categoriesModel->getPagination()->prefix . 'limitstart', 0);
		$limit         = 0;
		$paginationTag = '';

		// Get pagination limit
		if (preg_match('/{sub_category_pagination[^}]*}/i', $category->template->content, $matches) > 0)
		{
			// Only use first pagination tag
			$match = $matches[0];
			$tmp   = explode('|', $match);

			if (isset($tmp[1]))
			{
				// Have limit number
				$limit = (int) $tmp[1];
			}

			$paginationTag = $match;
		}

		$app->setUserState('com_reditem.sub_category_pagination.limit', $limit);

		$categoriesModel->setState('filter.parentid', $category->id);

		// Add filter by Category's title
		$filterSubCatTitle = $input->getRaw('filter_subcat_title', null);

		if ($filterSubCatTitle)
		{
			$categoriesModel->setState('filter.plgSearchCategory', $filterSubCatTitle);
		}

		// Add filter_subcatitemsavaiable
		$filterSubCatItemsAvaiable = $input->getString('filter_subcatitemsavaiable', '');

		if ($filterSubCatItemsAvaiable == '0')
		{
			$categoriesModel->setState('filter.published', '');
		}
		else
		{
			$categoriesModel->setState('filter.published', 1);
		}

		$categoriesModel->setState('list.ordering', $subCatOrdering);
		$categoriesModel->setState('list.direction', $subCatDestination);
		$categoriesModel->setState('list.limit', $limit);
		$categoriesModel->setState('list.start', $limitstart);

		$filterCategoriesIds = array();

		// Get Filter from filter Sub-Categories Items Avaiable
		if ($filterSubCatItemsAvaiable == '1')
		{
			// Only show sub-categories which have at least 1 items "published" inside
			$tmpCategoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true));
			$tmpCategoriesModel->setState('list.select', 'DISTINCT (c.id)');
			$tmpCategoriesModel->setState('filter.lft', $category->lft + 1);
			$tmpCategoriesModel->setState('filter.rgt', $category->rgt - 1);
			$tmpCategoriesModel->setState('filter.published', 1);
			$tmpAllSubCategories      = $tmpCategoriesModel->getItems();
			$tmpAllSubCategoriesArray = array();

			if ($tmpAllSubCategories)
			{
				// Convert to array
				foreach ($tmpAllSubCategories as $key => $value)
				{
					$tmpAllSubCategoriesArray[] = $value->id;
				}

				$tmpItemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
				$tmpItemsModel->setState('list.select', 'DISTINCT (x.category_id)');
				$tmpItemsModel->setState('filter.catid', $tmpAllSubCategoriesArray);
				$tmpItemsModel->setState('filter.published', 1);

				$tmpAvaiableCategories = $tmpItemsModel->getItems();

				if ($tmpAvaiableCategories)
				{
					$tmpAvaiableCategoriesArray = array();

					// Convert to array
					foreach ($tmpAvaiableCategories as $key => $value)
					{
						$tmpAvaiableCategoriesArray[] = $value->category_id;
					}

					if (empty($filterCategoriesIds))
					{
						$filterCategoriesIds = array_merge($filterCategoriesIds, $tmpAvaiableCategoriesArray);
					}
					else
					{
						$filterCategoriesIds = array_intersect($filterCategoriesIds, $tmpAvaiableCategoriesArray);
					}

					// Make sure return empty result if condition has been setted
					if (empty($filterCategoriesIds))
					{
						$filterCategoriesIds = array(-1);
					}
				}
			}
		}

		if (!empty($filterCategoriesIds))
		{
			$categoriesModel->setState('filter.ids', $filterCategoriesIds);
		}

		$category->sub_categories = $categoriesModel->getItems();

		// Process check view permission for sub-categories list.
		ReditemHelperACL::processCategoryACL($category->sub_categories);

		// Replace pagination data for {sub_category_pagination} tag
		$subCategoriesPagination        = $categoriesModel->getPagination();
		$subCategoriesPaginationData    = array(
			'prefix'       => $subCategoriesPagination->prefix,
			'limit'        => $subCategoriesPagination->limit,
			'limitstart'   => $subCategoriesPagination->limitstart,
			'total'        => $subCategoriesPagination->total,
			'limitfield'   => $subCategoriesPagination->getLimitBox(),
			'pagescounter' => $subCategoriesPagination->getPagesCounter(),
			'pages'        => $subCategoriesPagination->getPaginationPages(),
			'formName'     => $subCategoriesPagination->get('formName')
		);
		$subCategoriesPaginationOptions = array(
			'showLimitBox'   => false,
			'showPagesLinks' => true,
			'showLimitStart' => true
		);

		$layoutData                  = array('list' => $subCategoriesPaginationData, 'options' => $subCategoriesPaginationOptions);
		$subCatPaginationHTML        = RLayoutHelper::render('pagination.categories.links', $layoutData, null, array('component' => 'com_reditem'));
		$category->template->content = str_replace($paginationTag, $subCatPaginationHTML, $category->template->content);

		$category->sub_categories_pagination = $subCatPaginationHTML;

		$categoriesModel->setState('list.ordering', 'c.lft');
		$categoriesModel->setState('list.direction', 'asc');
		$categoriesModel->setState('list.limit', 0);
		$categoriesModel->setState('list.start', 0);
		$categoriesModel->setState('filter.featured', 1);
		$category->sub_categories_featured = $categoriesModel->getItems();

		// Process check view permission for sub-categories featured list.
		ReditemHelperACL::processCategoryACL($category->sub_categories_featured);

		// End of subcategories getting

		// Items order default value
		$itemOrderName = 'title';
		$itemOrderDest = 'asc';
		$itemOrderAvailable = array('title', 'ordering', 'created_time', 'publish_up', 'publish_down');

		// Get from input
		$itemOrderToolInput = $input->getString('items_sort', null);
		$itemDestToolInput = $input->getString('items_dest', null);

		// If input has value for items Order, set it.
		if (!empty($itemOrderToolInput) && in_array($itemOrderName, $itemOrderAvailable))
		{
			$itemOrderName = $itemOrderToolInput;
		}

		// If input has value for items Order Destination, set it.
		if (!empty($itemDestToolInput) && ($itemDestToolInput == 'desc'))
		{
			$itemOrderDest = $itemDestToolInput;
		}

		// If no value of input, try to find items_sort tag in template
		if (empty($itemOrderToolInput)
			&& empty($itemDestToolInput)
			&& (preg_match('/{items_sort\|[^}]*}/i', $category->template->content, $matches) > 0))
		{
			// Only use first items order tag
			$match    = $matches[0];
			$tmpMatch = str_replace('{', '', $match);
			$tmpMatch = str_replace('}', '', $tmpMatch);
			$tmpMatch = explode('|', $tmpMatch);

			// Get order column name
			if (isset($tmpMatch[1]) && in_array($tmpMatch[1], $itemOrderAvailable))
			{
				// {items_sort|fieldname|destination}
				$itemOrderName = $tmpMatch[1];
			}
			else
			{
				// {items_sort|fieldname|destination|table}. In case it's not in available list then we assume it's custom field ordering
				// Fieldname
				$customFieldTableFieldName = $tmpMatch[1];

				// Ordering
				// Because keep old tag structure. This attribute MUST BE exist
				$customFieldTableFieldOrder = $tmpMatch[2];

				// Table
				$customFieldTable = $tmpMatch[3];
			}

			// Get order column destination
			if (isset($tmpMatch[2]) && ($tmpMatch[2] == 'desc'))
			{
				$itemOrderDest = 'desc';
			}

			$category->template->content = str_replace($match, '', $category->template->content);
		}

		// Items
		$tagReplace = false;
		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsOrdering = $params->get('items_ordering', $itemOrderName);

		if ($itemsOrdering != 'random')
		{
			$itemsOrdering = 'i.' . $itemsOrdering;
		}

		$itemsDestination = $params->get('items_destination', $itemOrderDest);
		$limitstart = $input->getInt($itemsModel->getPagination()->prefix . 'limitstart', 0);
		$limit = 0;
		$app->setUserState('com_reditem.items_pagination.limit', $limit);

		// If we have custom field ordering than we create another session for this
		if (isset($customFieldTable))
		{
			$itemsModel->setState('filter.customfield.table', $customFieldTable);
			$itemsModel->setState('filter.customfield.field', $customFieldTableFieldName);
			$itemsModel->setState('filter.customfield.order', $customFieldTableFieldOrder);
		}
		else
		{
			// Reset this session
			$itemsModel->setState('filter.customfield.table', null);
			$itemsModel->setState('filter.customfield.field', null);
			$itemsModel->setState('filter.customfield.order', null);
		}

		$itemsModel->setState('filter.catid', $category->id);
		$itemsModel->setState('filter.published', 1);
		$itemsModel->setState('list.ordering', $itemsOrdering);
		$itemsModel->setState('list.direction', $itemsDestination);
		$itemsModel->setState('list.limit', $limit);
		$itemsModel->setState('list.start', $limitstart);

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
		$searchInFrontend = $input->getRaw('filter_searchinfrontend', '');

		if ($searchInFrontend != '')
		{
			$itemsModel->setState('filter.search', $searchInFrontend);
		}

		/*
		 * Add filter by item's title with alphabet
		 */
		$filterAlphabet = trim($input->getString('filter_alphabet', null));

		if ($filterAlphabet)
		{
			if (strlen($filterAlphabet) > 1)
			{
				$filterAlphabet = JString::substr($filterAlphabet, 0, 1);
			}

			$itemsModel->setState('filter.search_alphabet', $filterAlphabet);
		}

		/*
		 * Add filter by custom field value
		 */
		$filterFieldValues = $input->get('filter_customfield', array(), 'array');

		if (!empty($filterFieldValues))
		{
			$filterFieldData = array();
			$fieldModel      = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

			// Remove unused filter custom value
			foreach ($filterFieldValues as $fieldId => $value)
			{
				if (empty($value))
				{
					unset($filterFieldValues[$fieldId]);
					continue;
				}

				if (is_array($value))
				{
					$tmpValues = array();

					foreach ($value as $subValue)
					{
						$tmpValues[] = base64_decode($subValue);
					}

					$filterFieldData[$fieldId]['value'] = $tmpValues;
				}
				else
				{
					$filterFieldData[$fieldId]['value'] = base64_decode($value);
				}

				$field = $fieldModel->getItem($fieldId);
				$filterFieldData[$fieldId]['table'] = ReditemHelperType::getTableName($field->type_id);
			}

			$filterFieldData = new JRegistry($filterFieldData);
			$itemsModel->setState('filter.cfSearch', $filterFieldData->toString());
		}

		/*
		 * Add filter by custom field with ranges value
		 */
		$filterFieldsRange = $input->get('filter_ranges', array(), 'array');

		if ($filterFieldsRange)
		{
			foreach ($filterFieldsRange as $field => $value)
			{
				if (empty($value))
				{
					unset($filterFieldsRange[$field]);
				}
			}

			$itemsModel->setState('filter.cfSearchRanges', json_encode($filterFieldsRange));
		}

		// Add group by in query for make sure not duplicate item
		$itemsModel->setState('list.groupBy', 'i.id');

		if (strrpos($category->template->content, '{items_loop_start}') !== false)
		{
			$tagReplace = true;
			$paginationTag = '';

			// Get pagination limit
			if (preg_match('/{items_pagination[^}]*}/i', $category->template->content, $matches) > 0)
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
				$app->setUserState('com_reditem.items_pagination.limit', $limit);
				$itemsModel->setState('list.start', $input->getInt('com_reditem_categorydetail_items_limitstart', 0));
				$itemsModel->setState('list.limit', $limit);
			}

			// Check if tag {include_sub_category_items} has exists
			if (strrpos($category->template->content, '{include_sub_category_items}') !== false)
			{
				// If current category has sub categories
				$subCategories = ReditemHelper::getSubCategories($id);

				// Add parent category into array
				array_unshift($subCategories, $category->id);

				$itemsModel->setState('filter.catid', $subCategories);
				$itemsModel->setState('filter.item_ids', $this->getItemFilter());

				$category->template->content = str_replace('{include_sub_category_items}', '', $category->template->content);
			}

			// Groupped items tag
			$groupItems = false;

			if (preg_match('/{group_items[^}]*}/i', $category->template->content, $matches) > 0)
			{
				$groupItems = true;
				$itemsModel->setState('list.limit', 0);
			}
		}

		// Get items list
		$db    = $this->getDbo();
		$items = $itemsModel->getItems();
		$category->items = array();
		$category->all_items = $db->setQuery($itemsModel->getListQuery())->loadObjectList();
		ReditemHelperACL::processItemACL($category->all_items);
		$category->items_count = count($category->all_items);

		if (!empty($category->all_items))
		{
			$itemIds     = ReditemHelperItem::getItemIds($category->all_items);
			$pageItemIds = ReditemHelperItem::getItemIds($items);
			$iCategories = ReditemHelperItem::getCategories($itemIds, false);
			$cfValues    = ReditemHelperItem::getCustomFieldValues($itemIds);

			foreach ($category->all_items as $item)
			{
				if (isset($cfValues[$item->type_id][$item->id]))
				{
					$item->customfield_values = $cfValues[$item->type_id][$item->id];
				}

				$item->categories = $iCategories[$item->id];
				$item->type = ReditemHelperType::getType($item->type_id);
				$typeParams = new JRegistry($item->type->params);

				// If "Item Gmap Field" option has enabled in Type Configuration
				if ($typeParams->get('item_gmap_field', 0))
				{
					$itemParams = new JRegistry($item->params);
					$item->itemLatLng = $itemParams->get('itemLatLng', '');
				}

				if (in_array($item->id, $pageItemIds))
				{
					$category->items[] = $item;
				}
			}
		}

		if ($tagReplace)
		{
			// Replace pagination data for {items_pagination} tag
			$itemsPaginationHTML = '';

			if (!$groupItems)
			{
				$itemsPaginationData = $itemsModel->getPagination();
				$list = array(
					'prefix'       => $itemsPaginationData->prefix,
					'limit'        => $itemsPaginationData->limit,
					'limitstart'   => $itemsPaginationData->limitstart,
					'total'        => $itemsPaginationData->total,
					'limitfield'   => $itemsPaginationData->getLimitBox(),
					'pagescounter' => $itemsPaginationData->getPagesCounter(),
					'pages'        => $itemsPaginationData->getPaginationPages(),
					'formName'     => $itemsPaginationData->get('formName')
				);
				$options = array(
					'showLimitBox' => false,
					'showPagesLinks' => true,
					'showLimitStart' => true
				);

				$layoutData = array('list' => $list, 'options' => $options);

				$itemsPaginationHTML = RLayoutHelper::render('pagination.items.links', $layoutData, null, array('component' => 'com_reditem'));
			}

			$category->template->content = str_replace($paginationTag, $itemsPaginationHTML, $category->template->content);
			$category->pagination = $itemsPaginationHTML;
		}

		// Related Categories
		if ((!empty($category->related_categories)) && (is_array($category->related_categories)))
		{
			$relatedCategories = array();

			foreach ($category->related_categories as $relatedCategoryID)
			{
				$relatedCategories[] = $categoryModel->getItem($relatedCategoryID);
			}

			$category->relatedCategories = $relatedCategories;

			// Process check view permission for sub-categories featured list.
			ReditemHelperACL::processCategoryACL($category->relatedCategories);
		}

		// Check jQuery Mansory tags
		if (preg_match('/{items_masonry[^}]*}/i', $category->template->content, $matches) > 0)
		{
			$doc = JFactory::getDocument();
			$masonryWidth = 200;
			RHelperAsset::load('masonry.pkgd.min.js', 'com_reditem');

			// Get the width config if exist
			$match = $matches[0];
			$tmp = explode('|', $match);

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
			$category->template->content = str_replace($match, '', $category->template->content);
		}

		// Run event 'onReplaceCategoryTag'
		$dispatcher->trigger('onAfterPrepareDataCategoryDetail', array($category, compact(array_keys(get_defined_vars()))));

		// Set category fields
		$category->fields = $categoryModel->getCustomFields($category->id, true);

		return $category;
	}

	/**
	 * Get items data from filter
	 *
	 * @return  array
	 */
	public function getItemFilter()
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

	/**
	 * Get filters data based on filtered items.
	 *
	 * @param   array  $items  Array of item objects.
	 *
	 * @return  array
	 */
	public function getFilterValues($items)
	{
		$app     = JFactory::getApplication();
		$input   = $app->input;
		$filters = array_keys($input->get('filter_customfield', array(), 'array'));
		$values  = array();

		if (!empty($filters))
		{
			$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

			foreach ($filters as $fieldId)
			{
				$field     = $fieldModel->getItem($fieldId);
				$fieldCode = $field->fieldcode;
				$tmp       = array();
				$cls       = new stdClass;

				foreach ($items as $item)
				{
					if ($vals = ReditemHelperCustomfield::isJsonValue($item->customfield_values[$fieldCode]))
					{
						foreach ($vals as $val)
						{
							$tmp[] = base64_encode($val);
						}
					}
					else
					{
						$tmp[] = base64_encode($item->customfield_values[$fieldCode]);
					}
				}

				$cls->id   = $fieldId;
				$cls->vals = array_values(array_unique($tmp));
				$values[]  = $cls;
			}
		}

		return $values;
	}
}
