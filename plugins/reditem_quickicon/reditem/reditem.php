<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Plugins for provide icons for redITEM on left sidebar
 *
 * @package  RedITEM.Plugin
 *
 * @since    2.1.14
 */
class PlgReditem_QuickiconReditem extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Method for run when render sidebar
	 *
	 * @return  array   List of icons
	 */
	public function getSidebarIcons()
	{
		// Configuration link
		$uri      = JUri::getInstance();
		$return   = base64_encode('index.php' . $uri->toString(array('query')));
		$confLink = 'index.php?option=com_redcore&view=config&layout=edit&component=com_reditem&return=' . $return;

		$icons = array(
			array (
				'icon'  => 'icon-cog',
				'text'  => JText::_('COM_REDITEM_CPANEL_GENERAL_LABEL'),
				'items' => array(
					array (
						'view' => 'cpanel',
						'icon' => 'icon-home',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_HOME_LABEL'),
						'link' => 'index.php?option=com_reditem&view=cpanel'
					),
					array (
						'view' => 'explore',
						'icon' => 'icon-search',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_EXPLORE_LABEL'),
						'link' => 'index.php?option=com_reditem&view=explore'
					),
					array (
						'view' => 'templates',
						'icon' => 'icon-desktop',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_TEMPLATES_LABEL'),
						'link' => 'index.php?option=com_reditem&view=templates'
					),
					array (
						'view' => 'configuration',
						'icon' => 'icon-wrench',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_CONFIGURATION_LABEL'),
						'link' => $confLink
					)
				)
			),
			array (
				'icon'  => 'icon-file-text',
				'text'  => JText::_('COM_REDITEM_CPANEL_ITEMS_LABEL'),
				'items' => array(
					array (
						'view' => 'items',
						'icon' => 'icon-list-alt',
						'text' => JText::_('PLG_REDITEM_QUICKICON_LIST_LABEL'),
						'link' => 'index.php?option=com_reditem&view=items'
					),
					array (
						'view' => 'fields',
						'icon' => 'icon-puzzle-piece',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_FIELDS_LABEL'),
						'link' => 'index.php?option=com_reditem&view=fields'
					),
					array (
						'view' => 'types',
						'icon' => 'icon-book',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_TYPES_LABEL'),
						'link' => 'index.php?option=com_reditem&view=types'
					)
				)
			),
			array (
				'icon'  => 'icon-sitemap',
				'text'  => JText::_('COM_REDITEM_CPANEL_CATEGORIES_LABEL'),
				'items' => array(
					array (
						'view' => 'categories',
						'icon' => 'icon-list-alt',
						'text' => JText::_('PLG_REDITEM_QUICKICON_LIST_LABEL'),
						'link' => 'index.php?option=com_reditem&view=categories'
					),
					array (
						'view' => 'category_fields',
						'icon' => 'icon-puzzle-piece',
						'text' => JText::_('PLG_REDITEM_QUICKICON_REDITEM_FIELDS_LABEL'),
						'link' => 'index.php?option=com_reditem&view=category_fields'
					)
				)
			)
		);

		return $icons;
	}
}
