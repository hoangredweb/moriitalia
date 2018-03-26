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

if ($displayType)
{
	// Display as slider
	JHtml::_('rjquery.framework');

	RHelperAsset::load('jquery.resize.min.js', 'com_reditem');
	RHelperAsset::load('jquery.bxslider.min.js', 'com_reditem');
}

JHtml::_('rholder.image', '100x100');

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
		<div id="mod_reditem_items_<?php echo $module->id; ?>">
			<ul class="latestnews">
				<?php foreach ($items as $item) : ?>
					<li itemtype="http://schema.org/Article" itemscope="">
						<?php echo $item->content; ?></div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	<?php else : ?>
		<p><?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_NO_ITEM_FOUND'); ?>
	<?php endif; ?>
</div>
