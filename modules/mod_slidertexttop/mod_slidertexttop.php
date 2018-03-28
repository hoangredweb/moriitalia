<?php

defined('_JEXEC') or die('Restricted access');

require_once  __DIR__ . '/helper.php';

jimport( 'joomla.user.helper' ); 
jimport( 'joomla.form.formfield' );

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$textIds = $params->get('text_ids');

require JModuleHelper::getLayoutPath('mod_slidertexttop', $params->get('layout', 'default'));
