<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	}
	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		if (pressbutton == 'comments.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_COMMENTS_DELETE_COMMENTS")?>');
			if (r == true) form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_reditem&view=comments" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_comments_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr />
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="pagination-centered">
			<h3><?php echo JText::_('COM_REDITEM_NOTHING_TO_DISPLAY'); ?></h3>
		</div>
	</div>
	<?php else : ?>
	<table class="table table-striped" id="table-items">
		<thead>
			<tr>
				<th width="10" align="center">
					<?php echo '#'; ?>
				</th>
				<th width="10">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo JHTML::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<th width="30" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
				</th>
				<th width="100">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_COMMENTS_USER', 'user_name', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_COMMENTS_ITEM', 'item_title', $listDirn, $listOrder); ?>
				</th>
				<th width="50">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_COMMENTS_PRIVATE', 'cm.private', $listDirn, $listOrder); ?>
				</th>
				<th width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_COMMENTS_COMMENT', 'cm.comment', $listDirn, $listOrder); ?>
				</th>
				<th width="100">
					<?php echo Jtext::_('COM_REDITEM_COMMENTS_STATE'); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_COMMENTS_DATE', 'cm.created', $listDirn, $listOrder); ?>
				</th>
				<th width="10">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'cm.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $row) : ?>
				<tr>
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $row->id); ?>
					</td>
					<td>
						<?php echo JHtml::_('rgrid.published', $row->state, $i, 'comments.', true, 'cb'); ?>
					</td>
					<td>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=comment.edit&id=' . $row->id, $row->user_name); ?>
					</td>
					<td>
						<?php $title = JHTML::_('string.truncate', strip_tags($row->item_title), 50, true, false); ?>
						<?php echo $title; ?>
					</td>
					<td>
						<?php if ($row->private) : ?>
							<i class="icon-user"></i>
						<?php endif; ?>
					</td>
					<td>
						<?php $comment = JHTML::_('string.truncate', strip_tags($row->comment), 50, true, false) . '...'; ?>
						<?php echo $comment; ?>
					</td>
					<td>
						<?php if ($row->trash == 1) : ?>
							<span class="badge badge-warning"><?php echo JText::_('COM_REDITEM_COMMENTS_DELETED'); ?></span>
						<?php elseif ($row->trash == 2) : ?>
							<span class="badge badge-important"><?php echo JText::_('COM_REDITEM_COMMENTS_BLOCKED'); ?></span>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $row->created; ?>
					</td>
					<td>
						<?php echo $row->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
