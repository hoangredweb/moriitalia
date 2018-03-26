<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * redITEM Model List.
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 * @since       3.0
 */
abstract class ReditemModelList extends RModelList
{
	/**
	 * Counts the number of items.
	 *
	 * @return  integer
	 */
	public function countItems()
	{
		$db = JFactory::getDbo();

		$query = $this->getListQuery();
		$query->select('COUNT(*) AS _count');

		$db->setQuery($query, 0, 1);

		$result = $db->loadAssoc();

		if (!empty($result))
		{
			return (int) $result['_count'];
		}

		return 0;
	}
}
