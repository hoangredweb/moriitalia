<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

/**
 * Template edit view
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       2.0.0
 */
class RedsliderViewTemplate extends RedsliderView
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
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();

		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->tags = $this->get('Tags');
		$this->templateTags = array();
		$this->sectionId = $app->getUserState('com_redslider.global.template.section', '');

		// Get template's tags
		if ($this->sectionId)
		{
			// Get list of sections' name
			JPluginHelper::importPlugin('redslider_sections');
			$dispatcher = RFactory::getDispatcher();
			$tagsTmp = $dispatcher->trigger('getTagNames', array($this->sectionId));

			if (count($tagsTmp))
			{
				$this->templateTags = $tagsTmp[0];
			}
		}

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
		return JText::_('COM_REDSLIDER_TEMPLATE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @todo	We have setup ACL requirements for redSLIDER
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('template.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('template.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('template.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('template.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('template.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('template.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
