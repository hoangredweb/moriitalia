<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopControllerUser_point_config extends RedshopController
{
	public function __construct($default = array())
	{
		parent::__construct($default);
		$this->registerTask('add', 'edit');
	}

	public function edit()
	{
		JRequest::setVar('view', 'user_point_config');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	public function save()
	{
		$post = JRequest::get('post');
		$model = $this->getModel('user_point_config');

		if ($model->store($post))
		{
			$msg = JText::_('COM_REDSHOP_USER_POINT_CONFIG_SAVED');
		}
		else
		{
			$msg = JText::_('COM_REDSHOP_USER_POINT_CONFIG_SAVED_FAILED');
		}

		$this->setRedirect('index.php?option=com_redshop&view=user_point_config', $msg);
	}

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_redshop&view=user_point');
	}
}
