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
class RedproductfinderModelFilters extends RModelList
{
	protected $filter_fields = array('id', 'f.id',
									'tag_id', 'f.tag_id',
									'published', 'f.published',
									'filter_name', 'f.filter_name',
									);

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
	protected function populateState($ordering = 'f.filter_name', $direction = 'asc')
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * This method will get filter item on each id
	 *
	 * @param   int  $id  id filter item
	 *
	 * @return Array
	 */
	public function getFilter($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("f.*")
		->from("#__redproductfinder_filters f")
		->where($db->qn("id") . " = " . $db->q($id));

		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 * Show all tag
	 *
	 * @return Objects
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/* Get all the fields based on the limits */
		$query->select("id, type_name")
		->from("#__redproductfinder_types")
		->order($db->qn("ordering"));

		$db->setQuery($query);

		return $db->loadObjectlist();
	}

	/**
	 * Show tag name
	 *
	 * @param   string  $tagIds  list string id of tags
	 *
	 * @return string
	 */
	public function getTagname($tagIds = '')
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (!empty($tagIds))
		{
			$query->select("tag_name")
			->from("#__redproductfinder_tags");

			$tagsArray = explode(",", $tagIds);
			$tags = array();

			foreach ($tagsArray as $k => $value)
			{
				$arr = explode(".", $value);
				$tags[] = $arr[1];
			}

			$tagIds = implode(",", $tags);

			// Add where query
			$query->where("id IN (" . $tagIds . ")");

			$db->setQuery($query);

			$result = $db->loadAssocList();
		}
		else
		{
			$result = array();
		}

		$return = array();

		$str	= "";

		if (count($result) > 0)
		{
			foreach ($result as $k => $r)
			{
				$return[] = $r["tag_name"];
			}

			$str = implode(",", $return);
		}

		return $str;
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
		 * @todo Get filter by state - we will continue on the next version
		*/
		$state = "1";

		$query->select("f.*")
			->from($db->qn("#__redproductfinder_filters", "f"));

		// Filter by published state
		$published = $this->getState('filter.published');

		// Filter by search in formname
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape(trim($search, true) . '%'));
			$query->where('(f.filter_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'f.filter_name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * count Tags.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function countTags()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('count(*)');
		$query->from($db->qn('#__redproductfinder_tags'));
		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}
}
