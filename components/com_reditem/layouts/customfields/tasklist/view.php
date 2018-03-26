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
$required   = (boolean) $displayData['required'];
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
	function addNewTask(scroll)
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
			data: {'typeId' : typeId, 'addTable' : addTable, 'rowNo' : next},
			dataType: 'html',
			beforeSend : function(xhr) {
				if (addTable == 1)
				{
					jQuery('.taskslist').html('');
				}
			}
		}).done(function (data) {
			var temp = null;

			if (addTable == 1)
			{
				jQuery('.taskslist').html(data);
				temp = jQuery('#task-1-title');
			}
			else
			{
				jQuery('.taskslist tr:last').after(data);
				var count = jQuery('.tasks tr').length - 1;
				temp = jQuery('#task-' + count + '-title');
			}

			if (scroll)
			{
				jQuery('html, body').delay(2500).animate({
					scrollTop: temp.offset().top
				}, 2000, function() {
					temp.focus();
				});
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

	// Check if there is any task in the list
	function checkTaskList()
	{
		var tasks = jQuery('#cform_tasklist_<?php echo $fieldcode; ?>').val();

		if (tasks.length == 0 || tasks == '[]' || tasks == undefined)
		{
			return false;
		}

		return true;
	}

	// Override submit form for adding tasks change
	var originalSubmit = Joomla.submitbutton;
	Joomla.submitbutton = function (task) {
		if (task.indexOf('cancel') == -1 && task.indexOf('close') == -1)
		{
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

			jQuery('#cform_tasklist_<?php echo $fieldcode; ?>').val(JSON.stringify(tasks));
			<?php if ($required) : ?>

			if (!checkTaskList())
			{
				jQuery('#cform_tasklist_<?php echo $fieldcode; ?>').val('');
				var errMsg       = '<p><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_VALUE_REQUIRED');?></p>';
				var msgContainer = jQuery('#system-message-container');

				if (msgContainer.find('#system-message').length > 0)
				{
					var msg = msgContainer.find('#system-message');
					msg.append(errMsg);
					msg.removeClass().addClass('alert alert-error');
				}
				else
				{
					msgContainer.html('<div id="system-message" class="alert alert-error">' + errMsg + '</div>');
				}

				if (count > 1)
				{
					var first = jQuery(jQuery('.taskslist tr')[1]).find('input:first');
					jQuery('html, body').delay(2500).animate({
						scrollTop: first.offset().top
					}, 2000, function() {
						first.focus();
					});
				}
				else
				{
					addNewTask(true);
				}

				return false;
			}
			<?php endif;?>
		}

		originalSubmit(task);
	};
</script>
<div class="reditem_customfield_tasklist" <?php echo $attributes; ?>>
	<div class="taskslist" id="tasklist_<?php echo $fieldcode; ?>">
		<table class="table table-bordered tasks">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TITLE');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_DESCRIPTION');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($data)) : ?>
				<?php echo ReditemHelperLayout::render($type, 'customfields.tasklist.task', array('taskNo' => 1), array('component' => 'com_reditem'));?>
			<?php else : ?>
				<?php foreach ($data as $task) :?>
					<?php
					$task['taskNo'] = $i;
					$i++;
					echo ReditemHelperLayout::render($type, 'customfields.tasklist.task', $task, array('component' => 'com_reditem'));
					?>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<div class="btn-toolbar">
		<button class="btn btn-success" onclick="addNewTask(false); return false;">
			<i class="icon icon-plus-sign"></i>
			<span><?php echo JText::_('JTOOLBAR_NEW');?></span>
		</button>
	</div>
	<input type="hidden" name="cform[tasklist][<?php echo $fieldcode; ?>]" id="cform_tasklist_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?>/>
</div>
