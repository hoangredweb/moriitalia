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
 * Templates List View
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class RedsliderViewTemplates extends RedsliderView
{
	/**
	 * Display the templates list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_redslider.global.template.section', null);

		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');

		// Get the section name
		foreach ($this->items as $key => $item)
		{
			JPluginHelper::importPlugin('redslider_sections');
			$dispatcher = RFactory::getDispatcher();

			$list = $dispatcher->trigger('getSectionNameById', array($item->section));
			$item->sectionName = "";

			if (count($list))
			{
				$item->sectionName = $list[0];
			}
		}

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 *
	 * @since   2.0.0
	 */
	public function getTitle()
	{
		return JText::_('COM_REDSLIDER_TEMPLATES');
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

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.create', 'com_redslider'))
		{
			$new = RToolbarBuilder::createNewButton('template.add');
			$firstGroup->addButton($new);
		}

		if ($user->authorise('core.edit', 'com_redslider'))
		{
			$edit = RToolbarBuilder::createEditButton('template.edit');
			$secondGroup->addButton($edit);

			$checkin = RToolbarBuilder::createCheckinButton('templates.checkin');
			$secondGroup->addButton($checkin);
		}

		if ($user->authorise('core.edit.state', 'com_redslider'))
		{
			$publish = RToolbarBuilder::createPublishButton('templates.publish');
			$thirdGroup->addButton($publish);

			$unPublish = RToolbarBuilder::createUnpublishButton('templates.unpublish');
			$thirdGroup->addButton($unPublish);
		}

		if ($user->authorise('core.delete', 'com_redslider'))
		{
			$delete = RToolbarBuilder::createDeleteButton('templates.delete');
			$secondGroup->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)->addGroup($secondGroup)->addGroup($thirdGroup);

		return $toolbar;
	}
}
