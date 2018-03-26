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
 * Slide edit view
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       2.0.0
 */
class RedsliderViewSlide extends RedsliderView
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the slide edit page
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
		$this->sectionId = $app->getUserState('com_redslider.global.slide.section', '');

		if ($this->sectionId)
		{
			// Add form field from plugin section
			JPluginHelper::importPlugin('redslider_sections');
			$dispatcher = RFactory::getDispatcher();
			$dispatcher->trigger('onSlidePrepareForm', array($this->form, $this->sectionId));

			$editData = $app->getUserState('com_redslider.edit.slide.data', array());

			if (isset($editData['params']) && is_array($editData['params']))
			{
				foreach ($editData['params'] as $key => $value)
				{
					$this->form->setValue($key, 'params', $value);
				}
			}
			elseif (isset($this->item->params))
			{
				$params = new JRegistry($this->item->params);
				$params = $params->toArray();

				foreach ($params as $key => $value)
				{
					$this->form->setValue($key, 'params', $value);
				}
			}
		}

		// Display the slide
		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDSLIDER_SLIDE');
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

		$save = RToolbarBuilder::createSaveButton('slide.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('slide.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('slide.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('slide.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('slide.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('slide.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
