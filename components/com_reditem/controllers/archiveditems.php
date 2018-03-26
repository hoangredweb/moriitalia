<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Archive items controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class ReditemControllerArchiveditems extends JControllerLegacy
{
	/**
	 * Automatically update Items' state to archived when nowDate is greater than publish_down date (CRON)
	 *
	 * @return void
	 */
	public function updateArchived()
	{
		$app = JFactory::getApplication();
		$secretRequest = $app->input->getString('token', '');
		$redConfig = JComponentHelper::getParams('com_reditem');
		$secretCfg = $redConfig->get('cron_token');

		if ($secretRequest !== $secretCfg)
		{
			// Some one is trying to flood our system
			die;
		}
		else
		{
			$db = JFactory::getDbo();

			// Define null and now dates
			$nullDate	= $db->quote($db->getNullDate());
			$nowDate	= $db->quote(ReditemHelperSystem::getDateWithTimezone()->toSql());

			$prefix = $app->getCfg('dbprefix');

			$valid = false;

			$tables = $db->getTableList();

			$valid = in_array($prefix . 'reditem_items', $tables);

			if ($valid)
			{
				$cols = array_keys($db->getTableColumns('#__reditem_items'));

				$valid = in_array('publish_up', $cols);
				$valid = in_array('publish_down', $cols);

				if ($valid)
				{
					$query = $db->getQuery(true)
						->select($db->qn('id'))
						->from($db->qn('#__reditem_items'))
						->where($db->qn('published') . '= 1 AND' . $db->qn('publish_down') . ' <> ' . $nullDate . ' AND ' . $db->qn('publish_down') . ' < ' . $nowDate);

					$db->setQuery($query);
					$items = $db->loadObjectList();

					$query = $db->getQuery(true);
					$query->update($db->qn('#__reditem_items'))
						->set($db->qn('published') . ' = 2')
						->where($db->qn('publish_down') . ' <> ' . $nullDate . ' AND ' . $db->qn('publish_down') . ' < ' . $nowDate);

					$db->setQuery($query);
					$db->execute();

					if (count($items))
					{
						foreach ($items as $item)
						{
							// Email item archived due to time
							JPluginHelper::importPlugin('reditem');
							$dispatcher = RFactory::getDispatcher();
							$dispatcher->trigger('onAfterItemArchivedDueToTime', array($item));
						}
					}
				}
			}
		}

		// Close conneciton
		$app->close();
	}
}
