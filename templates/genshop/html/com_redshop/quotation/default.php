<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
$redTemplate = Redtemplate::getInstance();
$carthelper = new rsCarthelper;

$Itemid = JRequest::getInt('Itemid');
$return = JRequest::getString('return');
$session = JFactory::getSession();
$cart = $session->get('cart');

$detail = $this->detail;
$user = JFactory::getUser();
$extra_field = extra_field::getInstance();
$order_functions = order_functions::getInstance();
$info    = $order_functions->getBillingAddress($user->id);

$quotation_template = $redTemplate->getTemplate("quotation_request");

if (count($quotation_template) > 0 && $quotation_template[0]->template_desc != "")
{
	$template_desc = $quotation_template[0]->template_desc;
}
else
{
	$template_desc = "<fieldset class=\"adminform\"><legend>{order_detail_lbl}</legend> \r\n<table class=\"admintable\">\r\n<tbody>\r\n<tr>\r\n<td>{product_name_lbl}</td>\r\n<td>{quantity_lbl}</td>\r\n</tr>\r\n{product_loop_start}\r\n<tr>\r\n<td>{product_name}<br />{product_attribute}<br />{product_accessory}<br />{product_userfields}</td>\r\n<td>{update_cart}</td>\r\n</tr>\r\n{product_loop_end}\r\n</tbody>\r\n</table>\r\n</fieldset>\r\n<p>{customer_note_lbl}:{customer_note}</p>\r\n<fieldset class=\"adminform\"><legend>{billing_address_information_lbl}</legend> {billing_address}{quotation_custom_field_list} </fieldset> \r\n<table border=\"0\">\r\n<tbody>\r\n<tr>\r\n<td align=\"center\">{cancel_btn}{request_quotation_btn}</td>\r\n</tr>\r\n</tbody>\r\n</table>";
}?>
<script type="text/javascript">
	function validateInfo() {
		var frm = document.adminForm;

		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

		if (frm.user_email.value == '') {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_EMAIL_ADDRESS')?>");
			return false;
		}

		var email = frm.user_email.value;

		if (reg.test(email) == false) {
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_VALID_EMAIL_ADDRESS')?>");
			return false;
		}

		if (validateExtrafield(frm) == false) {
			return false;
		}
		return true;
	}
</script>
<?php

if (strstr($template_desc, "{product_loop_start}") && strstr($template_desc, "{product_loop_end}"))
{
	$template_sdata  = explode('{product_loop_start}', $template_desc);
	$template_start  = $template_sdata[0];
	$template_edata  = explode('{product_loop_end}', $template_sdata[1]);
	$template_end    = $template_edata[1];
	$template_middle = $template_edata[0];

	$template_middle = $carthelper->replaceCartItem($template_middle, $cart, 0, DEFAULT_QUOTATION_MODE);
	$template_desc   = $template_start . $template_middle . $template_end;
}

$template_desc = $carthelper->replaceLabel($template_desc);

if ($user->id)
{
	$template_desc = $carthelper->replaceBillingAddress($template_desc, $detail);
	$template_desc .= '<input type="hidden" name="user_email" id="user_email" value="' . $detail->user_email . '"/>';
}
else
{
	$billing = '<table width="90%">
	<tr><td width="100" align="right">' . JText::_('COM_REDSHOP_EMAIL') . ':</td>
		<td><input type="text" name="user_email" id="user_email" value=""/></td></tr>';

	if (strstr($template_desc, "{quotation_custom_field_list}"))
	{
		$billing .= $extra_field->list_all_field(16, $detail->user_info_id, "", "tbl");
		$template_desc = str_replace("{quotation_custom_field_list}", "", $template_desc);
	}
	else
	{
		$template_desc = $extra_field->list_all_field(16, $detail->user_info_id, "", "", $template_desc);
	}

	$billing .= '</table>';

	$template_desc = str_replace("{billing_address_information_lbl}", JText::_('COM_REDSHOP_BILLING_ADDRESS_INFORMATION_LBL'), $template_desc);
	$template_desc = str_replace("{billing_address}", $billing, $template_desc);
}

$userPointText   = '';
$userPoint       = '';
$userPointLbl    = '';
$point = $cart['user_point'];

if (!empty($point))
{
	$currentPoint = $point;
}
else
{
	$currentPoint = $info->point;
}

if (!empty($user->id))
{
	if (USER_POINT == 1)
	{
		if (!empty($info->point))
		{
			$userPointText = JText::sprintf('COM_REDSHOP_CURRENT_POINT', $info->point);
			$userPointLbl = JText::_('COM_REDSHOP_POINT');
			$userPoint .= '<div class="update_cart"><form action="index.php?option=com_redshop&view=cart&tmpl=component" name="discount_form" method="POST" >';
			$userPoint .= '<input class="inputbox" type="text" value="' . $currentPoint . '" name="user_point" id="user_point" size="5">';
			$userPoint .= '<input type="submit" id="user_point_button"  class="blackbutton" value="' . JText::_('COM_REDSHOP_SUBMIT') . '" onclick="document.user_point_form.submit();" />';
			$userPoint .= '<input type="hidden" name="task" value="update"><input type="hidden" name="Itemid" value="' . $Itemid . '">';
			$userPoint .= '</form></div>';
		}
	}
}

$template_desc = str_replace("{user_point_text}",  $userPointText, $template_desc);
$template_desc = str_replace("{user_point_lbl}",  $userPointLbl, $template_desc);
$template_desc = str_replace("{user_point}", $userPoint, $template_desc);

$cancel_btn = '<input type="submit" class="greenbutton" name="cancel" value="' . JText::_("COM_REDSHOP_CANCEL") . '" onclick="javascript:document.adminForm.task.value=\'cancel\';"/>';
$quotation_btn = '<input type="submit" class="greenbutton" name="addquotation" value="' . JText::_("COM_REDSHOP_REQUEST_QUOTATION") . '" onclick="return validateInfo();"/>';
$quotation_btn .= '<input type="hidden" name="option" value="com_redshop" />';
$quotation_btn .= '<input type="hidden" name="Itemid" value="' . $Itemid . '" />';
$quotation_btn .= '<input type="hidden" name="task" value="addquotation" />';
$quotation_btn .= '<input type="hidden" name="view" value="quotation" />';
$quotation_btn .= '<input type="hidden" name="return" value="' . $return . '" />';

$template_desc = str_replace("{cancel_btn}", $cancel_btn, $template_desc);
$template_desc = str_replace("{request_quotation_btn}", $quotation_btn, $template_desc);

$template_desc = str_replace("{order_detail_lbl}", JText::_('COM_REDSHOP_ORDER_DETAIL_LBL'), $template_desc);
$template_desc = str_replace("{customer_note_lbl}", JText::_('COM_REDSHOP_CUSTOMER_NOTE_LBL'), $template_desc);
$template_desc = str_replace("{customer_note}", '<textarea name="quotation_note" id="quotation_note"></textarea>', $template_desc);

$template_desc = '<form action="' . JRoute::_($this->request_url) . '" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">' . $template_desc . '</form>';

echo eval("?>" . $template_desc . "<?php ");?>
