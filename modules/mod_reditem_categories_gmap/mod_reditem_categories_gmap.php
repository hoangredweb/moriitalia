<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Load redCORE library
$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDITEM_REDCORE_INIT_FAILED'), 404);
}

include_once $redcoreLoader;

RBootstrap::bootstrap();

require_once JPATH_SITE . '/modules/mod_reditem_categories_gmap/helper.php';

// Prepare for cache
$idBase = $params->get('parent', 0);

$cacheid = md5(serialize(array($idBase, $module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'ModredITEMCategoriesGmapHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$category = JModuleHelper::moduleCache($module, $params, $cacheparams);

// Load JS & CSS stuff
$doc = JFactory::getDocument();
ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
$doc->addScript('http://www.google.com/jsapi');

$parentCategory			= $params->get('parent', 0);
$country				= $params->get('country', 'Denmark');
$gmapWidth				= $params->get('gmap_width', '');
$gmapHeight				= $params->get('gmap_height', '');
$gmapZoom				= $params->get('gmap_zoom', '5');
$gmapLatlng				= $params->get('gmap_latlng', '55.3906821,10.437969000000066');
$gmapInfoWindowUnique	= $params->get('gmap_unique_inforwindow', 1);
$gmapCluterer			= $params->get('gmap_clusterer', '0');
$moduleclass_sfx		= htmlspecialchars($params->get('moduleclass_sfx'));

if ($gmapWidth)
{
	$gmapWidth = 'width: ' . $gmapWidth;
}

if ($gmapHeight)
{
	$gmapHeight = 'height: ' . $gmapHeight;
}

require JModuleHelper::getLayoutPath('mod_reditem_categories_gmap', $params->get('layout', 'default'));
