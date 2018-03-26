<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode  = $displayData['fieldcode'];
$data       = $displayData['data'];
$attributes = $displayData['attributes'];
$name       = $displayData['name'];
?>

<div class="reditem_customfield_select">
	<?php if (empty($data)) : ?>
	<div class="alert alert-warning">
		<?php echo JText::sprintf('COM_REDITEM_FIELD_SELECTBOX_PLEASE_ADD_AN_OPTION', $name); ?>
	</div>
	<?php else : ?>
	<select name="cform[select][<?php echo $fieldcode; ?>][]" id="cform_select_<?php echo $fieldcode; ?>" <?php echo $attributes; ?> >
		<?php foreach ($data as $option) : ?>
			<?php $selected = ($option['selected']) ? 'selected="selected"' : ''; ?>
			<option value="<?php echo $option['value']; ?>" <?php echo $selected; ?>>
				<?php echo $option['text']; ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
</div>
