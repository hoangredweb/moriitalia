<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JHTML::_('behavior.tooltip');
$app        = JFactory::getApplication();
$Itemid     = JRequest::getInt('Itemid');
$loginlink  = 'index.php?option=com_redshop&view=login&Itemid=' . $Itemid;
$mywishlist = JRequest::getString('wishlist');
$menu = $app->getMenu();
$menuItems = $menu->getItems('link', 'index.php?option=com_users&view=reset', true);
$product_id = JRequest::getInt('product_id');
$document = JFactory::getDocument();
$renderer = $document->loadRenderer('modules');
$options = array('style' => 'raw');

if ($mywishlist != '')
{
	$newuser_link = 'index.php?wishlist=' . $mywishlist . '&option=com_redshop&view=registration&Itemid=' . $Itemid;
}
else
{
	$newuser_link = 'index.php?option=com_redshop&view=registration&Itemid=' . $Itemid;
}

$params       = $app->getParams('com_redshop');
$returnitemid = $params->get('login', $Itemid);

?>
<h1>
	<?php echo JText::_('COM_REDSHOP_LOGIN_DESCRIPTION'); ?>
</h1>
<form action="<?php echo JRoute::_($loginlink); ?>" method="post">
	<div class="row">
		<div class="left col-md-4 col-sm-5 col-xs-12">
			<div class="user row social">
						<span class="col-md-12 text">
							<?php echo JText::_('COM_REDSHOP_LOGIN_DESCRIPTION_SOCIAL'); ?>
						</span>
						<span class="col-md-12 login-social">
							<?php echo $renderer->render('login-social', $options, null); ?>
						</span>

			</div>
			<p class="or" style="text-align:center;">
				<span><?php echo JText::_('COM_REDSHOP_LOGIN_OR_TEXT'); ?></span>
			</p>
			<div class="user row form">
				<span class="col-md-12 text">
					<?php echo JText::_('COM_REDSHOP_USERNAME'); ?>:
				</span>
				<span class="col-md-12">
					<input class="inputbox" type="text" id="username" name="username"/>
				</span>
			</div>
			<div class=" pass_word row" style="margin-top:5px;">
				<span class="col-md-12 text">
					<?php echo JText::_('COM_REDSHOP_PASSWORD'); ?>:
				</span>
				<span class="col-md-12">
					<input class="inputbox" id="password" name="password" type="password"/>
				</span>
				<a class="forget col-sm-12" href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid=' . $menuItems->id); ?>">
							<?php echo JText::_('COM_REDSHOP_FORGOT_PWD_LINK'); ?></a>
			</div>
			<div class="login">
				<input type="submit" name="submit" class="btn btn-primary button" value="<?php echo JText::_('COM_REDSHOP_LOGIN'); ?>">
			</div>
		</div>
		<div class="col-md-1 hidden-sm col-xs-12">
		</div>
		<div class="right col-md-5 col-sm-7 col-xs-12">
			<h2><strong><?php echo JText::_('COM_REDSHOP_REGISTRY_TITLE'); ?></strong></h2>
			<p><?php echo JText::_('COM_REDSHOP_REGISTRY_DESCRIPTION'); ?></p>
			<a class="btn btn-primary button" href="<?php echo JRoute::_($newuser_link); ?>">
							<?php echo JText::_('COM_REDSHOP_CREATE_USER_LINK'); ?></a>


		</div>
		<div class="col-md-1 hidden-sm col-xs-12">
		</div>
	</div>
			<!-- <table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td colspan="5" height="40">
						<p><?php echo JText::_('COM_REDSHOP_LOGIN_DESCRIPTION'); ?></p>
					</td>
				</tr>
				<tr>
					<td>
						<label for="username">
							<?php echo JText::_('COM_REDSHOP_USERNAME'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" id="username" name="username"/>
					</td>
					<td>
						<label for="password">
							<?php echo JText::_('COM_REDSHOP_PASSWORD'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" id="password" name="password" type="password"/>
					</td>

					<td><input type="submit" name="submit" class="button" value="<?php echo JText::_('COM_REDSHOP_LOGIN'); ?>">
					</td>
				</tr>
				<tr>
					<td colspan="5">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="5">
						<a href="<?php echo JRoute::_($newuser_link); ?>">
							<?php echo JText::_('COM_REDSHOP_CREATE_USER_LINK'); ?></a>&nbsp;/&nbsp;<a
							href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
							<?php echo JText::_('COM_REDSHOP_FORGOT_PWD_LINK'); ?></a>
					</td>
				</tr>
			</table> -->
			<input type="hidden" name="task" id="task" value="setlogin">
			<input type="hidden" name="mywishlist" id="mywishlist" value="<?php echo JRequest::getString('wishlist'); ?>">
			<input type="hidden" name="returnitemid" id="returnitemid" value="<?php echo $returnitemid; ?>">
			<input type="hidden" name="option" id="option" value="com_redshop"/>
			<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
</form>
