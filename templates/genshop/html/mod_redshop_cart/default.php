<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_cart
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$redhelper     = new redhelper;
$app           = JFactory::getApplication();
$itemId        = (int) $redhelper->getCartItemid();

$getNewItemId = true;

if ($itemId != 0)
{
	$menu = $app->getMenu();
	$item = $menu->getItem($itemId);

	$getNewItemId = false;

	if (isset($item->id) === false)
	{
		$getNewItemId = true;
	}
}

if ($getNewItemId)
{
	$itemId = (int) $redhelper->getCategoryItemid();
}

$displayButton = JText::_('MOD_REDSHOP_CART_CHECKOUT');

if ($button_text != "")
{
	$displayButton = $button_text;
}

if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . ADDTOCART_BACKGROUND))
{
	JFactory::getDocument()->addStyleDeclaration(
		'.mod_cart_checkout{background-image:url(' . REDSHOP_FRONT_IMAGES_ABSPATH . ADDTOCART_BACKGROUND . ');}'
	);
}
?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('.mod_cart_main .mod_cart_title').each(function(){
			var cart = jQuery(this);
			var tempw = cart.find('.inner').innerWidth() + 2;
			cart.css({
				'height': tempw,
				'width': tempw
			});
		});
	});
</script>
<div class="mod_cart_main">
	<div class="mod_cart_top">
		<i class="icon icon-shopping-cart"></i>
		<div class="mod_cart_title">
			<div class="inner"><?php echo JText::sprintf('MOD_REDSHOP_CART_CART_TEXT', $count);?></div>
		</div>
	</div>
	<div class="mod_cart_total" id="mod_cart_total">
		<?php echo RedshopLayoutHelper::render(
			'cart.cart',
			array(
				'cartOutput' => $output_view,
				'totalQuantity' => $count,
				'cart' => $cart,
				'showWithVat' => $show_with_vat,
				'showShippingLine' => $show_shipping_line,
				'showWithDiscount' => $show_with_discount
			),
			'templates/genshop/html/layouts/com_redshop/'
		);
		?>
	</div>
</div>