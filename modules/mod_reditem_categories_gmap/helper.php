<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_reditem/helpers/reditem.php';

/**
 * Categories helper
 *
 * @since  1.0
 */
class ModredITEMCategoriesGmapHelper
{
	/**
	 * Get list of categories
	 *
	 * @param   array  &$params  Params of module
	 *
	 * @return  array
	 */
	public static function getList(&$params)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDBO();
		$categoryId = JFactory::getApplication()->input->getInt('id');

		// Check permission for this category
		$canView = (boolean) ReditemHelperACL::checkCategoryPermission('category.view', $categoryId);

		if (!$canView)
		{
			return false;
		}

		$inforTemplate = $params->get('inforbox', '');

		$query = $db->getQuery(true);
		$query->select($db->qn('id') . ', ' . $db->qn('link'))
			->from($db->qn('#__menu'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('link') . ' LIKE ' . $db->q('%option=com_reditem&view=categorydetail%'))
			->where($db->qn('published') . ' = 1');
		$db->setQuery($query);
		$menus = $db->loadObjectList();

		// Get Admin model
		$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
		$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');

		// Get subCategories of current category
		$category = $categoryModel->getItem($categoryId);

		return $category;
	}
}
