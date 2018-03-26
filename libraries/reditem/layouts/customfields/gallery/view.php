<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

RHelperAsset::load('jquery.colorbox.min.js', 'com_reditem');
RHelperAsset::load('colorbox.min.css', 'com_reditem');

$tag        = $displayData['tag'];
$images     = $displayData['value'];
$firstImage = $displayData['firstImage'];
$reditemId  = $displayData['reditemId'];
$item       = $displayData['item'];

$index      = (int) $displayData['index'];
$divId      = 'colorbox_group_' . $reditemId . '_' . $index;
?>

<?php if (!empty($images)): ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function () {
			$('.<?php echo $divId; ?>').colorbox({
				rel:"<?php echo $divId; ?>",
				maxWidth: "90%",
				maxHeight: "90%"
			});
		});
	})(jQuery);
</script>
<?php endif; ?>

<div class="reditem_gallery reditem_gallery_<?php echo $tag->id; ?>">
	<?php if ($firstImage && !empty($images)) : ?>
		<a class="<?php echo $divId; ?>" href="<?php echo $firstImage['original']['path']; ?>">
			<img src="<?php echo $firstImage['thumbnail']['path']; ?>" title="<?php echo $firstImage['original']['alt'];?>" />
		</a>
	<?php elseif ($firstImage): ?>
		<img src="<?php echo $firstImage['thumbnail']['path']; ?>" title="<?php echo $firstImage['original']['alt'];?>" />
	<?php endif; ?>
	<?php if (!empty($images)) : ?>
		<?php foreach ($images as $image) : ?>
			<a class="<?php echo $divId; ?> hidden" href="<?php echo $image['path']; ?>" title="<?php echo $image['alt'];?>"></a>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
