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

$order_functions = order_functions::getInstance();
$extra_field = extra_field::getInstance();
$extra_section = ($billingaddresses->is_company == 1) ? extraField::SECTION_COMPANY_BILLING_ADDRESS : extraField::SECTION_PRIVATE_BILLING_ADDRESS;
?>

<table border="0">


	<tr>
		<td><?php echo JText::_('COM_REDSHOP_FIRSTNAME');?>:</td>
		<td><?php echo $billingaddresses->firstname;?></td>
	</tr>

	<tr>
		<td><?php echo JText::_('COM_REDSHOP_LASTNAME');?>:</td>
		<td><?php echo $billingaddresses->lastname;?></td>
	</tr>

	<?php if ($billingaddresses->address != "") : ?>
	<tr>
		<td><?php echo JText::_('COM_REDSHOP_ADDRESS');?>:</td>
		<td><?php echo $billingaddresses->address;?></td>
	</tr>
	<?php endif; ?>



	<?php if ($billingaddresses->phone != "") : ?>
	<tr>
		<td><?php echo JText::_('COM_REDSHOP_PHONE');?>:</td>
		<td><?php echo $billingaddresses->phone;?></td>
	</tr>
	<?php endif; ?>

	<?php if ($billingaddresses->user_email != "") : ?>
	<tr>
		<td><?php echo JText::_('COM_REDSHOP_EMAIL');?>:</td>
		<td><?php echo $billingaddresses->user_email ? $billingaddresses->user_email : $user->email;?></td>
	</tr>
	<?php endif; ?>

</table>
