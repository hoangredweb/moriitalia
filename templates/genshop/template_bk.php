<?php
/**
 * @package    Template.Template
 *
 * @copyright  Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

	$app = JFactory::getApplication();
	$temp_op     ='';
	$temp_view   ='';
	$temp_layout ='';
	$temp_layout = JRequest::getVar('layout');
	$temp_op     = JRequest::getVar('option');
	$temp_view   = JRequest::getVar('view');
	$lang = JFactory::getLanguage();
	$langTag = $lang->getTag();
	$menu = $app->getMenu();
	$user = JFactory::getUser();
?>
<!DOCTYPE html>
	<html>
	<head>
		<!--
            ##########################################
            ## redWEB ApS                     		##
            ## Blangstedgaardsvej 1                 ##
            ## 5220 Odense SØ                       ##
            ## Danmark                              ##
            ## email@redweb.dk             			##
            ## http://www.redweb.dk          		##
            ## Dato: 2015-10-28                  ##
            ##########################################
        -->

		<w:head />
		<?php if ($menu->getActive() == $menu->getDefault($lang->getTag()) && $this->countModules('instagram')):?>
		 	<script type="text/javascript" src="<?php echo JURI::root();?>templates/genshop/js/instafeed.js"></script>
			<script type="text/javascript">

			    var feed = new Instafeed({
			        get: 'user',
				    userId: '3990872003',
				    clientId: 'de0d45ba4d6b47aba3c086f2ef84aa23',
				    accessToken:'3983090256.ba4c844.f897bdee2d994d34b5fbfd43ed0e23d6',

				    resolution: 'standard_resolution',
				    template: '<div class=" col-md-3 col-sm-6 col-xs-12"><a href="{{link}}" target="_blank"><img src="{{image}}" /><div class="likes">&hearts; {{likes}}</div></a></div>',
				    limit: 4
			    });
			    jQuery(window).on('load',function(){
			    	feed.run();
			    });
			</script>
 		<?php endif;?>
		<!--End of Zopim Live Chat Script-->
		<?php if ($this->countModules('map-home-page') || $this->countModules('map')) : ?>
			<link rel="stylesheet" href="<?php echo JURI::root();?>templates/genshop/css/jquery.mCustomScrollbar.css" />
			<script src="<?php echo JURI::root();?>templates/genshop/js/jquery.mCustomScrollbar.concat.min.js"></script>
			<script type="text/javascript">
				 (function($){
			        $(window).on("load",function(){
			            $(".scrollbar-inner").mCustomScrollbar();
			        });
			    })(jQuery);
			</script>
		<?php endif;?>
	</head>
	<?php if($temp_op=="com_redshop" && $temp_view=="manufacturers"):?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var bg_cate = $('.redSHOPSiteViewManufacturers .manufacturer_description img').attr('src');
				var title_cate = $('.redSHOPSiteViewManufacturers .manufacturer_name').html();

				var win_w = $(window).innerWidth();
				if(win_w>767){
					$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% auto');
				}
				else{
					$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% 100%');
				}
				if ( !bg_cate ){
					$('.bg-category .container-full').hide();
				}
				$(window).resize(function(event) {
					var win_w2 = $(window).innerWidth();
					if(win_w2>767){
						$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% auto');
					}
					else{
						$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% 100%');
					}
					if ( !bg_cate ){
						$('.bg-category .container-full').hide();
					}
				});
				$('.bg-category h1 .inner').html(title_cate);
			});
		</script>
	<?php endif;?>
	<?php if($temp_op=="com_redshop" && $temp_view=="category"):?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var bg_cate = $('.category-main-image img').attr('src');
				var title_cate = $('.category_main_title').html();

				var win_w = $(window).innerWidth();
				if(win_w>767){
					$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% auto');
				}
				else{
					$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% 100%');
				}
				if ( !bg_cate ){
					$('.bg-category .container-full').hide();
				}
				$(window).resize(function(event) {
					var win_w2 = $(window).innerWidth();
					if(win_w2>767){
						$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% auto');
					}
					else{
						$('.bg-category .container-full').css('background', 'url('+bg_cate+') no-repeat center top / 100% 100%');
					}
					if ( !bg_cate ){
						$('.bg-category .container-full').hide();
					}
				});
				$('.bg-category h1 .inner').html(title_cate);
				$('.category_s_desc p').insertAfter('.bg-category h1');
			});
		</script>
	<?php endif;?>
	<body <?php if ($bodyclass != "") : ?> class="<?php echo $langTag; ?> <?php echo $bodyclass . ' ' . $temp_view ?> "<?php endif; ?>>
			<div id="page-content-wrapper">
				<div id="toolbar-top">
					<div class="container">
						<?php if ($this->countModules('toolbar')) : ?>
							<w:module type="toolbar" name="toolbar" chrome="xhtml" />
						<?php endif; ?>
					</div>
				</div>
				<div class="container">
					<!-- header -->
					<header id="header">
						<div class="row clearfix">
			                <w:logo name="top" />
						</div>
					</header>
				</div>
				<!-- <div class="header-fixed">
					<div class="container">
						<div class="row">
							<div class="top-left col-sm-5"></div>
							<div class="logo col-sm-2">

							</div>
							<div id="top_primary" class="search-fix col-sm-5">

							</div>
							<div class="menu-fixed col-sm-12">
							</div>
						</div>
					</div>
				</div> -->
				<div id="menubar">
					<div class="container">
						<?php if ($this->countModules('menu')) : ?>
							<!-- menu -->
							<w:nav name="menu" />
						<?php endif; ?>
					</div>
				</div>
				<?php if ($temp_op != 'com_content' || ( $temp_view != 'category' && $temp_layout != "blog" ) ): ?>
					<?php if ($this->countModules('breadcrumbs')) : ?>
						<!-- breadcrumbs -->
						<div id="breadcrumbs">
						<div class="container">
							<w:module type="single" name="breadcrumbs" chrome="none" />
						</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			
				<?php if ($this->countModules('featured')) : ?>
					<!-- featured -->
					<div id="featured" class="clearfix">
						<w:module type="none" name="featured" chrome="xhtml" />
					</div>
				<?php endif; ?>
				<?php if ($this->countModules('manufacture')) : ?>
						<div id="manufacture" class="clearfix">
							<w:module type="none" name="manufacture" chrome="xhtml" />
						</div>
				<?php endif; ?>
				<?php if ($this->countModules('bellow-featured')) : ?>
					<!-- featured -->
					<div class="container">
						<div id="bellow-featured" class="clearfix">
							<w:module type="none" name="bellow-featured" chrome="xhtml" />
						</div>
					</div>
				<?php endif; ?>

				<?php if (($temp_op == 'com_redshop' && $temp_view == 'category') || ($temp_layout == 'products' && $temp_view == 'manufacturers')):?>
				<div class="bg-category">
					<div class="container-full"></div>
					<h1 class="container"><span class="inner"></span></h1>
				</div>
				<?php endif;?>
					<?php if ($this->countModules('grid-top4')) : ?>
						<!-- grid-top4 -->
						<div id="grid-top4" class="relative clearfix">
							<div class="container">
							<w:module type="row" name="grid-top4" chrome="wrightflexgrid" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('grid-top-slider')) : ?>
						<!-- grid-top3 -->
						<div id="grid-top-slider" class="clearfix">
							<w:module type="row" name="grid-top-slider" chrome="wrightflexgrid" />
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('grid-top3')) : ?>
						<!-- grid-top3 -->
						<div id="grid-top3" class="relative clearfix">
							<div class="container">
							<w:module type="row" name="grid-top3" chrome="wrightflexgrid" />
							</div>
						</div>
					<?php endif; ?>
					
					<?php if ($this->countModules('grid-top2')) : ?>
						<!-- grid-top2 -->
						<div id="grid-top2" class="relative clearfix">
							<div class="container">
							<w:module type="row" name="grid-top2" chrome="wrightflexgrid" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('grid-top5')) : ?>
						<!-- grid-top4 -->
						<div id="grid-top5" class="relative">
							<div class="container">
							<w:module type="row" name="grid-top5" chrome="wrightflexgrid" />
							<div>
						</div>
					<?php endif; ?>
					<div class="container container-content">
						<div id="main-content" class="row">
							<!-- sidebar1 -->

							<?php if ($this->countModules('sidebar1')) : ?>
								<aside id="sidebar1">
									<w:module name="sidebar1" chrome="xhtml" />
								</aside>
							<?php endif; ?>

							<!-- main -->
							<section id="main">
								<?php if ($this->countModules('above-content')) : ?>
									<!-- above-content -->
									<div id="above-content">
										<w:module type="none" name="above-content" chrome="wrightflexgrid" />
									</div>
								<?php endif; ?>
								<?php if ($this->countModules('above-content-1')) : ?>
									<!-- above-content -->
									<div id="above-content-1">
										<w:module type="none" name="above-content-1" chrome="xhtml" />
									</div>
								<?php endif; ?>
								<!-- component -->
								<w:content />
								<?php if ($this->countModules('below-content-1')) : ?>
									<!-- above-content -->
									<div id="below-content-1" class="clearfix">
										<w:module type="none" name="below-content-1" chrome="wrightflexgrid" />
									</div>
								<?php endif; ?>
								<?php if ($this->countModules('below-content-2')) : ?>
									<!-- above-content -->
									<div id="below-content-2" class="clearfix">
										<w:module type="none" name="below-content-2" chrome="wrightflexgrid" />
									</div>
								<?php endif; ?>
								<?php if ($this->countModules('below-content')) : ?>
									<!-- below-content -->
									<div id="below-content" class="clearfix">
										<div class="container">
										<w:module type="none" name="below-content" chrome="wrightflexgrid" />
									</div>
									</div>
								<?php endif; ?>
							</section>
							<!-- sidebar2 -->
							<aside id="sidebar2">
								<w:module name="sidebar2" chrome="xhtml" />
							</aside>
						</div>
					</div>
					<?php if ($this->countModules('grid-bottom')) : ?>
						<!-- grid-bottom -->
						<div id="grid-bottom" class="relative clearfix">
							<div class="container">
							<w:module type="row" name="grid-bottom" chrome="wrightflexgrid" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('grid-bottom2')) : ?>
						<!-- grid-bottom2 -->
						<div id="grid-bottom2" class="relative clearfix">
							<div class="container">
							<w:module type="row" name="grid-bottom2" chrome="wrightflexgrid" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($temp_view=="product" && $this->countModules('module-in-product')) : ?>
						<!-- grid-bottom2 -->
						<div id="grid-bottom3" class="relative clearfix">
							<div class="container">
								<w:module type="row" name="module-in-product" chrome="xhtml" />
							</div>
						</div>
					<?php endif; ?>
				<div class="container">
					<?php if ($this->countModules('instagram')) : ?>
								<div id="instagram-main" class="">
									<w:module type="none" name="instagram" chrome="xhtml" />
								</div>
					<?php endif; ?>
					<?php if ( $this->countModules('grid-top') && $this->countModules('next-grid-top') ) : ?>
						<div class="row grid-top-row">
							<div class="col-sm-6 grid-top-pos">
								<?php if ($this->countModules('grid-top')) : ?>
									<!-- grid-top -->
									<div id="grid-top">
										<w:module name="grid-top" chrome="xhtml" />
									</div>
								<?php endif; ?>
							</div>
							<div class="col-sm-6 next-grid-top-pos" style="">
								<?php if ($this->countModules('next-grid-top')) : ?>
									<!-- next-grid-top -->
									<div id="next-grid-top">
										<w:module name="next-grid-top" chrome="xhtml" />
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('bottom-menu')) : ?>
						<!-- bottom-menu -->
						<w:nav containerClass="container" rowClass="row" name="bottom-menu" />
					<?php endif; ?>
				</div>
				<?php if ($this->countModules('map-home-page')) : ?>
					<div class="map-home-page">
						<w:module type="none" name="map-home-page" chrome="xhtml" />
					</div>
				<?php endif; ?>
				<?php if ($this->countModules('map')) : ?>
						<div id="map" class="map-home-page">
							<w:module type="none" name="map" chrome="xhtml" />
						</div>
					<?php endif; ?>
				<!-- footer -->
				<div class="wrapper-footer">
					<footer id="footer" <?php if ($this->params->get('stickyFooter', 1)) : ?> class="sticky"<?php endif;?>>
						<div class="container">
							<?php if ($this->countModules('footer')) : ?>
								<w:module type="row" name="footer" chrome="wrightflexgrid" />
							<?php endif; ?>
						</div>
						<?php if ($this->countModules('footer-bellow')) : ?>
							<div class="container">
								<div class="container-inner">
									<w:module type="row" name="footer-bellow" chrome="wrightflexgrid" />
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->countModules('footer-info')) : ?>
						<div class="site-info">
							<w:module type="row" name="footer-info" chrome="wrightflexgrid" />
						</div>
						<?php endif; ?>
					</footer>
				</div>

			    <w:footer />
			    <a href="#" class="back-to-top"><i class="fa fa-chevron-up fa-5" aria-hidden="true"></i></a>
			</div>

			<?php if ( $this->countModules('signup')):?>
			<button type="button" class="btn btn-info btn-lg signuptosave" data-toggle="modal" data-target="#signup-to-save"><?php echo JText::_('SIGN_UP_SAVE')?></button>
			<div id="signup-to-save" class="modal fade" role="dialog">
	          	<div class="modal-dialog">
		            <div class="modal-content">
		              <div class="modal-header">
		              	<h4><?php echo JText::_('SIGN_UP_SAVE')?></h4>
		                <button type="button" class="close" data-dismiss="modal">×</button>
		              </div>
		              <div class="modal-body">
		                	<w:module name="signup" chrome="xhtml" />
		              </div>
		            </div>
	          	</div>
	        </div>
	    	<?php endif;?>
	    	<?php if ($this->countModules('banner-menu-sale')) : ?>
						<div id="banner-menu-sale" class="hidden">
							<w:module type="none" name="banner-menu-sale" chrome="xhtml" />
						</div>
			<?php endif; ?>
			<div id="wait" style="z-index:9999;display:none;width:128px;height:128px;border:none;position:fixed;top:50%;left:50%;padding:2px;margin-left:-59px;margin-top:-59px;"><img src='<?php echo JURI::root()?>templates/genshop/images/loading.gif' width="128" height="128" /></div>
	</body>
</html>
