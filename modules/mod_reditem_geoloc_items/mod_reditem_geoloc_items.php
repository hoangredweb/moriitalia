<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_geoloc_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

RBootstrap::bootstrap();
require_once JPATH_SITE . '/modules/mod_reditem_geoloc_items/helper.php';

$d = 0.0;
$b = explode(".", $_SERVER['REMOTE_ADDR'], 4);

for ($i = 0; $i < 4; $i++)
{
	$d *= 256.0;
	$d += $b[$i];
};

$db     = JFactory::getDbo();
$ipLong = $d;
$query  = $db->getQuery(true);

$query->select(
	array (
		$db->qn('latitude', 'lat'),
		$db->qn('longitude', 'lng'),
		$db->qn('city_name', 'city'),
		$db->qn('country_name', 'country'),
		'(' . $ipLong . ' - CAST(' . $db->qn('ip_from') . ' AS SIGNED)) AS ' . $db->qn('close_from'),
		'(CAST(' . $db->qn('ip_to') . ' AS SIGNED) - ' . $ipLong . ') AS ' . $db->qn('close_to')
	)
)
	->from($db->qn('#__ipligence2'))
	->where($db->q($ipLong) . ' BETWEEN ' . $db->qn('ip_from') . ' AND ' . $db->qn('ip_to'))
	->order($db->qn('close_from') . ' ASC, ' . $db->qn('close_to') . ' ASC');
$location = $db->setQuery($query)->loadObject();
$params->set('location', $location);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Prepare for cache
if (!empty($location))
{
	$cacheid     = md5(serialize(array($location->lat . '-' . $location->lng, $module->module)));
	$cacheparams = new stdClass;
	$cacheparams->cachemode = 'id';
	$cacheparams->class = 'ModredITEMGeolocItemsHelper';
	$cacheparams->method = 'getList';
	$cacheparams->methodparams = $params;
	$cacheparams->modeparams = $cacheid;
	$items = JModuleHelper::moduleCache($module, $params, $cacheparams);
}
else
{
	$items = ModredITEMGeolocItemsHelper::getList($params);
}

require JModuleHelper::getLayoutPath('mod_reditem_geoloc_items', $params->get('layout', 'default'));
