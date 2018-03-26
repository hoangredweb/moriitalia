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

// Load RedCORE bootstrap CSS
RHelperAsset::load('lib/bootstrap/css/bootstrap.min.css', 'redcore');
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

		if ((pressbutton != 'itemedit.close') && (pressbutton != 'itemedit.cancel'))
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
		action="<?php echo JRoute::_('index.php?option=com_reditem&task=itemedit.edit&id=' . $this->item->id);?>"
		method="post" name="adminForm" class="form-validate"
		id="adminForm">

		<?php echo $this->content;?>

		<?php echo $this->form->getInput('id'); ?>
		<input type="hidden" name="jform[type_id]" value="<?php echo $this->typeId;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
