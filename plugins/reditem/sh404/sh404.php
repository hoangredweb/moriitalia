<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

/**
 * Plugins for sh404 in reditem
 *
 * @package  RedITEM.Plugin
 *
 * @since    2.1.19
 */
class PlgReditemSh404 extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Email data
	 *
	 * @var    JApplicationCms
	 * @since  1.0
	 */
	protected $data;

	/**
	 * Method for run after item saved successfully.
	 *
	 * @param   object   $item        The Item object
	 * @param   boolean  $isNew       True on create new. False on edit.
	 * @param   boolean  $itemBefore  Item object before saving
	 *
	 * @return  boolean
	 */
	public function onAfterItemSave($item, $isNew, $itemBefore)
	{
		if (empty($item) || $isNew)
		{
			return false;
		}

		$newCategories = (array) json_decode(stripcslashes($item->categories));
		sort($newCategories);

		if (!JComponentHelper::isEnabled('com_sh404sef') || ($itemBefore->categories == $newCategories && $itemBefore->alias == $item->alias))
		{
			return false;
		}

		$db = RFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->qn('#__sh404sef_urls'))
			->where($db->qn('newurl') . ' LIKE ' . $db->q('%view=itemdetail%'))
			->where($db->qn('newurl') . ' LIKE ' . $db->q('%id=' . (int) $item->id . '%'))
			->where($db->qn('newurl') . ' LIKE ' . $db->q('%option=com_reditem%'));
		$db->setQuery($query);

		return $db->execute();
	}
}
