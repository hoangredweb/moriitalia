<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$doc = JFactory::getDocument();

if (!empty($items)){
	if ($displayType)
	{
		// Display as slider
		JHtml::_('rjquery.framework');
		RHelperAsset::load('jquery.resize.min.js', 'com_reditem');
		RHelperAsset::load('jquery.bxslider.min.js', 'com_reditem');
	}
	else{

		//swiper
		JHtml::_('rjquery.framework');
		JHtml::script(Juri::base() . 'templates/genshop/js/swiper.min.js', false, true);
		JHtml::script(Juri::base() . 'templates/genshop/js/functionswiper.js', false, true);
	}
	JHtml::_('rholder.image', '100x100');
}

?>


<?php if ((!empty($items)) && ($displayType)) : ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function() {
			$("#mod_reditem_items_<?php echo $module->id; ?>").bxSlider({
				controls: <?php echo $sliderControls; ?>,
				pager: <?php echo $slidePager; ?>,
				auto: <?php echo $slideAutoPlay; ?>
			});
		});
	})(jQuery);

</script>
<?php endif; ?>

<div class="mod_reditem_items_wrapper">
	<?php if (!empty($items)) : ?>
		<?php if (!$template || ($template->typecode != 'module_items')) : ?>
			<p class="alert alert-error">
				<?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_TEMPLATE_NOT_FOUND'); ?>
			</p>
			<?php else : ?>
		<div id="mod_reditem_items_<?php echo $module->id; ?>" class="swiper-wrapper">
				<?php foreach ($items as $item) : ?>
					<div class="col-sm-3 col-xs-6"><?php echo $item->content; ?></div>
				<?php endforeach; ?>
		</div>
		<?php endif; ?>
	<?php else : ?>
		<p><?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_NO_ITEM_FOUND'); ?>
	<?php endif; ?>
</div>

<script type="text/javascript">
		jQuery('#mod_reditem_items_<?php echo $module->id ?>').prepend('<div class="chevron-box"><div class="prevbtn"><b class="icon icon-angle-left"></b></div><div class="nextbtn"><b class="icon icon-angle-right"></b></div></div>');
		makeswiper('#mod_reditem_items_<?php echo $module->id ?>','.col-sm-3');
		reponSwiper_mostproduct('#mod_reditem_items_<?php echo $module->id ?>', '#mod_reditem_items_<?php echo $module->id ?> .nextbtn', '#mod_reditem_items_<?php echo $module->id ?> .prevbtn');
</script>