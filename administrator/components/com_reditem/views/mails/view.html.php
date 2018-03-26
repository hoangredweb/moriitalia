<?php
/**
 * @package     RedITEM
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Mails List View
 *
 * @package     RedITEM
 * @subpackage  View
 * @since       2.1.5
 */
class RedItemViewMails extends ReditemViewAdmin
{
	/**
	 * Display the template list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();

		$this->items			= $this->get('Items');
		$this->state			= $this->get('State');
		$this->pagination		= $this->get('Pagination');
		$this->filterForm		= $this->get('Form');
		$this->activeFilters	= $this->get('ActiveFilters');

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

		// Get mail section name
		foreach ($this->items as $item)
		{
			$item->section_name = ReditemHelperMail::getMailSectionName($item->section);

			if (empty($item->type_name))
			{
				$item->type_name = JText::_('COM_REDITEM_MAIL_TYPE_ID_UNCATEGORISED');
			}
		}

		// Edit permission
		$this->canEdit = false;

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$this->canEdit = true;
		}

		// Edit state permission
		$this->canEditState = false;

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$this->canEditState = true;
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   0.9.1
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_MAIL_MAILS');
	}

	/**
	 * Get the tool-bar to render.
	 *
	 * @todo	The commented lines are going to be implemented once we have setup ACL requirements for redITEM
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = ReditemHelperSystem::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_reditem'))
		{
			$new = RToolbarBuilder::createNewButton('mail.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_reditem'))
		{
			$edit = RToolbarBuilder::createEditButton('mail.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('mails.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.delete', 'com_reditem'))
		{
			$delete = RToolbarBuilder::createDeleteButton('mails.delete');
			$thirdGroup->addButton($delete);
		}

		if ($user->authorise('core.edit.state', 'com_reditem'))
		{
			$publish = RToolbarBuilder::createPublishButton('mails.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('mails.unpublish');
			$thirdGroup->addButton($unPublish);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
