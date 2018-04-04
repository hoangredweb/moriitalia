<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_filter
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$list = array(
			JHtml::_('select.option', '', JText::_('COM_REDSHOP_SELECT_FEATURE')),
			JHtml::_('select.option', 'p.product_price', JText::_('COM_REDSHOP_PRODUCT_PRICE_ASC')),
			JHtml::_('select.option', 'p.product_price desc', JText::_('COM_REDSHOP_PRODUCT_PRICE_DESC')),
			JHtml::_('select.option', 'p.product_id', JText::_('COM_REDSHOP_NEWEST'))
		);

$getOrderBy = JRequest::getString('order_by', Redshop::getConfig()->get('DEFAULT_PRODUCT_ORDERING_METHOD'));
$lists['order_select'] = JHTML::_('select.genericlist', $list, 'orderBy', 'class="inputbox" size="1" onchange="order(this);" ', 'value', 'text', $getOrderBy);

$document = JFactory::getDocument();
$document->addScript('plugins/system/redproductimagedetail/js/jquery.elevateZoom.min.js');

JText::script('COM_REDSHOP_TOTAL_PRODUCT_COUNT');
?>
<div class="<?php echo $moduleClassSfx; ?> mod_redshop_filter_wrapper">
	<form action="<?php echo $action; ?>" method="post" name="adminForm-<?php echo $module->id;?>" id="redproductfinder-form-<?php echo $module->id;?>" class="form-validate">
	<div class="form-horizontal">
		<?php if ($enableManufacturer == 1 && count($manufacturers) > 0): ?>
			<div id='manu' class=' dropdown-div'>
				<h3 class="title">
					<?php echo JText::_("MOD_REDSHOP_FILTER_MANUFACTURER_LABEL"); ?>
					<span class="dropdown show"></span>
				</h3>
				<div class="brand-input ">
					<input type="text" name="keyword-manufacturer" id="keyword-manufacturer" placeholder="<?php echo JText::_('TYPE_A_KEYWORD')?>" />
					<i class="icon-search"></i>
				</div>
				<ul class='taglist ' id="manufacture-list">
					<?php if (!empty($manufacturers)) : ?>
					<?php foreach ($manufacturers as $m => $manu) : ?>
						<li style="list-style: none">
							<label>
								<span class='taginput' data-aliases='manu-<?php echo $manu->id;?>'>
								<input type="checkbox" name="redform[manufacturer][]" value="<?php echo $manu->id ?>">
								<span class='tagname'><?php echo $manu->name; ?></span>
								</span>
								
							</label>
						</li>
					<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		<?php endif; ?>
		<div class="row-fluid dropdown-div">
			<?php if ($enableCategory == 1 && !empty($categories)): ?>
				<div id="categories">
					<?php if (($view == 'search') || (!empty($cid) && in_array($cid, $childCat)) || !empty($mid)) : ?>
						<?php if (!empty($categories)): ?>
							<h3>
								<?php echo JText::_('MOD_REDSHOP_FILTER_CATEGORY_LABEL');?>
								<span class="dropdown show"></span>
							</h3>
						<?php else : ?>
						<?php endif; ?>
					<?php else : ?>
						<?php if (!empty($categories[0]->child)): ?>
							<h3 class="title">
								<?php echo JText::_('MOD_REDSHOP_FILTER_CATEGORY_LABEL');?>
								<span class="dropdown show"></span>
							</h3>
						<?php else : ?>
						<?php endif; ?>
					<?php endif ?>
					<ul class='taglist show'>
						<?php foreach ($categories as $key => $cat) :?>
							<li>
								<?php if (($view == 'search') || (!empty($cid) && in_array($cid, $childCat)) || !empty($mid)) : ?>
								<label>
									<span class='taginput' data-aliases='cat-<?php echo $cat->category_id;?>'>
										<input type="checkbox" name="redform[category][]" value="<?php echo $cat->category_id ?>" onclick="javascript: checkclick(this);" />
										<span class='tagname'><?php echo $cat->category_name; ?></span>
									</span>
								</label>
								<?php endif; ?>
								<?php if (isset($cat->child) && !empty($cat->child)): ?>
									<ul class='taglist'>
										<?php foreach ($cat->child as $k => $child) :?>
											<li>
												<label>
													<span class='taginput' data-aliases='child-cat-<?php echo $child->category_id;?>'>
														<!-- <i class="icon icon-check-empty"></i> -->
														<input type="checkbox" name="redform[category][]" value="<?php echo $child->category_id ?>" onclick="javascript: checkclick(this);"" />
														<span class='tagname'><?php echo $child->category_name; ?></span>
													</span>
												</label>
												<?php if (!empty($child->sub)): ?>
													<ul class='taglist'>
														<?php foreach ($child->sub as $i => $sub) :?>
															<li>
																<label>
																	<span class='taginput' data-aliases='sub-cat-<?php echo $sub->category_id;?>'>
																		<input parent="<?php echo $child->category_id ?>" type="checkbox" name="redform[category][]" value="<?php echo $sub->category_id ?>" onclick="javascript: checkclick(this);" />
																		<span class='tagname'><?php echo $sub->category_name; ?></span>
																	</span>
																</label>
															</li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if ($enablePrice == 1 && $rangeMax != 0) : ?>
		<div class="row-fluid pricefilter">
			<div class="price"><?php echo JText::_("MOD_REDSHOP_FILTER_PRICE_LABEL"); ?></div>
			<div id="slider-range"></div>
			<div id="filter-price">
				<div id="amount-min">
					<div><?php echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL') ?></div>
					<input type="text" pattern="^\d*(\.\d{2}$)?" class="span12" name="redform[filterprice][min]" value="<?php echo $rangeMin; ?>" min="0" max="<?php echo $rangeMin; ?>" required/>
				</div>
				<div id="amount-max">
					<div><?php echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL') ?></div>
					<input type="text" pattern="^\d*(\.\d{2}$)?" class="span12" name="redform[filterprice][max]" value="<?php echo $rangeMax; ?>" min="0" max="<?php echo $rangeMax; ?>" required/>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<!-- <div id='order-by'>
			<h3 class="title"><?php echo JText::_("COM_REDSHOP_SELECT_ORDER_BY"); ?></h3>
			<div class="brand-input">
				<?php echo $lists['order_select']; ?>
			</div>
		</div> -->
		<span id="clear-btn" class="clear-btn" onclick="clearAll();"><?php echo JText::_("MOD_REDSHOP_FILTER_CLEAR_LABEL"); ?></span>
	</div>
	<input type="hidden" name="redform[cid]" value="<?php echo !empty($cid) ? $cid : 0; ?>" />
	<input type="hidden" name="redform[mid]" value="<?php echo !empty($mid) ? $mid : 0; ?>" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="limit" value="6" />
	<input type="hidden" name="redform[keyword]" value="<?php echo $keyword;?>" />
	<input type="hidden" name="check_list" value="" >
	<input type="hidden" name="order_by" value="" >
		<?php if (null !== $productOnSale): ?>
            <input type="hidden" name="redform[product_on_sale]" value="<?php echo (int) $productOnSale ?>" />
		<?php endif; ?>
	<input type="hidden" name="redform[template_id]" value="<?php echo $template; ?>" />
	<input type="hidden" name="redform[root_category]" value="<?php echo $rootCategory; ?>" />
	<input type="hidden" name="redform[category_for_sale]" value="<?php echo $categoryForSale; ?>" />

	<input type="hidden" name="option" value="<?php echo $option; ?>" >
	<input type="hidden" name="view" value="<?php echo $view; ?>" >
	<input type="hidden" name="layout" value="<?php echo $layout; ?>" >
	<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>" >
    <input type="hidden" name="pids" value="<?php echo implode(',', $pids) ?>" />
</form>
</div>

<link rel="stylesheet" type="text/css" href="<?php echo JUri::root() . 'modules/mod_redshop_filter/lib/css/jqui.css'; ?>">
<script type="text/javascript" src="<?php echo JUri::root() . 'modules/mod_redshop_filter/lib/js/jquery-ui.min.js'; ?>"></script>
<script type="text/javascript">
	function range_slide (min_range, max_range , cur_min , cur_max, callback) {
		jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
		jQuery("#slider-range").slider({
			range: true,
			min: min_range,
			max: max_range + 100000,
			step: 20000,
			values: [cur_min, cur_max + 100000],
			slide: function(event, ui) {
				jQuery('[name="redform[filterprice][min]"]').attr('value', ui.values[0]);
				jQuery('[name="redform[filterprice][max]"]').attr('value', ui.values[1] + 100000);
			}
			,change: function(event, ui){
				if (callback && typeof(callback) === "function") {
					jQuery('input[name="limitstart"]').val(0);
					jQuery('input[name="limit"]').val(6);
					callback();
				}
			}
		});
	}

	function modalWishlist(){
		// User
        jQuery('.redshop-wishlist-button, .redshop-wishlist-link').click(function(event) {
            event.preventDefault();

            var productId = jQuery(this).attr('data-productid');
            var formId = jQuery(this).attr('data-formid');
            var link = jQuery(this).attr('data-href');

            if (link == '' || typeof link == 'undefined') {
                link = jQuery(this).attr('href');
            }

            if (productId == '' || isNaN(productId)) {
                return false;
            }

            link += '&product_id=' + productId;

            if (formId == '') {
                var $form = jQuery('form#addtocart_prd_' + productId);
            } else {
                var $form = jQuery('form#' + formId);
            }


            if (!$form.length) {
                SqueezeBox.open(link, {
                    handler: 'iframe'
                });

                return true;
            }

            $form = jQuery($form[0]);

            var attribute = $form.children('input#attribute_data');
            var property = $form.children('input#property_data');
            var subAttribute = $form.children('input#subproperty_data');

            if (attribute.length) {
                link += '&attribute_id=' + encodeURIComponent(jQuery(attribute[0]).val());
            }

            if (property.length) {
                link += '&property_id=' + encodeURIComponent(jQuery(property[0]).val());
            }

            if (subAttribute.length)
                link += '&subattribute_id=' + encodeURIComponent(jQuery(subAttribute[0]).val());

            SqueezeBox.open(link, {
                handler: 'iframe'
            });

            return true;
        });

        // Guest
        jQuery('.redshop-wishlist-form-button, .redshop-wishlist-form-link').click(function(event) {
            event.preventDefault();
            var productId = jQuery(this).attr('data-productid');
            var formId = jQuery(this).attr('data-formid');

            if (productId == '' || isNaN(productId))
                return false;

            var $wishlistForm = jQuery('form#' + jQuery(this).attr('data-target'));

            if (!$wishlistForm.length)
                return false;

            if (formId == '') {
                var $form = jQuery('form#addtocart_prd_' + productId);
            } else {
                var $form = jQuery('form#' + formId);
            }

            if (!$form.length) {
                $wishlistForm.submit();
                return true;
            }

            $form = $($form[0]);

            var attribute = $form.children('input#attribute_data');
            var property = $form.children('input#property_data');
            var subAttribute = $form.children('input#subproperty_data');

            if (attribute.length) {
                $wishlistForm.children("input[name='attribute_id']").val(jQuery(attribute[0]).val());
            }

            if (property.length) {
                $wishlistForm.children("input[name='property_id']").val(jQuery(property[0]).val());
            }

            if (subAttribute.length) {
                $wishlistForm.children("input[name='subattribute_id']").val(jQuery(subAttribute[0]).val());
            }

            $wishlistForm.submit();

            return true;
        });
	}

	function modalCompare(){
		redSHOP = window.redSHOP || {};
		redSHOP.compareAction(jQuery('[id^="rsProductCompareChk"]'), "getItems");
		jQuery('[id^="rsProductCompareChk"]').click(function(event) {
		    redSHOP.compareAction(jQuery(this), "add");
		});
	}

	function clearAll(){
		jQuery('#redproductfinder-form-<?php echo $module->id;?> input[type="checkbox"]').prop('checked' , false);
		jQuery('#redproductfinder-form-<?php echo $module->id;?> input[type="checkbox"]').each(function(){
			checkclick(jQuery(this))
		});
		jQuery('input[name="redform[filterprice][min]"]').val('<?php echo $rangeMin;?>');
		jQuery('input[name="redform[filterprice][max]"]').val('<?php echo $rangeMax;?>');
		range_slide(<?php echo $rangeMin;?>, <?php echo $rangeMax;?>, <?php echo $rangeMin;?>, <?php echo $rangeMax;?>, submitpriceform );
		submitpriceform(null);
	}

	function checkclick(obj) {
		if (jQuery(obj).prop("checked") == true) {
			jQuery(obj).prev('.icon').addClass('active');
		}else{
			jQuery(obj).prev('.icon').removeClass('active');
		}
	}

	function submitform (argument) {
		jQuery('#redproductfinder-form-<?php echo $module->id;?> input[type="checkbox"], select').change(function(event) {
			jQuery('input[name="limitstart"]').val(0);
			jQuery('input[name="limit"]').val(6);
            submitpriceform("firstload");
		});
	}

	function submitpriceform (opload) {
		jQuery.ajax({
		 	type: "POST",
		 	url: "<?php echo JUri::root() ?>index.php?option=com_redshop&task=search.findProducts",
		 	data: jQuery('#redproductfinder-form-<?php echo $module->id;?>').serialize(),
		 	beforeSend: function() {
				jQuery('#wait').css('display', 'block');
				/*if( opload != "firstload"){
					jQuery('.category_header').css('display', 'none');
				}*/
			},
			success: function(data) {
				jQuery('.category_product_list').empty();
				//jQuery('#main-content .category_main_toolbar').first().remove();
				jQuery('.category_product_list').html(data);

				// Find and replace number products
				var count = jQuery('.cate_redshop_products_wrapper').size();

				if( opload != "firstload"){
					jQuery('.category_wrapper.parent .category_main_toolbar, .category_wrapper.parent .category_product_list').css('display', 'block');

					//add here
					jQuery('.all-product').css('display','none');
					jQuery('.back-category').css('display','block');

					//add more

				}

				// remove use html if there have no product found
				if( jQuery(data).find('.cate_redshop_products_wrapper').length == 0 && opload == "firstload" ){
					jQuery('#sidebar1').remove();
					jQuery('#main').addClass('col-sm-12').removeClass('col-sm-9');
					jQuery('.nav.features-menu li').css('width','33.33%');
					jQuery('.all-product').css('display','none');
					var window_width = jQuery(window).innerWidth();
					if(window_width<=1024){
						jQuery('.nav.features-menu li').css('width','50%');
					}
				}

				if( opload == "firstload" && jQuery('.all-product').length ){
					jQuery('.category_main_toolbar,.category_product_list,.pagination').hide();
					jQuery('.sidebar-parent .moduletable.filter-product .form-horizontal .row-fluid').css('display', 'none');
				}

				jQuery('.clear').appendTo('.category_main_toolbar .append-clear');
				jQuery('select#orderBy').select2();
				jQuery('.category-list').hide();
				modalCompare();
				modalWishlist();
				var window_w = jQuery(window).innerWidth();
				if(window_w>1024){
					jQuery('.cate_redshop_products_wrapper').each(function(index, el) {
						jQuery(this).find('.cate_products_title').insertBefore(jQuery(this).find('.category_product_price .product_price_val'));
					});
				}
				else{
					jQuery('.cate_redshop_products_wrapper').each(function(index, el) {
						jQuery(this).find('.cate_products_title').insertAfter(jQuery(this).find('.inner_redshop_products'));
					});
				}

				jQuery('[id*="quick-view-"]').each(function(index, el) {
					jQuery(this).find('.view-full .hidden').next('a').attr('href', jQuery(this).find('.view-full .hidden a').attr('href'));
				});
			},
			complete: function() {
				jQuery('#wait').css('display', 'none');
				jQuery('.cate_redshop_products_wrapper').each(function(){
					var proTitle = jQuery(this).find('.cate_products_title').text();
					var Titlelength = proTitle.length;
					if ( Titlelength > 41 ) {
						jQuery(this).addClass('widetitle');
					}else if( Titlelength < 41 && Titlelength > 30 ){
						jQuery(this).addClass('widetitle30');
					}
				});

				//fix firefox browser
				var checkExist = setInterval(function() {
					if ( jQuery('.cate_redshop_products_wrapper').length ) {
						jQuery('.cate_redshop_products_wrapper').responsiveEqualHeightGrid();
						clearInterval(checkExist);
					}
				}, 1000);

				//jQuery('.category_wrapper .category_main_toolbar').insertBefore('#sidebar1');

				// start
				

				// end

			}
		 });
	}

	function order(select){
		var value = jQuery(select).val();
		jQuery('input[name="order_by"]').val(value);
		submitpriceform(null);
	}

	function pagination(start){
		jQuery('input[name="limitstart"]').val(start);
		submitpriceform(null);
	}

	jQuery(document).ready(function(){
		var check = [];
		function checkList(){
			jQuery('#redproductfinder-form-<?php echo $module->id;?> #manu #manufacture-list input').on('change', function(){
				var id = jQuery(this).val();
				if (jQuery(this).is(':checked')){
					check.push(id);
				}
				else{
					check.splice(jQuery.inArray(id, check), 1);
				}
				
				jQuery('input[name="check_list"]').val(JSON.stringify(check));
			});
		}

		checkList();

		jQuery('input[name="keyword-manufacturer"]').on('keyup', function(){
			var json    = '<?php echo json_encode($manufacturers); ?>';
			var arr     = jQuery.parseJSON(json);
			var keyword = jQuery(this).val();
			var new_arr = [];
			var check = jQuery('input[name="check_list"]').val();
			var check_list = jQuery.parseJSON(check);
			jQuery.each(arr, function(i, value){
				if (value.manufacturer_name.toLowerCase().indexOf(keyword.toLowerCase()) > -1){
					new_arr.push(value);
				}
			});

			var html = '';

			jQuery.each(new_arr, function(key, data){
				var check = Object.keys(data).length;
				if (check > 0){
					if(jQuery.inArray(data.manufacturer_id, check_list) != -1) {
					    var is_check = 'checked=""';
					} else {
					    var is_check = '';
					}
					html += '<li style="list-style: none"><label>';
					html += '<span class="taginput" data-aliases="'+data.manufacturer_id+'">';
					html += '<input type="checkbox" '+is_check+' value="'+data.manufacturer_id+'" name="redform[manufacturer][]" />';
					html += '<span class="tagname">'+data.manufacturer_name+'</span>';
					html += '</span>'
					html += '</label></li>';
				}
			});

			jQuery('#redproductfinder-form-<?php echo $module->id;?> #manu #manufacture-list').html('');
			jQuery('#redproductfinder-form-<?php echo $module->id;?> #manu #manufacture-list').append(html);
			checkList();
		});

		submitform();


		jQuery('#redproductfinder-form-<?php echo $module->id;?> [type="checkbox"]').each(function(){
			checkclick(jQuery(this))
		});

		jQuery('#redproductfinder-form-<?php echo $module->id;?>').html(function(){
			jQuery('span.label_alias').click(function(event) {
				if (jQuery(this).hasClass('active')) {
					jQuery(this).removeClass('active').next('ul.collapse').removeClass('in');
				}else{
					var ultab = redfinderform.find('ul.collapse.in');
					ultab.removeClass('in').prev('span').removeClass('active');

					jQuery(this).addClass('active').next('ul.collapse').addClass('in');
				}
			});
			range_slide(<?php echo $rangeMin;?>, <?php echo $rangeMax;?>, <?php echo $rangeMin;?>, <?php echo $rangeMax;?>, submitpriceform );
		});

		jQuery('input[name="redform[filterprice][min]"], input[name="redform[filterprice][max]"]').on('keyup', function(){
			var min = jQuery('input[name="redform[filterprice][min]"]').val();
			var max = jQuery('input[name="redform[filterprice][max]"]').val();
			range_slide(<?php echo $rangeMin;?>, <?php echo $rangeMax;?>, parseFloat(min), parseFloat(max), submitpriceform );
		})
	});
	window.onload = function() {
		submitpriceform("firstload");
	};

</script>