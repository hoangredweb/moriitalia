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
$default             = $displayData['default'];
$isNew               = JFactory::getApplication()->input->getInt('id', 0) == 0;

if (!empty($default) && $isNew)
{
	$value = $default;
}
?>

<div class="reditem_customfield_textbox">
	<input type="text" name="jform[fields][text][<?php echo $fieldcode; ?>]" id="jform_fields_text_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?> />
	<?php if ($isLimitGuideEnabled): ?>
		<span id="number_character_<?php echo $fieldcode; ?>"><?php echo JString::strlen($value) ?></span><span>/<?php echo $limit ?></span>
	<?php endif ?>
</div>

<?php if ($isLimitGuideEnabled): ?>
<script type='text/javascript'>
(function($){
	$(document).ready(function($){
		$("#jform_fields_text_<?php echo $fieldcode; ?>").keyup(function(){
			$("#number_character_<?php echo $fieldcode; ?>").html($(this).val().length);
		});
	});
})(jQuery);
</script>
<?php endif ?>
