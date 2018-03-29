<!-- Toolbar -->
<div class="category_main_toolbar">
    <div class="row">
        <div class="col-sm-6 count_totalproduct">
            <div class="total_product">{total_product}</div>
        </div>
        <div class="col-sm-6 category_sortby">{order_by}</div>
    </div>
</div>

<div id="productlist">
    <div class="category_box_wrapper row grid">
        {product_loop_start}
        <div class="cate_redshop_products_wrapper col-sm-4 col-xs-6">

            <div class="category_box_inside">
                <div class="product-box-info">
                    <div class="product-box-topinfo">
                        <div class="product_image">{product_thumb_image}</div>
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
                            <div class="col-sm-7 col-xs-12 product-right">
                                <div class="redSHOP_product_box clearfix">
                                    <div class="redSHOP_product_box_right">
                                        <div class="redSHOP_product_detail_box">
                                            <!--<div class="brand">{manufacturer_image}</div>-->
                                            <div class="brand">
                                                {manufacturer_link}
                                            </div>
                                            <div class="product_title clearfix">
                                                <h3>{product_name}</h3>
                                            </div>
                                            <div id="product_price">
                                                <div class="product_price_discount">{product_price}</div>
                                                <div class="oldprice-labletag">
                                                    <span class="product_price_val">{product_old_price}</span>
                                                    <span class="in-stock">{producttag:rs_limit_item}</span>
                                                </div>
                                            </div>
                                            {product_rating_summary}

                                            {attribute_template:attributes}

                                        </div>
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
        <script type="text/javascript">
            jQuery(document).ready(function($){
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

                $('.category_box_inside .wishlist').click(function() {
                    window.location = jQuery(this).find('a').attr("href");
                    return false;
                });
            });
        </script>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                getImagename = function(link) {
                    var re = new RegExp("images\/(.*?)\/thumb\/(.*?)_w([0-9]*?)_h([0-9]*?)(_.*?|)([.].*?)$");
                    var m = link.match(re);
                    return m;
                };

                redproductzoom = function(element) {
                    var mainimg = element.find('img');
                    var m = getImagename(mainimg.attr('src'));
                    var newxsize = m[3];
                    var newysize = m[4];
                    var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

                    mainimg.attr('data-zoom-image', urlfull);

                    //more image
                    element.parents('.redSHOP_product_box_left').find('div[id*=additional_images]').find('.additional_image').each(function() {
                        $(this).attr('onmouseout', '');
                        $(this).attr('onmouseover', '');

                        $(this).find('a').attr('onmouseout', '');
                        $(this).find('a').attr('onmouseover', '');

                        //gl = $(this).attr('id');

                        var urlimg = $(this).find('img').attr('data-src');
                        if (typeof urlimg === 'undefined' || urlimg === false) {
                            urlimg = $(this).find('img').attr('src');
                        }

                        var m = getImagename(urlimg);

                        var urlthumb = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/thumb/' + m[2] + '_w' + newxsize + '_h' + newysize + m[5] + m[6];
                        var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

                        $(this).find('a').attr('data-image', urlthumb);
                        $(this).find('a').attr('data-zoom-image', urlfull);

                        $(this).find('a').attr('class', 'elevatezoom-gallery');
                    });

                    if (mainimg.data('elevateZoom')) {

                        var ez = mainimg.data('elevateZoom');
                        ez.currentImage = urlfull;
                        ez.imageSrc = urlfull;
                        ez.zoomImage = urlfull;
                        ez.closeAll();
                        ez.refresh();

                        $('.zoomContainer').remove();

                        //Create the image swap from the gallery
                        $('.' + ez.options.gallery + ' a').click(function (e) {

                            //Set a class on the currently active gallery image
                            if (ez.options.galleryActiveClass) {
                                $('#' + ez.options.gallery + ' a').
                                removeClass(ez.options.galleryActiveClass)
                                $(this).addClass(ez.options.galleryActiveClass)
                            }
                            //stop any link on the a tag from working
                            e.preventDefault()

                            //call the swap image function
                            if ($(this).data('zoom-image')) {
                                ez.zoomImagePre = $(this).data('zoom-image')
                            }
                            else {
                                ez.zoomImagePre = $(this).data('image')
                            }

                            ez.swaptheimage($(this).data('image'), ez.zoomImagePre)
                            return false
                        })

                    } else {
                        var gl = element.parents('.redSHOP_product_box_left').find('.redhoverImagebox').attr('id');

                        mainimg.elevateZoom({
                            zoomType: "inner",
                            scrollZoom : true,
                            cursor: "crosshair",
                            gallery: gl,
                            responsive:true,
                            loadingIcon: 'plugins/system/redproductzoom/js/zoomloader.gif'
                        });
                    }
                };

                $('.redImagepreview').remove();

                jQuery('.quick-view').each(function(idx, el) {
                    var quick_view_id = $(this).data('target');
                    var number = quick_view_id; // a string
                    number = number.replace(/\D/g, ''); // a string of only digits, or the empty string
                    number = parseInt(number, 10);

                    $(quick_view_id).find('div.product_image > .redhoverImagebox').attr('id', 'additional_images' + number);
                });

                $('.quick-view').on('click', function() {
                    var quick_view_id = $(this).data('target');
                    redproductzoom($(quick_view_id).find('div.product_image > .redhoverImagebox'));
                });

                $('#productlist .modal').on('hidden.bs.modal', function () {
                    $('.zoomContainer').remove();
                })

                if ($('.pagination').length)
                {
                    $('.pagination ul li:last-child').addClass("pagination-next");
                    $('.pagination ul li:last-child').prev('li').addClass("pagination-end");

                    $('.pagination ul li:first-child').addClass("pagination-start");
                    $('.pagination ul li:first-child').next('li').addClass("pagination-prev");
                }

                //$("#orderBy").select2({ width: '200px' });

                /*	setTimeout(function() {
						$("#orderBy").select2('destroy');
						$("#orderBy").select2({ containerCss : {"display":"block", "width": "200px"}, width: '200px' });
					}, 500);*/
                /*
				$("#orderBy").select2({ dropdownAutoWidth : true });*/
            });
        </script>

        <style type="text/css">
            #main-content .modal:not(a) .product .product-left .product_image img{
                width: 310px;
                height: 400px;
            }
            .modal .product .product-left .product_image a{
                height: 400px;
            }
            .category_product_list .limit.pull-right
            {
                display: none;
            }
            .category_product_list .pagination .pagenav span{
                color: #cd212a;
                font-family: 'ProximaNova-Bold';
            }
        </style>

        <div class="clearfix"></div>
        <div class="pagination">{pagination}</div>
    </div>
</div>