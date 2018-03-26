<div class="shopping-cart">
	<div class='table-responsive'>
		<table class="tdborder" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th colspan="2"></th>
					<th width="10%" style="text-align: center;">{quantity_lbl}</th>
					<th  width="30%" style="text-align: right;">{product_price_excl_lbl}</th>

				</tr>
			</thead>
			<tbody>
			<!-- {product_loop_start} -->
			<tr class="tdborder">
				<td>{product_thumb_image}</td>
				<td>
					<div class="cartproducttitle">{product_name}</div>
				</td>
				<td class="quantity" style="text-align: center;">{update_cart}</td>
				<td class="price" style="text-align: right;">{product_total_price}</td>

			</tr>
			<!-- {product_loop_end} -->
			</tbody>
		</table>
	</div>

	<div class="redshop-login form-horizontal">
		<div class="form-group">
			<label class="col-sm-6">{product_subtotal_lbl}:</label>
			<div class="col-sm-6">{product_subtotal}</div>
		</div>

		<!-- {if discount}-->
		<div class="form-group">
			<label class="col-sm-6">{discount_lbl}:</label>
			<div class="col-sm-6">{discount}</div>
		</div>
		<!-- {discount end if}-->

		<div class="form-group">
			<label class="col-sm-6">{shipping_lbl}:</label>
			<div class="col-sm-6">{shipping}</div>
		</div>

		<div class="form-group total">
			<label class="col-sm-6">{total_lbl}:</label>
			<div class="col-sm-6">{total}</div>
		</div>
	</div>

	<div class="form-group cart_customer_note">
		<label>{customer_note_lbl}:</label>
		{customer_note}
	</div>
	<div class="checkout_button">{checkout_button}</div>
</div>