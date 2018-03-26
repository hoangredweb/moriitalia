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
$renderStyle = $displayData['renderStyle'];
$required    = $displayData['required'];

if ($renderStyle == 'jquery')
{
	RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
	RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
}
?>

<?php if ($renderStyle == 'jquery' || $required) :?>
<script type="text/javascript">
	(function($){
		$(document).ready(function() {
			<?php if ($renderStyle == 'jquery') : ?>
			<?php foreach ($data as $index => $option) : ?>
			$('#cform_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>').button();
			<?php endforeach; ?>
			<?php endif; ?>

			<?php if ($required) : ?>
			var formValidate<?php echo $fieldcode; ?> = false;

			document.formvalidator.setHandler('atLeastOneRadio', function(value) {
				var obj  = $('input[value="' + value + '"]');
				var objs = $('input[type="radio"][name="' + obj.attr('name') + '"]:checked');

				if (objs.length > 0)
					formValidate<?php echo $fieldcode; ?> = true;
				else
					formValidate<?php echo $fieldcode; ?> = false;

				return formValidate<?php echo $fieldcode; ?>;
			});
			<?php endif; ?>
		});
	})(jQuery);
</script>
<?php endif; ?>

<div class="reditem_customfield_radio" id="reditem_customfield_radio_<?php echo $fieldcode; ?>">
	<?php if (empty($data)) : ?>
	<div class="alert alert-warning">
		<?php echo JText::sprintf('COM_REDITEM_FIELD_RADIO_PLEASE_ADD_AN_OPTION', $name); ?>
	</div>
	<?php else : ?>
		<?php if ($renderStyle == 'bootstrap') : ?>
			<fieldset class="radio btn-group">
			<?php foreach ($data as $index => $option) : ?>
				<input
					type="radio"
					name="cform[radio][<?php echo $fieldcode; ?>]"
					id="cform_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
					value="<?php echo $option['value']; ?>"
					<?php if ($required): ?>
						class="required validate-atLeastOneRadio"
						required
					<?php endif; ?>
					<?php if ($option['selected']) : ?>
					checked="checked"
					<?php endif; ?> />
				<label class="btn" for="cform_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>">
					<?php echo $option['text']; ?>
				</label>
			<?php endforeach; ?>
			</fieldset>
		<?php elseif ($renderStyle == 'jquery') : ?>
			<?php foreach ($data as $index => $option) : ?>
				<input
					type="radio"
					name="cform[radio][<?php echo $fieldcode; ?>]"
					id="cform_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
					value="<?php echo $option['value']; ?>"
					<?php if ($required): ?>
						class="required validate-atLeastOneRadio"
						required
					<?php endif; ?>
					<?php if ($option['selected']) : ?>
					checked="checked"
					<?php endif; ?>
				/>
				<label class="input-large" for="cform_radio_<?php echo $fieldcode; ?>_<?php echo $index; ?>">
					<?php echo $option['text']; ?>
				</label>
			<?php endforeach; ?>
		<?php else : ?>
			<?php foreach ($data as $index => $option) : ?>
			<label for="cform_radio_<?php echo $fieldcode ?>_<?php echo $index ?>">
				<input type="radio" name="cform[radio][<?php echo $fieldcode; ?>]" value="<?php echo $option['value']; ?>"
					id="cform_radio_<?php echo $fieldcode ?>_<?php echo $index ?>"
				<?php if ($required): ?>
					class="required validate-atLeastOneRadio"
					required
				<?php endif; ?>
				<?php if ($option['selected']) : ?>
					checked="checked"
				<?php endif; ?>
				/> <?php echo $option['text']; ?>
			</label>
			<?php endforeach; ?>
		<?php endif; ?>
		<button class="btn btn-danger" onclick="jQuery('input[name=\'cform[radio][<?php echo $fieldcode; ?>]\']').prop('checked', false);return false;"><?php echo JText::_('JCLEAR');?></button>
	<?php endif; ?>
</div>
