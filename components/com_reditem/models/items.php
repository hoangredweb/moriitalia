<?php
/**
 * @package     RedITEM.Front
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_reditem/helpers/helper.php';

/**
 * RedITEM Items Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Items
 * @since       2.0.19
 *
 */
class ReditemModelItems extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_items';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'items_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$filterFields = array(
				'title', 'i.title',
				'ordering', 'i.ordering',
				'published', 'i.published',
				'access', 'i.access', 'access_level',
				'template_name',
				'featured', 'i.featured',
				'type_id', 'i.type_id', 'type_name',
				'i.id', 'filter_types',
				'blocked', 'i.blocked'
			);

			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('filter.searchableInBackend', 1);

			$seachInBackendFields = $fieldsModel->getItems();

			if ($seachInBackendFields)
			{
				foreach ($seachInBackendFields as $seachInBackendField)
				{
					$filterFields[] = 'cfv.' . $seachInBackendField->fieldcode;
					$filterFields[] = 'cfv_' . $seachInBackendField->fieldcode;
				}
			}

			$config['filter_fields'] = $filterFields;
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery()
	{
		$db 	= JFactory::getDbo();
		$user	= ReditemHelperSystem::getUser();
		$groups	= $user->getAuthorisedViewLevels();

		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'i.*, ty.title AS type_name, tmpl.name AS template_name, ag.title AS access_level'
			)
		);
		$query->from($db->qn('#__reditem_items', 'i'));
		$query->leftJoin($db->qn('#__reditem_types', 'ty') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('ty.id'));
		$query->leftJoin($db->qn('#__reditem_templates', 'tmpl') . ' ON ' . $db->qn('i.template_id') . ' = ' . $db->qn('tmpl.id'));

		$filterType = $this->getState('filter.filter_types', 0);

		if ($filterType)
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$type = $typeModel->getItem($filterType);

			$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
			$fieldsModel->setState('filter.searchableInBackend', 1);
			$fieldsModel->setState('filter.types', $filterType);

			$seachInBackendFields = $fieldsModel->getItems();

			if ($seachInBackendFields)
			{
				$query->leftJoin($db->qn('#__reditem_types_' . $type->table_name, 'cfv') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('cfv.id'));

				foreach ($seachInBackendFields as $seachInBackendField)
				{
					$query->select($db->qn('cfv.' . $seachInBackendField->fieldcode, 'cfv_' . $seachInBackendField->fieldcode));
				}
			}
		}

		// Join over the asset groups.
		$query->leftJoin($db->qn('#__viewlevels', 'ag') . ' ON ' . $db->qn('ag.id') . ' = ' . $db->qn('i.access'));

		// If this is filter on custom value
		$cfSearch = $this->getState('filter.cfSearch', '');

		if (!empty($cfSearch))
		{
			$jsonSearch = json_decode($cfSearch, true);
			$tables     = array();
			$index      = 0;
			$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

			if (!empty($jsonSearch))
			{
				foreach ($jsonSearch as $fieldId => $filter)
				{
					$tableAlias = 'cf' . $index;
					$value      = $filter['value'];
					$column     = $fieldModel->getItem($fieldId)->fieldcode;

					if (!in_array($filter['table'], $tables))
					{
						$index++;
						$tables[]   = $filter['table'];
						$tableAlias = 'cf' . $index;
						$query->leftJoin($db->qn($filter['table'], $tableAlias) . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn($tableAlias . '.id'));
					}

					if (is_array($value))
					{
						$where = array();

						foreach ($value as $tmpValue)
						{
							$tmpWhere = array();

							// Check if search value has "%" character
							if (strpos($tmpValue, '%') !== false)
							{
								$where[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote($tmpValue);
							}
							else
							{
								$tmpWhere[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote($db->escape($tmpValue, true));
								$tmpWhere[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote('%' . $db->escape(json_encode($tmpValue), true) . '%');
							}

							$where[] = '(' . implode(') OR (', $tmpWhere) . ')';
						}

						$query->where('((' . implode(') OR (', $where) . '))');
					}
					else
					{
						$where = array();

						// Check if search value has "%" character
						if (strpos($value, '%') !== false)
						{
							$where[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote($value);
						}
						else
						{
							$where[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote($db->escape($value, true));
							$where[] = $db->qn($tableAlias . '.' . $column) . ' LIKE ' . $db->quote('%' . json_encode($value) . '%');
						}

						$query->where('((' . implode(') OR (', $where) . '))');
					}
				}
			}
		}

		// If this is filter on custom with ranges value
		$cfTableRanges = $this->getState('filter.cfTableRanges', '');

		if (!empty($cfTableRanges))
		{
			$query->leftJoin($db->qn($cfTableRanges, 'cfr') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('cfr.id'));

			$cfSearchRanges = $this->getState('filter.cfSearchRanges', '');

			if (!empty($cfSearchRanges))
			{
				$jsonSearchRanges = json_decode($cfSearchRanges, true);

				if ($jsonSearchRanges)
				{
					foreach ($jsonSearchRanges as $column => $value)
					{
						$value = explode('-', $value);

						if (is_array($value))
						{
							$query->where($db->qn('cfr.' . $column) . ' BETWEEN ' . (float) $value[0] . ' AND ' . (float) $value[1]);
						}
					}
				}
			}
		}

		// Filter: Category Id
		$catId = $this->getState('filter.catid', 0);

		if ($catId)
		{
			$query->leftJoin($db->qn('#__reditem_item_category_xref', 'x') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('x.item_id'));

			if (is_array($catId))
			{
				JArrayHelper::toInteger($catId);
				$query->where($db->qn('x.category_id') . ' IN (' . implode(',', $catId) . ')');
			}
			else
			{
				$query->where($db->qn('x.category_id') . ' = ' . $db->quote($catId));
			}
		}

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $db->escape($search, true) . '%');

			$where = array();

			// Add search on item's title
			$where[] = $db->qn('i.title') . ' LIKE ' . $like;

			// Add search on item's ID
			$where[] = $db->qn('i.id') . ' LIKE ' . $like;

			// Add search on category's title
			$avaiableItems = ReditemHelperHelper::searchItemsBaseCategoryTitle($like);

			if (!empty($avaiableItems))
			{
				$where[] = $db->qn('i.id') . ' IN (' . implode(',', $avaiableItems) . ')';
			}

			// Add search on custom fields value
			$filterType = $this->getState('filter.filter_types', 0);

			if ($filterType)
			{
				$types = array($filterType);
			}
			elseif ($catId)
			{
				$types = implode(',', ReditemHelperCategory::getItemsTypes($catId));
			}

			if (!empty($types))
			{
				$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
				$fieldsModel->setState('filter.searchableInFrontend', 1);
				$fieldsModel->setState('filter.types', $types);

				$searchInFrontendFields = $fieldsModel->getItems();

				if (!empty($searchInFrontendFields))
				{
					$whereCustomValues = array();
					$joinedTables      = array();

					foreach ($searchInFrontendFields as $searchInFrontendField)
					{
						$tableName = $searchInFrontendField->table_name;

						if (!isset($joinedTables[$tableName]) || $joinedTables[$tableName] != 1)
						{
							$joinedTables[$tableName] = 1;
							$query->leftJoin(
								$db->qn('#__reditem_types_' . $tableName, $tableName) . ' ON ' .
								$db->qn('i.id') . ' = ' . $db->qn($tableName . '.id')
							);
						}

						$whereCustomValues[] = $db->qn($tableName . '.' . $searchInFrontendField->fieldcode) . ' LIKE ' . $like;
					}

					$where[] = implode(') OR (', $whereCustomValues);
				}
			}

			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter: like / plugin Search Item
		$plgSearchItem = $this->getState('filter.plgSearchItem', '');

		if ($plgSearchItem != '')
		{
			$like = $db->quote('%' . $db->escape($plgSearchItem, true) . '%');

			$where = array(
				$db->qn('i.title') . ' LIKE ' . $like
			);

			$query->where('((' . implode(') OR (', $where) . '))');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		// Define null and now dates
		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(ReditemHelperSystem::getDateWithTimezone()->toSql());

		if (is_numeric($published))
		{
			$query->where($db->qn('i.published') . ' = ' . (int) $published);

			if (($published == 1) && (!$user->authorise('core.edit.state', 'com_reditem')) && (!$user->authorise('core.edit', 'com_reditem')))
			{
				$query->where('(' . $db->qn('i.publish_up') . ' = ' . $nullDate . ' OR ' . $db->qn('i.publish_up') . ' <= ' . $nowDate . ')')
					->where('(' . $db->qn('i.publish_down') . ' = ' . $nullDate . ' OR ' . $db->qn('i.publish_down') . ' >= ' . $nowDate . ')');
			}
		}
		elseif (($published === '') || (!isset($published)))
		{
			$query->where('(i.published IN (0, 1))');
		}

		// Filter: featured item
		$featured = $this->getState('filter.featured', 0);

		if ($featured)
		{
			$query->where($db->qn('i.featured') . ' = ' . $db->quote(1));
		}

		// Filter: types
		$filterType = $this->getState('filter.filter_types', 0);

		// Set state of Type
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.tid', $filterType);

		if ($filterType)
		{
			$query->where($db->qn('i.type_id') . ' = ' . $db->quote($filterType));
		}

		// Filter: ID
		$filterItemIds = $this->getState('filter.item_ids', array());

		if (!empty($filterItemIds))
		{
			JArrayHelper::toInteger($filterItemIds);
			$query->where($db->qn('i.id') . ' IN (' . implode(',', $filterItemIds) . ')');
		}

		// Check access level
		$query->where($db->qn('i.access') . ' IN (' . implode(',', $groups) . ')');

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 'i.title');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		// Check if order is custom fields
		if (substr($orderCol, 0, 4) == 'cfv_')
		{
			$displayableFieldcodes = array();

			if ($filterType)
			{
				$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
				$fieldsModel->setState('filter.searchableInBackend', 1);
				$fieldsModel->setState('filter.types', $filterType);

				$seachInBackendFields = $fieldsModel->getItems();

				if ($seachInBackendFields)
				{
					foreach ($seachInBackendFields as $displayableField)
					{
						$displayableFieldcodes[] = 'cfv_' . $displayableField->fieldcode;
					}
				}
			}

			if (!in_array($orderCol, $displayableFieldcodes))
			{
				$orderCol = 'i.title';
			}
		}

		// Does not allow client see "blocked" item
		$query->where($db->qn('i.blocked') . ' = 0');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method from prepare values items
	 *
	 * @param   array  $items  Array items
	 *
	 * @return  array  Array prepare items
	 */
	public function getPrepareItems($items)
	{
		$db = JFactory::getDbo();

		if ($items)
		{
			foreach ($items As &$item)
			{
				// Prepare Categories data for item
				$query = $db->getQuery(true);

				$query->select($db->qn('category_id'));
				$query->from($db->qn('#__reditem_item_category_xref'));
				$query->where($db->qn('item_id') . ' = ' . $db->quote($item->id));
				$db->setQuery($query);
				$categories_id = $db->loadObjectList();

				$categorymodel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

				if (count($categories_id))
				{
					$categories = array();

					foreach ($categories_id as $cid)
					{
						$category = $categorymodel->getItem($cid->category_id);
						$categories[$cid->category_id] = $category;
					}

					$item->categories = $categories;
				}

				// Prepare field data for item
				if (isset($item->type_id))
				{
					$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
					$type = $typeModel->getItem($item->type_id);

					$query = $db->getQuery(true);
					$query->select('cf.*')
						->from($db->qn('#__reditem_types_' . $type->table_name, 'cf'))
						->where($db->qn('cf.id') . '=' . $item->id);
					$db->setQuery($query);

					$customFields = $db->loadObject();

					if ($customFields)
					{
						$customFields = (array) $customFields;

						// Remove the id column of custom fields value
						array_shift($customFields);
						$item->customfield_values = $customFields;
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return	string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.plgSearchItem');
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.featured');
		$id	.= ':' . $this->getState('filter.cfSearch');
		$id	.= ':' . $this->getState('filter.cfTableRanges');
		$id	.= ':' . $this->getState('filter.cfSearchRanges');
		$catIds = $this->getState('filter.catid');

		if (is_array($catIds))
		{
			$id .= ':' . implode(',', $catIds);
		}
		else
		{
			$id .= ':' . $catIds;
		}

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'i.ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$filterPlgSearchItem = $this->getUserStateFromRequest($this->context . '.filter_plgSearchItem', 'filter_plgSearchItem');
		$this->setState('filter.plgSearchItem', $filterPlgSearchItem);

		$filterCatId = $this->getUserStateFromRequest($this->context . '.filter_catid', 'filter_catid');
		$this->setState('filter.catid', $filterCatId);

		$filterTypes = $this->getUserStateFromRequest($this->context . '.filter_types', 'filter_types');
		$this->setState('filter.filter_types', $filterTypes);

		$filterPublished = $this->getUserStateFromRequest($this->context . '.filter_published', 'filter_published');
		$this->setState('filter.published', $filterPublished);

		$filterFeatured = $this->getUserStateFromRequest($this->context . '.filter_featured', 'filter_featured');
		$this->setState('filter.featured', $filterFeatured);

		$filterItemIds = $this->getUserStateFromRequest($this->context . '.filter_item_ids', 'filter_item_ids');
		$this->setState('filter.item_ids', $filterItemIds);

		$filterCfSearch = $this->getUserStateFromRequest($this->context . '.filter_cfSearch', 'filter_cfSearch');
		$this->setState('filter.cfSearch', $filterCfSearch);

		$filterCfTableRanges = $this->getUserStateFromRequest($this->context . '.filter_cfTableRanges', 'filter_cfTableRanges');
		$this->setState('filter.cfTableRanges', $filterCfTableRanges);

		$filterCfSearchRanges = $this->getUserStateFromRequest($this->context . '.filter_cfSearchRanges', 'filter_cfSearchRanges');
		$this->setState('filter.cfSearchRanges', $filterCfSearchRanges);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}
}
