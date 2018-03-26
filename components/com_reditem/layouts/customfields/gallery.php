<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldCode         = $displayData['fieldcode'];
$index             = $displayData['index'];
$divId             = $displayData['divId'];
$imageData         = $displayData['imageData'];
$config            = $displayData['config'];
$defaultImageIndex = $displayData['defaultImage'];

$uploadMaxFilesize       = (int) $config->get('upload_max_filesize', 2);
$uploadMaxFilesizeInByte = $uploadMaxFilesize * 1024 * 1024;
$allowedFileExtension    = $config->get('allowed_file_extension', 'jpg,jpeg,gif,png');
$allowedMime             = $config->get('allowed_file_mimetype', 'image/jpg,image/jpeg,image/gif,image/png');

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
JText::script('COM_REDITEM_FEATURE_CROPIMAGE_FAIL');
JText::script('COM_REDITEM_UPLOAD_DELETE_FILE');
JText::script('COM_REDITEM_CUSTOMFIELD_GALLERY_SET_DEFAULT');
JText::script('COM_REDITEM_FEATURE_CROP_BTN_LBL');

// Load dragndrop scripts
RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
RHelperAsset::load('jquery/jquery.ajaxfileupload.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.css', 'com_reditem');
RHelperAsset::load('reditem.cropimage.min.js', 'com_reditem');

?>

<script type="text/javascript">
	var index_<?php echo $fieldCode; ?> = "<?php echo $index; ?>";
	var dragndropGallery<?php echo $fieldCode; ?>;

	(function($){
		$(document).ready(function(){
			dragndropGallery<?php echo $fieldCode; ?> = $('#reditem_customfield_gallery_<?php echo $fieldCode ?>').dragndrop({
				url: "index.php?option=com_reditem&task=item.ajaxUpload",
				text: "<?php echo JText::_('COM_REDITEM_ITEM_DRAG_A_FILE') ?>",
				img_preview: "<?php echo $divId; ?>",
				img_preview_path: "<?php echo JURI::root() . 'media/com_reditem/files/customfield/temporary/' ?>",
				includeBrowse: 1,
				config: {
					size: "<?php echo $uploadMaxFilesizeInByte ?>",
					ext: "<?php echo $allowedFileExtension ?>",
					mime: "<?php echo $allowedMime ?>"
				},
				cropConfig:{
					isEnable: "<?php echo $isCroppingEnable ?>",
					previewWidth: "<?php echo $previewWidth ?>",
					previewHeight: "<?php echo $previewHeight ?>",
					<?php if ($cropKeepRatio) : ?>
					keepRatio: true
					<?php else : ?>
					cropWidth: "<?php echo $cropWidth ?>",
					cropHeight: "<?php echo $cropHeight ?>"
					<?php endif; ?>
				}
			});

			// Default image select
			$('#<?php echo $divId ?> #default_cform_<?php echo $fieldCode . $defaultImageIndex ?>').prop('checked', true);
		});
	})(jQuery);

	function ri_<?php echo $fieldCode; ?>_add()
	{
		var id = "cform_<?php echo $fieldCode; ?>" + index_<?php echo $fieldCode; ?>;

		var str = "<div class='media' id='div_" + id + "'>";
		str += "<div class='pull-left'>";
		str += "<p>";
		str += "<input type='file' name='cform[gallery][<?php echo $fieldCode; ?>_file][" + index_<?php echo $fieldCode; ?> + "]' id='" + id + "' />";
		str += "<span class='clearfix'></span>";
		str += "</p>";
		str += "<div>";
		str += "<a class='modal-thumb btn' href='index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=" + id + "&amp;redcore=true'";
		str += "rel='{handler: \"iframe\", size: {x: 1050, y: 450}}' title=''><?php echo JText::_('COM_REDITEM_ITEM_IMAGE_MEDIA'); ?></a>";
		str += "<input type='hidden' id='" + id + "_value_media' name='cform[gallery_media][<?php echo $fieldCode; ?>_file][" + index_<?php echo $fieldCode; ?> + "]' value='' />";
		str += "</div>";
		str += "<div id='" + id + "_media_preview'></div>";
		str += "</div>";
		str += "<div class='media-body'>";
		str += "<a class='btn btn-danger' href='javascript:void(0);' onClick='javascript:ri_<?php echo $fieldCode; ?>_remove(\"" + id + "\");'>";
		str += "<i class='icon-remove'></i> <?php echo JText::_('COM_REDITEM_CUSTOMFIELD_IMAGE_REMOVE'); ?>";
		str += "</a>";
		str += "</div>";
		str += "</div>";

		jQuery("#<?php echo $divId; ?>").prepend(str);
		jQuery("#cform_<?php echo $fieldCode; ?>").val("");

		// Add input file handle for dragndrop
		dragndropGallery<?php echo $fieldCode; ?>.addInputTarget(id);

		index_<?php echo $fieldCode; ?>++;
		// Re-init modal popup
		SqueezeBox.initialize({});
		SqueezeBox.assign($$('a.modal-thumb'), {
			parse: 'rel'
		});
	}

	function ri_<?php echo $fieldCode; ?>_remove(id)
	{
		var obj = document.getElementById('div_' + id);
		jQuery(obj).remove();
		jQuery("#cform_<?php echo $fieldCode; ?>").val("");
	}

	function jInsertFieldValue(value, id) {
		(function($){
			$("#" + id + "_value_media").val(value);
			var imgPreview = "<img src='<?php echo JUri::root() ?>" + value + "' style='max-width: 100px; max-height: 100px;' />";
			$("#" + id + "_media_preview").html(imgPreview);
		})(jQuery);
	}
</script>

<div class="reditem_customfield_gallery dragndrop">
	<p>
		<a class="btn btn-primary" href="javascript:void(0);" onClick="javascript:ri_<?php echo $fieldCode; ?>_add()">
			<i class="icon-plus"></i>
			<?php echo JText::_('COM_REDITEM_CUSTOMFIELD_IMAGE_ADD'); ?>
		</a>
	</p>
	<div id="reditem_customfield_gallery_<?php echo $fieldCode ?>" upload-type="gallery" target="<?php echo $fieldCode ?>" ></div>
	<div id="<?php echo $divId; ?>">
		<?php if (is_array($imageData) && !empty($imageData)) : ?>
			<?php foreach ($imageData as $imageDiv) : ?>
				<?php echo $imageDiv; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<small>
		<?php echo JText::sprintf('COM_REDITEM_FIELD_GALLERY_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
		( <?php echo JText::sprintf('COM_REDITEM_FIELD_GALLERY_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?> )
	</small>
	<input type="hidden" name="cform[gallery][<?php echo $fieldCode; ?>][]" value="" />
</div>
