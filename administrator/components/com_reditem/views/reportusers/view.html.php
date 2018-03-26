<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Report Users List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       2.1.3
 */
class ReditemViewReportUsers extends ReditemViewAdmin
{
	/**
	 * Display the reports list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();

		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');

		// Edit State permission
		$this->canEditState = false;

		if ($user->authorise('core.edit.state'))
		{
			$this->canEditState = true;
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_REPORT_USERS_TITLE');
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
		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.edit.state'))
		{
			$unBlockButton = RToolbarBuilder::createStandardButton(
				'reportusers.unBlock',
				JText::_('COM_REDITEM_REPORT_USERS_TOOLBAR_UNBLOCK'),
				'btn-success',
				'icon-unlock');
			$firstGroup->addButton($unBlockButton);

			$blockButton = RToolbarBuilder::createStandardButton(
				'reportusers.block',
				JText::_('COM_REDITEM_REPORT_USERS_TOOLBAR_BLOCK'),
				'btn-danger',
				'icon-lock');
			$secondGroup->addButton($blockButton);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup);

		return $toolbar;
	}
}
