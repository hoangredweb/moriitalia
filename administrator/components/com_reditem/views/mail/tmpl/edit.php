<?php
/**
 * @package     RedITEM
 * @subpackage  Mail
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');
JHtml::_('rbootstrap.tooltip');

$isNew = true;

if ($this->item->id)
{
	$isNew = false;
}

?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		<?php if (!$isNew) : ?>
		jQuery('#jform_section').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		<?php endif; ?>

		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});
	});
</script>
<form
	enctype="multipart/form-data"
	action="index.php?option=com_reditem&task=mail.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">
	<div class="row-fluid">
		<div class="span8">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('section'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('section'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('type_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('type_id'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('subject'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('subject'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('description'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('content'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('content'); ?>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class='template_tags'>
				<?php echo $this->loadTemplate('tags'); ?>
			</div>
		</div>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
