<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class redhelper extends redhelperDefault
{
	// 	Order by list
	public function getOrderByList()
	{
		$list = array(
			JHtml::_('select.option', '', JText::_('COM_REDSHOP_SELECT')),
			JHtml::_('select.option', 'price', JText::_('COM_REDSHOP_PRODUCT_PRICE_ASC')),
			JHtml::_('select.option', 'price_desc', JText::_('COM_REDSHOP_PRODUCT_PRICE_DESC')),
			JHtml::_('select.option', 'id', JText::_('COM_REDSHOP_NEWEST'))
		);

		return $list;
	}
}