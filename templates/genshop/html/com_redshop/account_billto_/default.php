<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$userhelper = rsUserHelper::getInstance();
$user       = JFactory::getUser();
$Itemid     = JRequest::getInt('Itemid');

$post = (array) $this->billingaddresses;
$post["email1"] = $post["email"] = $post ["user_email"];

if ($user->username)
{
	$post["username"] = $user->username;
}

$create_account = 1;

if ($post["user_id"] < 0)
{
	$create_account = 0;
}

JPluginHelper::importPlugin('redshop_checkout');
$dispatcher = RedshopHelperUtility::getDispatcher();
$dispatcher->trigger('onRenderCustomField', array($this->billingaddresses->users_info_id));
?>
<script type="text/javascript">
	function cancelForm(frm) {
		frm.task.value = 'cancel';
		frm.submit();
	}
</script>
<?php
if ($this->params->get('show_page_heading', 1))
{
	?>
	<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->escape(JText::_('COM_REDSHOP_BILLING_ADDRESS_INFORMATION_LBL')); ?></h1>
<?php
}
?>
<div class="row">
<div class='col-md-3 hiddenonmodal'>
  	<?php
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('modules');
		echo $renderer->render('personalmenu', $options, null);
	?>
</div>
<div class="col-md-9">
	<form action="<?php echo JRoute::_($this->request_url); ?>" method="post" name="adminForm" id="adminForm"
	      enctype="multipart/form-data">
		<fieldset class="adminform">
			<?php
				$tplbilling = $userhelper->getBillingTable($post, $this->billingaddresses->is_company, $this->lists, 0, 0, $create_account);
				$tplbilling = str_replace("{person_information}", JText::_('COM_REDSHOP_ACCOUNT_PERSON_INFORMATION'), $tplbilling);
				$tplbilling = str_replace("{account_title}", JText::_('COM_REDSHOP_ACCOUNT_INFORMATION'), $tplbilling);
				echo $tplbilling;
			?>
			<div class="buttongroup">
				<input type="button" class="btn cancel" name="back" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>" onclick="javascript:cancelForm(this.form);">
				<input type="submit" class="btn btn-primary" name="submitbtn" value="<?php echo JText::_('COM_REDSHOP_SAVE'); ?>">
			</div>
		</fieldset>
		<input type="hidden" name="cid" value="<?php echo $this->billingaddresses->users_info_id; ?>"/>
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $post["user_id"]; ?>"/>
		<input type="hidden" name="task" value="save"/>
		<input type="hidden" name="view" value="account_billto"/>
		<input type="hidden" name="address_type" value="BT"/>
		<input type="hidden" name="is_company" id="is_company" value="<?php echo $this->billingaddresses->is_company; ?>"/>
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
	</form>
</div>
</div>

