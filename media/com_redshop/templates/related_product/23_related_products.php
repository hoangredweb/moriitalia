<div class="row">
  <div class="col-xs-12 related_products">
    		<div class="moduletable product-list most-product">
						<h3 class="head-title">{title_related}</h3>
						<div class="mod_redshop_products_wrapper mod_related">
                            <div id="mod_products_related" class="slide_wrapper">
                              {related_product_start}
                              <div class="mod_redshop_products">
                                <div class="product-wrapper">
                                  <div class="mod_redshop_products_image">
                                      {relproduct_image}
                                      <button type="button" class="btn btn-info btn-lg quick-view hidden" data-toggle="modal" data-target="#quick-view-{product_id}">{Quickview}</button>
                                  </div>
                       
                                  <div class="mod_product_price_wrapper">
                                    <div class="inner row">
                                        <div class="mod_redshop_products_title">
                                          <a href="{read_more_link}">{relproduct_name}</a>
                                        </div>
                                      <div class="mod_redshop_products_price col-xs-12 align-left">{relproduct_price}</div>
                                    	<div class="mod_redoldprice" id="mod_redoldprice">{relproduct_old_price}</div>
                                    	
                                    </div>
                                    <div class="wishlist hidden">
                                      {wishlist_link}
                                    </div>
                                    <div class="mod_redshop_products_addtocart hidden">
                                      {form_addtocart:gen_add_to_cart1}
                                    </div>
                                  </div>
                                
                                </div>
                              </div>
                              <!--modal-->
                              <div id="quick-view-{product_id}" class="modal fade" role="dialog">
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
														<div class="product_image">{relproduct_image}</div>
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
                                                                        <h3>{relproduct_name}</h3>
                                                                </div>
                                                                <div class="product_details">
                                                                  	<div id="product_price" class="on-related">
                                                                        <span class="product_price_val">{relproduct_old_price}</span>
                                                                        <span class="product_price_discount">{relproduct_price}</span>
                                                                    </div>
                                                                  	<div class="view-full">
                                                                      <div class="hidden">
                                                                        {read_more}
                                                                      </div>
                                                                      <a href="{read_more_link}">View full product details>></a>
                                                                      {wishlist_link}
																	</div>
                                                                    <div class="product_desc">
                                                                            <div class="product_desc_full">{relproduct_s_desc}</div>
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
				</div><!--end modal-->
                              {related_product_end}
                            </div>
						</div>
		</div>
	
  </div>
</div>