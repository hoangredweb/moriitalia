<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$comments      = $displayData['comments'];
$item          = $displayData['item'];
$canEdit       = $displayData['canEdit'];
$replyToId     = $displayData['replyTo'];
$report        = $displayData['report'];
$reportReasons = $displayData['reportReasons'];
$helpText      = $displayData['help'];

$currentUser = ReditemHelperSystem::getUser();
$uri         = JFactory::getURI();
$returnUrl   = $uri->toString();

RHelperAsset::load('reditem.item.comment.min.js', 'com_reditem');
?>

<div class="item_comments">
<?php foreach ($comments as $comment) : ?>
	<?php $class = ($comment->private) ? "private" : ""; ?>
	<div class="item_comment media <?php echo $class; ?>" id="item_comment_<?php echo $comment->id; ?>">
		<div class="comment_user pull-left">
			<?php if ($comment->user->guest) : ?>
				<?php echo JText::_('COM_REDITEM_COMMENT_USER_ANONYMOUS'); ?>
			<?php else : ?>
				<?php if (($comment->private) && isset($comment->replyToUser)) : ?>
					<?php echo JText::sprintf('COM_REDITEM_COMMENT_USER_TO_USER', $comment->user->name, $comment->replyToUser->name); ?>
				<?php else : ?>
					<?php echo $comment->user->name; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="media-body">
			<div class="comment_date">
				<small><?php echo $comment->posted; ?></small>
			</div>
			<div class="comment" id="reditemCommentContent_<?php echo $comment->id; ?>">
				<?php if ($comment->trash == 1) : ?>
					<?php echo JText::_('COM_REDITEM_COMMENT_DELETE_BY_USER'); ?>
				<?php elseif ($comment->trash == 2) : ?>
					<?php echo JText::_('COM_REDITEM_COMMENT_DELETE_DUE_TO_REPORT'); ?>
				<?php elseif ($comment->state == 0) : ?>
					<?php echo JText::_('COM_REDITEM_COMMENT_DELETE_BY_ADMIN'); ?>
				<?php else : ?>
					<?php echo $comment->comment; ?>
				<?php endif; ?>
			</div>
			<?php if ($comment->trash == 0) : ?>
			<div class="comment-tools">
				<ul class="nav nav-pills">
					<?php if ((!$currentUser->guest) && ($currentUser->id != $comment->user_id)) : ?>
						<?php if ($canEdit) : ?>
						<li>
							<?php
							if ($comment->user_id == 0) :
								$onClick = 'javascript:reditemCommentReply(' . $replyToId . ',' . $comment->id . ',1)';
							else :
								$onClick = 'javascript:reditemCommentReply(' . $replyToId . ',' . $comment->id . ',' . $comment->private . ')';
							endif;
							?>
							<a href="#reditemReplyModal<?php echo $replyToId; ?>" class="itemReplyLink" role="button" data-toggle="modal" onClick="<?php echo $onClick ?>">
								<?php echo JText::_('COM_REDITEM_COMMENT_REPLY'); ?>
							</a>
						</li>
						<?php endif; ?>
						<?php if (($report === true) && !$currentUser->guest) : ?>
						<li>
							<?php
							$hasReport = false;
							$reportReason = ReditemHelperReport::getReportCommentData($comment->id);

							if (!empty($reportReason)) :
								$hasReport = true;
							endif;

							$layoutData = array(
								'comment'      => $comment,
								'reasons'      => $reportReasons,
								'hasReport'    => $hasReport,
								'reportReason' => $reportReason,
								'help'         => $helpText
							);
							?>
							<?php
							echo ReditemHelperLayout::render(
								$item->type,
								'item_comment_report',
								$layoutData,
								array('component' => 'com_reditem')
							);
							?>
						</li>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ((!$currentUser->guest) && ($currentUser->id == $comment->user->id) && (!$comment->trash)) : ?>
					<li>
						<?php $onClick = 'javascript:reditemCommentEdit(' . $replyToId . ',' . $comment->id . ',\'' . JText::_('COM_REDITEM_COMMENT_EDIT_MODAL_HEADER') . '\', \'' . $comment->comment . '\')'; ?>
						<a href="#reditemReplyModal<?php echo $replyToId; ?>" role="button" data-toggle="modal" onClick="<?php echo $onClick; ?>">
							<?php echo JText::_('COM_REDITEM_COMMENT_EDIT_COMMENT'); ?>
						</a>
					</li>
					<li>
						<a href="javascript:void(0);" onClick="javascript:reditemCommentUserDelete(<?php echo $comment->id; ?>);"><?php echo JText::_('COM_REDITEM_COMMENT_DELETE_COMMENT'); ?></a>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		<?php if (!empty($comment->replyTo)) : ?>
			<div>
				<?php
				// Load reply to modal
				$layoutData = array(
					'comments'      => $comment->replyTo,
					'item'          => $item,
					'replyTo'       => $comment->id,
					'canEdit'       => $canEdit,
					'report'        => $report,
					'reportReasons' => $reportReasons,
					'help'          => $helpText
				);
				echo ReditemHelperLayout::render($item->type, 'item_comments', $layoutData, array('component' => 'com_reditem'));
				?>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>

<div id="reditemReplyModal<?php echo $replyToId; ?>" class="reditemReplyModal modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel<?php echo $replyToId; ?>" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h2 id="myModalLabel<?php echo $replyToId; ?>"><?php echo JText::_('COM_REDITEM_COMMENT_REPLY_MODAL_HEADER'); ?></h2>
	</div>
	<div class="modal-body">
		<form action="index.php?option=com_reditem&task=comment.add" method="post" id="reditemReplyModalForm<?php echo $replyToId; ?>">
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="controls">
						<textarea id="replyComment<?php echo $replyToId; ?>" name="replyComment<?php echo $replyToId; ?>" rows="10" style="width: 100%;"></textarea>
					</div>
				</div>
				<?php if ((!$currentUser->guest) && ($currentUser->id == $comment->user->id)) : ?>
				<div class="control-group hidden">
				<?php else : ?>
				<div class="control-group">
				<?php endif; ?>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="private" value="1" /> <?php echo JText::_('COM_REDITEM_COMMENT_PRIVATE_CHECKBOX'); ?>
						</label>
					</div>
				</div>
			</fieldset>
			<?php echo JHtml::_('form.token'); ?>
			<input class="reditemReplyModalParentId" type="hidden" name="parent_id" value="0" />
			<input type="hidden" name="item_id" value="<?php echo $item->id; ?>" />
			<input type="hidden" name="return_url" value="<?php echo base64_encode($returnUrl); ?>" />
			<input type="hidden" name="id" value="" />
		</form>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_REDITEM_COMMENT_CANCEL'); ?></button>
		<button class="btn btn-primary" id="reditemReplyModalSubmit<?php echo $replyToId; ?>"><?php echo JText::_('COM_REDITEM_COMMENT_SUBMIT'); ?></button>
	</div>
</div>
