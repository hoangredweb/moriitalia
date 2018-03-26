<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHTML::_('behavior.framework');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="index.php?option=com_reditem&view=subscriblers" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_subscriblers_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn,
				'filtersHidden' => false
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
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_SUBSCRIBLERS_USER', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th width="150" style="text-align: left;">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_SUBSCRIBLERS_EMAIL', 'u.email', $listDirn, $listOrder); ?>
				</th>
				<th width="100">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_SUBSCRIBLERS_SUBSCRIBLE', 'subscrible', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_SUBSCRIBLERS_NOTIFY', 'notifyType', $listDirn, $listOrder); ?>
				</th>
				<th width="70">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_SUBSCRIBLERS_ID', 'u.id', $listDirn, $listOrder); ?>
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
						<span class="<?php echo $class ?>"><?php echo $row->name ?></span>
						<span class="small">(<i><?php echo $row->username ?></i>)</span>
					</td>
					<td>
						<?php echo $row->email ?>
					</td>
					<td>
						<?php if ($row->subscrible) : ?>
							<i class="icon-ok-sign icon-green"></i>
						<?php else: ?>
							<i class="icon-remove icon-red"></i>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($row->notify == 2) : ?>
							<?php echo JText::_('COM_REDITEM_SUBSCRIBLERS_NOTIFY_TYPE_MAIL_PER_WEEKLY') ?>
						<?php elseif ($row->notify == 1) : ?>
							<?php echo JText::_('COM_REDITEM_SUBSCRIBLERS_NOTIFY_TYPE_MAIL_PER_DAILY') ?>
						<?php else : ?>
							<?php echo JText::_('COM_REDITEM_SUBSCRIBLERS_NOTIFY_TYPE_MAIL_PER_EVENT') ?>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $row->id ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)) ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
