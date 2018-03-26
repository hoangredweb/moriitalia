<?php
/**
 * @package     RedITEM.Layouts
 * @subpackage  Customfields.Tasklist
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
	function deleteTask(taskNo)
	{
		var trs = jQuery('.tasks tr').length;

		if (trs == 2)
		{
			jQuery('.taskslist').html('<div class="alert alert-warning"><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_NO_TASK_TO_SHOW'); ?></div>');
		}
		else
		{
			var tr = jQuery('#task-' + taskNo);
			tr.remove();
		}
	}

	// On new button hit
	function addNewTask()
	{
		var table    = jQuery('.tasks');
		var next     = 1;
		var addTable = 0;
		var typeId   = <?php echo $type->id; ?>;

		if (table.length)
		{
			next = jQuery('.tasks tr').length;
		}
		else
		{
			addTable = 1;
		}

		jQuery.ajax({
			url : 'index.php?option=com_reditem&task=item.ajaxCustomfieldTasklistAddTask',
			type: 'POST',
			data: {'typeId' : typeId, 'addTable' : addTable, 'rowNo' : next, '<?php echo JSession::getFormToken(); ?>' : 1},
			dataType: 'html',
			beforeSend : function(xhr) {
				if (addTable == 1)
				{
					jQuery('.taskslist').html('');
				}
			}
		}).done(function (data) {
			if (addTable == 1)
			{
				jQuery('.taskslist').html(data);
			}
			else
			{
				jQuery('.taskslist tr:last').after(data);
			}
		});
	}

	// On done button hit
	function changeTaskStatus(taskNo)
	{
		var input  = jQuery('#task-' + taskNo + '-done');
		var button = jQuery('#task-' + taskNo + '-done-btn');

		if (input.val() == 1)
		{
			// Mark task as not done
			jQuery('#task-' + taskNo).removeClass('success');
			button.find('i').removeClass('icon-check').addClass('icon-check-empty');
			button.find('span').html('<?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_NOT_DONE');?>');
			input.val('0');
		}
		else
		{
			// Mark task as done
			jQuery('#task-' + taskNo).addClass('success');
			button.find('i').removeClass('icon-check-empty').addClass('icon-check');
			button.find('span').html('<?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_DONE');?>');
			input.val('1');
		}
	}

	// On will be paid button hit
	function willBePaid(taskNo)
	{
		var input  = jQuery('#task-' + taskNo + '-paid');
		var button = jQuery('#task-' + taskNo + '-paid-btn');

		if (input.val() == 1)
		{
			// Mark task as won't be paid
			button.find('i').removeClass('icon-check').addClass('icon-check-empty');
			button.find('span').html('<?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?>');
			input.val('0');
		}
		else
		{
			// Mark task as will be paid
			button.find('i').removeClass('icon-check-empty').addClass('icon-check');
			button.find('span').html('<?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?>');
			input.val('1');
		}
	}

	// Override submit form for adding tasks change
	var originalSubmit = Joomla.submitbutton;
	Joomla.submitbutton = function (task) {
		var count = jQuery('.tasks tr').length;
		var tasks = [];
		var vals  = null;
		var title = '';
		var desc  = '';
		var done  = '';
		var paid  = '';

		jQuery('#tasklist_<?php echo $fieldcode; ?> tbody tr').each(function(){
			title = jQuery(this).find('.task-title').val();
			desc = jQuery(this).find('.task-desc').val();
			done = jQuery(this).find('.task-done').val();
			paid = jQuery(this).find('.task-paid').val();

			if (title != null && title != '')
			{
				tasks.push(title + '|' + desc + '|' + done + '|' + paid);
			}
		});

		jQuery('#jform_fields_tasklist_<?php echo $fieldcode; ?>').val('');

		<?php if (strpos($attributes, 'required')) : ?>
		if (tasks.length > 0)
		{
			jQuery('#jform_fields_tasklist_<?php echo $fieldcode; ?>').val(JSON.stringify(tasks));
		}
		<?php else : ?>
		jQuery('#jform_fields_tasklist_<?php echo $fieldcode; ?>').val(JSON.stringify(tasks));
		<?php endif;?>
		originalSubmit(task);
	};
</script>
<div class="reditem_customfield_tasklist" <?php echo $attributes; ?>>
	<div class="taskslist" id="tasklist_<?php echo $fieldcode; ?>">
		<?php if (empty($data)) : ?>
			<div class="alert alert-warning">
				<?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_NO_TASK_TO_SHOW'); ?>
			</div>
		<?php else : ?>
			<table class="table table-bordered tasks">
				<thead>
				<tr>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TITLE');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_DESCRIPTION');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_ACTIONS');?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $task) :?>
					<?php
					$task['taskNo'] = $i;
					$i++;
					echo ReditemHelperLayout::render($type, 'customfields.tasklist.task', $task, array('component' => 'com_reditem'));
					?>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<div class="btn-toolbar">
		<button class="btn btn-success" onclick="addNewTask(); return false;">
			<i class="icon icon-plus-sign"></i>
			<span><?php echo JText::_('JTOOLBAR_NEW');?></span>
		</button>
	</div>
	<input type="hidden" name="jform[fields][tasklist][<?php echo $fieldcode; ?>]" id="jform_fields_tasklist_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?>/>
</div>
