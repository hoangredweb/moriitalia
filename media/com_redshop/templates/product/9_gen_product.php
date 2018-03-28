<div class="product row">
    <div class="col-sm-8 col-xs-12 product-left">
        <div class="redSHOP_product_box_left col-xs-12">
            <div class="product_more_images col-sm-2">{more_images}{more_videos}</div>
            <div class="product_image col-sm-10">
                <div class="inner_product_image">
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
                    <div class="wishlist_link">{wishlist_link}</div>
                    {product_thumb_image}
                </div>
            </div>
            
        </div>
    </div>

    <div class="col-sm-4 col-xs-12 product-right">
        <div class="product_title clearfix">
            <div class="brand"><h3>{manufacturer_name}</h3>
            </div>
            <h1>{product_name}</h1>
            
        </div>
        <div class="product_details">
           <div id="product_price">
                <div class="product_price_discount product_r_price">{product_price}</div>
                <div class="old_price_and_stock">
                    <span class="product_price_val product_old_price">{product_old_price}</span>
                    <span class="in_stock">{rs_limit_item}</span>
                </div>
               
            </div>
            <div class="product_rating">{product_rating_summary}</div>
        </div>
        <div class="clearfix"></div>
        <!-- Start Attribute -->
        {attribute_template:gen_attributes}
        <!-- End Attribute -->

      	 <div class="product_short_desc">
            <div class="title_descriptio">{description}</div>
            <div class="content_s_desc">{product_s_desc}</div>
        </div>
        <div class="clearfix"></div>
       
      	

        <div class="product_details">
            <div class="about_manufacture">{manufacturer_product_link}</div>
            <div class="product_addtocart">
                
                <div id="add_to_cart_all">{form_addtocart:gen_add_to_cart2}</div>
                
            </div>
            <div class="clearfix"></div>
          
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
{accessory_template:accessory}
<div class="hidden">{rs_product_lbl}</div>

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
<div class="tab_content_review">
<ul class="nav nav-pills">
    <li class="active"><a data-toggle="pill" href="#block_description">{description}</a></li>
    <li><a data-toggle="pill" href="#specs_features">{rs_specs_features_lbl}</a></li>
    <li><a data-toggle="pill" href="#block_review">{reviews}</a></li>
  </ul>
  
  <div class="tab-content">
    <div id="block_description" class="tab-pane fade in active">
      {product_desc}
    </div>
    <div id="specs_features" class="tab-pane fade">
      {rs_specs_features}
    </div>
    <div id="block_review" class="tab-pane fade">
      <div class="rating-client col-md-4 col-xs-12">
        <h3>{average_rating}</h3>
        <div class="total-rating">{product_rating_summary}</div>
        {form_rating}
      </div>
      <div class="allcomment col-md-8 col-xs-12">
      <h3>{custom_rating}</h3>
        {product_rating}
      </div>
    </div>   
  </div>
</div>
 {related_product:related_products}

<script type="text/javascript">
    jQuery(document).ready(function($){
        jQuery('input[attribute_name="Color"]').each(function(idx, el){
            var color_text = $(this).next('label').text().trim().toLowerCase();
            $(this).next('label').andSelf().wrapAll("<div class='block-radio " + color_text + "'></div>");
        });

        jQuery('input[attribute_name="Size"]').each(function(idx, el){
            $(this).next('label').andSelf().wrapAll("<div class='block-radio'></div>");
        });
    });
</script>