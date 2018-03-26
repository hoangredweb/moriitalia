<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Item
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

JHTML::_('behavior.formvalidation');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal-thumb');
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

		<?php if ($this->item->id) : ?>
		jQuery('#jform_type_id').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		<?php endif; ?>

		<?php if (!$this->typeId) : ?>
		jQuery('#jform_categories').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		jQuery('#jform_template_id').prop('disabled', true).trigger("liszt:updated").prop('disabled', false);
		<?php endif; ?>
	});

	/*
	 * Add form validation
	 */
	Joomla.submitbutton = function (pressbutton)
	{
		submitbutton(pressbutton);
	}

	submitbutton = function (pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton)
		{
			form.task.value = pressbutton;
		}

		if ((pressbutton != 'item.close') && (pressbutton != 'item.cancel'))
		{
			if (document.formvalidator.isValid(form))
			{
				form.submit();
			}
		}
		else
		{
			form.submit();
		}
	}
</script>

<div class="reditem-edit-form">
	<form enctype="multipart/form-data"
		action="index.php?option=com_reditem&task=item.edit&id=<?php echo $this->item->id; ?>"
		method="post" name="adminForm" class="form-validate"
		id="adminForm">
		<ul class="nav nav-tabs" id="categoryTab">
			<li class="active">
				<a href="#item-information" data-toggle="tab"><strong><?php echo JText::_('COM_REDITEM_ITEM_GENERAL_INFORMATION'); ?></strong></a>
			</li>
			<li>
				<a href="#item-customfields" data-toggle="tab" id="additional-link"><strong><?php echo JText::_('COM_REDITEM_ITEM_ADDITIONAL_INFORMATION'); ?></strong></a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="item-information">
				<div class="row-fluid">
					<div class="span12">
						<fieldset class="form-horizontal">
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
									<?php echo $this->form->getLabel('categories'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('categories'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('access'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('access'); ?>
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
									<?php echo $this->form->getLabel('featured'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('featured'); ?>
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
									<?php echo $this->form->getLabel('publish_up'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('publish_up'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('publish_down'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('publish_down'); ?>
								</div>
							</div>
							<div class="control-group">
								<?php $relatedItemsField = $this->form->getField('related_items', 'params'); ?>
								<div class="control-label">
									<?php echo $relatedItemsField->label; ?>
								</div>
								<div class="controls">
									<?php echo $relatedItemsField->input; ?>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="control-group">
								<?php $metaDescriptionField = $this->form->getField('meta_description', 'params'); ?>
								<div class="control-label">
									<?php echo $metaDescriptionField->label; ?>
								</div>
								<div class="controls">
									<?php echo $metaDescriptionField->input; ?>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="control-group">
								<?php $metaKeywordsField = $this->form->getField('meta_keywords', 'params'); ?>
								<div class="control-label">
									<?php echo $metaKeywordsField->label; ?>
								</div>
								<div class="controls">
									<?php echo $metaKeywordsField->input; ?>
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="control-group">
								<?php $metaRobotsField = $this->form->getField('meta_robots', 'params'); ?>
								<div class="control-label">
									<?php echo $metaRobotsField->label; ?>
								</div>
								<div class="controls">
									<?php echo $metaRobotsField->input; ?>
								</div>
								<div class="clearfix"></div>
							</div>
						</fieldset>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="item-customfields">
				<?php echo $this->loadTemplate('customfields'); ?>
			</div>
		</div>
		<?php echo $this->form->getInput('id'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
