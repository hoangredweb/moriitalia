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
$fieldType           = $displayData['fieldType'];
?>

<div class="reditem_customfield_textbox">
	<input type="text" name="cform[text][<?php echo $fieldcode; ?>]" id="cform_text_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?> />
	<?php if ($isLimitGuideEnabled && ($fieldType != 'youtube')): ?>
		<span id="number_character_<?php echo $fieldcode; ?>"><?php echo JString::strlen($value) ?></span><span>/<?php echo $limit ?></span>
	<?php endif ?>
</div>

<?php if ($isLimitGuideEnabled && ($fieldType != 'youtube')): ?>
<script type='text/javascript'>
(function($){
	$(document).ready(function($){
		$("#cform_text_<?php echo $fieldcode; ?>").keyup(function(){
			$("#number_character_<?php echo $fieldcode; ?>").html($(this).val().length);
		});
	});
})(jQuery);
</script>
<?php endif ?>
