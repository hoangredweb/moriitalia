<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$config             = $displayData['config'];
$javascriptCallback = $displayData['javascriptCallback'];
$value              = $displayData['value'];

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName = 'filter_rating';
/** Filter stuff - END **/

$stars = (int) $config['stars'];
$step  = (float) $config['step'];
$count = (int) ($stars / $step);
?>

<select class="reditemFilterItemRating select2" name="<?php echo $filterName; ?>" onChange="javacript:<?php echo $javascriptCallback; ?>();">
	<option value=""><?php echo JText::_('COM_REDITEM_ITEM_RATING_FILTER_SHOW_ALL'); ?></option>
	<?php for ($i = 1; $i <= $count; $i++) : ?>
		<?php if ($i === 1) : ?>
			<?php $text = JText::sprintf('COM_REDITEM_ITEM_RATING_FILTER_STAR', $i); ?>
		<?php else : ?>
			<?php $text = JText::sprintf('COM_REDITEM_ITEM_RATING_FILTER_STARS', $i); ?>
		<?php endif; ?>
		<?php $selected = ($i == $value) ? 'selected="selected"' : ''; ?>
		<option value="<?php echo $i; ?>" <?php echo $selected ?>><?php echo $text; ?></option>
	<?php endfor; ?>
</select>
