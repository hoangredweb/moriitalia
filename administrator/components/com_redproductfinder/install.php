<?php
/**
 * @package    RedPRODUCTFINDER.Installer
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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

	$redcoreInstaller = JPath::find($searchPaths, 'install.php');
	
	if ($redcoreInstaller)
	{
		require_once $redcoreInstaller;
	}
}

// Register component prefix
JLoader::registerPrefix('Redproductfinder', __DIR__);

/**
 * Script file of redPRODUCTFINDER component
 *
 * @package  RedProductfinder.Installer
 *
 * @since    2.0
*/
class Com_RedProductfinderInstallerScript extends Com_RedcoreInstallerScript
{
	/**
	 * Installed redPRODUCTFINDER version.
	 *
	 * @var string
	 */
	private $currentVersion = '';

	/**
	 * Array for moving current templates from db to files.
	 * Used on update process to version 2.1.17.
	 *
	 * @var array
	 */
	private $tempTemplates = array();

	/**
	 * Method to install the component
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	*/
	public function installOrUpdate($parent)
	{
		parent::installOrUpdate($parent);
		
		$this->com_install();

		return true;
	}

	/**
	 * Main redPRODUCTFINDER installer Events
	 *
	 * @return  void
	 */
	private function com_install()
	{
		// Diplay the installation message
		$this->displayInstallMsg();
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		parent::uninstall($parent);
	}

	/**
	 * Display install message
	 *
	 * @return void
	 */
	public function displayInstallMsg()
	{
		echo '<p><img src="' . JUri::root() . '/media/com_redproductfinder/images/redproductfinder_logo_400width.png" alt="redPRODUCTFINDER Logo" width="500"></p>';
		echo '<br /><br /><p>Remember to check for updates at:<br />';
		echo '<a href="http://www.redcomponent.com/" target="_new">';
		echo '<img src="' . JUri::root() . '/media/com_redproductfinder/images/redcomponent_logo.jpg" alt="">';
		echo '</a></p>';
	}
}
?>
