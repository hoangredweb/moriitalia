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
$filterName = 'filter_customfield[' . $field->id . ']';
$filterId = 'filterCustomfieldSelect_' . $category->id . '_' . $field->id;
/** Filter stuff - END **/
?>

<select name="<?php echo $filterName; ?>" id="<?php echo $filterId; ?>" onChange="javascript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo JText::_('JALL'); ?> <?php echo $field->name; ?></option>
	<?php foreach ($options as $option): ?>
		<?php $selected = ($filterValue == $option->value) ? ' selected' : ''; ?>
	<option value="<?php echo $option->value; ?>" <?php echo $selected; ?> ><?php echo $option->text; ?></option>
	<?php endforeach; ?>
</select>
