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
$subCategories      = $displayData['subCategories'];
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

/** Filter stuff - DO NOT CHANGE THIS **/
$inputId   = 'filter_category_radio_' . $category->id;
$inputName = 'filter_category[' . $category->id . ']';
/** Filter stuff - END **/
?>
<!-- Create first option -->
<label for="<?php echo $inputId; ?>" id="<?php echo $inputId; ?>-lbl" class="radio">
	<input type="radio"
		name="<?php echo $inputName; ?>"
		id="<?php echo $inputId; ?>"
		value="<?php echo $category->id; ?>"
		<?php if (empty($filterValue)) : ?>
		checked
		<?php endif; ?>
		onClick="javascript:<?php echo $javascriptCallback; ?>();" />
	<?php echo JText::_('JALL'); ?>
</label>

<?php if (!empty($subCategories)) : ?>
	<?php foreach ($subCategories as $subCategory) : ?>
		<?php $inputId .= $subCategory->id; ?>
		<label for="<?php echo $inputId; ?>" id="<?php echo $inputId; ?>-lbl" class="radio">
			<input type="radio"
				name="<?php echo $inputName; ?>"
				id="<?php echo $inputId; ?>"
				value="<?php echo $subCategory->id; ?>"
				onClick="javascript:<?php echo $javascriptCallback; ?>();" />
			<?php echo $subCategory->title; ?>
		</label>
	<?php endforeach; ?>
<?php endif; ?>
