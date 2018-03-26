<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  RedITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$input = JFactory::getApplication()->input;

JLoader::import('reditem.library');

JLoader::import('joomla.html.parameter');

$option = $input->getCmd('option');
$view   = $input->getCmd('view');

// Register component prefix
JLoader::registerPrefix('Reditem', __DIR__);

// Register library prefix
RLoader::registerPrefix('Reditem', JPATH_LIBRARIES . '/reditem');

// Loading helper
JLoader::import('joomla.html.pagination');
JLoader::import('reditem', JPATH_COMPONENT . '/helpers');
JLoader::import('route', JPATH_COMPONENT . '/helpers');

// Load RedITEM stuff
RHelperAsset::load('reditem.min.js', 'com_reditem');
RHelperAsset::load('reditem.min.css', 'com_reditem');

$controller = $input->getCmd('view');

// Set the controller page
if (!file_exists(JPATH_COMPONENT . '/controllers/' . $controller . '.php'))
{
	$controller = 'categorydetail';
	$input->set('view', 'categorydetail');
}

require_once JPATH_COMPONENT . '/controllers/' . $controller . '.php';

// Execute the controller
$controller = JControllerLegacy::getInstance('reditem');
$controller->execute($input->getCmd('task'));
$controller->redirect();
