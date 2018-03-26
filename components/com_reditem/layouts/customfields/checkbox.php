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
$name        = $displayData['name'];
$data        = $displayData['data'];
$attributes  = $displayData['attributes'];
$renderStyle = $displayData['renderStyle'];
$checkAll    = $displayData['checkAll'];
JHTML::_('behavior.formvalidation');

if ($renderStyle == 'jquery')
{
	RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
	RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
}

$class = '';

if ($attributes['required'])
{
	$class = "validate-atLeastOneCheckbox";
}
?>

<script type="text/javascript">
	<?php if ($attributes['required']) : ?>
	(function($){
		$(document).ready(function(){
			var formValidate<?php echo $fieldcode; ?> = false;

			document.formvalidator.setHandler('atLeastOneCheckbox', function(value) {
				var obj  = $('input[type="checkbox"][value="' + value + '"]');
				var objs = $('input[type="checkbox"][name="' + obj.attr('name') + '"]:checked');

				if (objs.length > 0)
					formValidate<?php echo $fieldcode; ?> = true;
				else
					formValidate<?php echo $fieldcode; ?> = false;

				return formValidate<?php echo $fieldcode; ?>;
			});
		});
	})(jQuery);
	<?php endif; ?>
	<?php if ($attributes['show_checkall']) : ?>
		(function($){
			$(document).ready(function(){
				$('#cform_checkbox_<?php echo $fieldcode; ?>_all').click(function () {
					var checked = this.checked;
					<?php foreach ($data as $index => $option) : ?>
							$('#cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>').prop('checked', checked);
							<?php if ($renderStyle == 'jquery') : ?>
								$('#cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>').button('refresh');
							<?php endif; ?>
					<?php endforeach; ?>
					<?php if ($renderStyle == 'bootstrap') : ?>
					$('#reditem_customfield_checkbox_<?php echo $fieldcode; ?> label').each(function(index){
						if (checked)
							$(this).addClass('active').addClass('btn-success');
						else
							$(this).removeClass('active').removeClass('btn-success');
					});
					<?php endif; ?>
				});

				var $checkboxes = $('input[name="cform[checkbox][<?php echo $fieldcode; ?>][]"]');
			    $checkboxes.change(function(){
			        var checked = $checkboxes.filter(':checked').length;
			        var all = (checked == <?php echo count($data); ?>);

		        	$('#cform_checkbox_<?php echo $fieldcode; ?>_all').prop('checked', all);
		        	<?php if ($renderStyle == 'jquery') : ?>
		        		$('#cform_checkbox_<?php echo $fieldcode; ?>_all').button('refresh');
		        	<?php elseif ($renderStyle == 'bootstrap') : ?>

		        		var $lbl = $('#cform_checkbox_<?php echo $fieldcode; ?>_all-lbl');

		        		if (all)
							$lbl.addClass('active').addClass('btn-success');
						else
							$lbl.removeClass('active').removeClass('btn-success');
		        	<?php endif; ?>
			    });
			});
		})(jQuery);
	<?php endif; ?>

	<?php if ($renderStyle == 'bootstrap') : ?>
	(function($){
		$(document).ready(function(){
			$('#reditem_customfield_checkbox_<?php echo $fieldcode; ?> label').each(function(index){
				if ($(this).find('input[type="checkbox"]').is(':checked'))
					$(this).addClass('active').addClass('btn-success');
				else
					$(this).removeClass('active').removeClass('btn-success');

				$(this).click(function(){
					if ($(this).find('input[type="checkbox"]').is(':checked'))
						$(this).addClass('active').addClass('btn-success');
					else
						$(this).removeClass('active').removeClass('btn-success');
				});
			});
		});
	})(jQuery);
	<?php elseif ($renderStyle == 'jquery') : ?>
	(function($){
		$(document).ready(function() {
			<?php if ($attributes['show_checkall']) : ?>
				$('#cform_checkbox_<?php echo $fieldcode; ?>_all').button();
			<?php endif; ?>
			<?php foreach ($data as $index => $option) : ?>
				$('#cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>').button();
			<?php endforeach; ?>
		});
	})(jQuery);
	<?php endif; ?>
</script>

<div class="reditem_customfield_checkbox" id="reditem_customfield_checkbox_<?php echo $fieldcode; ?>">
	<?php if (empty($data)) : ?>
	<div class="alert alert-warning">
		<?php echo JText::sprintf('COM_REDITEM_FIELD_CHECKBOX_PLEASE_ADD_AN_OPTION', $name); ?>
	</div>
	<?php else : ?>
		<?php if ($renderStyle == 'bootstrap') : ?>
			<label class="btn" id="cform_checkbox_<?php echo $fieldcode; ?>_all-lbl">
				<input
					type="checkbox"
					id="cform_checkbox_<?php echo $fieldcode; ?>_all"
					class="hide"
					<?php if ($checkAll) : ?>
					checked="checked"
					<?php endif; ?>
					/>
				<?php echo JText::_('COM_REDITEM_FIELD_CHECKBOX_CHECK_ALL') ?>
			</label>
			<?php foreach ($data as $index => $option) : ?>
				<label class="btn">
					<input
						type="checkbox"
						name="cform[checkbox][<?php echo $fieldcode; ?>][]"
						id="cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
						value="<?php echo $option['value']; ?>"
						<?php if ($attributes['required']) : ?>
						class="hide validate-atLeastOneCheckbox"
						<?php else: ?>
						class="hide"
						<?php endif; ?>
						<?php if ($option['checked']) : ?>
						checked="checked"
						<?php endif; ?> />
					<?php echo $option['text']; ?>
				</label>
			<?php endforeach; ?>
		<?php elseif ($renderStyle == 'jquery') : ?>
			<?php if ($attributes['show_checkall']): ?>
				<input
					type="checkbox"
					id="cform_checkbox_<?php echo $fieldcode; ?>_all"
					<?php if ($checkAll) : ?>
					checked="checked"
					<?php endif; ?>
				/>
				<label class="input-large" for="cform_checkbox_<?php echo $fieldcode; ?>_all">
					<?php echo JText::_('COM_REDITEM_FIELD_CHECKBOX_CHECK_ALL') ?>
				</label>
			<?php endif; ?>
			<?php foreach ($data as $index => $option) : ?>
				<input
					type="checkbox"
					name="cform[checkbox][<?php echo $fieldcode; ?>][]"
					id="cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
					value="<?php echo $option['value']; ?>"
					<?php if ($attributes['required']) : ?>
						class="validate-atLeastOneCheckbox"
					<?php endif; ?>
					<?php if ($option['checked']) : ?>
					checked="checked"
					<?php endif; ?>
				/>
				<label class="input-large" for="cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>">
					<?php echo $option['text']; ?>
				</label>
			<?php endforeach; ?>
		<?php else : ?>
			<?php if ($attributes['show_checkall']): ?>
			<label class="lbl">
				<input
					type="checkbox"
					id="cform_checkbox_<?php echo $fieldcode; ?>_all"
					<?php if ($checkAll) : ?>
						checked="checked"
					<?php endif; ?>
				/> <?php echo JText::_('COM_REDITEM_FIELD_CHECKBOX_CHECK_ALL') ?>
			</label>
			<?php endif; ?>
			<?php foreach ($data as $index => $option) : ?>
				<label class="lbl">
					<input
						type="checkbox"
						name="cform[checkbox][<?php echo $fieldcode; ?>][]"
						id="cform_checkbox_<?php echo $fieldcode; ?>_<?php echo $index; ?>"
						value="<?php echo $option['value']; ?>"
						<?php if ($attributes['required']) : ?>
							class="validate-atLeastOneCheckbox"
						<?php endif; ?>
						<?php if ($option['checked']) : ?>
						checked="checked"
						<?php endif; ?>
					/> <?php echo $option['text']; ?>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endif; ?>
	<input type="hidden" name="cform[checkbox][<?php echo $fieldcode; ?>][]" value="" />
</div>