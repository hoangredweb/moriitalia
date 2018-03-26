<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The galleries controller
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controller.galleries
 * @since       2.0
 */
class RedsliderControllerGalleries extends RControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 */
	public function __construct()
	{
		parent::__construct();

		// Write this to make two tasks use the same method (in this example the add method uses the edit method)
		$this->registerTask('add', 'edit');
	}

	/**
	 * display the add and the edit form
	 *
	 * @return void
	 */
	public function edit()
	{
		$jInput = JFactory::getApplication()->input;
		$jInput->set('view', 'gallery');
		$jInput->set('layout', 'default');
		$jInput->set('hidemainmenu', 1);

		parent::display();
	}
}
