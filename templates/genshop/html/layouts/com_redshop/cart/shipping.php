<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

extract($displayData);

$extra_section = ($shippingaddresses->is_company == 1) ? RedshopHelperExtrafields::SECTION_COMPANY_SHIPPING_ADDRESS : RedshopHelperExtrafields::SECTION_PRIVATE_SHIPPING_ADDRESS;
?>

<div class="redshop-shippingaddresses">


    <div class="row">
        <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_FIRSTNAME'); ?>:</label>
        <div class="col-xs-7"><?php echo $shippingaddresses->firstname; ?></div>
    </div>

    <div class="row">
        <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_LASTNAME'); ?>:</label>
        <div class="col-xs-7"><?php echo $shippingaddresses->lastname; ?></div>
    </div>

	<?php if ($shippingaddresses->address != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_ADDRESS'); ?>:</label>
            <div class="col-xs-7"><?php echo $shippingaddresses->address; ?></div>
        </div>
	<?php endif; ?>



	<?php if ($shippingaddresses->phone != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_PHONE'); ?>:</label>
            <div class="col-xs-7"><?php echo $shippingaddresses->phone; ?></div>
        </div>
	<?php endif; ?>

        <?php if ($billingaddresses->user_email != "") : ?>
        <div class="row">
            <label class="col-xs-5"><?php echo JText::_('COM_REDSHOP_EMAIL'); ?>:</label>
            <div class="col-xs-7"><?php echo $billingaddresses->user_email ? $billingaddresses->user_email : $user->email; ?></div>
        </div>
    <?php endif; ?>


</div>
