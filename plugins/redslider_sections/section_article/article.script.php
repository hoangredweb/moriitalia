<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Find redCORE installer to use it as base system
if (!class_exists('Com_RedcoreInstallerScript'))
{
	$searchPaths = array(
		// Install
		dirname(__FILE__) . '/redCORE',
		// Discover install
		JPATH_ADMINISTRATOR . '/components/com_redcore'
	);

	if ($redcoreInstaller = JPath::find($searchPaths, 'install.php'))
	{
		require_once $redcoreInstaller;
	}
}

/**
 * Script file of redSLIDER plugins
 *
 * @package  RedSLIDER.Installer
 *
 * @since    2.0
 */
class PlgRedslider_SectionsSection_ArticleInstallerScript extends Com_RedcoreInstallerScript
{
	private $section = "SECTION_ARTICLE";

	/**
	 * method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function uninstall($parent)
	{
		$db    = JFactory::getDbo();
		$user  = JFactory::getUser();

		// Remove all slides which belong to this section
		$query = $db->getQuery(true)
			->delete($db->qn('#__redslider_slides'))
			->where($db->qn('section') . '=' . $db->q($this->section));
		$db->setQuery($query);
		$db->execute();

		// Remove all templates which belong to this section
		$query = $db->getQuery(true)
			->delete($db->qn('#__redslider_templates'))
			->where($db->qn('section') . '=' . $db->q($this->section));
		$db->setQuery($query);
		$db->execute();

		parent::uninstall($parent);
	}
}
