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
$extra_section = ($shippingaddresses->is_company == 1) ?
    RedshopHelperExtrafields::SECTION_COMPANY_SHIPPING_ADDRESS : RedshopHelperExtrafields::SECTION_PRIVATE_SHIPPING_ADDRESS;
?>

<table border="0">
>

	<tr>
		<td><?php echo JText::_('COM_REDSHOP_FIRSTNAME');?>:</td>
		<td><?php echo $shippingaddresses->firstname;?></td>
	</tr>

	<tr>
		<td><?php echo JText::_('COM_REDSHOP_LASTNAME');?>:</td>
		<td><?php echo $shippingaddresses->lastname;?></td>
	</tr>

	<?php if ($shippingaddresses->address != "") : ?>
	<tr>
		<td><?php echo JText::_('COM_REDSHOP_ADDRESS');?>:</td>
		<td><?php echo $shippingaddresses->address;?></td>
	</tr>
	<?php endif; ?>



	<?php if ($shippingaddresses->phone != "") : ?>
	<tr>
		<td><?php echo JText::_('COM_REDSHOP_PHONE');?>:</td>
		<td><?php echo $shippingaddresses->phone;?></td>
	</tr>
	<?php endif; ?>

</table>

