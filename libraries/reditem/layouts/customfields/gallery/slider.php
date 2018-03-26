<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag       = $displayData['tag'];
$images    = $displayData['value'];
$reditemId = $displayData['reditemId'];
$item      = $displayData['item'];

$index     = (int) $displayData['index'];
$width     = (int) $displayData['width'];
$divId     = 'flexslider_' . $reditemId . '_' . $index;

if (count($images)) :

	$sliderParams = array(
		'animation'    => 'slide',
		'smoothHeight' => false,
		'minItems'     => 1,
		'maxItems'     => 1
	);

	if ($width > 0) :
		$sliderParams['itemWidth'] = $width . 'px';
	endif;

	JHtml::_('rjquery.flexslider', '#' . $divId, $sliderParams);
	?>

	<div class="reditem_gallery_slider reditem_gallery_slider_<?php echo $tag->id; ?>">
		<div class="flexslider" id="<?php echo $divId; ?>">
			<ul class="slides">
				<?php foreach ($images as $image) : ?>
				<li><?php echo $image; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif; ?>
