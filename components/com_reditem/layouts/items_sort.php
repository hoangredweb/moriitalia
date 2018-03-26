<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$category      = $displayData['category'];
$jsCallback    = $displayData['jsCallback'];
$itemsSort     = $displayData['itemsSort'];
$itemsSortList = $displayData['itemsSortList'];
$itemsDest     = $displayData['itemsDest'];
$itemsDestList = $displayData['itemsDestList'];
?>

<select name="items_sort" class="select" onchange="javascript:<?php echo $jsCallback; ?>();">
	<?php if (!empty($itemsSortList)) : ?>
		<?php foreach ($itemsSortList as $option) : ?>
			<option value="<?php echo $option['value']; ?>"
				<?php if ($itemsSort == $option['value']) : ?>
				selected="selected"
				<?php endif; ?> >
					<?php echo $option['text']; ?>
				</option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>

<select name="items_dest" class="select" onchange="javascript:<?php echo $jsCallback; ?>();">
	<?php if (!empty($itemsDestList)) : ?>
		<?php foreach ($itemsDestList as $option) : ?>
			<option value="<?php echo $option['value']; ?>"
				<?php if ($itemsDest == $option['value']) : ?>
				selected="selected"
				<?php endif; ?> >
					<?php echo $option['text']; ?>
				</option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
