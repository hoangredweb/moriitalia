<div class="product row">
    <div class="col-sm-5 col-xs-12 product-left">
        <div class="redSHOP_product_box_left">
            <div class="product_image">{product_thumb_image}</div>
            <div class="product_more_images">{more_images}{more_videos}</div>

        </div>
    </div>

    <div class="col-sm-7 col-xs-12 product-right">
        <div class="brand">
            <h3>
                <span class="manufacture">{manufacturer_product_link}</span>
                <div class="hidden">{manufacturer_image}</div>
            </h3>
        </div>
        <div class="product_title clearfix">
            <h1>{product_name}</h1>
        </div>
        <div class="row">
            <div id="product_price" class="col-sm-6 col-xs-12">
                <span class="product_price_val">{product_old_price}</span>
                <span class="product_price_discount">{product_price}</span>
            </div>
            <div class="col-sm-6 col-xs-12 rating">
                <!--{product_rating}-->
                {product_rating_summary}
            </div>
        </div>
        <div class="product_desc">
            <div class="product_desc_full">{product_desc}</div>
        </div>
        <div class="row">
            <div class="col-sm-7 col-xs-12 in-stock">{products_in_stock}</div>
            <div class="col-sm-5 col-xs-12 add-wishlist">
                <div class="row">
                    <span class="col-sm-12">{wishlist_link}</span>
                    <span class="col-sm-12">{wedding_link}</span>
                </div>
            </div>
        </div>
        <div class="product_addtocart">
            <div id="add_to_cart_all">{form_addtocart:gen_add_to_cart2}{compare_products_button}</div>
        </div>
        <div class="div_compare">
            {compare_product_div}
        </div>
        <div class="social">
            <!--
            <span class="icon-fb"><i class="icon-facebook"></i></span>
            <span class="icon-tw"><i class="icon-twitter"></i></span>
          -->
            <div class="addthis_sharing_toolbox"></div>
        </div>
        <!--
        <div class="rating" style="display:none;">
              {form_rating}
        </div>
       -->
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