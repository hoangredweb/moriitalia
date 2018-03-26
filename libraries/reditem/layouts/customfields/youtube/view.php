<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag     = $displayData['tag'];
$value   = $displayData['value'];
$item    = $displayData['item'];
$width   = $displayData['width'];
$height  = $displayData['height'];
$display = $displayData['display'];
?>

<?php if (!empty($value)) : ?>
<div class="reditem_youtube reditem_youtube_<?php echo $tag->id; ?>">
	<?php if ($display == 'modal'):?>
	<a class="youtube"
	   href="#reditem_youtube_modal_<?php echo $tag->id; ?>"
	   data-toggle="modal"
	   youtube="<?php echo $value; ?>"
	   style="width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;">
	</a>
	<div id="reditem_youtube_modal_<?php echo $tag->id; ?>" class="modal fade reditem_youtube_modal"
	     tabindex="-1" role="dialog" aria-hidden="true"
	     style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;">
		<div id="reditem_youtube_video_<?php echo $tag->id; ?>" class="reditem_youtube_video" style="top: 50%; left: 50%; margin-left: <?php echo $width / 2;?>px; maring-top: <?php echo $height / 2;?>px"></div>
	</div>
	<?php else:?>
	<iframe width="<?php echo $width;?>" height="<?php echo $height;?>" src="https://www.youtube.com/embed/<?php echo $value;?>" frameborder="0" allowfullscreen></iframe>
	<?php endif;?>
</div>
<?php endif; ?>
