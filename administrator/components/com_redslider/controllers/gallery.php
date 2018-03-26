<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The gallery edit controller
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controller.gallery
 * @since       2.0
 */
class RedsliderControllerGallery extends RControllerForm
{
	/**
	 * For edit an gallery
	 *
	 * @param   int     $key     Gallery key to edit
	 * @param   string  $urlVar  Url variables
	 *
	 * @return void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$itemmodel = RModel::getAdminInstance('Gallery');

		$item = $itemmodel->getItem();

		$app = JFactory::getApplication();
		$app->setUserState('com_redslider.global.gid', array($item->id));

		return parent::edit($key, $urlVar);
	}
}
