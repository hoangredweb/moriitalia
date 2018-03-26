<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

JHtml::_('behavior.modal');

$tag       = $displayData['tag'];
$value     = $displayData['value'];
$reditemId = $displayData['reditemId'];
$width     = $displayData['width'];
$height    = $displayData['height'];
$index     = $displayData['index'];
$item      = $displayData['item'];
?>

<?php if (!empty($value)) : ?>
<div class="reditem_youtube reditem_youtube_<?php echo $tag->id; ?>">
	<a id="youtube_vid_<?php echo $reditemId; ?>_<?php echo $index; ?>" class="youtube modal"
		href="//www.youtube.com/embed/<?php echo $value; ?>"
		style="width:<?php echo $width; ?>px; height:<?php echo $height; ?>px;"
		rel="{handler: 'iframe', size: {x: 640, y: 360}}">
	</a>
</div>
<?php endif; ?>
