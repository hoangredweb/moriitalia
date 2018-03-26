<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Association controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderModelTags extends RModelList
{
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	/** @var integer pagination limit starter */
	protected $filterFormName = 'filter_tags';

	/** @var integer pagination limit */
	protected $limitField = 'tags_limit';

	protected $filter_fields = array('id', 't.id',
									'tt.type_id',
									'tag_name', 't.tag_name',
									'ordering', 't.ordering',
									'published', 't.published');

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = "t.ordering", $direction = "asc")
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Show all tags that have been created
	 *
	 * @return Objects
	 */
	function getTags()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$input = JFactory::getApplication()->input;
		$filtertype = $input->getInt('filtertype', 0);

		/* Get all the fields based on the limits */
		$query->select("t.*");
		$query->from($db->qn("#__redproductfinder_tags", "t"));
		$query->join("LEFT", $db->qn("#__redproductfinder_tag_type", "y") . " ON t.id = y.tag_id");
		$query->where("y.type_id = " . (int) $filtertype);
		$query->group("t.id");
		$query->order("t.ordering");

		$db->setQuery($query, $this->_limitstart, $this->_limit);

		return $db->loadObjectList();
	}

	/**
	 * Retrieve a tag to edit
	 *
	 * @return object
	 */
	function getTag()
	{
		$row = $this->getTable();
		$my = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('cid', 0);

		/* load the row from the db table */
		$row->load($id[0]);

		if ($id[0])
		{
			// Do stuff for existing records
			$result = $row->checkout($my->id);
		}
		else
		{
			// Do stuff for new records
			$row->published    = 1;
		}

		return $row;
	}

	/**
	 * Get the list of selected types for this tag
	 *
	 * @param   int  $id  id of tag type element
	 *
	 * @return array
	 */
	public function getTagTypes($id)
	{
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->select($db->qn("type_id"))
		->from($db->qn("#__redproductfinder_tag_type"))
		->where($db->qn("tag_id") . " = " . $db->q((int) $id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get the list of selected type names for this tag
	 *
	 * @return array
	 */
	public function getTagTypeNames()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('cid', 0);

		$query->select("tag_id, type_name")
			->from($db->qn("#__redproductfinder_tag_type", "j"))
			->from($db->qn("#__redproductfinder_types", "t"))
			->where("j.type_id = t.id");

		$db->setQuery($query);

		$list = $db->loadObjectList();

		$sortlist = array();

		foreach ($list as $key => $type)
		{
			$sortlist[$type->tag_id][] = $type->type_name;
		}

		return $sortlist;
	}

	/**
	 * Show all types
	 *
	 * @return object
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/* Get all the fields based on the limits */
		$query->select("id, type_name")
			->from("#__redproductfinder_types")
			->order("type_name");

		$db->setQuery($query, $this->_limitstart, $this->_limit);

		return $db->loadObjectList();
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		/*
		 * @todo: we will continue make filter by state on next version
		 */
		$state = "1";

		$query->select("t.*")
			->from($db->qn("#__redproductfinder_tags", "t"))
			->join("LEFT", $db->qn("#__redproductfinder_tag_type", "tt") . " ON tt.tag_id = t.id")
			->group($db->qn('t.id'));

		// Filter by Types
		$typeId = $this->getState('filter.types');

		if (is_numeric($typeId))
		{
			$query->where('tt.type_id = ' . (int) $typeId);
		}

		// Filter by search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape(trim($search, true) . '%'));
			$query->where('(t.tag_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 't.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * count Types.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function countTypes()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('count(*)');
		$query->from($db->qn('#__redproductfinder_types'));
		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}
}
