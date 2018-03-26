<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Galleries List View
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class RedsliderViewGalleries extends RedsliderView
{
	/**
	 * Display the galleries list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');

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
		return JText::_('COM_REDSLIDER_GALLERIES');
	}

	/**
	 * Get the tool-bar to render.
	 *
	 * @todo	The commented lines are going to be implemented once we have setup ACL requirements for redSLIDER
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = JFactory::getUser();

		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup  = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_redslider'))
		{
			$new = RToolbarBuilder::createNewButton('gallery.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_redslider'))
		{
			$edit = RToolbarBuilder::createEditButton('gallery.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('galleries.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.edit.state', 'com_redslider'))
		{
			$publish = RToolbarBuilder::createPublishButton('galleries.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('galleries.unpublish');
			$thirdGroup->addButton($unPublish);
		}

		if ($user->authorise('core.delete', 'com_redslider'))
		{
			$delete = RToolbarBuilder::createDeleteButton('galleries.delete');
			$secondGroup->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
