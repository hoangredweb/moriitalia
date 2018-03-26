<?php
/**
 * @package     RedITEM.Layouts
 * @subpackage  Customfields.Multitextarea
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode  = $displayData['fieldcode'];
$data       = $displayData['data'];
$attributes = $displayData['attributes'];
$name       = $displayData['name'];
$value      = $displayData['value'];
$type       = $displayData['type'];
$i          = 1;
?>
<script type="text/javascript">
	// On delete button hit
	function deleteTextarea(textNo)
	{
		var trs = jQuery('.texts tr').length;

		if (trs == 2)
		{
			jQuery('.textslist').html('<div class="alert alert-warning"><?php echo JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_NO_TEXT_TO_SHOW'); ?></div>');
		}
		else
		{
			var tr = jQuery('#text-' + textNo);
			tr.remove();
		}

		textareaUpdate();
	}

	// On new button hit
	function addNewTextarea()
	{
		var table    = jQuery('.texts');
		var next     = 1;
		var addTable = 0;
		var typeId   = <?php echo $type->id; ?>;

		if (table.length)
		{
			next = jQuery('.texts tr').length;
		}
		else
		{
			addTable = 1;
		}

		jQuery.ajax({
			url : 'index.php?option=com_reditem&task=item.ajaxCustomfieldMultitextareaAddTextarea',
			type: 'POST',
			data: {'typeId' : typeId, 'addTable' : addTable, 'rowNo' : next, '<?php echo JSession::getFormToken(); ?>' : 1},
			dataType: 'html',
			beforeSend : function(xhr) {
				if (addTable == 1)
				{
					jQuery('.textslist').html('');
				}
			}
		}).done(function (data) {
			if (addTable == 1)
			{
				jQuery('.textslist').html(data);
			}
			else
			{
				jQuery('.textslist tr:last').after(data);
			}
		});
	}

	function textareaUpdate()
	{
		var count = jQuery('.texts tr').length;
		var texts = [];
		var vals  = null;
		var userId = '';
		var content  = '';

		jQuery('.texts tr').each(function() {
			userId = jQuery(this).find('.text-userid').val();
			content = jQuery(this).find('.text-content').val();

			if (userId != null && content != null)
			{
				texts.push(userId + '|' + content);
			}
		});

		jQuery('#jform_fields_multitextarea_<?php echo $fieldcode; ?>').val('');

		<?php if (strpos($attributes, 'required')) : ?>
		if (texts.length > 0)
		{
			jQuery('#jform_fields_multitextarea_<?php echo $fieldcode; ?>').val(JSON.stringify(texts));
		}
		<?php else : ?>
		jQuery('#jform_fields_multitextarea_<?php echo $fieldcode; ?>').val(JSON.stringify(texts));
		<?php endif;?>
	}
</script>
<div class="reditem_customfield_multitextarea" <?php echo $attributes; ?>>
	<div class="textslist">
		<?php if (empty($data)) : ?>
			<div class="alert alert-warning">
				<?php echo JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_NO_TEXT_TO_SHOW'); ?>
			</div>
		<?php else : ?>
			<table class="table table-bordered texts">
				<thead>
				<tr>
					<th><?php echo JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_USER');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_CONTENT');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_ACTIONS');?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $text) :?>
					<?php
					$text['textNo'] = $i;
					$i++;
					echo ReditemHelperLayout::render($type, 'customfields.multitextarea.textarea', $text, array('component' => 'com_reditem'));
					?>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<div class="btn-toolbar">
		<button class="btn btn-success" onclick="addNewTextarea(); return false;">
			<i class="icon icon-plus-sign"></i>
			<span><?php echo JText::_('JTOOLBAR_NEW');?></span>
		</button>
	</div>
	<input type="hidden" name="jform[fields][multitextarea][<?php echo $fieldcode; ?>]" id="jform_fields_multitextarea_<?php echo $fieldcode; ?>" value='<?php echo $value; ?>' <?php echo $attributes; ?>/>
</div>
