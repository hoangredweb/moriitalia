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
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		<?php if ($this->item->id) : ?>
		jQuery('#controls-item_id').find('input.jmodal').prop('disabled', 'disabled');
		jQuery('#jform_private').find('label').addClass('disabled').unbind('click');
		<?php endif; ?>
	});
</script>

<form enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=comment.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">
	<div class="row-fluid">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('item_id'); ?>
			</div>
			<div class="controls" id="controls-item_id">
				<?php echo $this->form->getInput('item_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('parent_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('parent_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('private'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('private'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('user_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('user_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('comment'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('comment'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('state'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('state'); ?>
			</div>
		</div>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
