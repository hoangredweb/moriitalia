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

RHelperAsset::load('jquery/jquery.raty.customize.min.js', 'com_reditem');
RHelperAsset::load('jquery/jquery.raty.min.css', 'com_reditem');

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

			// Add point for reporters
			$('.reporter_point > .reporter_point_wrapper').each(function(){
				var score = parseFloat($(this).attr('score'));
				$(this).raty({
					score: score,
					numberMax: 5,
					number: 5,
					half: false,
					round: { up: 0.9 },
					readOnly: true,
					starOn: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-on.png' ?>",
					starOff: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-off.png' ?>",
					starHalf: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-half.png' ?>",
					starOver: "<?php echo JUri::root() . 'media/com_reditem/images/jquery-raty/star-on.png' ?>",
				});
			});
		});
	})(jQuery);
</script>

<form action="index.php?option=com_reditem&view=reporters" class="admin" id="adminForm" method="post" name="adminForm">
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
				<th class="title" width="auto">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORTERS_REPORTER', 'user.name', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORTERS_ITEMS_COUNT', 'reportedItems', $listDirn, $listOrder); ?>
				</th>
				<th class="title" width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORTERS_COMMENTS_COUNT', 'reportedComments', $listDirn, $listOrder); ?>
				</th>
				<th width="150">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORTERS_POINT', 'point', $listDirn, $listOrder); ?>
				</th>
				<th width="50">
					<?php echo JHTML::_('rsearchtools.sort', 'COM_REDITEM_REPORTERS_USER_ID', 'user.id', $listDirn, $listOrder); ?>
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
					<?php echo $row->name ?>
					<span class="small">(<i><?php echo $row->username ?></i>)</span>
				</td>
				<td>
					<?php echo $row->reportedItems ?>
				</td>
				<td>
					<?php echo $row->reportedComments ?>
				</td>
				<td>
					<span class="reporter_point">
						<div class="reporter_point_wrapper" target="<?php echo $row->id ?>" score="<?php echo $row->point ?>"></div>
					</span>
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
