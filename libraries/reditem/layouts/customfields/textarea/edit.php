<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode           = $displayData['fieldcode'];
$value               = $displayData['value'];
$attributes          = $displayData['attributes'];
$isLimitGuideEnabled = $displayData['isLimitGuideEnabled'];
$limit               = $displayData['limit'];
$isAutosize          = $displayData['isAutosize'];
$default             = $displayData['default'];
$isNew               = JFactory::getApplication()->input->getInt('id', 0) == 0;

if (!empty($default) && $isNew)
{
	$value = $default;
}

if ($isAutosize)
{
	RHelperAsset::load('jquery/jquery.autosize.min.js', 'com_reditem');
}
?>

<div class="reditem_customfield_textarea">
	<textarea cols="80" rows="10"
		name="jform[fields][textarea][<?php echo $fieldcode; ?>]"
		id="jform_fields_textarea_<?php echo $fieldcode; ?>"
		<?php if ($limit && $isLimitGuideEnabled): ?>
		maxlength="<?php echo $limit ?>"
		<?php endif; ?>
		<?php echo $attributes; ?>><?php echo $value; ?></textarea>
	<?php if ($isLimitGuideEnabled): ?>
		<span id="number_character_<?php echo $fieldcode; ?>"><?php echo JString::strlen($value) ?></span><span>/<?php echo $limit ?></span>
	<?php endif ?>
</div>

<script type='text/javascript'>
	(function($){
		$(document).ready(function(){
			<?php if ($isLimitGuideEnabled): ?>
			$("#jform_fields_textarea_<?php echo $fieldcode; ?>").keyup(function(){
				var value = $("#jform_fields_textarea_<?php echo $fieldcode; ?>").val();
				$("#number_character_<?php echo $fieldcode; ?>").html(value.length);
			});
			<?php endif ?>
			<?php if ($isAutosize): ?>
			$("#jform_fields_textarea_<?php echo $fieldcode; ?>").autosize();
			<?php endif; ?>
		});
	})(jQuery);
</script>
