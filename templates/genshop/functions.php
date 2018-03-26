<?php
/**
 * @package    Template.Function
 *
 * @copyright  Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 * Use this file to add any PHP to the template before it is executed
 */

// Restrict Access to within Joomla
defined('_JEXEC') or die('Restricted access');

$bodyclass = "";

if ($this->countModules('toolbar'))
{
	$bodyclass = "toolbarpadding";
}
$js = '
function checkDemail(elem){
	var temp = \'\';
	var flag = true;
	jQuery(elem).find(\'[type="email"]\').each(function(){
		if ( temp == jQuery(this).val() && temp != \'\' ) {
			alert("'. JText::_('COM_REDFORM_YOUR_EMAIL_DUPLICATE') .'");
			jQuery(this).focus();
			flag = false;
		}else{
			temp = jQuery(this).val();
		}
	});
	if (flag) {
		return true;
	}
	return false;
}
jQuery(document).ready(function($){
	jQuery(\'form.introcude\').each(function(){
		jQuery(this).attr( \'onsubmit\', \'return checkDemail(this);\');
		jQuery(this).find(\'[name="field34_1[email]"]\').attr(\'value\',\'\');
	});
});
';

$document->addScriptDeclaration($js);

$option = JRequest::getVar('option');
$view   = JRequest::getVar('view');
$layout = JRequest::getVar('layout');
// if ( ($option == 'com_redshop' && $view == 'product') || ($option == 'com_redshop' && $view == 'manufacturers' && $layout == null) )
// {
// 	$doc = Wright::getInstance();
// 	$doc->document->params->set('columns', 'sidebar1:0;main:12;sidebar2:0');
// }