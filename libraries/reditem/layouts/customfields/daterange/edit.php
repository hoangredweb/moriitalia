<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode	= $displayData['fieldcode'];
$value		= $displayData['value'];
$attributes	= $displayData['attributes'];
$default    = $displayData['default'];
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;

if ($isNew && !empty($default))
{
	$tmp   = explode(' - ', $default);
	$value = array('start' => $tmp[0], 'end' => $tmp[1]);
}

$class = $attributes['class'];

RHelperAsset::load('daterangepicker/moment.min.js', 'com_reditem');
RHelperAsset::load('daterangepicker/daterangepicker.min.js', 'com_reditem');
RHelperAsset::load('daterangepicker/daterangepicker-bs2.min.css', 'com_reditem');

$textValue = '';
$format    = ReditemHelperCustomfield::convertDateFormat($attributes['format'], 'php', 'moment');
$startDate = (!empty($value['start'])) ? ReditemHelperSystem::getDateWithTimezone($value['start'])->format($format) : '';
$endDate   = (!empty($value['end'])) ? ReditemHelperSystem::getDateWithTimezone($value['end'])->format($format) : '';

if (!empty($startDate) && !empty($endDate))
{
	$textValue = $startDate . ' - ' . $endDate;
}
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			<?php if (!empty($attributes['dateLimit'])): ?>
			<?php $dateLimit = explode(' ', $attributes['dateLimit']); ?>
			var dateLimit<?php echo $fieldcode; ?> = moment.duration({'<?php echo (string) $dateLimit[1] ?>' : <?php echo (int) $dateLimit[0] ?>});
			<?php endif; ?>
			var format<?php echo $fieldcode; ?> = '<?php echo !empty($attributes['format']) ? $attributes['format'] : ''; ?>';

			$('#jform_fields_daterange_<?php echo $fieldcode; ?>').daterangepicker(
				{
					format: format<?php echo $fieldcode; ?>,
					<?php if (!empty($attributes['showPreset'])) : ?>
					ranges: {
						'Today': [moment(), moment()],
						'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
						'Last 7 Days': [moment().subtract(6, 'days'), moment()],
						'Last 30 Days': [moment().subtract(29, 'days'), moment()],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
					<?php endif; ?>
					<?php if (!empty($attributes['minDate']) && empty($attributes['showPreset'])) : ?>
					minDate: '<?php echo $attributes['minDate'] ?>',
					<?php endif; ?>
					<?php if (!empty($attributes['maxDate']) && empty($attributes['showPreset'])) : ?>
					maxDate: '<?php echo $attributes['maxDate'] ?>',
					<?php endif; ?>
					<?php if (!empty($startDate)) : ?>
					startDate: '<?php echo $startDate; ?>',
					<?php endif; ?>
					<?php if (!empty($endDate)) : ?>
					endDate: '<?php echo $endDate;  ?>',
					<?php endif; ?>
					<?php if (!empty($attributes['dateLimit']) && empty($attributes['showPreset'])): ?>
					dateLimit: dateLimit<?php echo $fieldcode; ?>,
					<?php endif; ?>
					showDropdowns: <?php echo !empty($attributes['showDropdowns']) ? 'true' : 'false' ?>,
					showWeekNumbers: <?php echo !empty($attributes['showWeekNumbers']) ? 'true' : 'false' ?>,
					timePicker: <?php echo !empty($attributes['timePicker']) ? 'true' : 'false' ?>,
					timePickerIncrement: <?php echo (int) !empty($attributes['timePickerIncrement'])?$attributes['timePickerIncrement']:0 ?>,
					timePicker12Hour: <?php echo !empty($attributes['timePicker12HourFormat']) ? 'true' : 'false' ?>,
					timePickerSeconds: <?php echo !empty($attributes['timePickerSeconds']) ? 'true' : 'false' ?>,
				},
				function(start, end, label) {
					$('#jform_fields_daterange_<?php echo $fieldcode; ?>_start').val(start.format(moment.ISO_8601()));
					$('#jform_fields_daterange_<?php echo $fieldcode; ?>_end').val(end.format(moment.ISO_8601()));
				}
			);
			$('#jform_fields_daterange_<?php echo $fieldcode; ?>_icon').on('click', function(event) {
				event.preventDefault();
				$('#jform_fields_daterange_<?php echo $fieldcode; ?>').click();
			});
		});
	})(jQuery);
</script>

<div class="reditem_customfield_daterange" id="jform_fields_daterange_<?php echo $fieldcode; ?>_wrapper">
	<div class="input-append">
		<input id="jform_fields_daterange_<?php echo $fieldcode; ?>" type="text" class="input input-xlarge <?php echo $class ?>"
			   value="<?php echo $textValue ?>">
		<span class="add-on" id="jform_fields_daterange_<?php echo $fieldcode; ?>_icon">
			<i class="icon-calendar"></i>
		</span>
	</div>
	<input type="hidden" name="jform[fields][daterange][<?php echo $fieldcode; ?>][start]" id="jform_fields_daterange_<?php echo $fieldcode; ?>_start" value="<?php echo $value['start']; ?>" />
	<input type="hidden" name="jform[fields][daterange][<?php echo $fieldcode; ?>][end]" id="jform_fields_daterange_<?php echo $fieldcode; ?>_end" value="<?php echo $value['end']?>" />
</div>
