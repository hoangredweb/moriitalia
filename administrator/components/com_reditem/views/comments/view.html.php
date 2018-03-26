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
 * Comments List View
 *
 * @package     RedITEM.Backend
 * @subpackage  View
 * @since       2.1
 */
class ReditemViewComments extends ReditemViewAdmin
{
	/**
	 * Display the comments list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   0.9.1
	 */
	public function display($tpl = null)
	{
		$this->items			= $this->get('Items');
		$this->state			= $this->get('State');
		$this->pagination		= $this->get('Pagination');
		$this->filterForm		= $this->get('Form');
		$this->activeFilters	= $this->get('ActiveFilters');

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   2.1
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_COMMENTS_COMMENTS');
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

		if ($user->authorise('core.admin'))
		{
			$new = RToolbarBuilder::createNewButton('comment.add');
			$secondGroup->addButton($new);

			$edit = RToolbarBuilder::createEditButton('comment.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('comments.checkin');
			$secondGroup->addButton($checkin);

			$delete = RToolbarBuilder::createDeleteButton('comments.delete');
			$thirdGroup->addButton($delete);

			$publish = RToolbarBuilder::createPublishButton('comments.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('comments.unpublish');
			$thirdGroup->addButton($unPublish);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
