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
JHTML::_('behavior.modal');

JLoader::load('RedshopHelperAdminCategory');
JLoader::load('RedshopHelperProduct');
JLoader::load('RedshopHelperHelper');

$config = Redconfiguration::getInstance();
$producthelper = new producthelper;
$redhelper = new redhelper;

$uri = JURI::getInstance();
$url = $uri->root();
$Itemid = JRequest::getInt('Itemid', 903);
$wishlists = $this->wishlists;
$product_id = JRequest::getInt('product_id');
$user = JFactory::getUser();
$session = JFactory::getSession();
$auth = $session->get('auth');

?>
<div id="newwishlist" class="wishlist_prompt_header">
	<?php

	$pagetitle = JText::_('COM_REDSHOP_LOGIN_NEWWISHLIST');
	?>

	<h3 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $pagetitle; ?>
	</h3>

</div>

<div id="wishlist" class="wishlist_prompt_text">
	<?php
	if ($user->id || (isset($auth['users_info_id']) && $auth['users_info_id'] > 0))
	{
		$wishreturn = JRoute::_('index.php?loginwishlist=1&option=com_redshop&view=wishlist&Itemid=' . $Itemid, false);
		$app->redirect($wishreturn);
	}
	else
	{
	$pagetitle = JText::_('COM_REDSHOP_LOGIN_PROMPTWISHLIST');
	?>

	<p class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $pagetitle; ?>
	</p>

	<form name="adminForm" method="post" action="">
		<table class="adminlist">
			<tbody>
			<tr>
				<td colspan="3" align="center" class="wishlist_prompt_button_wrapper">
					<input type="button" class="wishlist_prompt_button_login btn btn-primary"
					       value="<?php echo JText::_('COM_REDSHOP_ADD_TO_LOGINWISHLIST'); ?>"
					       onclick="window.parent.location.href='<?php echo JRoute::_('index.php?option=com_redshop&view=login&wishlist=1'); ?>'"/>&nbsp;
					<input type="button" class="wishlist_prompt_button_create btn btn-cancel"
					       value="<?php echo JText::_('COM_REDSHOP_CREATE_LOGINACCOUNT'); ?>"
					       onclick="window.parent.location.href='<?php echo JRoute::_('index.php?option=com_redshop&view=registration&Itemid=' . $Itemid . '&wishlist=1'); ?>'"/>
				</td>
			</tr>
			</tbody>
		</table>
		<input type="hidden" name="wishlist" value="1"/>
		<input type="hidden" name="view" value="wishlist"/>
		<input type="hidden" name="boxchecked" value=""/>
		<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
		<input type="hidden" name="option" value="com_redshop"/>
		<input type="hidden" name="task" value="viewloginwishlist"/>
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<?php
	}