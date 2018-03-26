<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$url = JURI::base();
$user = JFactory::getUser();
$productId = JRequest::getInt('product_id');
JHtml::_('behavior.framework');
?>
<?php if (!empty($user->id)) : ?>
<div id="add-wedding-list">
	<form name="newwishlistForm" method="post" action="<?php echo JRoute::_('index.php?option=com_redshop&view=wedding_list'); ?>">
		<table>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="<?php echo JText::_('COM_REDSHOP_CREATE_SAVE'); ?>"/>&nbsp;
					<input type="button" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>"
						       onclick="window.parent.SqueezeBox.close();"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="product_id" value="<?php echo $productId ?>"/>
		<input type="hidden" name="view" value="wedding_list"/>
		<input type="hidden" name="option" value="com_redshop"/>
		<input type="hidden" name="task" value="createsave"/>
	</form>
</div>
<?php else: ?>
	<form action="<?php echo JRoute::_('index.php?option=com_redshop&view=login&Itemid=' . $Itemid); ?>" method="post">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
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
	</table>
	<input type="hidden" name="task" id="task" value="setlogin">
	<input type="hidden" name="option" id="option" value="com_redshop"/>
</form>
<?php endif; ?>