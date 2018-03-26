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
JHtml::_('rholder.image', '50x50');

$saveOrderLink = 'index.php?option=com_reditem&task=items.saveOrderAjax&tmpl=component';
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$ordering = ($listOrder == 'i.ordering');
$saveOrder = ($listOrder == 'i.ordering' && strtolower($listDirn) == 'asc');
$search = $this->state->get('filter.search');

$user = ReditemHelperSystem::getUser();
$userId = $user->id;

if (($saveOrder) && ($this->canEditState))
{
	JHTML::_('rsortablelist.sortable', 'table-items', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
}

$typeId = JFactory::getApplication()->getUserState('com_reditem.global.tid', '0');
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

		if (pressbutton == 'items.delete')
		{
			var r = confirm('<?php echo JText::_("COM_REDITEM_ITEM_DELETE_ITEMS")?>');
			if (r == true)    form.submit();
			else return false;
		}
		form.submit();
	}
</script>
<form action="index.php?option=com_reditem&view=items" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_items_limit',
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
					<?php echo JHTML::_('rsearchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
				</th>
				<?php if ($this->canEdit) : ?>
				<th width="1" nowrap="nowrap">
				</th>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
				<th width="40">
					<?php echo JHTML::_('rsearchtools.sort', '<i class=\'icon-sort\'></i>', 'i.ordering', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_NAME', 'i.title', $listDirn, $listOrder); ?>
				</th>
				<th width="50">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_TYPE', 'type_name', $listDirn, $listOrder); ?>
				</th>
				<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
					<?php foreach ($this->displayableFields as $displayField) : ?>
						<th>
						<?php $fieldName = JHTML::_('string.truncate', $displayField->name, 20, true, false); ?>
						<?php if ($displayField->type != 'image') : ?>
							<?php echo JHTML::_('rsearchtools.sort', $fieldName, 'cfv_' . $displayField->fieldcode, $listDirn, $listOrder); ?>
						<?php else : ?>
							<?php echo $fieldName; ?>
						<?php endif; ?>
						</th>
					<?php endforeach; ?>
				<?php endif; ?>
				<th width="150">
					<?php echo JText::_('COM_REDITEM_ITEM_CATEGORIES'); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ITEM_TEMPLATE', 'template_name', $listDirn, $listOrder); ?>
				</th>
				<th width="30">
					<?php echo JHtml::_('rsearchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="20" nowrap="nowrap">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'i.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php $n = count($this->items); ?>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $orderkey = array_search($item->id, $this->ordering[0]); ?>
			<tr>
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
				<td align="center">
					<fieldset class="btn-group">
						<?php if ($this->canEditState) : ?>
							<?php echo JHtml::_('rgrid.published', $item->published, $i, 'items.', true, 'cb', $item->publish_up, $item->publish_down); ?>
						<?php else : ?>
							<?php if ($item->published) : ?>
								<a class="btn btn-small disabled"><i class="icon-ok-sign icon-green"></i></a>
							<?php else : ?>
								<a class="btn btn-small disabled"><i class="icon-remove-sign icon-red"></i></a>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($item->featured) : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setUnFeatured', 'items.', '', '', '', false, 'star featured', 'star featured', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-star featured"></i></span>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($this->canEditState) : ?>
								<?php echo JHtml::_('rgrid.action', $i, 'setFeatured', 'items.', '', '', '', false, 'star-empty', 'star-empty', true, true, 'cb'); ?>
							<?php else : ?>
								<span class="btn btn-small disabled"><i class="icon-star-empty"></i></span>
							<?php endif; ?>
						<?php endif; ?>
					</fieldset>
				</td>
				<?php if ($this->canEdit) : ?>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php
						$editor = ReditemHelperSystem::getUser($item->checked_out);
						$canCheckin = $item->checked_out == $userId || $item->checked_out == 0;
						echo JHtml::_('rgrid.checkedout', $i, $editor->name, $item->checked_out_time, 'items.', $canCheckin);
						?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<?php if (($search == '') && ($this->canEditState)) : ?>
				<td class="order nowrap center">
					<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
						<i class="icon-move"></i>
					</span>
					<input type="text" style="display:none" name="order[]" value="<?php echo $orderkey + 1;?>" class="text-area-order" />
				</td>
				<?php endif; ?>
				<td>
					<?php $itemTitle = JHTML::_('string.truncate', $item->title, 50, true, false); ?>
					<?php if (($item->checked_out) || (!$this->canEdit)) : ?>
						<?php echo $itemTitle; ?>
					<?php else : ?>
						<?php echo JHtml::_('link', 'index.php?option=com_reditem&task=item.edit&id=' . $item->id, $itemTitle); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->type_name; ?>
				</td>
				<!-- Add displayable fields data -->
				<?php if (($this->displayableFields) && (count($this->displayableFields) > 0)) : ?>
					<?php foreach ($this->displayableFields as $displayField) : ?>
						<td>
						<?php if ($displayField->type == 'image') : ?>
							<?php
							$image = json_decode($item->customfield_values[$displayField->fieldcode], true);
							$fileName = explode('/', $image[0]);
							$fileName = array_pop($fileName);
							$thumbnailPath = ReditemHelperImage::getImageLink($item, 'customfield', $fileName, 'thumbnail', 50, 50, true);
							?>
							<img src="<?php echo $thumbnailPath; ?>" />
						<?php else : ?>
							<?php $cfValue = $item->customfield_values[$displayField->fieldcode]; ?>
							<?php if ($cfValue) : ?>
								<?php if ($displayField->type == "checkbox") : ?>
									<?php $cfValue = implode(', ', json_decode($cfValue)); ?>
								<?php endif; ?>
								<?php echo JHTML::_('string.truncate', strip_tags($cfValue), 50, true, false); ?>
							<?php endif; ?>
						<?php endif; ?>
						</td>
					<?php endforeach; ?>
				<?php endif; ?>
				<!-- End add displayable fields data -->
				<td>
					<?php if (isset($item->categories)) : ?>
						<?php $categories = array(); ?>
						<?php foreach ($item->categories As $cat) : ?>
							<?php if (!empty($cat)) : ?>
								<?php $categories[] = $cat->title; ?>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php echo implode('<br />', $categories); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $item->template_name; ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>

	<!-- Load the batch processing form. -->
	<?php if ($user->authorise('core.create', 'com_reditem') && $user->authorise('core.edit', 'com_reditem') && $user->authorise('core.edit.state', 'com_reditem')): ?>
		<?php if ($typeId != 0): ?>
			<div id="batchForm">
				<?php echo $this->loadTemplate('batch'); ?>
			</div>
		<?php else: ?>
			<div class="alert alert-info">
				<?php echo JText::_("COM_REDITEM_ITEMS_BATCH_PROCESS_CHOOSE_TYPE"); ?>
			</div>
		<?php endif; ?>
	<?php endif;?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
