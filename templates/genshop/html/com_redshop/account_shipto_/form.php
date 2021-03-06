<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JPluginHelper::importPlugin('redshop_checkout');
$dispatcher = RedshopHelperUtility::getDispatcher();
$Itemid = JRequest::getInt('Itemid');
$userhelper = rsUserHelper::getInstance();

$post = (array) $this->shippingAddresses;

$post['firstname_ST']    = $post['firstname'];
$post['lastname_ST']     = $post['lastname'];
$post['address_ST']      = $post['address'];
$post['city_ST']         = $post['city'];
$post['zipcode_ST']      = $post['zipcode'];
$post['phone_ST']        = $post['phone'];
$post['country_code_ST'] = $post['country_code'];
$post['state_code_ST']   = $post['state_code'];

$input = JFactory::getApplication()->input;
$infoId = $input->getInt('infoid', 0);

$dispatcher->trigger('onRenderCustomField', array($infoId));
?>
<script type="text/javascript">
	function cancelForm(frm) {
		frm.task.value = 'cancel';
		frm.submit();
	}
	function validateInfo() {
		var frm = document.adminForm;

		if (frm.firstname.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_FIRST_NAME')?>");
			return false;
		}

		if (frm.lastname.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_LAST_NAME')?>");
			return false;
		}

		if (frm.address.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_ADDRESS')?>");
			return false;
		}

		if (frm.zipcode.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_ZIPCODE')?>");
			return false;
		}

		if (frm.city.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_CITY')?>");
			return false;
		}

		if (frm.phone.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_PHONE')?>");
			return false;
		}

		return true;
	}

</script>
<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">

	<div id="divShipping">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_REDSHOP_SHIPPING_ADDRESSES');?></legend>
			<?php    echo $userhelper->getShippingTable($post, $this->billingAddresses->is_company, $this->lists);    ?>
			<input type="button" class="button" name="back" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>"
			onclick="javascript:cancelForm(this.form);">
			<input type="submit" class="button" name="submitbtn" value="<?php echo JText::_('COM_REDSHOP_SAVE'); ?>">
		</fieldset>
	</div>
	<input type="hidden" name="cid" value="<?php echo $this->shippingAddresses->users_info_id; ?>"/>
	<input type="hidden" name="user_id" value="<?php echo $this->billingAddresses->user_id; ?>"/>
	<input type="hidden" name="is_company" value="<?php echo $this->billingAddresses->is_company; ?>"/>
	<input type="hidden" name="email" value="<?php echo $this->billingAddresses->user_email; ?>"/>
	<input type="hidden" name="shopper_group_id" value="<?php echo $this->billingAddresses->shopper_group_id; ?>"/>
	<input type="hidden" name="company_name" value="<?php echo $this->billingAddresses->company_name; ?>"/>
	<input type="hidden" name="vat_number" value="<?php echo $this->billingAddresses->vat_number; ?>"/>
	<input type="hidden" name="tax_exempt" value="<?php echo $this->billingAddresses->tax_exempt; ?>"/>
	<input type="hidden" name="requesting_tax_exempt"
	       value="<?php echo $this->billingAddresses->requesting_tax_exempt; ?>"/>
	<input type="hidden" name="tax_exempt_approved"
	       value="<?php echo $this->billingAddresses->tax_exempt_approved; ?>"/>
	<input type="hidden" name="task" value="save"/>
	<input type="hidden" name="address_type" value="ST"/>
	<input type="hidden" name="view" value="account_shipto"/>
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
	<input type="hidden" name="option" value="com_redshop"/>
</form>
