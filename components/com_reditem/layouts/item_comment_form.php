<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

RHelperAsset::load('jquery/jquery.autosize.min.js', 'com_reditem');

$item = $displayData['item'];

$editor = JFactory::getEditor();
$uri    = JFactory::getURI();
$user   = ReditemHelperSystem::getUser();

$returnUrl	= $uri->toString();
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('.reditemCommentInput').autosize();
		});
	})(jQuery);
</script>

<div class="reditemCommentForm">
	<form action="index.php?option=com_reditem&task=comment.add" method="post">
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="controls">
					<textarea class="reditemCommentInput" name="comment" style="width: 100%;"></textarea>
				</div>
			</div>
			<?php if (!$user->guest) : ?>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="private" value="1" /> <?php echo JText::_('COM_REDITEM_COMMENT_PRIVATE_CHECKBOX'); ?>
					</label>
				</div>
			</div>
			<?php endif; ?>
			<hr />
			<div class="control-group">
				<div class="controls">
					<input type="reset" class="btn" value="<?php echo JText::_('COM_REDITEM_COMMENT_CLEAR_FORM'); ?>" />
					<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_REDITEM_COMMENT_SUBMIT'); ?>" />
				</div>
			</div>
		</fieldset>
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="parent_id" value="0" />
		<input type="hidden" name="item_id" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="return_url" value="<?php echo base64_encode($returnUrl); ?>" />
	</form>
</div>
