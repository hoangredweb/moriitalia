<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;
JHTML::_('behavior.formvalidation');

$fieldcode  = $displayData['fieldcode'];
$name       = $displayData['name'];
$data       = $displayData['data'];
$attributes = $displayData['attributes'];
$checkAll   = $displayData['checkAll'];
$default    = $displayData['default'];
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;
$class      = (!empty($attributes['class'])) ? $attributes['class'] : '';
$required   = '';

if (!empty($attributes['required']) && $attributes['required'])
{
	$required = ' required aria-required="true"';
}
?>
<?php if ($isNew && !empty($default)):?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var checked = $('input[type="checkbox"][name="jform[fields][checkbox][<?php echo $fieldcode; ?>][]"]:checked');

			if (checked.length == 0)
			{
				$('input[type="checkbox"][name="jform[fields][checkbox][<?php echo $fieldcode; ?>][]"][value="<?php echo $default;?>"]').prop('checked', 'checked');
			}
		});
	})(jQuery);
</script>
<?php endif;?>

<?php if (empty($data)) : ?>
<div class="alert alert-warning">
	<?php echo JText::sprintf('COM_REDITEM_FIELD_CHECKBOX_PLEASE_ADD_AN_OPTION', $name); ?>
</div>
<?php else : ?>
<fieldset name="jform[fields][checkbox][<?php echo $fieldcode; ?>][]" class="checkboxes <?php echo $class;?>" id="reditem_customfield_checkbox_<?php echo $fieldcode; ?>" <?php echo $required;?>>
	<?php foreach ($data as $index => $option) : ?>
	<label class="checkbox">
		<input
			type="checkbox"
			name="jform[fields][checkbox][<?php echo $fieldcode; ?>][]"
			id="jform_fields_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
			value="<?php echo $option['value']; ?>"
			<?php if ($option['checked']) : ?>
			checked="checked"
			<?php endif; ?> />
		<?php echo $option['text']; ?>
	</label>
	<?php endforeach; ?>
</fieldset>
<div class="btn-group">
	<?php if ($attributes['show_checkall']) : ?>
		<a href="#" class="btn btn-primary" onclick="jQuery('input[name=\'jform[fields][checkbox][<?php echo $fieldcode; ?>][]\']').prop('checked', true);return false;">
			<i class="icon icon-check"></i>
			<?php echo JText::_('COM_REDITEM_FIELD_CHECK_ALL') ?>
		</a>
	<?php endif;?>
	<a href="#" class="btn btn-danger" onclick="jQuery('input[name=\'jform[fields][checkbox][<?php echo $fieldcode; ?>][]\']').prop('checked', false);return false;">
		<i class="icon icon-check-empty"></i>
		<?php echo JText::_('COM_REDITEM_FIELD_CLEAR_ALL') ?>
	</a>
</div>
<?php endif; ?>
