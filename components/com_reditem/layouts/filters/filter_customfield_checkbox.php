<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$category           = $displayData['category'];
$field              = $displayData['field'];
$options            = $displayData['options'];
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

if (empty($category->id))
{
	$category->id = 0;
}

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName = 'filter_customfield[' . $field->id . '][]';
$inputId    = 'filterCustomfieldCheckbox_' . $category->id . '_' . $field->id;
/** Filter stuff - END **/
?>

<?php if (!empty($options)) : ?>
	<?php foreach ($options as $option) : ?>
		<?php $inputId .= '_' . $field->id; ?>
		<label for="<?php echo $inputId; ?>" id="<?php echo $inputId; ?>-lbl">
			<input type="checkbox"
				name="<?php echo $filterName; ?>"
				id="<?php echo $inputId; ?>"
				value="<?php echo $option->value; ?>"
				<?php if (!empty($filterValue) && in_array($option->value, $filterValue)) : ?>
				checked
				<?php endif; ?>
				onclick="javascript:<?php echo $javascriptCallback; ?>();" />
				<?php echo $option->text; ?>
		</label>
	<?php endforeach; ?>
<?php endif; ?>
