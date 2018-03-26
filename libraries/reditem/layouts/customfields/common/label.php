<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;
$customfield = $displayData['customfield'];
$params = new JRegistry($customfield->params);
$required = $params->get('required', 0);
$tooltip = $params->get('tooltip', '');
$isTooltipEnabled = $params->get('enable_tooltip', "1");
$icon = RHelperAsset::load('tooltip.png', 'com_reditem');
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
	jQuery(document).ready(function() {
		jQuery("' . $customfield->divId . '-tooltip").tooltip();
	});
');
?>
<label
	for="<?php echo $customfield->divId ?>"
	id="<?php echo $customfield->divId ?>-lbl">
	<?php echo  $customfield->name ?>
	<?php  if ($required): ?>
	<span>&nbsp;*</span>
	<?php endif ?>
	<?php if ($isTooltipEnabled && !empty($tooltip)): ?>
	<i class="icon-question-circle" id="<?php echo $customfield->divId ?>-tooltip" data-toggle="tooltip" title="<?php echo $tooltip; ?>"></i>
	<?php endif;?>
</label>
