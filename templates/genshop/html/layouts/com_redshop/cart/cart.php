<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JLoader::load('RedshopHelperProduct');
JLoader::load('RedshopHelperCart');
$productHelper = new producthelper;
$cartHelper    = new rsCarthelper;
$cart          = $displayData['cart'];
$total         = 0;
$redhelper     = new redhelper;
$app           = JFactory::getApplication();
$itemId        = (int) $redhelper->getCartItemid();

if (isset($cart) && isset($cart['idx']) && $cart['idx'] > 0)
{
	$total = $cart['mod_cart_total'];
}
?>

<h3><?php echo JText::_('MOD_REDSHOP_CART_ADDED_TO_BAG');?></h3>

<?php
if ($displayData['cartOutput'] == 'simple'): ?>
	<div class="mod_cart_extend_total_pro_value" id="mod_cart_total_txt_product" >
	<?php if ($displayData['totalQuantity']): ?>
		<?php echo JText::_('MOD_REDSHOP_CART_TOTAL_PRODUCT') . ' ' . $displayData['totalQuantity'] . ' ' . JText::_('MOD_REDSHOP_CART_PRODUCTS_IN_CART'); ?>
	<?php endif; ?>
	</div>
<?php else: ?>
	<div class="mod_cart_products" id="mod_cart_products">
	<?php if ($displayData['totalQuantity']):
		$total = $cart['mod_cart_total'];
		?>
		<div id="cart-total-quantity" class="hide"><?php echo $displayData['totalQuantity']; ?></div>
		<?php for($i = 0; $i < $cart['idx']; $i++):

			if ($cartHelper->rs_multi_array_key_exists('giftcard_id', $cart[$i]) && $cart[$i]['giftcard_id'])
			{
				$giftCardData = $productHelper->getGiftcardData($cart[$i]['giftcard_id']);
				$name         = $giftCardData->giftcard_name;
			}
			else
			{
				$productDetail = RedshopHelperProduct::getProductById($cart[$i]['product_id']);
				$name           = $productDetail->product_name;
			}


			$product = RedshopHelperProduct::getProductById($cart[$i]['product_id']);
			$product_image = "";
			$prd_image     = '';
			$type          = 'product';

			if (WANT_TO_SHOW_ATTRIBUTE_IMAGE_INCART && isset($cart[$i]['hidden_attribute_cartimage']))
			{
				$image_path    = REDSHOP_FRONT_IMAGES_ABSPATH;
				$product_image = str_replace($image_path, '', $cart[$i]['hidden_attribute_cartimage']);
			}

			if ($product_image && is_file(REDSHOP_FRONT_IMAGES_RELPATH . $product_image))
			{
				$val        = explode("/", $product_image);
				$prd_image  = $val[1];
				$type       = $val[0];
			}
			elseif ($product->product_full_image && is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . $product->product_full_image))
			{
				$prd_image = $product->product_full_image;
				$type      = 'product';
			}
			elseif (is_file(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . PRODUCT_DEFAULT_IMAGE))
			{
				$prd_image = PRODUCT_DEFAULT_IMAGE;
				$type      = 'product';
			}

			$isAttributeImage = false;

			if (isset($cart[$i]['attributeImage']))
			{
				$isAttributeImage = is_file(REDSHOP_FRONT_IMAGES_RELPATH . "mergeImages/" . $cart[$i]['attributeImage']);
			}

			if ($isAttributeImage)
			{
				$prd_image = $cart[$i]['attributeImage'];
				$type      = 'mergeImages';
			}

			if ($prd_image !== '')
			{
				$redhelper = new redhelper;

				if (WATERMARK_CART_THUMB_IMAGE && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . "product/" . WATERMARK_IMAGE))
				{
					$product_cart_img = $redhelper->watermark($type, $prd_image, CART_THUMB_WIDTH, CART_THUMB_HEIGHT, WATERMARK_CART_THUMB_IMAGE, '0');

					$product_image = "<div  class='product_image'><img src='" . $product_cart_img . "'></div>";
				}
				else
				{
					$thumbUrl = RedShopHelperImages::getImagePath(
							$prd_image,
							'',
							'thumb',
							$type,
							CART_THUMB_WIDTH,
							CART_THUMB_HEIGHT,
							USE_IMAGE_SIZE_SWAPPING
						);

					$product_image = "<div  class='product_image'><img src='" . $thumbUrl . "'></div>";
				}
			}
			else
			{
				$product_image = "<div  class='product_image'></div>";
			}

			?>
			<div class="mod_cart_product">
				<div class="mod_cart_product_img">
					<?php
						echo $product_image;
					?>
				</div>
				<div class="mod_cart_product_content">
					<div class="mod_cart_product_name">
						<?php echo $name . ' x ' . $cart[$i]['quantity']; ?>
					</div>
					<?php if (!DEFAULT_QUOTATION_MODE || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE)):
						if ($displayData['showWithVat'])
						{
							$price = $cart[$i]['product_price'];
						}
						else
						{
							$price = $cart[$i]['product_price_excl_vat'];
						}
						?>
					<div class="mod_cart_product_price">
						<?php echo $productHelper->getProductFormattedPrice($price, true); ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		<?php endfor; ?>
	<?php endif; ?>
	</div>
<?php endif; ?>
<?php if ((!DEFAULT_QUOTATION_MODE || (DEFAULT_QUOTATION_MODE && SHOW_QUOTATION_PRICE)) && $displayData['totalQuantity']): ?>
<div class="mod_cart_totalprice hidden">
	<?php if ($displayData['showShippingLine']):
		$shippingValue = $cart['shipping'];

		if (!$displayData['showWithVat'])
		{
			if (!isset($cart['shipping_tax']))
			{
				$cart['shipping_tax'] = 0;
			}

			$shippingValue = $cart['shipping'] - $cart['shipping_tax'];
		}
		?>
		<div class="mod_cart_shipping_txt cartItemAlign" id="mod_cart_shipping_txt_ajax" >
			<?php echo JText::_('MOD_REDSHOP_CART_SHIPPING_LBL'); ?> :
		</div>
		<div class="mod_cart_shipping_value cartItemAlign" id="mod_cart_shipping_value_ajax">
			<?php echo $productHelper->getProductFormattedPrice($shippingValue); ?>
		</div>
		<div class="clr"></div>
	<?php endif; ?>

	<?php if ($displayData['showWithDiscount']):
		$discountValue = $cart['discount_ex_vat'];

		if ($displayData['showWithVat'])
		{
			$discountValue = $cart['discount_ex_vat'] + $cart['discount_vat'];
		}

		if ($discountValue > 0) :
		?>
		<div class="mod_cart_discount_txt cartItemAlign" id="mod_cart_discount_txt_ajax" >
			<?php echo JText::_('MOD_REDSHOP_CART_DISCOUNT_LBL'); ?> :
		</div>
		<div class="mod_cart_discount_value cartItemAlign" id="mod_cart_discount_value_ajax">
			<?php echo $productHelper->getProductFormattedPrice($discountValue); ?>
		</div>
		<div class="clr"></div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="mod_cart_total_txt cartItemAlign" id="mod_cart_total_txt_ajax" >
		<?php echo JText::_('MOD_REDSHOP_CART_TOTAL'); ?>
	</div>
	<div class="mod_cart_total_value cartItemAlign" id="mod_cart_total_value_ajax">
		<?php echo $productHelper->getProductFormattedPrice($total); ?>
	</div>
	<div class="clr"></div>

</div>
<div class="mod_cart_checkout" id="mod_cart_checkout_ajax">
	<a class="btn btn-primary" href="<?php echo JRoute::_("index.php?option=com_redshop&view=checkout&Itemid=" . $itemId); ?>">
		<?php echo JText::_('MOD_REDSHOP_CART_CHECKOUT');?>
	</a>
	<a class="btn btn-default" href="<?php echo JRoute::_("index.php?option=com_redshop&view=cart&Itemid=" . $itemId); ?>">
		<?php echo JText::_('MOD_REDSHOP_CART_VIEW');?>
	</a>
</div>
<?php else: ?>
	<?php echo JText::_('MOD_REDSHOP_CART_EMPTY_CART'); ?>
<?php endif;
