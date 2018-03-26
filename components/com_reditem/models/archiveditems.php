<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Item detail model
 *
 * @package     RedITEM.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class ReditemModelArchiveditems extends RModel
{
	/**
	 * Get data of item
	 *
	 * @return  boolean/array
	 */
	public function getData()
	{
		$app = JFactory::getApplication();

		$result = array(
			'items' => array(),
			'pagination' => ''
		);

		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('filter.published', '2');

		$limit = $app->getUserStateFromRequest('global.list.limit', 'com_reditem_items_items_limit', $app->getCfg('list_limit'), 'uint');
		$itemsModel->setState('list.limit', $limit);

		$limitStart = $app->input->getInt($itemsModel->getPagination()->prefix . 'limitstart', 0);
		$itemsModel->setState('list.start', $limitStart);

		$items = $itemsModel->getItems();

		// Process check view permission for sub-categories list.
		ReditemHelperACL::processItemACL($items);

		if (count($items))
		{
			$templateModel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');

			foreach ($items as &$item)
			{
				$item->template = $templateModel->getItem($item->template_id);
			}
		}

		$result['items'] = $items;
		$result['pagination'] = $itemsModel->getPagination()->getPagesLinks();

		return $result;
	}
}
