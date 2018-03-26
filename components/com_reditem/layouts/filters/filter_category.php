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
$filterName = 'filter_category[' . $category->id . ']';
/** Filter stuff - END **/
?>

<select name="<?php echo $filterName; ?>" class="chosen input-xlarge" onChange="javascript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo JText::_('JALL') . ' ' . $category->title; ?></option>
	<?php
	foreach ($subCategories as $subCategory) :
		$selected = '';
		if (!empty($filterValue) && ($subCategory->id == $filterValue)) :
			$selected = ' selected="selected"';
		endif;
		$text = str_repeat('<span class="gi">|&mdash;</span>', $subCategory->level - 1) . ' ' . $subCategory->title;
		$value = $subCategory->id;
	?>
		<option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $text; ?></option>
	<?php endforeach; ?>
</select>
