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
$data		= $displayData['data'];

RHelperAsset::load('bootstrap-slider.min.js', 'com_reditem');
RHelperAsset::load('bootstrap-slider.min.css', 'com_reditem');
?>

<?php if (empty($data['sliderOrientation'])) : ?>
<style type="text/css">
	#jform_fields_range_<?php echo $fieldcode; ?>Slider .slider-selection { background: <?php echo $data['backgroundColor']; ?>; }
	#jform_fields_range_<?php echo $fieldcode; ?>Slider .slider-handle { background: <?php echo $data['selectionColor']; ?>; }
</style>
<?php endif; ?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#jform_fields_range_<?php echo $fieldcode; ?>').slider({
				formater: function(value) {
					var returnValue = "";
					<?php if (!empty($data['tooltip'])) : ?>
					returnValue = "<?php echo $data['tooltip']; ?> ";
					<?php endif; ?>
					returnValue += value;

					return returnValue;
				},
				<?php if (!empty($data['tooltipDisplay'])) : ?>
				tooltip: 'always',
				<?php endif; ?>
				<?php if (!empty($data['sliderOrientation'])) : ?>
				reversed : true,
				<?php endif; ?>
			});
		});
	})(jQuery);
</script>

<div class="reditem_customfield_range">
	<input class="jform_fields_range_<?php echo $fieldcode; ?>" type="text" name="jform[fields][range][<?php echo $fieldcode; ?>]" id="jform_fields_range_<?php echo $fieldcode; ?>"
		data-slider-id="jform_fields_range_<?php echo $fieldcode; ?>Slider"
		data-slider-min="<?php echo $data['min']; ?>"
		data-slider-max="<?php echo $data['max']; ?>"
		data-slider-step="<?php echo $data['step']; ?>"
		data-slider-value="<?php echo $value; ?>"
		data-slider-handle="<?php echo $data['pointStyle']; ?>"
		data-slider-orientation="<?php echo $data['sliderOrientation']; ?>"
	    value="<?php echo $value; ?>"
	/>
</div>
