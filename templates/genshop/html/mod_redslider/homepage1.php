<?php
/**
 * @package     RedSLIDER.Frontend
 * @subpackage  mod_redslider
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
?>
<div id="redslider-<?php echo $module->id?>" class="redslider2 <?php echo $class; ?>" >
	<!-- Main slider -->
	<div class="slider" >
	<?php $dem =1; $count =count($slides); if ($count>0): ?>
		<ul class="slides">
			<?php foreach ($slides as $slide): ?>
				<?php if (isset($slide->template_content) && isset($slide->background)): ?>
					<?php if($dem==1):?>
						<div class="bg-slider" style="background-image:url(<?php echo JURI::base() . $slide->background ?>);">
						</div>
					<?php endif;?>
				<li class="<?php echo $slide->class . ' ' . $dem; ?>">
					<div class="slide-wrapper">
						<div class="slide-content" ><?php echo $slide->template_content ?></div>
						<div class="slide-img" style="background:url(<?php echo JURI::base() . $slide->background ?>) no-repeat;">
						</div>
					</div>
				</li>
				<?php $dem++; ?>
				<?php endif ?>
			<?php endforeach ?>
		</ul>
		<?php endif ?>
	</div>
	<!-- End main slider -->

	<!-- Thumbnails slider -->
	<?php if ($thumbNums && $slideThumbnail): ?>
		<div class="carousel" >
			<ul class="slides">
				<?php foreach ($slides as $slide): ?>
					<li><img src="<?php echo JURI::base() . $slide->background ?>" /></li>
				<?php endforeach ?>
			</ul>
		</div>
	<?php endif ?>
	<!-- End thumbnails slider -->
</div>