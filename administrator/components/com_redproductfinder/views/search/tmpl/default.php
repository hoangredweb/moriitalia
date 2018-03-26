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

$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
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
?>
<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm" class="admin">
	<div id="j-main-container" class="span12 j-toggle-main">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTACT_FILTER_SEARCH_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_REDPRODUCTFINDER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>

		<div class="clearfix"></div>

		<table class="table table-striped" id="table-formslist" class="adminlist">
		<thead>
			<tr>
				<th width="1%" class="center">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th width="20%" class="title">
					<?php echo JHtml::_('grid.sort', 'COM_REDPRODUCTFINDER_SEARCH_KEYWORD', 'k.keyword', $listDirn, $listOrder); ?>
				</th>
				<th width="20%" class="title">
					<?php echo JHtml::_('grid.sort', 'COM_REDPRODUCTFINDER_SEARCH_TIMES', 'k.times', $listDirn, $listOrder); ?>
				</th>
				<th width="20%" class="title center">
					<?php echo JHtml::_('grid.sort', 'COM_REDPRODUCTFINDER_SEARCH_DATE', 'k.created_date', $listDirn, $listOrder); ?>
				</th>
				<th width="2%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'k.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$k = 0;

				for ($i=0, $n=count( $this->items); $i < $n; $i++)
				{
					$item = $this->items[$i];

					JFilterOutput::objectHTMLSafe($item);

					$my  = JFactory::getUser();
					?>
					<tr class="<?php echo 'row'. $k; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<?php echo $item->keyword ?>
						</td>
						<td>
							<?php echo $item->times ?>
						</td>
						<td class="center">
						<?php
							echo $item->created_date;
						?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				};

			?>
		</tbody>
        <tfoot>
        	<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo JHtml::_('form.token'); ?>
</form>
