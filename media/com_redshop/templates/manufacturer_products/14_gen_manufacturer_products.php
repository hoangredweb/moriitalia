<div class='category_header' style='display:none;'>
	 <div class="manufacturer_description">{manufacturer_description}</div>
	 <div class="manufacturer_name">{manufacturer_name}</div>
	 <div class="manufacturer_image">{manufacturer_image}</div>
</div>

<div class="category_main_toolbar">
	<div class="row">
		<div class="col-sm-5">
			<div class="category_sortby">{order_by_lbl}{order_by}</div>
			<div class="count_totalproduct">{total_product}</div>
		</div>
		<div class="col-sm-7 pagination pagin-wrapper">{pagination}</div>
		{perpagelimit:15}
	</div>
</div>
<div class="clearfix"></div>
<div class="category_product_list">
			<div id="productlist" class="row">
			<!-- remove it -->
			<!-- {product_loop_start_} -->
			<div class="cate_redshop_products_wrapper col-sm-4 col-xs-6 hidden">
				<div class="inner_redshop_products">
								<button type="button" class="btn btn-info btn-lg quick-view" data-toggle="modal" data-target="#quick-view-{product_id}">{Quickview}</button>
							<div class="hover"></div>
							{if product_on_sale}
										<div class="on_sale_wrap">
												<div class="on-sale">
														<p>{sale_text}<p>
												</div>
												<div class="sale-savings">
														<p>{product_price_saving_percentage}</p>
												</div>
										</div>
								 {product_on_sale end if}
						<div class='cate_products_image'>{product_thumb_image}</div>
						<div class='cate_products_title'>{product_name}</div>
						<div class="category_product_price">
							<span class="product_price_val">{product_old_price}</span>
							<span class="product_price_discount">{product_price}</span>
						</div>
						<div class="button-add">
							<div class="row">
								<div class="detail col-sm-6 col-xs-6 pull-left">
									<a class="see-more" href="{read_more_link}">{detail_text}</a>
								</div>
								<div class="cart col-sm-6 col-xs-6 pull-right">
									{form_addtocart:gen_add_to_cart1}
								</div>
							</div>
						</div>
				</div>
		</div>
			<!-- Modal -->
				<div id="quick-view-{product_id}" class="modal fade" role="dialog">
					<div class="modal-dialog">

						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">×</button>
							</div>
							<div class="modal-body">
								{tab Thông tin chung}
										<div class="product row">
											<div class="col-sm-4 col-xs-12 product-left">
												<div class="redSHOP_product_box_left">
													<div class="product_image">{product_thumb_image}</div>
												</div>
											</div>
											<div class="col-sm-4 col-md-offset-0 col-xs-12 product-center">
												<div class="redSHOP_product_box clearfix">
													<div class="redSHOP_product_box_right">
														<div class="redSHOP_product_detail_box">
															<div class="brand">
																Nhà sản xuất: <a href="{manufacturer_product_link}">{manufacturer_name}</a>
															</div>
															<div class="product_title clearfix">
																<h3>{product_name}</h3>
															</div>
															<div id="product_price">
																<span class="product_price_val">{product_old_price}</span>
																<span class="product_price_discount">{product_price}</span>
															</div>

															<div class="in-stock"><i class="icon-time"></i>{producttag:rs_low_in_stock}</div>

															<div class="cardiv1">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-4 col-md-offset-0 col-xs-12 product-right">
												<div class="product_addtocart">
													<div id="add_to_cart_all">{form_addtocart:gen_add_to_cart1}</div>
												</div>
												<div class="wishlist">
													<span>{wishlist_link}</span>
													<span class="right"><i class="icon-plus"></i></span>
												</div>
												{delivery}
											</div>
										</div>
								{tab Mô tả}
										<div class="product_desc">
												<div class="product_desc_full">{product_desc}</div>
										</div>
								{/tabs}
							</div>

						</div>
					</div>
				</div>
			<!-- {product_loop_end_} -->
		</div>
</div>