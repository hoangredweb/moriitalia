<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$id           = $displayData['id'];
$fieldcode    = $displayData['fieldcode'];
$value        = $displayData['value'];
$attributes   = $displayData['attributes'];
$basePath     = $displayData['basepath'];
$imagePreview = $displayData['imagePreview'];
$config       = $displayData['config'];

$uploadMaxFilesize       = (int) $config->get('upload_max_filesize', 2);
$uploadMaxFilesizeInByte = $uploadMaxFilesize * 1024 * 1024;
$allowedFileExtension    = $config->get('allowed_file_extension', 'jpg,jpeg,gif,png');
$allowedMime             = $config->get('allowed_file_mimetype', 'image/jpg,image/jpeg,image/gif,image/png');
$fieldName               = 'cform[image][' . $fieldcode . '_file]';
$fieldId                 = 'cform_' . $fieldcode;

$isCroppingEnable = $config->get('enable_cropping_image', '1');
$cropKeepRatio    = (boolean) $config->get('crop_keep_ratio', 0);
$previewWidth     = $config->get('preview_image_width', '300');
$previewHeight    = $config->get('preview_image_height', '300');
$cropWidth        = $config->get('crop_width', '');
$cropHeight       = $config->get('crop_height', '');

// Load string for javascripts
JText::script('COM_REDITEM_UPLOAD_1_FILE_ONLY');
JText::script('COM_REDITEM_ITEM_DRAG_AN_IMAGE');
JText::script('COM_REDITEM_ITEM_DRAG_IMAGES');
JText::script('COM_REDITEM_ITEM_DRAG_FEATURE_NOT_SUPPORT');
JText::script('COM_REDITEM_UPLOAD_FILE_INVALID');
JText::script('COM_REDITEM_UPLOAD_FILE_TOO_BIG');
JText::script('COM_REDITEM_UPLOAD_ABORT');
JText::script('COM_REDITEM_FEATURE_CROP_BTN_LBL');
JText::script('COM_REDITEM_FEATURE_CROPIMAGE_FAIL');

// Load dragndrop scripts
RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
RHelperAsset::load('jquery/jquery.ajaxfileupload.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.css', 'com_reditem');
RHelperAsset::load('reditem.cropimage.min.js', 'com_reditem');
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#imgfield_<?php echo $fieldcode; ?>_<?php echo $id ?>').dragndrop({
			url: "index.php?option=com_reditem&task=item.ajaxUpload",
			text: "<?php echo JText::_('COM_REDITEM_ITEM_DRAG_A_FILE') ?>",
			img_preview: "div_<?php echo $fieldId; ?>",
			img_preview_path: "<?php echo JURI::root() . 'media/com_reditem/files/customfield/temporary/' ?>",
			config: {
				size: "<?php echo $uploadMaxFilesizeInByte ?>",
				ext: "<?php echo $allowedFileExtension?>",
				mime: "<?php echo $allowedMime ?>"
			},
			cropConfig:{
				isEnable: "<?php echo $isCroppingEnable ?>",
				previewWidth: "<?php echo $previewWidth ?>",
				previewHeight: "<?php echo $previewHeight ?>",
				<?php if ($cropKeepRatio) : ?>
				keepRatio: true,
				<?php endif; ?>
				<?php if ($cropWidth && $cropHeight) : ?>
				cropWidth: "<?php echo $cropWidth ?>",
				cropHeight: "<?php echo $cropHeight ?>",
				<?php endif; ?>
			}
		});
	});

	function jInsertFieldValue(value, id) {
		(function($){
			$("#" + id + "_value_media").val(value);
			var imgPreview = "<img src='<?php echo JUri::root() ?>" + value + "' style='max-width: 100px; max-height: 100px;' />";
			$("#" + id + "_media_preview").html(imgPreview);
		})(jQuery);
	}
</script>

<div>
	<div class="media" id="div_<?php echo $fieldId; ?>">
		<?php if (!empty($imagePreview)) : ?>
		<div class="pull-left">
			<div class="img-preview-container" style="position:relative;">
				<img id="img_preview_<?php echo $fieldcode?>" src="<?php echo $imagePreview; ?>" class="img-polaroid" style="max-width: <?php echo $previewWidth ?>px; max-height: <?php echo $previewHeight ?>px; margin-right: 20px;" />
			</div>
		</div>
		<?php endif; ?>
		<div class="media-body">
			<?php if (!empty($imagePreview)) : ?>
			<label class="checkbox">
				<input type="checkbox" name="jform[customfield_image_rm][]" value="<?php echo $fieldcode; ?>" />
				<?php echo JText::_('COM_REDITEM_CUSTOMFIELD_IMAGE_REMOVE'); ?>
			</label>
			<?php endif; ?>
			<p>
				<input type="file" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>" <?php echo $attributes; ?> />
				<div class="clearfix"></div>
			</p>
			<div id="imgfield_<?php echo $fieldcode; ?>_<?php echo $id ?>" class="dragndrop" upload-type="image"
				input-target="<?php echo $fieldId; ?>" target="<?php echo $fieldcode ?>"></div>
			<p>
				<a class="modal-thumb btn" rel="{handler: 'iframe', size: {x: 1050, y: 450}}" title=""
					href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=<?php echo $fieldId; ?>&amp;redcore=true">
					<?php echo JText::_('COM_REDITEM_ITEM_IMAGE_MEDIA'); ?>
				</a>
				<input type="hidden" id="<?php echo $fieldId; ?>_value_media" name="cform[image_media][<?php echo $fieldcode; ?>]" value="" />
			</p>
			<div id="<?php echo $fieldId; ?>_media_preview"></div>
			<div class="clearfix"></div>
			<input type="hidden" id="<?php echo $fieldId; ?>_value" name="cform[image][<?php echo $fieldcode; ?>]" value="<?php echo htmlspecialchars($value); ?>" />
		</div>
	</div>
	<div class="clearfix"></div>
	<small>
		<?php echo JText::sprintf('COM_REDITEM_FIELD_IMAGE_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
		( <?php echo JText::sprintf('COM_REDITEM_FIELD_IMAGE_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?> )
	</small>
</div>