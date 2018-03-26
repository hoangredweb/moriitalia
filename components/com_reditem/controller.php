<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  RedITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Base Controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class ReditemController extends JControllerLegacy
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 *
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		// Set default framework to bootstrap 2
		RHtmlMedia::setFramework('bootstrap2');

		$input = JFactory::getApplication()->input;
		$view  = $input->getCmd('view', '');

		// Check permission
		if ((($view == 'item') || ($view == 'items')) && (!ReditemHelperSystem::getUser()->authorise('core.create', 'com_reditem')))
		{
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=login', false));
		}

		parent::__construct($config);
	}

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->getCmd('view', 'categorydetail'));
		$input->set('task', $input->getCmd('task', 'display'));

		return parent::display($cachable, $urlparams);
	}
}
