<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
?>
<div class="reset-confirm<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="row">
		<div class="page-header col-sm-12">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	</div>
	<?php endif; ?>
	<div class="row">
		<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="form-validate form-horizontal well col-sm-12">
			<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
				<fieldset>
					<p><?php echo JText::_($fieldset->label); ?></p>
					<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field) : ?>
						<div class="control-group row">
							<div class="control-label col-sm-4">
								<?php echo $field->label; ?>
							</div>
							<div class="controls col-sm-8">
								<?php echo $field->input; ?>
								</div>
							</div>
					<?php endforeach; ?>
				</fieldset>
			<?php endforeach; ?>

			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
