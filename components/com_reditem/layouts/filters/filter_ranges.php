<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$field        = $displayData['field'];
$options      = $displayData['options'];
$defaultValue = $displayData['value'];
$topOption    = $displayData['topOption'];
$javascriptCallback = $displayData['jsCallback'];

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName = 'filter_ranges[' . $field->fieldcode . ']';
$filterId   = 'filter_ranges_' . $field->id;
/** Filter stuff - END **/
?>

<?php if (!empty($defaultValue)) : ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function () {
			$("select#<?php echo $filterId; ?>").select2("val", "<?php echo $defaultValue; ?>");
			$("select#<?php echo $filterId; ?>").change();
		});
	})(jQuery);
</script>
<?php endif; ?>

<select class="select2"
	name="<?php echo $filterName; ?>"
	id="<?php echo $filterId; ?>"
	onChange="javacript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo $topOption; ?></option>
	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $option) : ?>
			<option value="<?php echo $option['value']; ?>"><?php echo $option['text']; ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
