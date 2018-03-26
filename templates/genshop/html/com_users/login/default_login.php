<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<div class="login<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="row">
		<div class="page-header col-md-6">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	</div>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif; ?>

		<?php if ($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JText::_('COM_USERS_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>
	<div class="row">
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well col-sm-7 signin">

		<fieldset>
			<?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<div class="control-group row">
						<span class="control-label col-sm-4">
							<?php echo $field->label; ?>
						</span>
						<span class="controls col-sm-8">
							<?php echo $field->input; ?>
						</span>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ($this->tfa): ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getField('secretkey')->label; ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getField('secretkey')->input; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div  class="control-group Remember">
				<div class="control-label"><label><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME') ?></label></div>
				<div class="controls"><input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"/></div>
			</div>
			<?php endif; ?>
			<div class="row">
				<div class="col-sm-4">
				</div>
				<div class="col-sm-8">
					<div class="Forgot-password">
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
						<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
					</div>

					<div class="control-group submitbutton">
						<div class="controls">
							<button type="submit" class="btn btn-primary">
								<?php echo JText::_('JLOGIN'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>


			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
	</div>
</div>