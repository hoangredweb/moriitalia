<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$user = JFactory::getUser();
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

		if (pressbutton == 'templates.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDSLIDER_TEMPLATES_DELETE")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_redslider&view=templates" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_templates_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="pagination-centered">
			<h3><?php echo JText::_('COM_REDSLIDER_NOTHING_TO_DISPLAY'); ?></h3>
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
				<th width="30" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 't.published', $listDirn, $listOrder); ?>
				</th>
				<th width="1" >
				</th>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDSLIDER_TEMPLATE', 't.title', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo JText::_('COM_REDSLIDER_TEMPLATE_SECTION'); ?>
				</th>
				<th width="10" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDSLIDER_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $row) :
			$canCreate		= $user->authorise('core.create',		'com_redslider');
			$canEdit		= $user->authorise('core.edit',			'com_redslider');
			$canCheckin		= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			$canEditOwn		= $user->authorise('core.edit.own',		'com_redslider');
			$canEditState	= $user->authorise('core.edit.state',		'com_redslider');
			$canChange		= $canEditState && $canCheckin;
			$editor 		= JFactory::getUser($row->checked_out);
			?>
			<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td>
					<?php if ($canEditState): ?>
						<?php echo JHtml::_('rgrid.published', $row->published, $i, 'templates.', true, 'cb'); ?>
					<?php else: ?>
						<?php if ($row->published) : ?>
							<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
						<?php else : ?>
							<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
						<?php endif; ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($row->checked_out) : ?>
						<?php
						$editor = JFactory::getUser($row->checked_out);
						$canCheckin = $row->checked_out == $userId || $row->checked_out == 0;
						echo JHtml::_('rgrid.checkedout', $i, $editor->name, $row->checked_out_time, 'templates.', $canCheckin);
						?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_redslider&task=template.edit&id=' . $row->id, $row->title); ?>
					<?php else : ?>
						<?php echo $this->escape($row->title); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $row->sectionName; ?>
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
	<?php echo JHtml::_('form.token'); ?>
</form>
