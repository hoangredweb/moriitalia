<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
JFactory::getDocument()->addScript('//www.gstatic.com/charts/loader.js');
$producthelper = producthelper::getInstance();
$quotationHelper = quotationHelper::getInstance();
$config = Redconfiguration::getInstance();
$lists = $this->lists;
$model = $this->getModel('quotation');
?>
<script type="text/javascript">
	//Load the Visualization API and the piechart package.
	google.charts.load("current", {packages:['corechart']});

	//Set a callback to run when the Google Visualization API is loaded.
	google.charts.setOnLoadCallback(drawChart);

	//Callback that creates and populates a data table,
	//instantiates the pie chart, passes in the data and
	//draws it.
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['<?php echo JText::_('COM_REDSHOP_STATISTIC_DURATION');?>', '<?php echo JText::_('COM_REDSHOP_SALES_AMOUNT');?>', {role: 'style'}, {role: 'annotation'}],
			<?php if (count($this->quotation) > 0) :?>
				<?php foreach ($this->quotation as $row) : ?>
					<?php $userarr = $producthelper->getUserInformation($row->user_id); ?>
	         		['<?php echo $userarr->firstname . ' ' . $userarr->lastname; ?>', <?php echo $row->total; ?>, 'blue', '<?php echo $producthelper->getProductFormattedPrice($row->total); ?>'],
	       	 	<?php endforeach; ?>
	       	 <?php else: ?>
	       	 	[0, 0, 'blue', 0],
	       	 <?php endif; ?>
	      ]);

		var options = {
			  chart: {
	            title: '<?php echo JText::_("COM_REDSHOP_STATISTIC_ORDER"); ?>',
	            subtitle: '<?php echo JText::_("COM_REDSHOP_STATISTIC_ORDER"); ?>',
	          },
			  annotations: {
			    boxStyle: {
			      // Color of the box outline.
			      stroke: '#888',
			      // Thickness of the box outline.
			      strokeWidth: 1,
			      // x-radius of the corner curvature.
			      rx: 10,
			      // y-radius of the corner curvature.
			      ry: 10,
			      // Attributes for linear gradient fill.
			      gradient: {
			        // Start color for gradient.
			        color1: '#fbf6a7',
			        // Finish color for gradient.
			        color2: '#33b679',
			        // Where on the boundary to start and
			        // end the color1/color2 gradient,
			        // relative to the upper left corner
			        // of the boundary.
			        x1: '0%', y1: '0%',
			        x2: '100%', y2: '100%',
			        // If true, the boundary for x1,
			        // y1, x2, and y2 is the box. If
			        // false, it's the entire chart.
			        useObjectBoundingBoxUnits: true
			      }
			    }
			  }
			};

		//Instantiate and draw our chart, passing in some options.
		var chart = new google.visualization.ColumnChart(document.getElementById('quotation_statistic_chart'));
		chart.draw(data, options);
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_redshop&view=quotation_statistic'); ?>" method="post"
      name="adminForm" id="adminForm">
	<div id="editcell">
		<div class="filterItem">
			<div class="btn-wrapper input-append">
				<input placeholder="<?php echo JText::_('COM_REDSHOP_FILTER'); ?>" type="text" name="filter" id="filter" value="<?php echo $this->state->get('filter'); ?>" />
				<input type="submit" class="btn" value="<?php echo JText::_("COM_REDSHOP_SEARCH") ?>">
				<button class="btn"
						onclick="document.getElementById('filter').value='';document.getElementById('filter_status').value='0';document.getElementById('filter_sale').value='0';document.getElementById('filteroption').value='0';this.form.submit();"><?php echo JText::_('COM_REDSHOP_RESET'); ?></button>
			</div>
		</div>
		<div class="filterItem">
			<?php echo JText::_('COM_REDSHOP_QUOTATION_STATUS') . ": " . $lists['filter_status']; ?>
			<?php echo JText::_('COM_REDSHOP_VIEW_QUOTATION_COUNT_SALE') . ": " . $lists['filter_sale']; ?>
			<?php echo JText::_('COM_REDSHOP_FILTER') . ": " . $this->lists['filteroption'];?>
		</div>
		<div id="quotation_statistic_chart"></div>
		<table class="adminlist table table-striped">
			<thead>
			<tr>
				<th width="5%"><?php echo JText::_('COM_REDSHOP_NUM'); ?></th>
				<th width="5%" class="title">
					<?php echo JHtml::_('redshopgrid.checkall'); ?>
				</th>
				<th class="title" width="5%">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_VIEW_QUOTATION_COUNT', 'count', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th width="20%">
					<?php echo JText::_('COM_REDSHOP_FULLNAME'); ?></th>
				<th width="20%">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_QUOTATION_STATUS', 'quotation_status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th width="10%">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_TOTAL', 'quotation_total', $this->lists['order_Dir'], $this->lists['order']); ?></th>
				<th width="10%" nowrap="nowrap">
					<?php echo JHTML::_('grid.sort', 'COM_REDSHOP_QUOTATION_DATE', 'quotation_cdate', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			</tr>
			</thead>
			<?php
			$k = 0;
			$disdate = "";
			for ($i = 0, $n = count($this->quotation); $i < $n; $i++)
			{
				$row = $this->quotation[$i];
				$row->id = $row->quotation_id;
				$display = $row->user_email;
				if ($row->user_id)
				{
					$userarr = $producthelper->getUserInformation($row->user_id);
					if (count($userarr) > 0)
					{
						$display = $userarr->firstname . ' ' . $userarr->lastname;
						$display .= ($userarr->is_company && $userarr->company_name != "") ? "<br>" . $userarr->company_name : "";
					}
				}
				if ($this->filteroption && $row->viewdate != $disdate)
				{
					$disdate = $row->viewdate;    ?>
					<tr>
						<td colspan="8"><?php echo JText::_("COM_REDSHOP_DATE") . ": " . $disdate;?></td>
					</tr>
				<?php
				}
				$link = JRoute::_('index.php?option=com_redshop&view=quotation_detail&task=edit&cid[]=' . $row->quotation_id);
				$status = $quotationHelper->getQuotationStatusName($row->quotation_status);
				if ($row->quotation_status == 5)
				{
					//$status .= "<a href='index.php?option=com_redshop&view=order_detail&task=edit&cid[]=" . $row->order_id . "'> (" . JText::_('COM_REDSHOP_ORDER_ID') . "-" . $row->order_id . " )</a>";
				}    ?>

				<tr class="<?php echo "row$k"; ?>">
					<td align="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td align="center"><?php echo JHTML::_('grid.id', $i, $row->id); ?></td>
					<td align="center"><?php echo $row->count; ?>
					</td>
					<td><a href="index.php?option=com_redshop&view=user_detail&task=edit&user_id=<?php echo $row->user_id; ?>&cid[]=<?php echo $userarr->users_info_id; ?>"><?php echo $display; ?></a></td>
					<td align="center"><?php echo $status;?></td>
					<td align="center"><?php echo $producthelper->getProductFormattedPrice($row->quotation_total); ?></td>
					<td align="center"><?php echo $config->convertDateFormat($row->quotation_cdate); ?></td>
				</tr>
				<?php    $k = 1 - $k;
			}    ?>
			<tr>
				<td colspan="8">
					<?php if (version_compare(JVERSION, '3.0', '>=')): ?>
						<div class="redShopLimitBox">
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					<?php endif; ?>
					<?php echo $this->pagination->getListFooter(); ?></td>
		</table>
		<h2><?php echo JText::_('COM_REDSHOP_VIEW_QUOTATION_GENERAL_STATISTIC'); ?></h2>
		<table class="adminlist table table-striped">
			<tr>
				<td><?php echo JText::_('COM_REDSHOP_VIEW_QUOTATION_COUNT'); ?>: <strong><?php echo $this->amountStatistic->count; ?></strong></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_REDSHOP_TOTAL_LBL'); ?>: <strong><?php echo $producthelper->getProductFormattedPrice($this->amountStatistic->total); ?></strong></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_REDSHOP_ORDER_SUBTOTAL'); ?>: <strong><?php echo $producthelper->getProductFormattedPrice($this->amountStatistic->subtotal); ?></strong></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_REDSHOP_VIEW_QUOTATION_COUNT_SALE'); ?>: <strong><?php echo $this->saleStatistic; ?></strong></td>
			</tr>
		</table>
	</div>

	<input type="hidden" name="view" value="quotation_statistic"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>"/>
</form>
