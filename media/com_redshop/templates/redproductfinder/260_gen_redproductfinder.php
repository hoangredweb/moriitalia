<div class="title_search hidden">
		{category_main_name}
</div>
<div class='category_wrapper category_wrapper_finder last-cate'>
	<div class="category_main_toolbar clearfix">

		<div class="col-sm-7 pagination pagin-wrapper">
          <div class="row">
            <div class="col-sm-5 append-clear">

            </div>
          </div>
          <h3>
             {refine_by_label}
          </h3>
          {pagination}{perpagelimit:15}
        </div>
      	<div class="col-sm-5">
			<div class="category_sortby">{order_by_lbl}{order_by}</div>
			<!--<div class="count_totalproduct">{total_product}</div>-->
		</div>
	</div>

			{product_loop_start}
				<div class="cate_redshop_products_wrapper col-sm-4 col-xs-6">
					<div class="inner_redshop_products">
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
							<div class='cate_products_image'>
								<button type="button" class="btn btn-info btn-lg quick-view" data-toggle="modal" data-target="#quick-view-{product_id}">{Quickview}</button>
								<div class="hover"></div>
								<span class="productImageWrap" id="productImageWrapID_{product_id}">
									{product_thumb_image_4}
								</span>
							</div>
							<div class="brand">{manufacturer_name}</div>
							<div class="category_product_price" >
								<span class="product_price_val">{product_old_price}</span>
								<span class="product_price_discount">{product_price}</span>
								<div class="cart {if product_on_sale}cart-sale{product_on_sale end if}">
									{form_addtocart:gen_add_to_cart1}
								</div>
                              <div class="wishlist">
                                {wishlist_link}
                              </div>
							</div>
							<div class='cate_products_title'>{product_name}</div>
					</div>
				</div>
				<!-- Modal -->
				<div id="quick-view-{product_id}" class="modal fade sss" role="dialog">
					<div class="modal-wrapper">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">×</button>
									<!--<h4 class="modal-title">Modal Header</h4>-->
								</div>
								<div class="modal-body">
											<div class="product row">
												<div class="col-sm-6 col-xs-12 product-left">
													<div class="redSHOP_product_box_left">
														<div class="product_image">{product_thumb_image}</div>
                                                      	<div class="product_more_images">
                                                        	 {more_images}{more_videos}
                                                      	</div>
													</div>

												</div>

												<div class="col-sm-6 col-xs-12 product-right">
                                                  	<div class="product_title">
                                                  		<div class="brand">
                                                          	<div class="redSHOP_product_detail_box">
                                                              	<div class="title">
																	<div class="brand-link hidden">
                                                                      {manufacturer_link}
                                                                  	</div>
																	<a href="" class="brand-logo">{manufacturer_image}</a>
                                                                        <h3>{product_name}</h3>
                                                                </div>
                                                                <div class="product_details">
                                                                  	<div id="product_price">
                                                                        <span class="product_price_val">{product_old_price}</span>
                                                                        <span class="product_price_discount">{product_price}</span>
                                                                    </div>
                                                                  	<div class="view-full">
                                                                      <div class="hidden">
                                                                        {read_more}
                                                                      </div>
                                                                      <a href="#">View full product details>></a>
                                                                      {wishlist_link}
																	</div>
                                                                    <div class="product_desc">
                                                                            <div class="product_desc_full">{product_s_desc}</div>
                                                                    </div>
                                                                </div>


																<div class="in-stock hidden"><i class="icon-time"></i>{producttag:rs_low_in_stock}</div>
																<div class="cardiv1 hidden">
																</div>
                                                      			<div class="buttons">
                                                                  	<div class="wishlist">
                                                                        <span>{compare_products_button}</span>
                                                                        <span class="right"><i class="icon-plus"></i></span>
                                                                    </div>
                                                                  	<div class="product_addtocart">
                                                                        <div id="add_to_cart_all">{form_addtocart:gen_add_to_cart1}</div>
                                                                    </div>
                                                      			</div>
                                                                <div class="div_compare_modal hidden">
                                                                        {compare_product_div}
                                                                </div>
                                                      		</div>
                                                  		</div>
                                              		</div>
												</div>
											</div>

								</div>

							</div>
						</div>
					</div>
				</div>
			{product_loop_end}
			<script language="javascript">
				jQuery(document).ready(function($){
					$(".modal-body .quick_zoom").hover(function () { 
					var elevateZoom=$(this).data('elevateZoom');
					if (typeof elevateZoom === 'undefined') {
						$(this).elevateZoom({loadingIcon: "plugins/system/redproductzoom/js/zoomloader.gif",cursor: "crosshair",zoomType: "window",scrollZoom: true,gallery: "additional_image",tint: false,tintColour: "#828282",tintOpacity: 0.5,zoomWindowWidth: 400,zoomWindowHeight: 400});
					   
					} else {					   
					   elevateZoom.changeState('enable');
					} // if
			 });
				});
			</script>
</div>
