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
 * Plugins for changing states on items of RedITEM
 *
 * @package  RedITEM.Plugin
 *
 * @since    2.0
 */
class PlgSystemReditem_States extends JPlugin
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
	 * Function auto-change the state of item to archived when today is greater than publish_down date
	 *
	 * @return true
	 */
	public function onAfterDispatch()
	{
		$db     = JFactory::getDbo();
		$app    = JFactory::getApplication();
		$prefix = $app->getCfg('dbprefix');
		$tables = $db->getTableList();

		if (!in_array($prefix . 'reditem_items', $tables))
		{
			// Table doesn't exit.
			return false;
		}

		$cols = array_keys($db->getTableColumns('#__reditem_items'));

		if (!in_array('publish_up', $cols) || !in_array('publish_down', $cols))
		{
			// Column "Publish Up" & "Publish Down" doesn't exist in table #__reditem_items
			return false;
		}

		// Define null and now dates
		$nullDate = $db->getNullDate();
		$nowDate  = JFactory::getDate()->toSql();

		// Prepare condition
		$where = array();
		$where[] = $db->qn('published') . ' = ' . $db->quote(1);
		$where[] = $db->qn('publish_down') . ' <> ' . $db->quote($nullDate);
		$where[] = $db->qn('publish_down') . ' < ' . $db->quote($nowDate);

		// Get list of items which match the archived condition
		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__reditem_items'))
			->where($where);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$results)
		{
			// If no items match the condition
			return true;
		}

		$itemIds = array();

		foreach ($results as $result)
		{
			$itemIds[] = $result->id;
		}

		// Update archived date due to Publish Down date
		$query = $db->getQuery(true)
			->update($db->qn('#__reditem_items'))
			->set($db->qn('published') . ' = 2')
			->where($db->qn('id') . ' IN (' . implode(',', $itemIds)) . ')';
		$db->setQuery($query);
		$db->execute();

		$item = RTable::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');

		foreach ($itemIds as $itemId)
		{
			$item->load($itemId);

			// Email item archived due to time
			JPluginHelper::importPlugin('reditem');
			$dispatcher = RFactory::getDispatcher();
			$dispatcher->trigger('onAfterItemArchivedDueToTime', array($item));
		}

		return true;
	}
}
