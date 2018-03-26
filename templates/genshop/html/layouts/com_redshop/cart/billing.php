<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * =============================
 * @var  array   $displayData       Display data
 * @var  object  $billingaddresses  Billing addresses
 */
extract($displayData);

$extraSections = ($billingaddresses->is_company == 1) ? extraField::SECTION_COMPANY_BILLING_ADDRESS : extraField::SECTION_PRIVATE_BILLING_ADDRESS;

?>

<div class="redshop-billingaddresses">
	<?php if ($billingaddresses->is_company == 1) : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_COMPANY_NAME') ?>:</label>
            <div class="col-xs-7"><?php echo $billingaddresses->company_name; ?></div>
        </div>
	<?php endif; ?>

    <div class="row">
        <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_FIRSTNAME'); ?>:</label>
        <div class="col-xs-7"><?php echo $billingaddresses->firstname; ?></div>
    </div>

    <div class="row">
        <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_LASTNAME'); ?>:</label>
        <div class="col-xs-7"><?php echo $billingaddresses->lastname; ?></div>
    </div>

	<?php if ($billingaddresses->address != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_ADDRESS'); ?>:</label>
            <div class="col-xs-7"><?php echo $billingaddresses->address; ?> <?php echo $billingaddresses->extraField['rs_kerry_billing_ward']; ?> <?php echo $billingaddresses->extraField['rs_kerry_billing_district']; ?> <?php echo $billingaddresses->extraField['rs_kerry_billing_city']; ?></div>
        </div>
	<?php endif; ?>

    <?php echo RedshopHelperExtrafields::listAllFieldDisplay($extraSections, $billingaddresses->users_info_id) ?>



	<?php if ($billingaddresses->phone != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_PHONE'); ?>:</label>
            <div class="col-xs-7"><?php echo $billingaddresses->phone; ?></div>
        </div>
	<?php endif; ?>

	<?php if ($billingaddresses->user_email != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_EMAIL'); ?>:</label>
            <div class="col-xs-7"><?php echo $billingaddresses->user_email ? $billingaddresses->user_email : $user->email; ?></div>
        </div>
	<?php endif; ?>
</div>
