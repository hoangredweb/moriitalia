<?php
/**
 * @package    RedSLIDER.Installer
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
		dirname(__FILE__) . '/redCORE/extensions',
		// Discover install
		JPATH_ADMINISTRATOR . '/components/com_redcore'
	);

	if ($redcoreInstaller = JPath::find($searchPaths, 'install.php'))
	{
		require_once $redcoreInstaller;
	}
}

/**
 * Script file of redSLIDER component
 *
 * @package  RedSLIDER.Installer
 *
 * @since    2.0
 */
class Com_RedSliderInstallerScript extends Com_RedcoreInstallerScript
{
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
		$this->installModules($parent);
		$this->installPlugins($parent);
		$this->copyAssets();

		return true;
	}

	/**
	 * Main redSLIDER installer Events
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
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		// Error handling
		JError::SetErrorHandling(E_ALL, 'callback', array('Com_RedSliderInstallerScript', 'error_handling'));

		// Check redSLIDER library
		$where = array(
			$db->qn('e.type') . ' = ' . $db->quote('library'),
			$db->qn('e.element') . ' = ' . $db->quote('redslider')
		);
		$query = $db->getQuery(true);
		$query->select('count(*) AS count')
			->from($db->qn('#__extensions', 'e'))
			->where($where);
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result->count > 0)
		{
			$app->enqueueMessage(JText::_('COM_REDSLIDER_UNINSTALL_ERROR_DELETE_LIBRARY_FIRST'), 'error');

			$app->redirect('index.php?option=com_installer&view=manage');
		}

		// Uninstall extensions
		$this->com_uninstall();
		$this->uninstallModules($parent);
		$this->uninstallPlugins($parent);
	}

	/**
	 * Main redSLIDER uninstaller Events
	 *
	 * @return  void
	 */
	private function com_uninstall()
	{
	}

	/**
	 * Install the package modules
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
				$result    = 0;

				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}

				$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Install the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
				$extPath  = $src . '/plugins/' . $extGroup . '/' . $extName;
				$result   = 0;

				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}

				// Store the result to show install summary later
				$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));

				// Enable the installed plugin
				if ($result)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->update($db->quoteName("#__extensions"));
					$query->set("enabled=1");
					$query->where("type='plugin'");
					$query->where("element=" . $db->quote($extName));
					$query->where("folder=" . $db->quote($extGroup));
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Uninstall the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallLibraries($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->libraries->library)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
				$extPath = $src . '/libraries/' . $extName;
				$result  = 0;

				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('extension_id')
					->from($db->quoteName("#__extensions"))
					->where("type='library'")
					->where("element=" . $db->quote($extName));

				$db->setQuery($query);

				if ($extId = $db->loadResult())
				{
					$result = $installer->uninstall('library', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('libraries', array('name' => $extName, 'result' => $result));
			}
		}
	}

	/**
	 * Uninstall the package modules
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
				$result    = 0;

				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('extension_id')
					->from($db->quoteName("#__extensions"))
					->where("type='module'")
					->where("element=" . $db->quote($extName));

				$db->setQuery($query);

				if ($extId = $db->loadResult())
				{
					$result = $installer->uninstall('module', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Uninstall the package plugins
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
				$extPath  = $src . '/plugins/' . $extGroup . '/' . $extName;
				$result   = 0;

				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('extension_id')
					->from($db->quoteName("#__extensions"))
					->where("type='plugin'")
					->where("element=" . $db->quote($extName))
					->where("folder=" . $db->quote($extGroup));

				$db->setQuery($query);

				if ($extId = $db->loadResult())
				{
					$result = $installer->uninstall('plugin', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));
			}
		}
	}

	/**
	 * Error handler
	 *
	 * @param   array  $e  Exception array
	 *
	 * @return  void
	 */
	public static function error_handling(Exception $e)
	{
	}

	/**
	 * Display install message
	 *
	 * @return void
	 */
	public function displayInstallMsg()
	{
		echo '<p><img src="' . JUri::root() . '/media/com_redslider/images/redslider_logo.jpg" alt="redSLIDER Logo" width="500"></p>';
		echo '<br /><br /><p>' . JText::_('COM_REDSLIDER_WELCOME_REMEMBER_CHECK_UPDATES') . '<br />';
		echo '<a href="http://www.redcomponent.com/" target="_new">';
		echo '<img src="' . JUri::root() . '/media/com_redslider/images/redcomponent_logo.jpg" alt="">';
		echo '</a></p>';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		parent::postflight($type, $parent);

		// Redirect to the welcome screen.
		if ($type === 'discover_install')
		{
			$app = JFactory::getApplication();

			return $app->redirect('index.php?option=com_redslider&view=welcome&type=' . $type);
		}

		return $parent->getParent()->setRedirectURL('index.php?option=com_redslider&view=welcome&type=' . $type);
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	public function preflight($type, $parent)
	{
		if (method_exists('Com_RedcoreInstallerScript', 'preflight') && !parent::preflight($type, $parent))
		{
			return false;
		}

		if ($type == "update")
		{
			$this->migrationData();
		}

		return true;
	}

	/**
	 * Method for migration data from old version
	 *
	 * @return  boolean  True if success. False otherwise.
	 */
	public function migrationData()
	{
		return true;
	}

	/**
	 * Get the common JInstaller instance used to install all the extensions
	 *
	 * @return JInstaller The JInstaller object
	 */
	public function getInstaller()
	{
		if (is_null($this->installer))
		{
			$this->installer = new JInstaller;
		}

		return $this->installer;
	}

	/**
	 * Store the result of trying to install an extension
	 *
	 * @param   string  $type    Type of extension (libraries, modules, plugins)
	 * @param   array   $status  The status info
	 *
	 * @return void
	 */
	private function _storeStatus($type, $status)
	{
		// Initialise status object if needed
		if (is_null($this->status))
		{
			$this->status = new stdClass;
		}

		// Initialise current status type if needed
		if (!isset($this->status->{$type}))
		{
			$this->status->{$type} = array();
		}

		// Insert the status
		array_push($this->status->{$type}, $status);
	}

	/**
	 * Copy sample slide images to images/stories folder
	 *
	 * @return  [type]  [description]
	 */
	private function copyAssets()
	{
		// Create new directory in images/stories
		$this->createIndexFolder(JPATH_ROOT . '/images/stories');
		$this->createIndexFolder(JPATH_ROOT . '/images/stories/redslider');

		// Copy sample media
		$src = JPATH_ROOT . '/media/com_redslider/images/slides';
		$dst = JPATH_ROOT . '/images/stories/redslider';

		if (JFolder::exists($dst))
		{
			if (!JFolder::delete($dst))
			{
				$app = JFactory::getApplication();
				$app->enqueueMessage('Couldnt delete ' . $dst);
			}
		}

		if (!JFolder::move($src, $dst))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('Couldnt move ' . $src . ' to ' . $dst);
		}

		if (is_dir($src))
		{
			JFolder::delete($src);
		}
	}

	/**
	 * creates a folder with empty html file
	 *
	 * @param   string  $path  Directory path
	 *
	 * @return  boolean
	 */
	public function createIndexFolder($path)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if (JFolder::create($path))
		{
			if (!JFile::exists($path . '/index.html'))
			{
				JFile::copy(JPATH_ROOT . '/components/index.html', $path . '/index.html');
			}

			return true;
		}

		return false;
	}
}
