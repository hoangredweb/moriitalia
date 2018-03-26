<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode	= $displayData['fieldcode'];
$value		= $displayData['value'];
$attributes	= $displayData['attributes'];
?>

<div class="reditem_customfield_url">
	<p>
		<input type="text"
			class="input-xlarge"
			name="jform[fields][url][<?php echo $fieldcode; ?>][link]"
			id="jform_fields_url_<?php echo $fieldcode; ?>_link"
			value="<?php echo $value['link']; ?>"
			placeholder="<?php echo JText::_('COM_REDITEM_FIELD_URL_LINK'); ?>"
			<?php echo $attributes; ?> />
	</p>
	<p>
		<input type="text"
			class="input-xlarge"
			name="jform[fields][url][<?php echo $fieldcode; ?>][title]"
			id="jform_fields_url_<?php echo $fieldcode; ?>_title"
			value="<?php echo $value['title']; ?>"
			placeholder="<?php echo JText::_('COM_REDITEM_FIELD_URL_TITLE'); ?>"
			<?php echo $attributes; ?> />
	</p>
	<!-- Target -->
	<p>
		<select
			name="jform[fields][url][<?php echo $fieldcode; ?>][target]"
			class=""
			id="jform_fields_url_<?php echo $fieldcode; ?>_target"
		>
			<option value="_blank" <?php echo ($value['target'] == '_blank') ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_REDITEM_FIELD_URL_LINK_BLANK');?></option>
			<option value="_self" <?php echo ($value['target'] == '_self') ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_REDITEM_FIELD_URL_LINK_SELF');?></option>
			<option value="_parent" <?php echo ($value['target'] == '_parent') ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_REDITEM_FIELD_URL_LINK_PARENT');?></option>
			<option value="_top" <?php echo ($value['target'] == '_top') ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_REDITEM_FIELD_URL_LINK_TOP');?></option>
		</select>
	</p>
</div>
