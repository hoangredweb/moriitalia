<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * $displayData extract
 *
 * @param   object  $form        A JForm object
 * @param   int     $product_id  Id current product
 * @param   int     $modal       Flag use form in modal
 */
extract($displayData);
$app     = JFactory::getApplication();
$input   = $app->input;
$compare = $displayData['object'];

$Itemid  = $input->getInt('Itemid');
$cmd     = JRequest::getVar('cmd');

$total   = $compare->getItemsTotal();

if (empty($Itemid))
{
	$Itemid  = 218;
}
?>
<ul id='compare_ul'>
<?php foreach ($compare->getItems() as $data) : ?>
	<?php
	$productId  = $data['item']->productId;
	$categoryId = $data['item']->categoryId;
	$product    = RedshopHelperProduct::getProductById($productId);

	$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $productId . '&cid=' . $categoryId . '&Itemid=' . $Itemid);
	?>
	<li>
		<span>
			<a href="<?php echo $link; ?>"><?php echo $product->product_name; ?></a>
		</span>
		<span>
			<a
				class="remove-link-compare icon-remove" id="removeCompare<?php echo $productId . '.' . $categoryId; ?>"
				href='javascript:void(0);'
				value="<?php echo $productId . '.' . $categoryId; ?>"
			>
				<?php echo JText::_('COM_REDSHOP_DELETE'); ?>
			</a>
		</span>
	</li>
<?php endforeach; ?>
</ul>
<div id="totalCompareProduct" style="display:none;" ><?php echo $total; ?></div>