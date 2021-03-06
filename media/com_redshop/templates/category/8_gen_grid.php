<div class='category_wrapper'>
	<div class='category_header'>
		<!-- Category title -->
		<div class="category_main_title">{category_main_name}</div>	
		<div class="category-main-image">{category_main_thumb_image}</div>
			<!-- {if subcats}
			<div class="cate_feature_mod">
				<div class="moduletable">
					<ul class="nav menu navbar-nav features-menu">
								 {category_loop_start}
						<li class="item-145">
							<div class="inner">
								<a href="{category_link}">
									 <img src="{category_image_nolink}" /><span class="btn image-title">{category_name_nolink}</span>
								 </a>
							</div>
						</li>
									{category_loop_end}
					</ul>
				</div>
			</div>
				{subcats end if} -->
	</div>
<div class="clearfix"></div>	 
<div class="category_product_list">
<!-- Toolbar -->
<div class="category_main_toolbar">
	<div class="row">
		<div class="col-sm-6 count_totalproduct">
			<div class="total_product">{total_product}</div>
		</div>
		<div class="col-sm-6 category_sortby">{order_by}</div>
	</div>
</div>

<div class="category_box_wrapper row grid">
	{product_loop_start}

	<div class="category_box_outside col-sm-6 col-md-4">
    
		<div class="category_box_inside">
			<div class="product-box-info">
				<div class="product-box-topinfo">
					<div class="inner_product_box_topinfo">
						<div class="product_image">{product_category_thumb_image}
						<!-- <div class="product_more_images">{more_images}</div>		 -->			
						</div>
						<div class="wishlist">
							<span>{wishlist_link}</span>
						</div>
						<div class="quickview-quickadd">						
							<div class="quickadd">{form_addtocart:gen_add_to_cart1}</div>
							<div class="quickview">
								<button type="button" class="btn btn-info btn-lg quick-view" data-toggle="modal" data-target="#quick-view-{product_id}">{Quickview}</button>
							</div>
						</div>
					</div>
				</div>
				<div class="product-content">
					<div class="product-name-manu">
						<div class="product_manufacturer">{manufacturer_link}</div> 
						<h3 class="product_name">{product_name}</h3>					
					</div>
	             
			        <div class="product_price">
			          <div class="product_real">{product_price}</div>
			          <div class="oldprice-and-percentage">
				         <span class="category_product_oldprice">{product_old_price}</span>
				         <span class="sale_percentage">{product_price_saving_percentage}</span>
			         </div>
			         <div class="product_lable">{producttag:rs_product_lbl}</div>

			        </div>
		        </div>
			</div>
		</div>
	</div>
	<!-- Modal -->
				<div id="quick-view-{product_id}" class="modal fade wrapper-quickview" role="dialog">
					<div class="modal-dialog">

						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">×</button>
								<!--<h4 class="modal-title">Modal Header</h4>-->
							</div>
							<div class="modal-body">
								
								<div class="product row">
									<div class="col-sm-5 col-xs-12 product-left">
										<div class="redSHOP_product_box_left">
											<div class="product_image">{product_thumb_image}</div>
											<div class="product_more_images">{more_images}</div>
										</div>									
									</div>
									<div class="col-sm-6 col-xs-12 product-right">
										<div class="redSHOP_product_box clearfix">
											<div class="redSHOP_product_box_right">
												<div class="redSHOP_product_detail_box">
													<!--<div class="brand">{manufacturer_image}</div>-->
													<div class="brand">
														 <h3>{manufacturer_name}</h3>
														<div class="about_manufacture">{manufacturer_product_link}</div>
													</div>
													<div class="product_title clearfix">
														<h3>{product_name}</h3>
													</div>
													<div id="product_price">
														<div class="product_real">{product_price}</div>
														<div class="oldprice-labletag">
															<span class="product_price_val">{product_old_price}</span>	
															<span class="in-stock">{producttag:rs_limit_item}</span>
														</div>																									
													</div>

													{attribute_template:attributes}
													<div class="size-guide">
											            <a href="#">Size guide</a>
										          	</div>
												</div>
											</div>
										</div>																	
										<div class="product_desc">
											<div class="product_desc_full">{product_s_desc}</div>
										</div>
										
										<div class="product_addtocart">
											<div id="add_to_cart_all">{form_addtocart:gen_add_to_cart1}</div>
										</div>
									</div>
									
								</div>								

								
								
							</div>

						</div>
					</div>
				</div>
	{product_loop_end}
	
	<div class="pagination">{pagination}</div>
</div>
</div>

</div>

