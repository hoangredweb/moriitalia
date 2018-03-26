<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

JHtml::_('rjquery.select2', '.select2');

$fieldcode  = $displayData['fieldcode'];
$data       = $displayData['data'];
$attributes = $displayData['attributes'];
?>

<div class="reditem_customfield_itemfromtypes">
	<select class="select2" name="cform[itemfromtypes][<?php echo $fieldcode; ?>][]" id="cform_itemfromtypes_<?php echo $fieldcode; ?>" <?php echo $attributes; ?>>
		<?php if (!empty($data)) : ?>
			<?php foreach ($data as $key=>$item) : ?>
			<optgroup label="<?php echo $key; ?>">
				<?php foreach ($item as $option) : ?>
					<?php $selected = ($option['selected']) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $option['value']; ?>" <?php echo $selected; ?>>
						<?php echo $option['text']; ?>
					</option>
				<?php endforeach; ?>
			</optgroup>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</div>
