<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Class RedsliderHelperCpanelIcons
 *
 * @since  2.0
 */
class RedsliderHelperCpanelIcons extends JObject
{
	/**
	 * Protected! Use the getInstance
	 *
	 * @return array $icon_array
	 */
	protected function RedsliderHelperCpanelIcons()
	{
		// Parent Helper Construction
		parent::__construct();
	}

	/**
	 * Some function which was in obscure reddesignhelper class.
	 *
	 * @return array
	 */
	public static function getIconArray()
	{
		$uri = JUri::getInstance();
		$return = base64_encode('index.php' . $uri->toString(array('query')));
		$configurationLink = 'index.php?option=com_redcore&view=config&layout=edit&component=com_redslider&return=' . $return;

		$icon_array = array(
				"galleries" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=galleries'),
					"icon"   	=> "icon-sitemap",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_GALLERIES_LABEL'),
				),
				"slides" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=slides'),
					"icon"      => "icon-file-text",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_SLIDES_LABEL'),
				),
				"templates" => array(
					"link"      => JRoute::_('index.php?option=com_redslider&view=templates'),
					"icon"      => "icon-desktop",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_TEMPLATES_LABEL'),
				),
				"configuration" => array(
					"link"      => JRoute::_($configurationLink),
					"icon"      => "icon-cog",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_CONFIGURATION_LABEL')
				),
				"help" => array(
					"link"      => 'http://wiki.redcomponent.com/index.php?title=RedSLIDER',
					"icon"      => "icon-question-sign",
					"title"     => JText::_('COM_REDSLIDER_CPANEL_HELP_LABEL'),
					'target'    => '_blank'
				)
		);

		return $icon_array;
	}
}
