<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

// Load moment library
RHelperAsset::load('daterangepicker/moment.min.js', 'com_reditem');

$fieldcode	= $displayData['fieldcode'];
$value		= $displayData['value'];
$attributes	= $displayData['attributes'];
$default    = $displayData['default'];
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;

if ($isNew && !empty($default))
{
	$value = $default;
}

JHtml::_('rjquery.datepicker');

$showTimePicker = (boolean) $attributes['showTimePicker'];
$format         = (isset($attributes['altFormat']) && !empty($attributes['altFormat'])) ? $attributes['altFormat'] : 'yy-mm-dd';
$phpFormat      = ReditemHelperCustomfield::convertDateFormat($format, 'php', 'jquery');
$momentFormat   = ReditemHelperCustomfield::convertDateFormat($format, 'moment', 'jquery');
$class          = $attributes['class'];
$disablePast    = false;

if (isset($attributes['disable_past'])) :
	$disablePast = (boolean) $attributes['disable_past'];
endif;

$dateObject = null;
$dateValue  = '';
$dateFValue = '';
$timeValue  = '';

if ($showTimePicker) :
	RHelperAsset::load('jquery.ui.timepicker.min.js', 'com_reditem');
	RHelperAsset::load('jquery.ui.timepicker.min.css', 'com_reditem');

	$timeValue = '';
endif;

// If has value
if (!empty($value) && ($value != JFactory::getDbo()->getNullDate())) :
	$dateObject = ReditemHelperSystem::getDateWithTimezone($value);
	$dateFValue = $dateObject->format($phpFormat, true);
	$dateValue  = $dateObject->format('Y-m-d', true);
	$value      = $dateObject->format('Y-m-d H:i:s', true);

	if ($showTimePicker) :
		$timeValue = ($dateObject->format('H:i', true) == '00:00') ? '' : $dateObject->format('H:i', true);
	endif;
endif;
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#jform_fields_date_<?php echo $fieldcode; ?>').datepicker({
				dateFormat: 'yy-mm-dd',
				<?php if ($disablePast) : ?>
				<?php $now = ReditemHelperSystem::getDateWithTimezone(); ?>
				minDate: new Date(<?php echo $now->format('Y', true) ?>, <?php echo $now->format('m', true) - 1 ?>, <?php echo $now->format('d', true) ?>),
				<?php endif; ?>
				onSelect: function(dateValue, objectInstance)
				{
					// Call hook function if available
					if (typeof reditemCustomFieldDate<?php echo $fieldcode; ?>_beforeSelectValue != 'undefined') {
						dateValue = reditemCustomFieldDate<?php echo $fieldcode; ?>_beforeSelectValue(dateValue);
					}

					reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(dateValue);
					$('#<?php echo $fieldcode; ?>_tmp_date').val(dateValue);
					$(this).focus().focusout();
				},
				altFormat: "<?php echo $format;?>",
				altField: "#jform_fields_date_<?php echo $fieldcode; ?>",
			}).on('change', function(event) {
				event.preventDefault();

				var dateValue = $(this).val();
				var date = moment(dateValue, '<?php echo $momentFormat;?>');
				reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(date.format('YYYY-MM-DD'));
				var value = $('#jform_fields_date_<?php echo $fieldcode; ?>_value').val();

				if (!date.isValid())
				{
					$(this).val(moment().format('<?php echo $momentFormat;?>'));
					var today = moment().format('YYYY-MM-DD');
					reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(today);
					$('#<?php echo $fieldcode; ?>_tmp_date').val(today);
				}
				else
				{
					$('#<?php echo $fieldcode; ?>_tmp_date').val(date.format('YYYY-MM-DD'));
				}
			});

			$('#jform_fields_date_<?php echo $fieldcode; ?>_icon').click(function(event){
				event.preventDefault();
				$('#jform_fields_date_<?php echo $fieldcode; ?>').datepicker('show');
			});

			<?php if ($showTimePicker) : ?>
			$('#jform_fields_date_<?php echo $fieldcode; ?>_time').timepicker({
				showPeriodLabels: false,
				minutes: {
					interval: 5
				},
				showOn: 'both',
				button: $('#jform_fields_date_<?php echo $fieldcode; ?>_time_icon'),
				onSelect: function(time, instance)
				{
					time += ":00";
					var dateValue = $('#<?php echo $fieldcode; ?>_tmp_date').val();
					reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(dateValue);
				}
			})
			.on('change', function(event){
				event.preventDefault();
				var dateValue = $('#<?php echo $fieldcode; ?>_tmp_date').val();
				reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(dateValue);
			});
			<?php endif; ?>
		});

		function reditemCustomFieldDateProcess_<?php echo $fieldcode; ?>(dateValue)
		{
			// If date value is empty, stop function
			if (dateValue == '')
			{
				return;
			}

			var timeValue = '00:00:00';

			<?php if ($showTimePicker) : ?>
			// Process for time value
			var timeValueFromInput = $('#jform_fields_date_<?php echo $fieldcode; ?>_time').val();
			if (timeValueFromInput != '') timeValue = timeValueFromInput;
			<?php endif; ?>

			$('#jform_fields_date_<?php echo $fieldcode; ?>_value').val(dateValue + ' ' + timeValue);
		}
	})(jQuery);
</script>

<div class="reditem_customfield_date" id="jform_fields_date_<?php echo $fieldcode; ?>_wrapper">
	<div class="input-append">
		<input  id="jform_fields_date_<?php echo $fieldcode; ?>"
				type="text" class="input <?php echo $class ?>"
				value="<?php echo $dateFValue;?>"
				placeholder="<?php echo $format;?>"
		>
		<span class="add-on" id="jform_fields_date_<?php echo $fieldcode; ?>_icon">
			<i class="icon-calendar"></i>
		</span>
	</div>
	<?php if ($showTimePicker) : ?>
	<div class="input-append">
		<input id="jform_fields_date_<?php echo $fieldcode; ?>_time" type="text" class="input-small <?php echo $class ?>" value="<?php echo $timeValue; ?>">
		<span class="add-on" id="jform_fields_date_<?php echo $fieldcode; ?>_time_icon">
			<i class="icon-time"></i>
		</span>
	</div>
	<?php endif; ?>
	<input type="hidden" id="<?php echo $fieldcode; ?>_tmp_date" value="<?php echo $dateValue;?>" />
	<input type="hidden" name="jform[fields][date][<?php echo $fieldcode; ?>]" id="jform_fields_date_<?php echo $fieldcode; ?>_value" value="<?php echo $value; ?>" />
</div>
