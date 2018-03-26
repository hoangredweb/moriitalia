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
 * Gallery edit view
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       2.0.0
 */
class RedsliderViewGallery extends RedsliderView
{
	/**
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the gallery edit page
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$document = JFactory::getDocument();

		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->tags = $this->get('Tags');

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
		return JText::_('COM_REDSLIDER_GALLERY');
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

		$save = RToolbarBuilder::createSaveButton('gallery.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('gallery.save');
		$saveAndNew = RToolbarBuilder::createSaveAndNewButton('gallery.save2new');
		$save2Copy = RToolbarBuilder::createSaveAsCopyButton('gallery.save2copy');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($saveAndNew)
			->addButton($save2Copy);

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('gallery.cancel');
		}
		else
		{
			$cancel = RToolbarBuilder::createCloseButton('gallery.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
