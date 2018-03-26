<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redproductfinder
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDPRODUCTFINDER_REDCORE_INIT_FAILED'), 404);
}

include_once $redcoreLoader;

// Bootstraps redCORE
RBootstrap::bootstrap();

require_once JPATH_COMPONENT . '/helpers/redproductfinder.php';

if (!JFactory::getUser()->authorise('core.manage', 'com_redproductfinder'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('redproductfinder');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
