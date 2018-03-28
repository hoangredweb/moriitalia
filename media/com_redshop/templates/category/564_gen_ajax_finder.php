<div class="category_box_wrapper row grid">{product_loop_start}
<div class="cate_redshop_products_wrapper col-sm-4 col-xs-6">
<div class="category_box_inside">
<div class="product-box-info">
<div class="product-box-topinfo">
<div class="product_image">{product_thumb_image}</div>
<div class="wishlist"><span>{wishlist_link}</span></div>
<div class="quickview-quickadd">
<div class="quickadd">{form_addtocart:gen_add_to_cart1}</div>
<div class="quickview"><button type="button" class="btn btn-info btn-lg quick-view" data-toggle="modal" data-target="#quick-view-{product_id}">{Quickview}</button></div>
</div>
</div>
<div class="product-content">
<div class="product-name-manu">
<div class="product_manufacturer">{manufacturer_link}</div>
<h3 class="product_name">{product_name}</h3>
</div>
<div class="product_price">
<div class="product_real">{product_price}</div>
<div class="oldprice-and-percentage"><span class="category_product_oldprice">{product_old_price}</span> <span class="sale_percentage">{product_price_saving_percentage}</span></div>
<div class="product_lable">{producttag:rs_product_lbl}</div>
</div>
</div>
</div>
</div>
</div>
<!-- Modal -->
<div id="quick-view-{product_id}" class="modal fade wrapper-quickview">
<div class="modal-dialog"><!-- Modal content-->
<div class="modal-content">
<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button> <!--<h4 class="modal-title">Modal Header</h4>--></div>
<div class="modal-body">
<div class="product row">
<div class="col-sm-5 col-xs-12 product-left">
<div class="redSHOP_product_box_left">
<div class="product_image">{product_thumb_image}</div>
<div class="product_more_images">{more_images}</div>
</div>
</div>
<div class="col-sm-7 col-xs-12 product-right">
<div class="redSHOP_product_box clearfix">
<div class="redSHOP_product_box_right">
<div class="redSHOP_product_detail_box"><!--<div class="brand">{manufacturer_image}</div>-->
<div class="brand">{manufacturer_link}</div>
<div class="product_title clearfix">
<h3>{product_name}</h3>
</div>
<div id="product_price">
<div class="product_price_discount">{product_price}</div>
<div class="oldprice-labletag"><span class="product_price_val">{product_old_price}</span> <span class="in-stock">{producttag:rs_limit_item}</span></div>
</div>
{product_rating_summary} {attribute_template:attributes}</div>
</div>
</div>
<div class="product_desc">
<h3>{description}</h3>
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
<script type="text/javascript">jQuery(document).ready(function($){
	        jQuery('input[attribute_name="Color"]').each(function(idx, el){
	            var color_text = $(this).next('label').text().trim().toLowerCase();
	            $(this).next('label').andSelf().wrapAll("<div class='block-radio " + color_text + "'></div>");
	        });

	        jQuery('input[attribute_name="Size"]').each(function(idx, el){
	            $(this).next('label').andSelf().wrapAll("<div class='block-radio'></div>");
	        });
	  
     jQuery('.attributes_box').each(function(i,e){
  		 var textlabel = $(this).find('.attribute_wrapper >label').text().toLowerCase();
  		 jQuery(this).addClass(textlabel)
  	});
});
</script>
</div>