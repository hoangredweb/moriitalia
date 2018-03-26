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
$options            = $displayData['options'];
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName        = 'filter_category[' . $category->id . ']';
$filterId          = 'filter_related_' . $category->id;
$filterTemporaryId = $filterId . '_tmp';
/** Filter stuff - END **/
?>

<select
	name="<?php echo $filterName; ?>"
	id="<?php echo $filterId; ?>"
	class="chosen reditemFilterRelated"
	onChange="javascript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo JText::_('JALL') . ' ' . $category->title; ?></option>
	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $option) : ?>
			<option value="<?php echo $option['value']; ?>"
				<?php if (!empty($filterValue) && ($filterValue == $option['value'])) : ?>
					selected="selected"
				<?php endif; ?>
			>
				<?php echo $option['text']; ?>
			</option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
<select
	id="<?php echo $filterTemporaryId; ?>"
	class="hidden"
	onChange="javascript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo JText::_('JALL') . ' ' . $category->title; ?></option>
	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $option) : ?>
			<option value="<?php echo $option['value']; ?>"><?php echo $option['text']; ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
