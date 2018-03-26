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
?>
<label for="<?php  echo $customfield->divId ?>" id="<?php  echo $customfield->divId ?>-lbl">
	<?php echo  $customfield->name ?>
	<?php  if ($required): ?>
		<span class="star">&nbsp;*</span>
	<?php endif ?>
</label>
<?php if ($isTooltipEnabled): ?>
<div id="customfield_tooltip_<?php echo $customfield->fieldcode ?>" class="customfield-tooltip hasTooltip" data-original-title="<?php echo $tooltip; ?>">
	<?php echo $icon ?>
</div>
<?php endif; ?>
