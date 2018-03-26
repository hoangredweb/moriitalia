<?php
/**
 * @package     RedITEM.Layouts
 * @subpackage  Customfields.Tasklist
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

if (empty($displayData))
{
	$taskNo      = 1;
	$title       = '';
	$description = '';
	$isDone      = 0;
	$willBePaid  = 0;
}
else
{
	if (isset($displayData['taskNo']) && !empty($displayData['taskNo']))
	{
		$taskNo = (int) $displayData['taskNo'];
	}
	else
	{
		$taskNo = 1;
	}

	if (isset($displayData['title']) && !empty($displayData['title']))
	{
		$title = (string) $displayData['title'];
	}
	else
	{
		$title = '';
	}

	if (isset($displayData['desc']) && !empty($displayData['desc']))
	{
		$description = (string) $displayData['desc'];
	}
	else
	{
		$description = '';
	}

	if (isset($displayData['done']) && !empty($displayData['done']))
	{
		$isDone = (int) $displayData['done'];
	}
	else
	{
		$isDone = 0;
	}

	if (isset($displayData['paid']) && !empty($displayData['paid']))
	{
		$willBePaid = (int) $displayData['paid'];
	}
	else
	{
		$willBePaid = 0;
	}
}

?>
<tr class="<?php if ($isDone) : echo 'success'; endif;?>" id="task-<?php echo $taskNo; ?>">
	<td>
		<input type="text" value="<?php echo $title;?>" class="task-vals-<?php echo $taskNo; ?> task-title" id="task-<?php echo $taskNo; ?>-title"/>
	</td>
	<td>
		<textarea class="task-vals-<?php echo $taskNo; ?> task-desc" id="task-<?php echo $taskNo; ?>-desc" cols="50" rows="20"><?php echo $description;?></textarea>
	</td>
	<td>
		<div class="btn-group">
			<button class="btn" id="task-<?php echo $taskNo; ?>-done-btn" onclick="changeTaskStatus(<?php echo $taskNo; ?>); return false;">
			<?php if ($isDone):?>
				<i class="icon icon-check"></i>
				<span><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_DONE');?></span>
			<?php else: ?>
				<i class="icon icon-check-empty"></i>
				<span><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_NOT_DONE');?></span>
			<?php endif; ?>
			</button>
			<button class="btn" id="task-<?php echo $taskNo; ?>-paid-btn" onclick="willBePaid(<?php echo $taskNo; ?>); return false;">
			<?php if ($willBePaid):?>
				<i class="icon icon-check"></i>
				<span><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?></span>
			<?php else: ?>
				<i class="icon icon-check-empty"></i>
				<span><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?></span>
			<?php endif; ?>
			</button>
			<input type="hidden" value="<?php echo $isDone; ?>" class="task-vals-<?php echo $taskNo; ?> task-done" id="task-<?php echo $taskNo; ?>-done"/>
			<input type="hidden" value="<?php echo $willBePaid; ?>" class="task-vals-<?php echo $taskNo; ?> task-paid" id="task-<?php echo $taskNo; ?>-paid"/>
			<button class="btn btn-danger" id="task-del-<?php echo $taskNo; ?>" onclick="deleteTask(<?php echo $taskNo; ?>); return false;">
				<i class="icon icon-trash"></i>
				<span><?php echo JText::_('JTOOLBAR_DELETE');?></span>
			</button>
		</div>
	</td>
</tr>