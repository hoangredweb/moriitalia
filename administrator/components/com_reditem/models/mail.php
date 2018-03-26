<?php
/**
 * @package     RedITEM
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM mail Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Mail
 * @since       2.1.5
 *
 */
class RedItemModelMail extends RModelAdmin
{
	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$user = ReditemHelperSystem::getUser();

		if (!$user->authorise('core.edit.state', 'com_reditem'))
		{
			// Disable change publish state
			$form->setFieldAttribute('published', 'readonly', true);
			$form->setFieldAttribute('published', 'class', 'btn-group disabled');
		}

		return $form;
	}

	/**
	 * Method for set email as an default on section
	 *
	 * @param   int  $mailId         ID of mail
	 * @param   int  $statusDefault  Default status
	 *
	 * @return  boolean             True on success. False otherwise.
	 */
	public function setDefault($mailId, $statusDefault)
	{
		$mailId        = (int) $mailId;
		$statusDefault = (int) $statusDefault;

		if (!$mailId)
		{
			return false;
		}

		$mail = $this->getItem($mailId);
		$db = RFactory::getDbo();
		$query = $db->getQuery(true);

		if ($statusDefault == 1)
		{
			// Remove old default
			$query = $db->getQuery(true)
				->update($db->qn('#__reditem_mail'))
				->set($db->qn('default') . ' = 0')
				->where($db->qn('section') . ' = ' . $db->quote($mail->section))
				->where($db->qn('type_id') . ' = ' . $db->quote($mail->type_id));
			$db->setQuery($query);
			$db->execute();
		}

		// Insert new default
		$query->clear()
			->update($db->qn('#__reditem_mail'))
			->set($db->qn('default') . ' = ' . $statusDefault)
			->where($db->qn('id') . ' = ' . $db->quote($mail->id));
		$db->setQuery($query);

		return $db->execute();
	}
}
