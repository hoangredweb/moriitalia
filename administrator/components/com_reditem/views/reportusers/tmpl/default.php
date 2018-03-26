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

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			Joomla.submitbutton = function(pressbutton)
			{
				submitbutton(pressbutton);
			}

			submitbutton = function(pressbutton)
			{
				var form = document.adminForm;

				if (pressbutton)
				{
					form.task.value = pressbutton;
				}

				form.submit();
			}
		});
	})(jQuery);
</script>

<form action="index.php?option=com_reditem&view=reportusers" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_reportusers_limit',
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
				<th width="10">
					<?php if (version_compare(JVERSION, '3.0', 'lt')) : ?>
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					<?php else : ?>
						<?php echo JHTML::_('grid.checkall'); ?>
					<?php endif; ?>
				</th>
				<th width="50" style="text-align: center;">
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 'u.block', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_USERS_USER', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th width="150" style="text-align: left;">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_USERS_EMAIL', 'u.email', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_USERS_REPORTED_ITEMS_COUNT', 'reportedItemsCount', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_USERS_REPORTED_COMMENTS_COUNT', 'reportedCommentsCount', $listDirn, $listOrder); ?>
				</th>
				<th width="150" style="text-align: center;">
					<?php echo JText::_('COM_REDITEM_REPORT_USERS_LATEST_REPORT_DATE') ?>
				</th>
				<th width="70">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_USER_ID', 'u.id', $listDirn, $listOrder); ?>
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
					<td style="text-align: center;">
						<?php if ($row->block) : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'unBlock', 'reportusers.', '', '', '', false, 'unlock icon-red', 'unlock icon-red', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-unlock icon-red"></i></span>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'block', 'reportusers.', '', '', '', false, 'lock', 'lock', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-lock"></i></span>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td>
						<?php $class = ($row->block) ? 'text-danger' : ''; ?>
						<span class="<?php echo $class ?>"><?php echo $row->name ?></span>
						<span class="small">(<i><?php echo $row->username ?></i>)</span>
					</td>
					<td>
						<?php echo $row->email ?>
					</td>
					<td>
						<?php if ($row->reportedItemsCount) : ?>
							<a href="index.php?option=com_reditem&view=reportitems&filter[owner]=<?php echo $row->id ?>">
								<?php echo $row->reportedItemsCount ?>
							</a>
						<?php endif; ?>
					</td>
					<td>
						<?php if ($row->reportedCommentsCount) : ?>
							<a href="index.php?option=com_reditem&view=reportcomments&filter[owner]=<?php echo $row->id ?>">
								<?php echo $row->reportedCommentsCount ?>
							</a>
						<?php endif; ?>
					</td>
					<td style="text-align: center;">
						<?php if ($row->reportedItemsLastDate >= $row->reportedCommentsLastDate) : ?>
							<?php echo $row->reportedItemsLastDate ?>
						<?php else : ?>
							<?php echo $row->reportedCommentsLastDate ?>
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
