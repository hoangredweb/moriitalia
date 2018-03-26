<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_redmanufacturer
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('rjquery.framework');

// JHtml::_('redshopjquery.flexslider', '#redManufSlider_' . $module->id . ' .flexslider',
// 	array(
// 		'minItems' => 1,
// 		'slideshow' => false,
// 		'maxItems' => $scrollBehavior,
// 		'move' => $scrollAuto,
// 		'itemWidth' => ($scrollWidth / $scrollBehavior),
// 		'animation' => 'slide',
// 		'itemMargin' => 0,
// 		'animationSpeed' => $scrollDelay,
// 		'controlNav' => !empty((boolean) $controlNav) ? 1 : 0,
// 		'directionNav' => (boolean) $directionNav
// 	)
// );

$document = JFactory::getDocument();
$document->addStyleDeclaration('
#redManufSlider_' . $module->id . ' .flexslider{
	max-width: ' . $scrollWidth . 'px;
}
#redManufSlider_' . $module->id . ' .slides li{
	overflow: hidden;
	margin-right: 5px;
}
#redManufSlider_' . $module->id . ' .flexslider{
	margin-bottom: 40px;
}
#redManufSlider_' . $module->id . ' .slideImage, #redManufSlider_' . $module->id . ' .slideTitle{
	text-align: center;
	margin-bottom: 5px;
}
#redManufSlider_' . $module->id . ' .slideImage img{
	width: auto !important;
	height: auto !important;
	display: inline;
	max-width: 100%;
}
');
?>


<script type="text/javascript">
	(function($){
		$(document).ready(function() {
			  var $window = $(window),
		      flexslider = { vars:{} };
		 
			  // tiny helper function to add breakpoints
			  function getGridSize() {
			    return (window.innerWidth < 600) ? 2 :
			           (window.innerWidth < 900) ? 4 : 6;
			  }
			 
			  //$window.load(function() {
			    $('#redManufSlider_<?php echo $module->id ?>').flexslider({

			      animation: 'slide',
			      minItems: 1,
			      slideshow: false,
			      maxItems: <?php echo $scrollBehavior ?>,
			      move: <?php echo $scrollAuto ?>,
			      itemWidth: <?php echo ($scrollWidth / $scrollBehavior) ?>,
			      itemMargin: 20,
			      animationSpeed: <?php echo $scrollDelay?>,
			      controlNav: '<?php echo !empty((boolean) $controlNav) ? 1 : 0; ?>',
			      directionNav: '<?php echo (boolean) $directionNav ?>',
			      animationLoop: false,
			      minItems: getGridSize(),
			      maxItems: getGridSize()
			    });
			  //});
		 
		  // check grid size on resize event
		  $window.resize(function() {
		    var gridSize = getGridSize();
		 
		    flexslider.vars.minItems = gridSize;
		    flexslider.vars.maxItems = gridSize;
		  });

		});
		  

	})(jQuery);

	
</script>
<div class="redManufSlider" id="redManufSlider_<?php echo $module->id; ?>">
<?php if ($preText): ?>
<div class="preText">
	<?php echo $preText ?>
</div>
<?php endif; ?>
<?php if ($list): ?>
<div class="flexslider">
	<ul class="slides">
		<?php foreach ($list as $slide):
			$thumbUrl = RedShopHelperImages::getImagePath(
				$slide->media_name,
				'',
				'thumb',
				'manufacturer',
				$ImageWidth,
				$ImageHeight,
				USE_IMAGE_SIZE_SWAPPING
			);
			$link = JRoute::_(
				'index.php?option=com_redshop&view=manufacturers&layout='
				. $PageLink . '&mid=' . $slide->manufacturer_id . '&Itemid=' . $slide->item_id, false
			);
			$title = $slide->manufacturer_name;
			?>
		<li>
			<?php if ($showImage): ?>
				<div class="slideImage">
					<a href="<?php echo $link; ?>"><img src="<?php echo $thumbUrl; ?>" /></a>
				</div>
			<?php endif; ?>
			<?php if ($showProductName): ?>
				<div class="slideTitle">
				<?php if ($showLinkOnProductName): ?>
					<a href="<?php echo $link; ?>">
				<?php endif; ?>
			<?php echo $title; ?>
				<?php if ($showLinkOnProductName): ?>
					</a>
				<?php endif; ?>
				</div>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>
</div>
