<?php
/**
 * @package     RedITEM
 * @subpackage  Mail
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
$user = ReditemHelperSystem::getUser();
$userId = $user->id;

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

		if (pressbutton == 'mails.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_MAIL_DELETE_MAILS")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_reditem&view=mails" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_mails_limit',
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
	<table class="table table-striped">
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
				<th width="50" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 't.published', $listDirn, $listOrder); ?>
				</th>
				<?php if ($this->canEdit) : ?>
				<th width="1" align="center">
				</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_MAIL_SUBJECT', 't.subject', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_TEMPLATE_TYPE', 'type_name', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_MAIL_SECTION', 't.section', $listDirn, $listOrder); ?>
				</th>
				<th width="200">
					<?php echo JText::_('COM_REDITEM_MAILS_DESCRIPTION'); ?>
				</th>
				<th width="10" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $row) : ?>
			<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td>
					<fieldset class="btn-group">
					<?php if ($this->canEditState) : ?>
						<?php echo JHtml::_('rgrid.published', $row->published, $i, 'mails.', true, 'cb'); ?>
						<?php if ($row->default): ?>
							<?php echo JHtml::_('rgrid.action', $i, 'setUnDefault', 'mails.', '', '', '', false, 'star featured', 'star featured', true, true, 'cb'); ?>
						<?php else: ?>
							<?php echo JHtml::_('rgrid.action', $i, 'setDefault', 'mails.', '', '', '', false, 'star-empty', 'star-empty', true, true, 'cb'); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php if ($row->published) : ?>
							<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
						<?php else : ?>
							<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
						<?php endif; ?>
						<?php if ($row->default): ?>
							<a class="btn btn-small disabled"><i class="icon-star"></i></a>
						<?php else: ?>
							<a class="btn btn-small disabled"><i class="icon-star"></i></a>
						<?php endif; ?>
					<?php endif; ?>
					</fieldset>
				</td>
				<?php if ($this->canEdit) : ?>
				<td>
					<?php if ($row->checked_out) : ?>
						<?php
						$editor = ReditemHelperSystem::getUser($row->checked_out);
						$canCheckin = $row->checked_out == $userId || $row->checked_out == 0;
						echo JHtml::_('rgrid.checkedout', $i, $editor->name, $row->checked_out_time, 'mails.', $canCheckin);
						?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<td>
					<?php if (($row->checked_out) || (!$this->canEdit)) : ?>
						<?php echo $row->subject; ?>
					<?php else : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=mail.edit&id=' . $row->id, $row->subject); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $row->type_name; ?>
				</td>
				<td>
					<span class="badge badge-info"><?php echo JText::_($row->section_name) ?></span>
				</td>
				<td>
					<?php echo JHTML::_('string.truncate', $row->description, 50, true, false); ?>
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
