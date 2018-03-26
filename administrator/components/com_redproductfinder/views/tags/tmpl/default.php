<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
RHtml::_('rsearchtools.form', "#adminForm", array());

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 't.ordering' && strtolower($listDirn) == 'asc');
$sortFields = $this->getSortFields();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");

		order = table.options[table.selectedIndex].value;

		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, "");
	};
');

$saveOrderLink = JRoute::_('index.php?option=com_redproductfinder&task=tags.saveOrderAjax&tmpl=component', false);

if ($saveOrder)
{
	JHTML::_('rsortablelist.sortable', 'table-tagslist', 'adminForm', strtolower($listDirn), $saveOrderLink, false, true);
}

?>
<script language="javascript" type="text/javascript">
	resetfilter = function()
	{
		document.getElementById('filter_search').value='';
		document.getElementById('filter_types').value='';
		document.adminForm.submit();
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder&view=tags'); ?>" method="post" name="adminForm" id="adminForm" class="admin">
	<div id="j-main-container" class="span12 j-toggle-main">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_REDPRODUCTFINDER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="resetfilter();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-left">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<?php
						$group = $this->filterForm->getGroup('filter');
						echo $group['filter_types']->input;
					?>
				</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php 	$group = $this->filterForm->getGroup('list');
						echo $group['list_tags_limit']->input;
				?>
			</div>
		</div>

		<div class="clearfix"></div>
	<?php if ($this->count == 0) : ?>
	<div class="alert alert-warning alert-dismissible fade in" role="alert">
		<button class="close" aria-label="Close" data-dismiss="alert" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
		<p><?php echo JText::_('COM_REDPRODUCTFINDER_NOT_TYPES'); ?></p>
		<p><a class="btn btn-default" href="index.php?option=com_redproductfinder&view=types">
			<span class="icon-folder"></span>
			<?php echo JText::_('COM_REDPRODUCTFINDER_GO_TO_TYPES'); ?>
		</a></p>
	</div>
	<?php elseif (empty($this->items)) : ?>
	<div class="alert alert-info">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div class="pagination-centered">
			<h3><?php echo JText::_('COM_REDPRODUCTFINDER_NOTHING_TO_DISPLAY'); ?></h3>
		</div>
	</div>
	<?php else : ?>
		<table class="table table-striped" id="table-tagslist" class="adminlist">
			<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHTML::_('rgrid.sort', 'COM_REDPRODUCTFINDER_ORDER', 't.ordering', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="center">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDPRODUCTFINDER_TAG_NAME', 't.tag_name', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDPRODUCTFINDER_TYPE_NAME', 'tt.type_id', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDPRODUCTFINDER_PUBLISHED', 't.published', $listDirn, $listOrder); ?>
				</th>
				<th width="20">
					<?php echo JHtml::_('rgrid.sort', 'JGRID_HEADING_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$k = 0;

				for ($i=0, $n=count( $this->items ); $i < $n; $i++)
				{
					$item = $this->items[$i];

					JFilterOutput::objectHTMLSafe($item);
					$link 	= 'index.php?option=com_redproductfinder&task=tag.edit&id='. $item->id;

					$checked = JHTML::_('grid.checkedout',  $item, $i);
					$my  = JFactory::getUser();
					?>
					<tr class="<?php echo 'row'. $k; ?>">
						<td class="order nowrap center hidden-phone">
							<span class="sortable-handler hasTooltip <?php echo ($saveOrder) ? '' : 'inactive'; ?>">
								<i class="icon-move"></i>
							</span>
							<input type="text" style="display:none" name="order[]" value="<?php echo $item->ordering;?>" class="text-area-order" />
						</td>
						<td>
						<?php echo $checked; ?>
						</td>
						<td>
						<?php
						if ($item->checked_out && ( $item->checked_out != $my->id ))
						{
							?>
							<?php echo $item->tag_name; ?>
							&nbsp;[ <i><?php echo JText::_('Checked Out'); ?></i> ]
							<?php
						}
						else
						{
							?>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('Edit field'); ?>">
							<?php
							if (strstr($item->tag_name, '{more}'))
							{
								$item->tag_name = str_replace("{more}", "&gt;", $item->tag_name);
							}

							if (strstr($item->tag_name, '{less}'))
							{
								$item->tag_name = str_replace("{less}", "&lt;", $item->tag_name);
							}
							echo $item->tag_name;
							?>
							</a>
							<?php
						}
						?>
						</td>
						<td>
							<?php
							if (!empty($this->types[$item->id]))
							{
								foreach ($this->types[$item->id] as $tagid => $type)
								{
									echo $type.'<br />';
								};
							}
							?>
						</td>
						<td width="10%" align="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'tags.', 1, 'cb', $item->publish_up, $item->publish_down); ?>
						</td>
						<td>
							<?php echo $item->id; ?>
						</td>
					</tr>
				<?php
					$k = 1 - $k;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
		            <td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
		         </tr>
	         </tfoot>
		</table>
	<?php endif; ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="tags" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo JHtml::_('form.token'); ?>
</form>