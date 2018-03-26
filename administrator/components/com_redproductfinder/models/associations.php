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
 * RedPRODUCTFINDER Associations Model
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderModelAssociations extends RModelList
{
	protected $filter_fields = array('id', 'a.id',
									'product_id', 'a.product_id',
									'published', 'a.published');
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
	protected function populateState($ordering = 'a.id', $direction = 'asc')
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Retrieve an association to edit
	 *
	 * @return void
	 */
	function getAssociation()
	{
		$row = $this->getTable();
		$my = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('cid', 0);

		/* load the row from the db table */
		$row->load($id[0]);

		if ($id[0])
		{
			// Do stuff for existing recordsrdering
			$result = $row->checkout($my->id);
		}
		else
		{
			// Do stuff for new records
			$row->published = 1;
		}

		return $row;
	}

	/**
	 * Get the list of selected category
	 *
	 * @param   int  $id  Id should be int variable
	 *
	 * @return object
	 */

	public function getProductByCategory($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("p.product_id") . ",  CONCAT(p.product_number, '::', p.product_name) AS full_product_name")
		->from($db->qn("#__redshop_product", "p"))
		->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON pc.product_id = p.product_id")
		->where($db->qn("pc.category_id") . " = " . $db->q((int) $id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get category
	 *
	 * @param   int  $id  product id
	 *
	 * @return object
	 */

	public function getCategoryById($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("c.category_id"))
		->from($db->qn("#__redshop_category", "c"))
		->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON pc.category_id = c.category_id")
		->where($db->qn("pc.product_id") . " = " . $db->q((int) $id));

		$db->setQuery($query);
		$row = $db->loadAssoc();

		return $row['category_id'];
	}

	/**
	 * Get category
	 *
	 * @param   int  $id  association id
	 *
	 * @return object
	 */

	public function getProductByAssociation($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("a.product_id"))
		->from($db->qn("#__redproductfinder_associations", "a"))
		->where($db->qn("a.id") . " = " . $db->q((int) $id));

		$db->setQuery($query);
		$row = $db->loadAssoc();

		return $row['product_id'];
	}

	/**
	 * Retrieve a list of categories from Redshop
	 *
	 * @return void
	 */
	public function getCategories()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("category_id") . "," . $db->qn("category_name"))
		->from($db->qn("#__redshop_category"))
		->order($db->qn("category_name"));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Retrieve a list of products from Redshop
	 *
	 * @return void
	 */
	public function getProducts()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("product_id") . ", CONCAT(product_number, '::', product_name) AS full_product_name")
		->from($db->qn("#__redshop_product"))
		->order($db->qn("product_name"));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get the list of selected category
	 *
	 * @param   int  $id  Id should be int variable
	 *
	 * @return object
	 */

	public function getAssociationCategory($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("c.category_id"))
		->from($db->qn("#__redproductfinder_associations", "a"))
		->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON pc.product_id = a.product_id")
		->join("LEFT", $db->qn("#__redshop_category", "c") . " ON c.category_id = pc.category_id")
		->where($db->qn("a.id") . " = " . $db->q((int) $id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get the list of selected product
	 *
	 * @param   int  $id  Id should be int variable
	 *
	 * @return object
	 */

	public function getAssociationProduct($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("p.product_id"))
		->from($db->qn("#__redproductfinder_associations", "a"))
		->join("LEFT", $db->qn("#__redshop_product_category_xref", "pc") . " ON pc.product_id = a.product_id")
		->join("LEFT", $db->qn("#__redshop_product", "p") . " ON p.product_id = pc.product_id")
		->where($db->qn("a.id") . " = " . $db->q((int) $id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get the list of selected types for this tag
	 *
	 * @param   int  $id  Id should be int variable
	 *
	 * @return object
	 */

	public function getAssociationTags($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("tag_id") . "," . $db->qn("type_id") . ", CONCAT(tag_id, '.', type_id ) as tag_type")
		->from($db->qn("#__redproductfinder_association_tag"))
		->where($db->qn("association_id") . " = " . $db->q((int) $id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get a multi-select list with types and tags
	 *
	 * @return array
	 */
	public function getTypeTagList()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/* 1. Get all types */
		$query->select("id, type_name")
		->from($db->qn("#__redproductfinder_types"))
		->where("published = 1")
		->order($db->qn("ordering"));

		$db->setQuery($query);

		$types = $db->loadAssocList('id');

		/* 2. Go through each type and get the tags */
		foreach ($types as $id => $type)
		{
			$query = $db->getQuery(true);
			$query->select("t.id, tag_name")
				->from($db->qn("#__redproductfinder_tag_type", "j"))
				->join("LEFT", $db->qn("#__redproductfinder_tags", "t") . " ON j.tag_id = t.id")
				->where("j.type_id = " . $db->q((int) $id))
				->where("t.published = 1")
				->order($db->qn("t.ordering"));

			$db->setQuery($query);

			$types[$id]['tags'] = $db->loadAssocList('id');
		}

		return $types;
	}

	/**
	 * Get the list of selected type names for this tag
	 *
	 * @return array
	 */
	public function getAssociationTagNames()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('cid', 0);

		$query->select("association_id, CONCAT(y.type_name, ':', g.tag_name) AS tag_name")
				->from($db->qn("#__redproductfinder_association_tag", "a"))
				->join("LEFT", $db->qn("#__redproductfinder_tags", "g") . " ON a.tag_id = g.id")
				->join("LEFT", $db->qn("#__redproductfinder_types", "y") . " ON a.type_id = y.id");

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$sortlist = array();

		if (count($list) > 0)
		{
			foreach ($list as $key => $tag)
			{
				$sortlist[$tag->association_id][] = $tag->tag_name;
			}
		}

		return $sortlist;
	}

	/**
	 * save dependent tags
	 *
	 * @return void
	 */
	public function savedependent()
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$args[] = "product_id='" . (int) $input->getInt('product_id', 0) . "'";
		$args[] = "tag_id='" . (int) $input->getInt('tag_id', 0) . "'";
		$args[] = "type_id='" . (int) $input->getInt('type_id', 0) . "'";

		$where = implode(" AND ", $args);

		$query->select("count(dependent_tags)")
			->from($db->qn("#__redproductfinder_dependent_tag"))
			->where($where);

		$db->setQuery($query);

		$dependent_tags = $db->loadResult();

		if (!$dependent_tags)
		{
			$args[] = "dependent_tags='" . $input->getInt('dependent_tags', 0) . "'";
			$set = implode(" , ", $args);
			$ins_query = "INSERT INTO #__redproductfinder_dependent_tag SET " . $set;
		}
		else
		{
			$set = "dependent_tags='" . $input->getInt('dependent_tags', 0) . "'";
			$ins_query = "UPDATE #__redproductfinder_dependent_tag SET " . $set . " WHERE " . $where;
		}

		$db->setQuery($ins_query);

		if ($db->query())
			return JText::_('Depedent Tag added Successfully !');
		else
			return JText::_('Error occur while adding Depedent Tag !');
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

		// Get filter state - do it later
		$state = "1";

		$query->select("a.*, p.product_name")
		->from($db->qn("#__redproductfinder_associations", "a"))
		->join("LEFT", $db->qn("#__redshop_product", "p") . " ON a.product_id = p.product_id");

		if ($state == "-2")
		{
			$query->where($db->qn("a.published") . "=" . $db->q("-2"));
		}
		else
		{
			$query->where($db->qn("a.published") . "!=" . $db->q("-2"));
		}

		// Filter by search in formname.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape(trim($search, true) . '%'));
			$query->where('(p.product_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
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
