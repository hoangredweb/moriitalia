<?php
/**
 * @package     RedITEM
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The mail edit controller
 *
 * @package     RedITEM
 * @subpackage  Controller.Template
 * @since       2.1.5
 */
class RedItemControllerMail extends RControllerForm
{
	/**
	 * Add mail
	 *
	 * @return void
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.mail.section', '');
		$app->setUserState('com_reditem.global.mail.typeId', '');

		return parent::add();
	}

	/**
	 * Edit mail
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return void
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$mailModel = RModel::getAdminInstance('Mail');

		$mail = $mailModel->getItem();
		$app->setUserState('com_reditem.global.mail.section', $mail->section);
		$app->setUserState('com_reditem.global.mail.typeId', $mail->type_id);

		return parent::edit($key, $urlVar);
	}

	/**
	 * For auto-submit form when client choose Section
	 *
	 * @return void
	 */
	public function setSection()
	{
		$app = JFactory::getApplication();

		$recordId = $app->input->getInt('id', 0);
		$data     = $app->input->get_Array('jform', array());

		$app->setUserState('com_reditem.edit.mail.data', $data);
		$app->setUserState('com_reditem.global.mail.section', $data['section']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		$this->setRedirect($redirect);
	}

	/**
	 * For auto-submit form when client choose Type
	 *
	 * @return void
	 */
	public function setType()
	{
		$app = JFactory::getApplication();

		$recordId = $app->input->getInt('id', 0);
		$data     = $app->input->get_Array('jform', array());

		$app->setUserState('com_reditem.edit.mail.data', $data);
		$app->setUserState('com_reditem.global.mail.typeId', $data['type_id']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		$this->setRedirect($redirect);
	}
}
