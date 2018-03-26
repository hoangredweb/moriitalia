<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_items
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

require_once JPATH_SITE . '/components/com_reditem/helpers/route.php';
require_once JPATH_SITE . '/components/com_reditem/helpers/reditem.php';
require_once JPATH_SITE . '/modules/mod_reditem_items/helper.php';

$displayType = $params->get('display', 0);
$paramItemId = $params->get('setItemId', 0);
$paramTemplateId = $params->get('templateId', 0);
$paramSlideControls = $params->get('slider_controls', 1);
$paramSlidePager = $params->get('slider_pager', 1);
$paramSlideAutoPlay = $params->get('slider_autoplay', 1);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$sliderControls = false;
$slidePager = false;
$slideAutoPlay = false;

$templateModel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
$template = $templateModel->getItem($paramTemplateId);

$params->set('moduleTemplateInstance', $template);
$params->set('moduleId', $module->id);

// Prepare for cache
$categoriesId = $params->get('categoriesIds', array());
$idBase = implode('-', $categoriesId);

$cacheid = md5(serialize(array($idBase, $module->module)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'ModredITEMItemsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$items = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!empty($items))
{
	if ($paramSlidePager)
	{
		$slidePager = 'true';
	}
	else
	{
		$slidePager = 'false';
	}

	if ($paramSlideControls)
	{
		$sliderControls = 'true';
	}
	else
	{
		$sliderControls = 'false';
	}

	if ($paramSlideAutoPlay)
	{
		$slideAutoPlay = 'true';
	}
	else
	{
		$slideAutoPlay = 'false';
	}
}

require JModuleHelper::getLayoutPath('mod_reditem_items', $params->get('layout', 'default'));
