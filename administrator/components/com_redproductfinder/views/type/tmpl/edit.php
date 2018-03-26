<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHTML::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "type.cancel" || document.formvalidator.isValid(document.getElementById("redproductfinder-form")))
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
						<?php echo $this->form->renderField('type_name'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('form_id'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('class_name'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('type_select'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('tooltip'); ?>
					</div>
					<div>
						<?php echo $this->form->renderField('published'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->form->renderField('id'); ?>
	<input type="hidden" name="task" value="types" />
	<?php echo JHtml::_('form.token'); ?>
</form>
