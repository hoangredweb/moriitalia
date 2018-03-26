<?php
/**
 * @package     RedITEM
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_reditem/helpers/helper.php';

/**
 * Category edit view
 *
 * @package     RedITEM
 * @subpackage  View
 * @since       2.1.5
 */
class RedItemViewMail extends ReditemViewAdmin
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the template edit page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$app = RFactory::getApplication();

		$this->form     = $this->get('Form');
		$this->item     = $this->get('Item');
		$this->mailTags = array();
		$mailSection = $app->getUserState('com_reditem.global.mail.section', '');
		$mailType    = $app->getUserState('com_reditem.global.mail.typeId', 0);

		$keyItem        = JText::_('COM_REDITEM_MAIL_TAG_ITEM_AVAILABLE_TAGS');
		$keyReport      = JText::_('COM_REDITEM_MAIL_TAG_REPORT_AVAILABLE_TAGS');
		$keyComment     = JText::_('COM_REDITEM_MAIL_TAG_COMMENT_AVAILABLE_TAGS');
		$keyCommentator = JText::_('COM_REDITEM_MAIL_TAG_COMMENTATOR_AVAILABLE_TAGS');
		$keyReceiver    = JText::_('COM_REDITEM_MAIL_TAG_RECEIVER_AVAILABLE_TAGS');
		$keySender      = JText::_('COM_REDITEM_MAIL_TAG_COMMON_AVAILABLE_TAGS');
		$keyRater       = JText::_('COM_REDITEM_MAIL_TAG_RATER_AVAILABLE_TAGS');
		$keyReporter    = JText::_('COM_REDITEM_MAIL_TAG_REPORTER_AVAILABLE_TAGS');

		$this->canAccess = JPluginHelper::isEnabled('reditem', 'mail');
		$errors = $this->get('Errors');

		if (!$this->canAccess)
		{
			$errors[] = JText::_('COM_REDITEM_MAIL_NO_ACCESS');
		}

		// Check for errors.
		if (count($errors))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		switch ($mailSection)
		{
			case 'get_item_rating':
				$tags = ReditemHelperMail::getAvailableItemTags($mailType);
				$tags['{item_rating_point}'] = JText::_('COM_REDITEM_MAIL_TAG_ITEM_RATING_POINT');

				$this->mailTags[$keyItem]  = $tags;
				$this->mailTags[$keyRater] = ReditemHelperMail::getUserTags('rater');
				break;

			case 'item_reported':
				$this->mailTags[$keyItem]   = ReditemHelperMail::getAvailableItemTags($mailType);
				$this->mailTags[$keyReport] = array(
					'{report_reason}' => JText::_('COM_REDITEM_MAIL_TAG_REPORT_REASON')
				);
				$this->mailTags[$keyReporter] = ReditemHelperMail::getUserTags('reporter');
				break;

			case 'item_self_removed':
			case 'item_removed_by_reported':
			case 'item_removed_by_expired':
			case 'item_removed_by_admin':
			case 'item_republished':
				$this->mailTags[$keyItem] = ReditemHelperMail::getAvailableItemTags($mailType);
				break;

			case 'get_item_comment':
			case 'get_item_comment_reply':
			case 'get_private_comment':
				$this->mailTags[$keyItem]        = ReditemHelperMail::getAvailableItemTags($mailType);
				$this->mailTags[$keyComment]     = ReditemHelperMail::getCommentTags();
				$this->mailTags[$keyCommentator] = ReditemHelperMail::getUserTags('commentator');
				break;

			case 'comment_reported':
				$this->mailTags[$keyItem]    = ReditemHelperMail::getAvailableItemTags($mailType);
				$this->mailTags[$keyComment] = ReditemHelperMail::getCommentTags();
				$this->mailTags[$keyReport]  = array(
					'{report_reason}' => JText::_('COM_REDITEM_MAIL_TAG_REPORT_REASON')
				);
				$this->mailTags[$keyReporter] = ReditemHelperMail::getUserTags('reporter');
				break;

			case 'comment_self_removed':
			case 'comment_removed_by_reported':
				$this->mailTags[$keyComment] = ReditemHelperMail::getCommentTags();
				break;

			default:
				break;
		}

		if (!empty($mailSection))
		{
			$this->mailTags[$keyReceiver] = ReditemHelperMail::getUserTags();
			$this->mailTags[$keySender]   = ReditemHelperMail::getCommonTags();
		}

		// Load more sections from plugins
		$dispatcher	= RFactory::getDispatcher();
		JPluginHelper::importPlugin('reditem');
		$dispatcher->trigger('OnPrepareAvailableTagsForMailSection', array(&$this->mailTags, $mailSection));

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		$subTitle = ' <small>' . JText::_('COM_REDITEM_NEW') . '</small>';

		if ($this->item->id)
		{
			$subTitle = ' <small>' . JText::_('COM_REDITEM_EDIT') . '</small>';
		}

		return JText::_('COM_REDITEM_MAIL_MAIL') . $subTitle;
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo	We have setup ACL requirements for redITEM
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('mail.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('mail.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('mail.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('mail.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('mail.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('mail.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
