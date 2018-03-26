<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('script', 'system/core.js', false, true);
?>
<form method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">

		<div class="row-fluid pagination-centered">
			<h1><img src="<?php echo JURI::root(true) . '/media/com_redslider/images/redslider_logo.png'; ?>" alt="redSLIDER Logo"></h1>

			<h3><?php echo JText::_('COM_REDSLIDER_WELCOME_TEXT') . $this->redsliderversion ?></h3>
			<br/>

			<p>
				<?php if ($this->installationType != 'update'): ?>
				<button class="btn btn-large btn-warning" type="button" onclick="Joomla.submitbutton('cpanel.demoContentInsert')">
					<i class="icon-download-alt"> </i>
					<?php echo JText::_('COM_REDSLIDER_WELCOME_INSTALL_DEMO') ?>
				</button>
				<?php endif; ?>
				<button class="btn btn-large btn-success" type="button" onclick="Joomla.submitbutton('welcome.toPanel')"
				        href="#">
					<i class="icon-signin"> </i>
					<?php echo JText::_('COM_REDSLIDER_WELCOME_USE_IT_NOW') ?>
				</button>
			</p>
		</div>

		<div class="row-fluid pagination-centered">
			<p class="muted">
				<small>
					<?php echo JText::_('COM_REDSLIDER_WELCOME_RELEASED_UNDER') ?>
					<a href="http://www.gnu.org/licenses/gpl-2.0.html">
						GNU General Public License
					</a>
				</small>
			</p>

			<p class="muted">
				<small>
					<?php echo JText::_('COM_REDSLIDER_WELCOME_REMEMBER_CHECK_UPDATES') ?>
					<a href="http://redcomponent.com/">
						redCOMPONENT
					</a>
				</small>
			</p>
		</div>

	</div>	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
