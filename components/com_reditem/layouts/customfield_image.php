<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

//RHelperAsset::load('jquery.colorbox.min.js', 'com_reditem');
//RHelperAsset::load('colorbox.min.css', 'com_reditem');

JHtml::_('rholder.image', '100x100');

$tag   = $displayData['tag'];
$value = $displayData['value'];
$thumb = $displayData['thumb'];
$item  = $displayData['item'];
?>

<?php if ($thumb): ?>
<script type="text/javascript">
	// (function($){
	// 	$(document).ready(function () {
	// 		$('.reditem_image_colorbox_<?php echo $tag->id; ?>').colorbox({
	// 			maxWidth: "90%",
	// 			maxHeight: "90%"
	// 		});
	// 	});
	// })(jQuery);
</script>
<?php endif; ?>

<?php if (!empty($value)): ?>
<div class="reditem_image reditem_image_<?php echo $tag->id ?>">
	<?php if ($thumb): ?>
	<a class="reditem_image_colorbox_<?php echo $tag->id ?>" href="<?php echo $value ?>">
		<img src="<?php echo $thumb; ?>" />
	</a>
	<?php else : ?>
	<img src="<?php echo $value ?>" />
	<?php endif; ?>
</div>
<?php elseif ($thumb): ?>
<div class="reditem_image reditem_image_<?php echo $tag->id ?>">
	<img src="<?php echo $thumb ?>" />
</div>
<?php endif; ?>
