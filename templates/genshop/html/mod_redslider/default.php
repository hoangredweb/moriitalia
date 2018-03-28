<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
/*var_dump(json_decode($slides[0]->params));
die;*/
?>
<div id="redslider-<?php echo $module->id?>" class="redslider2 <?php echo $class; ?>" >
	<!-- Main slider -->
	<div class="slider" >
	<?php if (count($slides)): ?>
		<ul class="slides">
			<?php foreach ($slides as $slide): ?>
				<?php if (isset($slide->template_content) && isset($slide->background)): ?>
				<li class="<?php echo $slide->class; ?>">
					<div class="slide-wrapper">
						<!--<div class="slide-content" ><?php echo $slide->template_content ?></div>-->
						<div class="slide-img" style="background:url(<?php echo JURI::base() . $slide->background ?>) no-repeat center top / 100% 100%;">
						</div>
					</div>
				</li>
				<?php endif ?>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	</div>
	<!-- End main slider -->

	<!-- Category slider -->
	<?php if (count($slides)): ?>
		<div class="slide-category">
			<nav class="" id="slide-category">
			<?php 
			$i = 1;	
			foreach ($slides as $slide): ?>
				<?php if (isset($slide->params)): 
				$slideParams = json_decode($slide->params);
				?>
					<a href="#" data-index="<?php echo $i?>" class="block-shop col-sm-4 col-xs-12"><span class="inner-block-shop"><span class="caption"><?php echo $slideParams->caption?></span><span class="desc"><?php echo $slideParams->description?></span></span></a>
				<?php endif ?>
			<?php 
			$i++;
			endforeach;?>
			</nav>
		</div>
	<?php endif ?>
	<!-- End category slider -->
</div>

<script>
jQuery(document).ready(function($) {
	$('.flex-control-nav a').on('mousedown', function(e) {
		var data_index = $(this).text();

		$(".slide-category nav a").removeClass('slide-selected');
		$(".slide-category nav a[data-index='" + data_index +"']").addClass('slide-selected');

		e.preventDefault();
		return false;
	})

	$('.slide-category nav a').on('click', function(e) {
		var data_index = $(this).data('index');
		console.log(data_index);
		$(".flex-control-nav a:contains(" + data_index + ")").trigger('click');
		$(".flex-control-nav a:contains(" + data_index + ")").trigger('mousedown');	
		e.preventDefault();
		return false;
	})

	setSlider();
	// Init
	function setSlider() {
	    var data_index = parseInt($('.flex-control-nav a.flex-active').text());
	    var count_index = parseInt($('.flex-control-nav a').size());

		$(".slide-category nav a").removeClass('slide-selected');
		$(".slide-category nav a").removeClass('slide-beside');
		$(".slide-category nav a").removeAttr('id');
		$(".slide-category nav a[data-index='" + data_index + "']").addClass('slide-selected block-shop2');

		$(".slide-category nav a[data-index='" + data_index + "']").attr('id', "my2");

		if (data_index > 1 && data_index < count_index)
		{
			var tmp1 = data_index - 1;
			var tmp2 = data_index + 1;
		}
		else if (data_index == 1)
		{
			var tmp1 = count_index;
			var tmp2 = data_index + 1;
		
		}
		else if (data_index == count_index)
		{
			var tmp1 = data_index - 1;
			var tmp2 = 1;
		}

		$(".slide-category nav a[data-index='" + tmp1 + "']").addClass('slide-beside');
		$(".slide-category nav a[data-index='" + tmp2 + "']").addClass('slide-beside');

		$(".slide-category nav a[data-index='" + tmp1 + "']").attr('id', "my1");
		$(".slide-category nav a[data-index='" + tmp2 + "']").attr('id', "my3");

	    setTimeout(setSlider, 1000);
	}
})
</script>

<style>
#slide-category{
	display: -webkit-flex; /* Safari */
    display: flex;
}

#slide-category a#my1{
	order: 1;
	-webkit-order: 1;
}
#slide-category a#my2{
	order: 2;
	-webkit-order: 2;
}
#slide-category a#my3{
	order: 3;
	-webkit-order: 3;
}

.slide-category nav a{
	display: none;
}
.slide-category nav a.slide-beside{
	display: block;
}
.slide-category nav a.slide-selected{
	font-weight: bold;
	display: block;
}
</style>