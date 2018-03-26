<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class product_category extends product_categoryDefault
{
	public function getParentCategories()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('c.category_id'))
			->select($db->qn('c.category_name'))
			->from($db->qn('#__redshop_category', 'c'))
			->leftjoin($db->qn('#__redshop_category_xref', 'x') . ' ON ' . $db->qn('c.category_id') . ' = ' . $db->qn('x.category_child_id'))
			->where($db->qn('x.category_parent_id') . ' = 0')
			->where($db->qn('c.published') . ' = 1')
			->order($db->qn('c.ordering'))
			->group($db->qn('c.category_id'));

		return $db->setQuery($query)->loadObjectList();
	}
}