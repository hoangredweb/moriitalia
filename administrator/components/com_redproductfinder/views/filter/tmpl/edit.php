<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHTML::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "filter.cancel" || document.formvalidator.isValid(document.getElementById("redproductfinder-form")))
		{
			Joomla.submitform(task, document.getElementById("redproductfinder-form"));
		}
	};
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div>
						<?php echo $this->form->renderField("filter_name"); ?>
					</div>
					<div>
						<?php echo $this->form->renderField("type_select"); ?>
					</div>
					<div>
						<?php echo $this->form->renderField("select_name"); ?>
					</div>
					<div>
						<?php echo $this->form->renderField("tag_id"); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('published'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="filters" />
	<?php echo JHtml::_('form.token'); ?>
</form>