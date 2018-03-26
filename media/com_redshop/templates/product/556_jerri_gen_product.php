<h1>{product_name}</h1>
<div class="product row">
    <div class="col-sm-5 col-xs-12 product-left">
        <div class="redSHOP_product_box_left">
            <div class="product_image">
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
                {product_thumb_image}
            </div>
            <div class="product_more_images">{more_images}{more_videos}</div>
        </div>
    </div>

    <div class="col-sm-7 col-xs-12 product-right">
        <div class="product_title clearfix">
            <div class="brand">
                <h3>
                    <span class="manufacture">{manufacturer_image}{manufacturer_product_link}</span>
                </h3>
            </div>
            
        </div>
        <div class="product_details">
           <div id="product_price">
                <span class="product_price_val product_old_price">{product_old_price}</span>
                <span class="product_price_discount product_r_price">{product_price}</span>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="product_desc">
            <div class="product_desc_full">{product_desc}</div>
        </div>
        <div class="row toolproduct">
            <div class="col-sm-7 col-xs-12 in-stock">{products_in_stock}</div>
            <div class="col-sm-5 col-xs-12 add-wishlist">
                <div class="row">
                    <span class="col-sm-12">{wishlist_link}</span>
                    <span class="col-sm-12">{wedding_link}</span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        {accessory_template:accessory}

        <div class="product_details">
            <div class="product_addtocart">
                <div id="add_to_cart_all">{form_addtocart:gen_add_to_cart2}  {compare_products_button}</div>
            </div>
            <div class="clearfix"></div>
            <div class="div_compare">
                {compare_product_div}
            </div>
        </div>
        <div class="social hidden">
            <div class="addthis_sharing_toolbox"></div>
        </div>
        <div class="rating">
          {product_rating}
        </div>
        <div class="rating form">
          <div class="btnrating">{form_rating}</div>
          <div class="summary">{product_rating_summary}</div>
        </div>
    </div>
</div>
<div class="hidden">{rs_product_lbl}</div>