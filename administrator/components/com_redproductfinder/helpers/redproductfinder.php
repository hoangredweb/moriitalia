<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER form controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_REDPRODUCTFINDER'),
			'index.php?option=com_redproductfinder&view=redproductfinder',
			$vName == 'redproductfinder'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_FORMS'),
			'index.php?option=com_redproductfinder&view=forms',
			$vName == 'forms'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_TYPES'),
			'index.php?option=com_redproductfinder&view=types',
			$vName == 'types'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_TAGS'),
			'index.php?option=com_redproductfinder&view=tags',
			$vName == 'tags'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_ASSOCIATIONS'),
			'index.php?option=com_redproductfinder&view=associations',
			$vName == 'associations'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_KEYWORD'),
			'index.php?option=com_redproductfinder&view=search',
			$vName == 'search'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_REDPRODUCTFINDER_HELPERS_SUBMENU_FILTERS'),
			'index.php?option=com_redproductfinder&view=filters',
			$vName == 'filters'
		);
	}
}
