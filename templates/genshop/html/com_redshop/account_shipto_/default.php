<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$Itemid = JRequest::getInt('Itemid');

$add_addlink = "index.php?option=com_redshop&view=account_shipto&task=addshipping&Itemid=" . $Itemid;
$backlink = "index.php?option=com_redshop&view=account&Itemid=" . $Itemid;

?>
<div class="account_title">
  <h1>My account</h1>
</div>
<div class="col-md-3">
	<?php
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('modules');
		echo $renderer->render('personalmenu', $options, null);
	?>
</div>
<div class="col-md-9">
	<fieldset class="adminform">
		<div class='table-responsive account-box account-wrapper ac-orderlist'>
			<h4><?php echo JText::_('COM_REDSHOP_SHIPPING_ADDRESSES'); ?></h4>
			<table cellpadding="3" cellspacing="0" border="0" width="100%" class="acc_shipping">
				<?php
				if (OPTIONAL_SHIPPING_ADDRESS)
				{
					?>
					<tr>
						<td>- <?php echo JText::_('COM_REDSHOP_DEFAULT_SHIPPING_ADDRESS'); ?></td>
					</tr>
				<?php
				}

				for ($i = 0; $i < count($this->shippingaddresses); $i++)
				{
					$edit_addlink = "index.php?option=com_redshop&view=account_shipto&task=addshipping&infoid=" . $this->shippingaddresses[$i]->users_info_id . "&Itemid=" . $Itemid;?>
					<tr>
						<td>
							<?php    echo "- <a href='" . JRoute::_($edit_addlink) . "'>" . $this->shippingaddresses[$i]->text . "</a>"; ?>
						</td>
					</tr>
				<?php
				}
				?>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td  class="edit-account-btn">
						<a class="btn_submit" href="<?php echo JRoute::_($add_addlink); ?>">
							<?php echo JText::_('COM_REDSHOP_ADD_ADDRESS'); ?></a>&nbsp;
						<a class="btn_default" href="index.php?option=com_redshop&Itemid=127&view=checkout">
							<?php echo JText::_('COM_REDSHOP_BACK'); ?></a></td>
				</tr>
			</table>
		</div>

	</fieldset>
</div>