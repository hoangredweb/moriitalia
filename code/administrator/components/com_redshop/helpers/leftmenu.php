<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class leftmenu extends leftmenuDefault
{
	/**
	 * Set Order Group menu
	 *
	 * @return  void
	 */
	protected static function setOrderGroup()
	{
		self::$menu->section('order')
			->title('COM_REDSHOP_ORDER')
			->addItem(
				'index.php?option=com_redshop&view=order',
				'COM_REDSHOP_ORDER_LISTING',
				(self::$view == 'order' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=order&layout=labellisting',
				'COM_REDSHOP_DOWNLOAD_LABEL',
				(self::$view == 'order' && self::$layout == 'labellisting') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=orderstatus',
				'COM_REDSHOP_ORDERSTATUS_LISTING',
				(self::$view == 'orderstatus') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=opsearch',
				'COM_REDSHOP_PRODUCT_ORDER_SEARCH',
				(self::$view == 'opsearch') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=quotation',
				'COM_REDSHOP_QUOTATION_LISTING',
				(self::$view == 'quotation') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=quotation_statistic',
				'COM_REDSHOP_QUOTATION_STATISTIC',
				(self::$view == 'quotation_statistic') ? true : false
			);

		self::$menu->group('ORDER');
	}
}