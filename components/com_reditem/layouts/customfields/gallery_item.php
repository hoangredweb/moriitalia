<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$basePath  = $displayData['basepath'];
$fieldCode = $displayData['fieldcode'];
$imageName = $displayData['image'];
$index     = $displayData['index'];
$config    = $displayData['config'];
$default   = $displayData['default'];

$fieldId       = 'cform_' . $fieldCode . $index;
$imagePath     = JURI::root() . 'media/com_reditem/images/' . $basePath . '/' . $imageName;
$previewWidth  = $config->get('preview_image_width', '300');
$previewHeight = $config->get('preview_image_height', '300');
?>

<div class="media" id="div_<?php echo $fieldId; ?>">
	<img src="<?php echo $imagePath; ?>" class="img-polaroid pull-left" style="max-width: <?php echo $previewWidth ?>px; max-height: <?php echo $previewHeight ?>px; margin-right: 20px;" />
	<div class="media-body">
		<label class="checkbox">
			<input class="cfgallery-remove-checkbox" type="checkbox" name="jform[customfield_gallery_rm][<?php echo $fieldCode; ?>][]" value="<?php echo $imageName; ?>" />
			<?php echo JText::_('COM_REDITEM_CUSTOMFIELD_IMAGE_REMOVE'); ?>
		</label>
		<label class="radio">
			<input
				class="cfgallery-set-default-radio"
				type="radio"
				name="jform[customfield_gallery_default][<?php echo $fieldCode; ?>]"
				value="<?php echo $index ?>" id="default_<?php echo $fieldId ?>"
				<?php if ($default): ?>
				checked="checked"
				<?php endif; ?>
			/>
			<?php echo JText::_('COM_REDITEM_CUSTOMFIELD_GALLERY_SET_DEFAULT'); ?>
		</label>
		<input type="hidden" name="cform[gallery][<?php echo $fieldCode; ?>][<?php echo $index; ?>]" id="<?php echo $fieldId; ?>_value" value="<?php echo $imageName; ?>" />
	</div>
</div>
<div class="clearfix"></div>
