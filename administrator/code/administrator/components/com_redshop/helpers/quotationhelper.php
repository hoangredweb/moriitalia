<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');

class quotationHelper extends quotationHelperDefault
{
	public function getQuotationSale()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('uf.firstname, uf.lastname, q.sale_id')
			->from($db->qn('#__redshop_quotation', 'q'))
			->leftJoin($db->qn('#__redshop_users_info', 'uf') . ' ON q.sale_id = uf.user_id')
			->where('(uf.address_type = ' . $db->q('BT') . ' OR q.user_id = 0)')
			->group($db->qn('q.sale_id'));
		$data = $db->setQuery($query)->loadObjectList();
		$list = array();
		$list[] = JHTML::_('select.option', 0, JText::_('COM_REDSHOP_SELECT'));

		foreach ($data as $key => $value)
		{
			$list[] = JHTML::_('select.option', $value->sale_id, trim($value->lastname . ' ' . $value->firstname));
		}

		return $list;
	}
}