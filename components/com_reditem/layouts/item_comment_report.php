<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$helpText     = $displayData['help'];
$comment      = $displayData['comment'];
$reasons      = $displayData['reasons'];
$hasReport    = $displayData['hasReport'];
$reportReason = $displayData['reportReason'];

$uri       = JFactory::getURI();
$returnUrl = $uri->toString();

$spanClass = '';
$toolClass = 'hide';

if ($hasReport === false)
{
	$spanClass = 'hide';
	$toolClass = '';
}
?>
<script type="text/javascript">
	(function($){
		$(document).ready(function($) {
			$('#reportCommentForm<?php echo $comment->id; ?> input[name="reason"]').live('click', function(){
				if ($(this).val() === 'other')
					$('#reportCommentOther<?php echo $comment->id; ?>').attr('disabled', false).removeClass('hidden');
				else
					$('#reportCommentOther<?php echo $comment->id; ?>').attr('disabled', true).addClass('hidden');
			});
			$('#reportCommentForm<?php echo $comment->id; ?>').submit(function(event) {
				event.preventDefault();
				if ($('#reportCommentForm<?php echo $comment->id; ?>').find('[name="reason"]:checked').val() == 'other'
					&& $('#reportCommentOther<?php echo $comment->id; ?>').val() == '')
				{
					alert('<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_INVALID_REASON_OTHER'); ?>');
					$('#reportCommentOther<?php echo $comment->id; ?>').addClass('invalid');
				}
				else {
					$.ajax({
						type: $(this).attr('method'),
						url: $(this).attr('action'),
						data: $(this).serialize(),
						dataType: 'JSON',
						cache: false
					})
						.done(function (data) {
							if ((data.status == undefined) || (data.status != 1)) {
								alert("<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_FAIL'); ?>");
							}
							else {
								$('#reditemCommentReport_<?php echo $comment->id; ?>').modal('hide');
								$('#reditemReportTextReason<?php echo $comment->id; ?>').html(data.reason);
								$('#reditemReportText<?php echo $comment->id; ?>').fadeIn();
								$('#reditemReportTool<?php echo $comment->id; ?>').fadeOut();
							}
						});
					return false;
				}
			});
			$('#reportRemove<?php echo $comment->id; ?>').live('click', function(){
				$.ajax({
					url: "<?php echo JUri::root() . 'index.php?option=com_reditem&task=comment.ajaxRemoveReport&id=' . $comment->id ?>",
					dataType: 'JSON',
					cache: false
				})
				.done(function (data){
					if ((data.status == undefined) || (data.status != 1))
					{
						alert("<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_FAIL_REMOVE'); ?>");
					}
					else
					{
						$('#reditemCommentReport_<?php echo $comment->id; ?>').modal('hide');
						$('#reditemReportTool<?php echo $comment->id; ?>').removeClass('hide');
						$('#reditemReportTool<?php echo $comment->id; ?>').css('display', 'block');
						$('#reditemReportTool<?php echo $comment->id; ?>').fadeIn();
						$('#reditemReportText<?php echo $comment->id; ?>').fadeOut();
					}
				});
			});
		});
	})(jQuery);
</script>

<span id="reditemReportTool<?php echo $comment->id; ?>" class="reditem_report reditem_report_<?php echo $comment->id; ?> <?php echo $toolClass; ?>">
	<a href="#reditemCommentReport_<?php echo $comment->id; ?>" role="button" data-toggle="modal">
		<?php echo JText::_('COM_REDITEM_COMMENT_REPORT'); ?>
	</a>
</span>
<div id="reditemReportText<?php echo $comment->id; ?>" class="alert alert-warning <?php echo $spanClass; ?>">
	<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_ALREADY'); ?>
	<span id="reditemReportTextReason<?php echo $comment->id; ?>"><?php echo $reportReason->reason; ?></span>
	<button class="btn btn-danger" id="reportRemove<?php echo $comment->id; ?>"><?php echo JText::_('COM_REDITEM_COMMENT_REPORT_REMOVE'); ?></button>
</div>

<div id="reditemCommentReport_<?php echo $comment->id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<form id="reportCommentForm<?php echo $comment->id; ?>" action="<?php echo JUri::root() ?>index.php?option=com_reditem&task=comment.ajaxReport" method="post">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_('COM_REDITEM_REPORT_MODAL_HEADER'); ?></h3>
		</div>
		<div class="modal-body">
			<?php if (!empty($helpText)) : ?>
			<div class="reportHelp"><?php echo $helpText; ?></div>
			<?php endif; ?>
			<?php if (count($reasons)) : ?>
				<?php foreach ($reasons as $reason) : ?>
					<label><input type="radio" name="reason" value="<?php echo $reason; ?>" /> <?php echo $reason; ?></label>
				<?php endforeach; ?>
				<label>
					<input type="radio" id="reason_other<?php echo $comment->id; ?>" name="reason" value="other" />
					<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_REASON_OTHER'); ?>
				</label>
				<textarea id="reportCommentOther<?php echo $comment->id; ?>" class="hidden" style="width: 90%;" name="reason_other" disabled="disabled" rows="5"></textarea>
			<?php else : ?>
				<textarea id="reportCommentOther<?php echo $comment->id; ?>" style="width: 90%;" name="reason" rows="5"></textarea>
			<?php endif; ?>
				<?php echo JHtml::_('form.token'); ?>
				<input type="hidden" name="id" value="<?php echo $comment->id; ?>" />
				<input type="hidden" name="return_url" value="<?php echo base64_encode($returnUrl); ?>" />
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_REDITEM_COMMENT_REPORT_CANCEL'); ?></button>
			<input type="submit" class="btn btn-primary" id="reportSubmit<?php echo $comment->id; ?>" value="<?php echo JText::_('COM_REDITEM_COMMENT_REPORT_SUBMIT'); ?>" />
		</div>
	</form>
</div>
