<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Notifications controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.1.12
 */
class ReditemControllerNotifications extends JControllerLegacy
{
	/**
	 * Automatically send mail notifications for users.
	 *
	 * @return void
	 */
	public function runProgress()
	{
		$app = JFactory::getApplication();
		$tokenRequest  = $app->input->getString('token', '');
		$redItemConfig = JComponentHelper::getParams('com_reditem');
		$tokenConfig   = $redItemConfig->get('cron_token');

		if (empty($tokenRequest) || empty($tokenConfig) || ($tokenConfig != $tokenRequest))
		{
			// Wrong token.
			die();
		}
		else
		{
			// Correct token and run process.
			$this->processUserSendPerDate();
			$this->processUserSendPerWeek();
		}

		$app->close();
	}

	/**
	 * Method for process all users has "Collect and send per date" in their Mail Setting
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public function processUserSendPerDate()
	{
		$today    = ReditemHelperSystem::getDateWithTimezone();
		$sendTime = strtotime($today->format('Y-m-d') . ' 21:00:00');
		$sendTime = ReditemHelperSystem::getDateWithTimezone(gmdate('Y-m-d H:i:s', $sendTime));

		// Check current time is for send email or not
		if ($today->toUnix() < $sendTime->toUnix())
		{
			return false;
		}

		$db = $this->db;

		$query = $db->getQuery(true)
			->select('ms.*')
			->from($db->qn('#__reditem_mail_settings', 'ms'))
			->where($db->qn('ms.state') . ' = 1')
			->where($db->qn('ms.type') . ' = 1');
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if (!$users)
		{
			return false;
		}

		$startTime = strtotime($today->format('Y-m-d') . ' 00:00:00');
		$startTime = ReditemHelperSystem::getDateWithTimezone(gmdate('Y-m-d H:i:s', $startTime));

		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get('mailfrom', null, ''),
			$config->get('fromname', null, '')
		);

		foreach ($users as $user)
		{
			$query->clear()
				->select($db->qn(array('id', 'section', 'subject', 'body')))
				->from($db->qn('#__reditem_mail_queue'))
				->where($db->qn('state') . ' = 0')
				->where($db->qn('created') . ' >= ' . $db->quote($startTime->toSql()))
				->where($db->qn('created') . ' <= ' . $db->quote($sendTime->toSql()))
				->where($db->qn('recipient') . ' = ' . (int) $user->user_id);
			$db->setQuery($query);
			$mails = $db->loadObjectList();

			if (!$mails)
			{
				continue;
			}

			$userData    = ReditemHelperSystem::getUser($user->user_id);
			$mailSubject = JText::_('PLG_SYSTEM_REDITEM_SENDMAIL_SEND_PER_DATE_MAIL_SUBJECT');
			$mailBody    = '';
			$recipient   = array($userData->email, $userData->name);
			$mailIds     = array();

			foreach ($mails as $mail)
			{
				// Combine all mail body
				$mailBody .= $mail->body;

				$mailIds[] = $mail->id;
			}

			$mailer->isHTML(true);
			$mailer->setSubject($mailSubject);
			$mailer->setSender($sender);
			$mailer->setBody($mailBody);
			$mailer->addRecipient($recipient);
			$sent = $mailer->Send();

			if (!$sent)
			{
				continue;
			}

			// Update state of these mail in queue
			$query->clear()
				->update($db->qn('#__reditem_mail_queue'))
				->set($db->qn('state') . ' = 1')
				->where($db->qn('id') . ' IN (' . implode(',', $mailIds) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * Method for process all users has "Collect and send weekly" in their Mail Setting
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public function processUserSendPerWeek()
	{
		$db = $this->db;

		$query = $db->getQuery(true)
			->select('ms.*')
			->from($db->qn('#__reditem_mail_settings', 'ms'))
			->where($db->qn('ms.state') . ' = 1')
			->where($db->qn('ms.type') . ' = 2');
		$db->setQuery($query);
		$users = $db->loadObjectList();

		if (!$users)
		{
			return false;
		}

		$today = ReditemHelperSystem::getDateWithTimezone();

		// Check today is Sunday
		if ($today->format('w', true) != '0')
		{
			return false;
		}

		$sendTime = strtotime($today->format('Y-m-d') . ' 21:00:00');
		$sendTime = ReditemHelperSystem::getDateWithTimezone(gmdate('Y-m-d H:i:s', $sendTime));

		// Check current time is for send email or not
		if ($today->toUnix() < $sendTime->toUnix())
		{
			return false;
		}

		$lastMonday = strtotime('last Monday');
		$lastMonday = ReditemHelperSystem::getDateWithTimezone(gmdate('Y-m-d H:i:s', $lastMonday));
		$startTime  = strtotime($lastMonday->format('Y-m-d') . ' 00:00:00');
		$startTime  = ReditemHelperSystem::getDateWithTimezone(gmdate('Y-m-d H:i:s', $startTime));

		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array(
			$config->get('mailfrom', null, ''),
			$config->get('fromname', null, '')
		);

		foreach ($users as $user)
		{
			$query->clear()
				->select($db->qn(array('id', 'section', 'subject', 'body')))
				->from($db->qn('#__reditem_mail_queue'))
				->where($db->qn('state') . ' = 0')
				->where($db->qn('created') . ' >= ' . $db->quote($startTime->toSql()))
				->where($db->qn('created') . ' <= ' . $db->quote($sendTime->toSql()))
				->where($db->qn('recipient') . ' = ' . (int) $user->user_id);
			$db->setQuery($query);
			$mails = $db->loadObjectList();

			if (!$mails)
			{
				continue;
			}

			$userData    = ReditemHelperSystem::getUser($user->user_id);
			$mailSubject = JText::_('PLG_SYSTEM_REDITEM_SENDMAIL_SEND_PER_WEEK_MAIL_SUBJECT');
			$mailBody    = '';
			$recipient   = array($userData->email, $userData->name);
			$mailIds     = array();

			foreach ($mails as $mail)
			{
				// Combine all mail body
				$mailBody .= $mail->body;

				$mailIds[] = $mail->id;
			}

			$mailer->isHTML(true);
			$mailer->setSubject($mailSubject);
			$mailer->setSender($sender);
			$mailer->setBody($mailBody);
			$mailer->addRecipient($recipient);
			$sent = $mailer->Send();

			if (!$sent)
			{
				continue;
			}

			// Update state of these mail in queue
			$query->clear()
				->update($db->qn('#__reditem_mail_queue'))
				->set($db->qn('state') . ' = 1')
				->where($db->qn('id') . ' IN (' . implode(',', $mailIds) . ')');
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}
