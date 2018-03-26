<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode   = $displayData['fieldcode'];
$attributes  = $displayData['attributes'];
$data        = $displayData['data'];
$name        = $displayData['name'];
$required    = $displayData['required'];
$class       = (!empty($attributes['class'])) ? $attributes['class'] : '';
$default     = $displayData['default'];
$isNew       = JFactory::getApplication()->input->getInt('id', 0) == 0;

if (!empty($required) && $required)
{
	$required = ' required aria-required="true"';
}
?>
<?php if ($isNew && !empty($default)):?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var checked = $('input[type="radio"][name="jform[fields][radio][<?php echo $fieldcode; ?>]"]:checked');

			if (checked.length == 0)
			{
				$('input[type="radio"][name="jform[fields][radio][<?php echo $fieldcode; ?>]"][value="<?php echo $default;?>"]').prop('checked', 'checked');
			}
		});
	})(jQuery);
</script>
<?php endif;?>
<?php if (empty($data)) : ?>
<div class="alert alert-warning">
	<?php echo JText::sprintf('COM_REDITEM_FIELD_RADIO_PLEASE_ADD_AN_OPTION', $name); ?>
</div>
<?php else : ?>
<fieldset class="radio reditem_customfield_radio <?php echo $class;?>" id="reditem_customfield_radio_<?php echo $fieldcode; ?>" <?php echo $required; ?> style="padding-left: 0px;">
	<?php foreach ($data as $index => $option) : ?>
	<label class="radio" for="jform_fields_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>">
		<input
			type="radio"
			name="jform[fields][radio][<?php echo $fieldcode; ?>]"
			id="jform_fields_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
			value="<?php echo $option['value']; ?>"
			<?php if ($option['selected']) : ?>
			checked="checked"
			<?php endif; ?> />
		<?php echo $option['text']; ?>
	</label>
	<?php endforeach; ?>
</fieldset>
<div class="btn-group">
	<a href="#" class="btn btn-danger" onclick="jQuery('input[name=\'jform[fields][radio][<?php echo $fieldcode; ?>]\']').prop('checked', false);return false;">
		<i class="icon icon-circle-blank"></i><?php echo JText::_('COM_REDITEM_FIELD_CLEAR_ALL') ?>
	</a>
</div>
<?php endif; ?>
