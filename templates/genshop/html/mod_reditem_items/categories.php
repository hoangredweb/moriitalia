<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
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

<?php
	$model = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
	$categories = $model->getItems();
?>
<div class="mod_catelist_wrapper">
	<?php if (count($items)) : ?>
		<?php if (!$template || ($template->typecode != 'module_items')) : ?>
			<p class="alert alert-error">
				<?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_TEMPLATE_NOT_FOUND'); ?>
			</p>
			<?php else : ?>
		<ul id="mod_catelist_items_<?php echo $module->id; ?>">
			<?php foreach ($categories as $cate) : ?>
				<?php if (in_array($cate->id, $categoriesId)): ?>
					<?php
						$paramItemId = $params->get('setItemId', 0);
						$link = JRoute::_('index.php?option=com_reditem&view=categorydetail&id='.$cate->id.'&templateId='.$cate->template_id.'&Itemid='.$paramItemId);
					?>
					<li><a href='<?php echo $link;?>'><?php echo $cate->title; ?></a></li>
				<?php endif ?>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	<?php else : ?>
		<p><?php echo JText::_('MOD_REDITEM_ITEMS_ERROR_NO_ITEM_FOUND'); ?>
	<?php endif; ?>
</div>
