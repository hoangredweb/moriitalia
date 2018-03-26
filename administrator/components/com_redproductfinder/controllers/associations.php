<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Associations controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderControllerAssociations extends RControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  JModel
	 */
	public function getModel($name = 'Association', $prefix = 'RedproductfinderModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Save dependent tags
	 *
	 * @return JModel
	 */
	function savedependent()
	{
		$model = $this->getModel('associations');
		$msg = $model->savedependent();
		echo $msg;
		exit;
	}
}
