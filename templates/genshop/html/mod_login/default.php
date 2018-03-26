<?php
// Wright v.3 Override: Joomla 3.2.2
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');

$app   = JFactory::getApplication();
$menu  = $app->getMenu();
$com   = JComponentHelper::getComponent('com_users');
$items = $menu->getItems('component_id', $com->id);

foreach ($items as $menu) {
	if ($menu->query['option'] == 'com_users' && $menu->query['view'] == 'login') {
		$Itemid = $menu->id;
		break;
	}
}
foreach ($items as $menu) {
	if ($menu->query['option'] == 'com_users' && $menu->query['view'] == 'registration') {
		$Itemid_reg = $menu->id;
		break;
	}
}

?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
	<?php if ($params->get('pretext')) : ?>
		<div class="pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<?php $link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid); ?>
	<div class="login-greeting">
		<a href="<?php echo $link;?>"><i class="icon icon-signin"></i> <?php echo JText::_('MOD_LOGIN_LABEL') ?></a>
		<div class="user_list">
			<ul class="shadow-box">
				<?php
				jimport('joomla.application.module.helper');
				$modules = JModuleHelper::getModules('login-social');
				foreach($modules as $module)
				{
				echo JModuleHelper::renderModule($module);
				}
				?>
				<!-- <li><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid='.$Itemid_reg);?>"><?php echo JText::_('MOD_LOGIN_NEW_ACCOUNT_TEXT')?><br/><?php echo JText::_('MOD_LOGIN_REGISTRY_LABEL')?></a></li> -->
			</ul>
		</div>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="posttext">
			<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>