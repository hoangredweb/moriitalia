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
class RedproductfinderModelRedproductfinder extends RModelList
{
	/**
	 * Get total information of redproductfinder
	 *
	 * @return void
	 */
	public function getTotals()
	{
		$db = JFactory::getDBO();
		$totals = array();
		/* Type totals */
		$query = $db->getQuery(true)
			->select('COUNT(id) AS total')
			->from($db->qn('#__redproductfinder_types'));
		$db->setQuery($query);
		$totals['Types']['total'] = $db->loadResult();

		$query = $db->getQuery(true)
			->select('COUNT(id) AS total')
			->from($db->qn('#__redproductfinder_tags'));
		$db->setQuery($query);
		$totals['Tags']['total'] = $db->loadResult();

		$query = $db->getQuery(true)
			->select('COUNT(id) AS total')
			->from($db->qn('#__redproductfinder_associations'));
		$db->setQuery($query);
		$totals['Associations']['total'] = $db->loadResult();

		return $totals;
	}
}
