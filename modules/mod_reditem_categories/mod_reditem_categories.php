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

require_once JPATH_SITE . '/modules/mod_reditem_categories/helper.php';

// Prepare for cache
$idBase = $params->get('parent', 0);

$cacheid = md5(serialize(array($idBase, $module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'ModredITEMCategoriesHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$lists = JModuleHelper::moduleCache($module, $params, $cacheparams);

$parentDetail = $lists['parent'];
$categories = $lists['sub'];

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_reditem_categories', $params->get('layout', 'default'));
