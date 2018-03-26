<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag  = $displayData['tag'];
$data = $displayData['data'];
$item = $displayData['item'];
?>

<?php if (!empty($data)) : ?>
	<div class="reditem_tasklist reditem_tasklist_<?php echo $tag->id; ?>">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TITLE');?></th>
					<th><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_DESCRIPTION');?></th>
					<th><?php echo JText::_('JSTATUS');?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($data as $row) : ?>
				<tr>
					<td><?php echo $row[0];?></td>
					<td><?php echo $row[1];?></td>
					<td>
					<?php if (isset($row[2]) && $row[2]) : ?>
						<span><i class="icon-check"></i><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_DONE');?></span><br />
					<?php else : ?>
						<span><i class="icon-check-empty"></i><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_DONE');?></span><br />
					<?php endif; ?>
					<?php if (isset($row[3]) && $row[3]) : ?>
						<span><i class="icon-check"></i><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?></span>
					<?php else : ?>
						<span><i class="icon-check-empty"></i><?php echo JText::_('COM_REDITEM_FIELD_TASKLIST_TASK_WILL_BE_PAID_BY_TENANT');?></span>
					<?php endif; ?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
