<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('behavior.framework');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

RHelperAsset::load('jquery/jquery.raty.customize.min.js', 'com_reditem');
RHelperAsset::load('jquery/jquery.raty.min.css', 'com_reditem');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		submitbutton(pressbutton);
	}
	submitbutton = function(pressbutton) {
		var form = document.adminForm;
		if (pressbutton) {
			form.task.value = pressbutton;
		}

		if (pressbutton == 'reports.delete') {
			var r = confirm('<?php echo JText::_("COM_REDITEM_REPORT_ITEMS_DELETE_REPORTS")?>');
			if (r == true) form.submit();
			else return false;
		}
		form.submit();
	}
</script>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('.reportCount').each(function(){
				$(this).click(function(event){
					event.preventDefault();
					var icon = $(this).find('i');

					if ($(icon).hasClass('icon-chevron-right'))
						$(icon.attr('class', 'icon-chevron-down'));
					else
						$(icon.attr('class', 'icon-chevron-right'));

					$('#table-items .reports' + $(this).attr('data-id')).toggle('fast');
				});
			});

			// Add point for reporters
			$('.reporter_point > .reporter_point_wrapper').each(function(){
				var rateLink = "<?php echo JUri::root() . 'administrator/index.php?option=com_reditem&task=reportitems.addPoint' ?>";
				rateLink += "&report_id=" + $(this).attr('target-report');
				rateLink += "&user_id=" + $(this).attr('target-user');
				var score = parseFloat($(this).attr('score'));
				$(this).raty({
					score: score,
					numberMax: 5,
					number: 5,
					half: false,
					round: { up: 0.9 },
					click: function(score, evt) {
						var url = rateLink + "&value=" + score;
						$.ajax({
							url: url,
							dataType: "json",
							cache: false
						})
						.done(function (data){

						});
					},
					starOn: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-on.png' ?>",
					starOff: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-off.png' ?>",
					starHalf: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-half.png' ?>",
					starOver: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-on.png' ?>",
				});
			});

			// Ignore report button
			$('.ignoreReport').each(function(){
				$(this).click(function(event){
					event.preventDefault();
					var reportId = $(this).attr('target-report');
					var itemId   = $(this).attr('target-item');
					$.ajax({
						url: "index.php?option=com_reditem&task=reportitems.ignoreReport&report_id=" + reportId + "&item_id=" + itemId,
						dataType: "json",
						cache: false
					})
					.done(function (data){
						if (data == "1")
						{
							$('#ignoreReport' + reportId).addClass('hidden');
							$('#approveReport' + reportId).removeClass('hidden');
							$('#reportText' + reportId).children('span').addClass('muted');
						}
					});
				});
			});

			// Approve report button
			$('.approveReport').each(function(){
				$(this).click(function(event){
					event.preventDefault();
					var reportId = $(this).attr('target-report');
					var itemId = $(this).attr('target-item');
					$.ajax({
						url: "index.php?option=com_reditem&task=reportitems.approveReport&report_id=" + reportId + "&item_id=" + itemId,
						dataType: "json",
						cache: false
					})
					.done(function (data){
						if (data == "1")
						{
							$('#approveReport' + reportId).addClass('hidden');
							$('#ignoreReport' + reportId).removeClass('hidden');
							$('#reportText' + reportId).children('span').removeClass('muted');
						}
					});
				});
			});
		});
	})(jQuery);
</script>
<form action="index.php?option=com_reditem&view=reportitems" class="admin" id="adminForm" method="post" name="adminForm">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'searchField' => 'search',
				'searchFieldSelector' => '#filter_search',
				'limitFieldSelector' => '#list_reports_limit',
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
	<table class="table" id="table-items">
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
				<th width="100">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_ITEMS_REPORTED_COUNT', 'reportedCount', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_ITEMS_ITEM', 'i.title', $listDirn, $listOrder); ?>
				</th>
				<th width="200">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_ITEMS_OWNER', 'owner_name', $listDirn, $listOrder); ?>
				</th>
				<th width="40">
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_ITEMS_FIRST_DATE', 'firstDateReported', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORT_ITEMS_LAST_DATE', 'lastDateReported', $listDirn, $listOrder); ?>
				</th>
				<th width="50">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_ID', 'i.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $row) : ?>
				<?php if ($row->blocked): ?>
				<tr class="item-blocked">
				<?php else: ?>
				<tr>
				<?php endif; ?>
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $row->id); ?>
					</td>
					<td>
						<a class="reportCount" data-id="<?php echo $row->id ?>" href="javascript:void(0);">
							<i class="icon-chevron-right"></i>
							<?php echo $row->reportedCount ?>
						</a>
					</td>
					<td class="item-title">
						<?php $itemEditLink = JRoute::_('index.php?option=com_reditem&task=item.edit&id=' . $row->id); ?>
						<a href="<?php echo $itemEditLink ?>" target="_blank">
							<?php echo JHTML::_('string.truncate', strip_tags($row->title), 50, true, false); ?>
						</a>
					</td>
					<td>
						<?php if (empty($row->owner_name)) : ?>
							<small class="text-error" style="text-decoration: line-through;">
								<?php echo JText::_('COM_REDITEM_DELETED_USER') ?>
							</small>
						<?php else : ?>
							<?php echo $row->owner_name ?>
						<?php endif; ?>
					</td>
					<td>
						<?php $viewLink = JRoute::_(JUri::root() . 'index.php?option=com_reditem&view=itemdetail&id=' . $row->id); ?>
						<a class="btn btn-info" href="<?php echo $viewLink; ?>" target="_blank"><i class="icon-globe"></i></a>
					</td>
					<td>
						<?php echo $row->firstDateReported ?>
					</td>
					<td>
						<?php echo $row->lastDateReported ?>
					</td>
					<td>
						<?php echo $row->id ?>
					</td>
				</tr>
				<?php if (!empty($row->reports)) : ?>
					<?php foreach ($row->reports as $report) : ?>
						<tr class="reports<?php echo $row->id ?>" style="display: none; background-color: #f9f9f9;">
							<td style="border-top: none;"></td>
							<td style="border-top: none;"></td>
							<td style="border-top: none;"></td>
							<td style="border-top: none;" id="reportText<?php echo $report->id ?>">
								<?php if ($report->state):?>
									<span>
										<?php echo $report->reason ?>
									</span>
								<?php else: ?>
									<small class="muted">
										<?php echo $report->reason ?></span>
									</small>
								<?php endif; ?>
							</td>
							<td style="border-top: none;">
								<?php if (empty($report->reporter)) : ?>
									<small class="text-error" style="text-decoration: line-through;">
										<?php echo JText::_('COM_REDITEM_DELETED_USER') ?>
									</small>
								<?php else : ?>
									<?php echo $report->reporter ?>
								<?php endif; ?>
							</td>
							<td style="border-top: none;">
							</td>
							<td style="border-top: none;">
								<span class="reporter_point">
									<div class="reporter_point_wrapper"
										target-report="<?php echo $report->id ?>"
										target-user="<?php echo $report->user_id ?>"
										score="<?php echo $report->point ?>">
									</div>
								</span>
							</td>
							<td style="border-top: none; text-align: left;">
								<?php
								$ignoreClass  = 'hidden';
								$approveClass = '';

								if ($report->state) :
									$ignoreClass = '';
									$approveClass = 'hidden';
								endif;
								?>
								<a href="javascript:void(0);" id="ignoreReport<?php echo $report->id ?>"
									class="btn btn-danger ignoreReport <?php echo $ignoreClass ?>"
									target-item="<?php echo $row->id ?>" target-report="<?php echo $report->id ?>">
									<?php echo JText::_('COM_REDITEM_REPORT_ITEMS_IGNORE_REPORT') ?>
								</a>
								<a href="javascript:void(0);" id="approveReport<?php echo $report->id ?>"
									class="btn btn-success approveReport <?php echo $approveClass ?>"
									target-item="<?php echo $row->id ?>" target-report="<?php echo $report->id ?>">
									<?php echo JText::_('COM_REDITEM_REPORT_ITEMS_APPROVE_REPORT') ?>
								</a>
							</td>
							<td style="border-top: none;"></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
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
