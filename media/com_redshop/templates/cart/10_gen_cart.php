<div class="shopping-cart">
	<h1>{cart_lbl}</h1>

	<div class="row">
		<div class="col-sm-8">
			<div class='table-responsive'>
				<table class="tdborder" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th colspan="2" width="42%"></th>
							<th width="17%">{product_price_excl_lbl}</th>
							<th width="15%" style="text-align: right">{quantity_lbl}</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
					<!-- {product_loop_start} -->
						<tr class="tdborder">
							<td>{product_thumb_image}</td>
							<td>
								<div class="cartproducttitle">{product_name}</div>
							</td>
							<td class="price">{product_price_excl_vat}</td>
							<td class="quantity">
								{update_cart}

							</td>
							<td><span class="remove_product">{remove_product}</span></td>

						</tr>
					<!-- {product_loop_end} -->
					</tbody>
				</table>
			</div>

			<div>{shop_more}</div>
		</div>

		<div class="col-sm-4">
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

			<div class="form-group cart_discount_form">
				{coupon_code_lbl}{discount_form_lbl} {discount_form}
			</div>

			<div>{checkout_button}</div>
		</div>
	</div>
</div>


