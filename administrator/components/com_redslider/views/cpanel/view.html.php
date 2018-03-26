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
 * Control panel view for redSLIDER.
 *
 * @package     RedSLIDER.Backend
 * @subpackage  View
 * @since       0.9.1
 */
class RedSliderViewCpanel extends RedsliderView
{
	/**
	 * Hide sidebar in cPanel
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * Display the control panel
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 *
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		$this->user = JFactory::getUser();
		$userType = array_keys($this->user->groups);
		$this->user->usertype = $userType[0];
		$this->user->gid = $this->user->groups[$this->user->usertype];

		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/cpanel_icons.php';

		// Get stats
		$this->stats = $this->get('Stats');
		$this->redsliderversion = $this->get('Version');
		$this->iconArray = RedsliderHelperCpanelIcons::getIconArray();

		$layout = JFactory::getApplication()->input->getCmd('layout');
		$this->layout = $layout;

		parent::display($tpl);
	}

	/**
	 * Render CPanel icons
	 *
	 * @param   array   $iconArray       Icon arrays
	 * @param   string  $quickLinkIcons  [description]
	 *
	 * @return  void
	 */
	public function renderCpanelIconSet($iconArray = array(), $quickLinkIcons = null)
	{
		$iconCount = 0;

		foreach ($iconArray as $icon)
		{
			// Disable only for quicklinks?
			if (($icon['name'] == "container") && ($this->config->get('use_container') == 0) && (isset($quickLinkIcons)))
			{
				continue;
			}

			if (isset($quickLinkIcons) && (!in_array($icon['name'], $quickLinkIcons)))
			{
				continue;
			}

			$link = 'index.php?option=com_redslider&amp;view=' . $icon['name'];

			$this->quickiconButton($link, $icon['icon'], JText::_("COM_REDSLIDER_CPANEL_" . $icon['title'] . '_LABEL'));

			$iconCount++;
		}

		if (($iconCount == 0) && (!isset($quickLinkIcons)))
		{
			echo JText::_("COM_REDSLIDER_CPANEL_NO_ACCESS_DESC");
		}
	}

	/**
	 * Display the quick icons
	 *
	 * @param   string    $link   The link to the view
	 * @param   string    $image  The name of the image to show
	 * @param   string    $text   The text to show on the button
	 * @param   bool|int  $modal  Set whether this is shown in a modal
	 *
	 * @return   string
	 *
	 * @since   2.0
	 */
	public function quickiconButton($link, $image, $text, $modal = 0)
	{
		echo RLayoutHelper::render('cpanel_icon',
			array(
				'view' => $this,
				'link' => $link,
				'image' => $image,
				'text' => $text,
				'modal' => $modal,
			)
		);
	}
}
