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
 * Item look model
 *
 * @package     RedITEM.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class ReditemModelItemlook extends RModel
{
	/**
	 * Get data of item
	 *
	 * @return  boolean/array
	 */
	public function getData()
	{
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$id = $app->input->getRaw('id', 0);

		if (empty($id))
		{
			return false;
		}

		$query = $db->getQuery(true)
			->select($db->qn('data'))
			->from($db->qn('#__reditem_item_preview'))
			->where($db->qn('id') . ' = ' . $db->quote($id));
		$db->setQuery($query);
		$result = $db->loadObject();

		if (!$result)
		{
			return false;
		}

		$data = new JRegistry($result->data);
		$data = $data->toObject();

		// Get template detail
		$templateModel = RModel::getAdminInstance('Template', array(), 'com_reditem');
		$data->template = $templateModel->getItem($data->template_id);

		return $data;
	}
}
