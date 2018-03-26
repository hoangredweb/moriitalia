<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Template table
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Table
 * @since       2.0.0
 */
class RedsliderTableTemplate extends RTable
{
	/**
	 * The name of the table with template
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $_tableName = 'redslider_templates';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'published';

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		$db            = RFactory::getDbo();
		$user          = RFactory::getUser();
		$query         = $db->getQuery(true);
		$templateModel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_redslider');

		if ($pk)
		{
			// Remove related slides
			$query->clear()
				->delete($db->qn('#__redslider_slides'))
				->where($db->qn('template_id') . '=' . (int) $pk);
			$db->setQuery($query);
			$db->execute();
		}

		return parent::delete($pk);
	}
}
