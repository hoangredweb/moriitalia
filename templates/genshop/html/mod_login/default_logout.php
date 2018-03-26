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

JHtml::_('behavior.keepalive');

$redhelper = new redhelper;
$menus = $redhelper->getRedshopMenuItems();

foreach ($menus as $oneMenuItem)
{
	if ($redhelper->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'account')))
	{
		$Itemid_account = $oneMenuItem->id;

		break;
	}
}
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
<?php if ($params->get('greeting')) : ?>
	<div class="login-greeting">
		<div class="greetingtext">
			<?php if ($params->get('name') == 0) : {
				echo '<i class=\'icon icon-signin\'></i>'.JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
			} else : {
				echo '<i class=\'icon icon-signin\'>'.JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
			} endif; ?>
		</div>
		<div class="user_list">
			<ul class="logout-shadow">
				<li><a href="<?php echo JRoute::_('index.php?option=com_redshop&view=account&Itemid='.$Itemid_account);?>"><?php echo JText::_('MOD_LOGIN_ACOUNT_LABEL')?></a></li>
				<li><input type="submit" name="Submit" value="<?php echo JText::_('MOD_LOGIN_LOGOUT_LINK');?>" /></li>
			</ul>
		</div>
	</div>
<?php endif; ?>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.logout" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>