<div class="product_print">{print}</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{order_information_lbl}</h3>
	</div>

	<div class="panel-body">
		<div class="row">
			<label class="col-sm-4">{order_id_lbl}:</label>
			<div class="col-sm-8">{order_id}</div>
		</div>


		<div class="row">
			<label class="col-sm-4">{order_date_lbl}:</label>
			<div class="col-sm-8">{order_date}</div>
		</div>

		<div class="row">
			<label class="col-sm-4">{order_status_lbl}:</label>
			<div class="col-sm-8">{order_status}</div>
		</div>



	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{billing_address_information_lbl}</h3>
			</div>

			<div class="panel-body">
				{billing_address}
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">{shipping_address_information_lbl}</h3>
			</div>

			<div class="panel-body">
				{shipping_address}
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="table-responsive">
		<table class="table table-striped" border="0" cellspacing="0" cellpadding="0">
			<tbody>
			<tr>
				<th>{product_name_lbl}</th>
				<th></th>
				<th>{price_lbl}</th>
				<th>{quantity_lbl}</th>
				<th>{total_price_lbl}</th>
			</tr>
			<!--  {product_loop_start} -->
			<tr>
				<td>{product_name}<br/>{product_attribute}{product_accessory}{product_userfields}{product_wrapper}</td>
				<td>{product_thumb_image}</td>
				<td>{product_price}</td>
				<td>{product_quantity}</td>
				<td>{product_total_price}</td>
			</tr>
			<!--  {product_loop_end} -->
			</tbody>
		</table>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="cart_totals">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group cart_customer_note">
						<label>{customer_note_lbl}:</label>
						{customer_note}
					</div>

				</div>

				<div class="col-sm-6">
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
							<div class="col-sm-6">{order_total}</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>