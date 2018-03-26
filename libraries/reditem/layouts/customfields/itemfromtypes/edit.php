<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

RHelperAsset::load('select2/select2.min.js', 'com_reditem');
RHelperAsset::load('select2/select2.min.css', 'com_reditem');

$fieldcode  = $displayData['fieldcode'];
$data       = $displayData['data'];
$attributes = $displayData['attributes'];
$default    = $displayData['default'];
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;
$def        = '';

if ($isNew && !empty($default))
{
	$def = '\'val\', \'' . $default . '\'';
}
?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#itemfromtypes_<?php echo $fieldcode; ?>').select2();
			<?php if (!empty($def)) : ?>
			$('#itemfromtypes_<?php echo $fieldcode; ?>').select2(<?php echo $def; ?>);
			<?php endif; ?>
		});
	})(jQuery);
</script>

<div class="reditem_customfield_itemfromtypes">
	<select id="itemfromtypes_<?php echo $fieldcode; ?>" name="jform[fields][itemfromtypes][<?php echo $fieldcode; ?>][]" id="jform_fields_itemfromtypes_<?php echo $fieldcode; ?>" <?php echo $attributes; ?>>
		<?php if (!empty($data)) : ?>
			<?php foreach ($data as $key => $item) : ?>
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
