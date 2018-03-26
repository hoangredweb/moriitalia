<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_redshop_redmanufacturer
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// JHtml::_('redshopjquery.flexslider', '#redManufSlider_' . $module->id . ' .flexslider',
// 	array(
// 		'minItems'=> 1,
// 		'maxItems' => $scrollBehavior,
// 		'move' => $scrollAuto,
// 		'itemWidth' => ($scrollWidth / $scrollBehavior),
// 		'animation' => 'slide',
// 		'itemMargin' => 0,
// 		'animationSpeed' => $scrollDelay,
// 		'controlNav' => (boolean) $controlNav,
// 		'directionNav' => (boolean) $directionNav
// 	)
// );

$app = JFactory::getApplication();
$uri = JURI::getInstance();
$url = $uri->root();
$Itemid = $app->input->getInt('Itemid', 0);
$document = JFactory::getDocument();
// $document->addStyleDeclaration('
// #redManufSlider_' . $module->id . ' .flexslider{
// 	max-width: ' . $scrollWidth . 'px;
// }
// #redManufSlider_' . $module->id . ' .slides li{
// 	overflow: hidden;
// 	margin-right: 5px;
// }
// #redManufSlider_' . $module->id . ' .flexslider{
// 	margin-bottom: 40px;
// }
// #redManufSlider_' . $module->id . ' .slideImage, #redManufSlider_' . $module->id . ' .slideTitle{
// 	text-align: center;
// 	margin-bottom: 5px;
// }
// #redManufSlider_' . $module->id . ' .slideImage img{
// 	width: auto !important;
// 	height: auto !important;
// 	display: inline;
// 	max-width: 100%;
// }
// ');
?>
<div class="redManufSlider" id="redManufSlider_<?php echo $module->id; ?>">
<?php if ($list): ?>
		<ul>
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
					'index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $slide->manufacturer_id . '&Itemid=' . $Itemid, false
				);
				$title = $slide->manufacturer_name;
				?>
			<li>
				<?php if ($showProductName): ?>
					<?php if ($showLinkOnProductName): ?>
						<a href="<?php echo $link; ?>">
					<?php endif; ?>
				<?php echo $title; ?>
					<?php if ($showLinkOnProductName): ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</li>
			<?php endforeach; ?>
		</ul>
<?php endif; ?>
</div>
