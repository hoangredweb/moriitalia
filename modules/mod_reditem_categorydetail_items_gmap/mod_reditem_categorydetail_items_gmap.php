<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('reditem.library');

require_once JPATH_SITE . '/modules/mod_reditem_categorydetail_items_gmap/helper.php';

// Load JS & CSS stuff
$lazyLoad = $params->get('lazyload', false);
ReditemHelperSystem::loadGoogleMapJavascriptLibrary();

// Init module
ModredITEMCategoryDetailItemsGmapHelper::setParams($params);
$data = ModredITEMCategoryDetailItemsGmapHelper::init();
$path = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/mobiledetect/mobile_detect.php';

if (file_exists($path))
{
	require_once $path;
	$detect = new Mobile_Detect;
	$isMobile = $detect->isMobile();
}
else
{
	$isMobile = false;
}

require JModuleHelper::getLayoutPath('mod_reditem_categorydetail_items_gmap', $params->get('layout', 'default'));

// NOTE: This is applus specific module atm. Idea is to generalize this module later on.
