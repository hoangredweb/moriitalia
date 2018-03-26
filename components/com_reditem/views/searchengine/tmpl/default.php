<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Load jQuery framework
RBootstrap::bootstrap();

$app = JFactory::getApplication();

$itemId = $app->input->getInt('Itemid', 0);
$action = JRoute::_('index.php?option=com_reditem&view=searchengine&Itemid=' . $itemId);
?>

<?php if (empty($this->items)): ?>
	<p><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_NO_ITEMS') ?></p>
<?php else: ?>
	<script type="text/javascript">
		/**
		 * Method for remove an search engine using AJAX
		 *
		 * @param   int  id  ID of search engine.
		 *
		 * @return  void
		 */
		function reditemSERemove(id)
		{
			(function($){
				$('#reditemSEStatusMessage').fadeOut();
				var row = $("#reditemSERow-" + id);
				$(row).addClass('disabled');
				var url = "index.php?option=com_reditem&task=searchengine.ajaxRemove&id=" + id;

				$.ajax({
					url: url,
					dataType: "json",
					cache: false
				})
				.done(function(data){
					$(row).removeClass('disabled');

					if (data.status == 1) {
						$(row).hide('slow', function(){ $(this).remove(); });
					}

					if (data.msg != "") {
						// Remove system message div
						if ($('#system-message').length > 0) {
							$('#system-message').remove();
						}

						var msgClass = (data.status == 1) ? 'alert alert-success' : 'alert alert-error';
						$('#reditemSEStatusMessage').html(data.msg).addClass(msgClass).fadeIn();
					}
				});
			})(jQuery);
		}
	</script>
	<div id="reditemSEStatusMessage"></div>
	<form action="<?php echo $action ?>" class="adminForm" id="adminForm" method="post" name="adminForm">
		<table class="table table-striped" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone"><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TABLE_HEADER_ID') ?></th>
					<th width="auto"><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TABLE_HEADER_SEARCH_DATA') ?></th>
					<th width="1%"><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TABLE_HEADER_TYPE') ?></th>
					<th width="1%" class="hidden-phone"><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TABLE_HEADER_PAGE') ?></th>
					<th width="20%"><?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TABLE_HEADER_SEND_TOOLS') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $item): ?>
					<?php $text = ''; ?>
					<?php if (strpos($item->url, 'view=search') !== false): ?>
						<?php $text = JText::_('COM_REDITEM_SEARCH_ENGINE_SEARCH_PAGE'); ?>
					<?php elseif (strpos($item->url, 'view=categorydetail') !== false): ?>
						<?php $text = JText::_('COM_REDITEM_SEARCH_ENGINE_CATEGORY_DETAIL_PAGE'); ?>
					<?php endif; ?>
				<tr id="reditemSERow-<?php echo $item->id ?>">
					<td class="hidden-phone"><?php echo $item->id ?></td>
					<td>
						<?php if (!empty($item->searchData)): ?>
							<?php foreach ($item->searchData as $filter => $value): ?>
								<p><strong><?php echo $filter ?></strong>: <i><?php echo $value ?></i></p>
							<?php endforeach; ?>
						<?php endif; ?>
					</td>
					<td>
						<span class="label label-info"><?php echo $item->type_name ?></span>
					</td>
					<td class="hidden-phone">
						<small><i><?php echo $text ?></i></small>
					</td>
					<td>
						<?php if (empty($item->url)): ?>
						<a class="btn btn-mini disabled" disable="disable" title="<?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TOOLS_VIEW') ?>" href="javascript:void(0);">
							<i class="icon-eye-open"></i>
						</a>
						<?php else: ?>
						<a class="btn btn-mini" target="_blank" title="<?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TOOLS_VIEW') ?>" href="<?php echo $item->url ?>">
							<i class="icon-eye-open"></i>
						</a>
						<?php endif; ?>
						<a class="btn btn-danger btn-mini" href="javascript:void(0);"
							title="<?php echo JText::_('COM_REDITEM_SEARCH_ENGINE_TOOLS_REMOVE') ?>"
							onClick="javascript:reditemSERemove(<?php echo $item->id ?>);">
							<i class="icon-remove"></i>
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	</form>
<?php endif; ?>
