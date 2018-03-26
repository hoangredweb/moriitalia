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
?>

<div class="reditem_customfield_user">
	<select name="cform[user][<?php echo $fieldcode; ?>][]" id="cform_user_<?php echo $fieldcode; ?>" <?php echo $attributes; ?>>
		<?php if (!empty($data)) : ?>
			<?php foreach ($data as $option) : ?>
				<?php $selected = ($option['selected']) ? 'selected="selected"' : ''; ?>
				<option value="<?php echo $option['value']; ?>" <?php echo $selected; ?>><?php echo $option['text']; ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</div>
