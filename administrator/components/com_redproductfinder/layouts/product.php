<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;
JHtml::_('rjquery.select2', '.select2');

$products = $displayData["products"];
$product_id = $displayData["product_id"];
$producthelper = $displayData["producthelper"];
$name = "jform[product_id]";
?>
<div class="reditem_customfield_itemfromtypes">
	<select class="select2" name="jform[product_id]" id="jform_product_id">
		<option value="">
			<?php echo JText::_('COM_REDPRODUCTFINDER_MODELS_FORMS_ASSOCIATION_PRODUCT_ID_LABEL') ?>
		</option>
		<?php if (!empty($products)) : ?>
			<?php foreach ($products as $key => $item) : ?>
					<option value="<?php echo $item['product_id']; ?>" <?php if ($product_id == $item['product_id']) echo 'selected="selected"' ?>>
						<?php echo $item['full_product_name']; ?>
					</option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</div>
