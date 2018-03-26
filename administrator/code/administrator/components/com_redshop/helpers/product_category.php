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
		$query = 'SELECT DISTINCT c.category_name, c.category_id'
			. ' FROM ' . $this->_table_prefix . 'category c '
			. ' LEFT JOIN ' . $this->_table_prefix . 'category_xref AS x ON c.category_id = x.category_child_id '
			. 'WHERE x.category_parent_id=0 '
			. 'ORDER BY ordering';
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}