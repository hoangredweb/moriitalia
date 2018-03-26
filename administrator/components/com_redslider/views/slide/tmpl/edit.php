<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');

$isNew = true;
$user = JFactory::getUser();
JPluginHelper::importPlugin('redslider_sections');
$dispatcher = RFactory::getDispatcher();

if ($this->item->id)
{
	$isNew = false;
}
?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});
	});
</script>
<form enctype="multipart/form-data"
	action="index.php?option=com_redslider&task=slide.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">

	<div class="row-fluid">
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
				<?php echo $this->form->getLabel('gallery_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('gallery_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('template_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('template_id'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('title'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('title'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('alias'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('alias'); ?>
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

		<?php // Load template dynamically from plugin ?>
		<?php if ($user->authorise('core.create', 'com_redslider') && $user->authorise('core.edit', 'com_redslider') && $user->authorise('core.edit.state', 'com_redslider')): ?>
			<?php if ($this->sectionId): ?>
				<?php $sectionTemplates = $dispatcher->trigger('onSlidePrepareTemplate', array($this, $this->sectionId)); ?>
				<?php if (count($sectionTemplates)):?>
					<?php foreach ($sectionTemplates as $template): ?>
						<?php echo $template ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif;?>
	</div>

	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
