<div class="social">
        <!--
        <span class="icon-fb"><i class="icon-facebook"></i></span>
        <span class="icon-tw"><i class="icon-twitter"></i></span>
    -->
    <div class="addthis_sharing_toolbox"></div>
      </div>
<div class="product row" style="display:none;">
  <div class="col-sm-4 col-xs-12 product-left">
    <div class="redSHOP_product_box_left">
      <div class="product_image">{product_thumb_image}</div>
      <div class="product_more_images">{more_images}{more_videos}</div>

    </div>
  </div>
  <div class="col-sm-5 col-md-offset-0 col-xs-12 product-center">
    <div class="redSHOP_product_box clearfix">

      <div class="redSHOP_product_box_right">
        <div class="redSHOP_product_detail_box">
          <div class="brand">{manufacturer_image}</div>
          <div class="product_title clearfix">
            <h3>{product_name}</h3>
          </div>
          <div id="product_price">
            <span class="product_price_val">{product_old_price}</span>
            <span class="product_price_discount">{product_price}</span>
          </div>

          <div class="in-stock"><i class="icon-time"></i>{rs_low_in_stock}</div>

          <div class="cardiv1">

            <div class="color">	{attribute_template:gen_attributes}</div>

            <div class="color">	{accessory_template:accessory}</div>
          </div>
          <!--
          <div class="size-guide">
            <a href="#">Size guide<i class="icon-angle-right"></i></a>
          </div>
			-->
          <div class="product_box_outside">
            <div class="title"><h4>{description}</h4></div>
            <div class="product_desc">
              <div class="product_desc_full">{product_desc}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-3 col-md-offset-0 col-xs-12 product-right">

    <div class="product_addtocart">
      <div id="add_to_cart_all">{form_addtocart:gen_add_to_cart2}</div>
    </div>
    <div class="wishlist">
      <span>{wishlist_link}</span>
      <span class="right"><i class="icon-plus"></i></span>
    </div>

    <div class="wishlist">
      <span>{compare_products_button}</span>
      <span class="right"><i class="icon-plus"></i></span>
    </div>
    {delivery}
    <div class="div-link-compare">
      {compare_product_div}
    </div>
    <div class="rating" style="display:none;">
      	{form_rating}
    </div>
  </div>
</div>
<div class="hidden">{rs_product_lbl}</div>

{related_product:related_products}

<div class="row" >
  <div class="col-xs-12 giam-gia">
    {loadposition giam-gia}
  </div>
</div>
<div class="row" >
  <div class="col-xs-12 ban-chay">
    <div class="row">
      {loadposition ban-chay}
    </div>
  </div>
</div>