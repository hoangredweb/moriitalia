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
 * Slide table
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Table
 * @since       2.0.0
 */
class RedsliderTableSlide extends RTable
{
	/**
	 * The name of the table with slide
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $_tableName = 'redslider_slides';

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
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$dispatcher = RFactory::getDispatcher();
		JPluginHelper::importPlugin('redslider_sections');
		$dispatcher->trigger('onSlideStore', array($this, $input));

		$jform = $input->get('jform', null, 'array');

		if (isset($jform['params']))
		{
			$params = new JRegistry($jform['params']);
			$this->params = $params->toString();
		}

		if (!parent::store($updateNulls))
		{
			return false;
		}

		return true;
	}
}
