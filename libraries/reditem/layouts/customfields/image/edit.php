<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$id				= $displayData['id'];
$fieldcode		= $displayData['fieldcode'];
$value			= $displayData['value'];
$attributes		= $displayData['attributes'];
$basePath		= $displayData['basepath'];
$imagePreview	= $displayData['imagePreview'];
$config			= $displayData['config'];
$alt            = $displayData['alt'];
$default        = $displayData['default'];
$attrString     = '';
$jId            = JFactory::getApplication()->input->getInt('id', 0);
$isNew          = $jId == 0;
$imgRealName    = !empty($imagePreview) ? JFile::getName($imagePreview) : '';

if ($basePath == 'customfield')
{
	$fieldType = 'item';
}
else
{
	$fieldType = 'category';
}

if ($isNew && !empty($default))
{
	$value = $default;
}

foreach ($attributes as $key => $val)
{
	$attrString .= $key . '="' . $val . '" ';
}

$uploadMaxFilesize       = (int) $config->get('upload_max_filesize', 2);
$uploadMaxFilesizeInByte = $uploadMaxFilesize * 1024 * 1024;
$allowedFileExtension    = $config->get('allowed_file_extension', 'jpg,jpeg,gif,png');
$fieldName               = 'jform[fields][images][' . $fieldcode . ']';
$fieldId                 = 'jform_fields_images_' . $fieldcode;
$isCroppingEnable        = $config->get('enable_cropping_image', '1');

// Load string for javascripts
JText::script('COM_REDITEM_UPLOAD_1_FILE_ONLY');
JText::script('COM_REDITEM_ITEM_DRAG_AN_IMAGE');
JText::script('COM_REDITEM_ITEM_DRAG_IMAGES');
JText::script('COM_REDITEM_ITEM_DRAG_FEATURE_NOT_SUPPORT');
JText::script('COM_REDITEM_UPLOAD_FILE_INVALID');
JText::script('COM_REDITEM_UPLOAD_FILE_TOO_BIG');
JText::script('COM_REDITEM_UPLOAD_ABORT');
JText::script('COM_REDITEM_UPLOAD_DELETE_FILE');
JText::script('COM_REDITEM_FEATURE_CROP_BTN_LBL');
JText::script('COM_REDITEM_FEATURE_CROP_CONFIRM');

// Load dragndrop scripts
RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
RHelperAsset::load('jquery/jquery.ajaxfileupload.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.css', 'com_reditem');
RHelperAsset::load('cropper/cropper.min.js', 'com_reditem');
RHelperAsset::load('cropper/cropper.min.css', 'com_reditem');
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#imgfield_<?php echo $fieldcode; ?>_<?php echo $id ?>').dragndrop({
			url: "index.php?option=com_reditem&task=field.ajaxUpload",
			text: "<?php echo JText::_('COM_REDITEM_ITEM_DRAG_A_FILE') ?>",
			img_preview: "div_<?php echo $fieldId; ?>",
			img_preview_path: "<?php echo JURI::root() . 'media/com_reditem/files/customfield/temporary/' ?>",
			config: {
				size: "<?php echo $uploadMaxFilesizeInByte ?>",
				ext: "<?php echo $allowedFileExtension?>"
			},
			fieldType: "<?php echo $fieldType; ?>"
		});
	});

	function jInsertFieldValue(value, id) {
		(function($){
			$("#jform_fields_media_" + id).val(value);
			var name = value.split('/').pop();
			$('#jform_fields_file_names_' + id).val(name);
			var img = $('#img_preview_' + id);
			$('#img-crop-' + id).remove();

			if (img.length > 0)
			{
				img.attr('src', '<?php echo JUri::root() ?>' + value);
			}
			else
			{
				var imgPreview = "<img src='<?php echo JUri::root() ?>" + value + "' style='max-width: 100px; max-height: 100px;' />";
				$("#" + id + "_media_preview").html(imgPreview);
			}
		})(jQuery);
	}

	function clear_<?php echo $fieldcode; ?>()
	{
		jQuery('#<?php echo $fieldId; ?>').val('');
		jQuery('#jform_fields_file_names_<?php echo $fieldcode; ?>').val('');
		jQuery('#jform_fields_images_alt_<?php echo $fieldcode; ?>').val('');
		jQuery('#img-preview-container-<?php echo $fieldcode?>').remove();
		jQuery('<?php echo $fieldId; ?>_media_preview').html('');
		jQuery('#<?php echo $fieldId; ?>_rm').val('-1');
		jQuery('#img-crop-<?php echo $fieldcode; ?>').remove();
	}

	<?php if (!empty($imagePreview) && $isCroppingEnable) : ?>
	function cropResultUpdate()
	{
		var image      = jQuery('#modal-<?php echo $fieldcode; ?>-preview');
		var crop       = image.cropper('getCropBoxData');
		var canvas     = image.cropper('getCanvasData');
		var rate       = canvas.naturalWidth / canvas.width;
		var width      = crop.width * rate;
		var height     = crop.height * rate;
		var html       = '<?php echo JText::_('COM_REDITEM_FEATURE_CROP_RESULT_SIZE'); ?>'
			+ parseFloat(width).toFixed(2)
			+ 'px <small>&#10005;</small> '
			+ parseFloat(height).toFixed(2)
			+ 'px <?php echo JText::_('COM_REDITEM_FEATURE_CROP_WIDTH_X_HEIGHT'); ?>';
		jQuery('#<?php echo $fieldcode; ?>-cropper-result').html(html);
	}

	function setCropperSize(fieldcode)
	{
		var image      = jQuery('#modal-' + fieldcode + '-preview');
		var crop       = image.cropper('getCropBoxData');
		var canvas     = image.cropper('getCanvasData');
		var rate       = canvas.naturalWidth / canvas.width;
		var width      = parseFloat(jQuery('#' + fieldcode + '-cropper-width').val());
		var height     = parseFloat(jQuery('#' + fieldcode + '-cropper-height').val());
		image.cropper('setCropBoxData', {
			left   : crop.left,
			top    : crop.top,
			width  : width / rate,
			height : height / rate
		});
		cropResultUpdate();
	}

	function show_modal_<?php echo $fieldcode; ?>()
	{
		jQuery('#modal-<?php echo $fieldcode?>-preview').cropper({
			minContainerWidth: 810,
			minContainerHeight: 515,
			viewMode: 2
		}).on('built.cropper', cropResultUpdate).on('cropend.cropper', cropResultUpdate);
		jQuery('#<?php echo $fieldcode?>-modal').modal('show').draggable({scroll: false, handle: ".modal-header"});
	}

	function crop_<?php echo $fieldcode; ?>()
	{
		reditemCropImage(
			'<?php echo implode(
				'\',\'',
				array (
					$imgRealName,
					$fieldcode,
					'images/' . $basePath . '/' . $jId, $imagePreview,
					'#img_preview_' . $fieldcode,
					'#modal-' . $fieldcode . '-preview'
				)
			); ?>'
		);
		jQuery('#modal-<?php echo $fieldcode?>-preview').cropper('destroy');
	}
	<?php endif; ?>
</script>

<div>
	<div class="media" id="div_<?php echo $fieldId; ?>">
		<?php if (!empty($imagePreview)) : ?>
		<div class="pull-left">
			<div class="img-preview-container" id="img-preview-container-<?php echo $fieldcode?>" style="position:relative;">
				<img id="img_preview_<?php echo $fieldcode?>" src="<?php echo $imagePreview; ?>" class="img-polaroid" style="max-width: 300px; max-height: 300px; margin-right: 20px;" />
			</div>
		</div>
		<?php endif; ?>
		<div class="media-body">
			<div class="input-prepend input-append">
				<?php if (!empty($imagePreview)):?>
				<span class="label label-info add-on" id="<?php echo $fieldcode; ?>_name_desc" style="background-color: #3a87ad">
					<i class="icon-picture"></i>
					<?php echo strtoupper(JFile::getExt($imagePreview)); ?>
				</span>
				<?php endif; ?>
				<?php
				$imgRealNameCount = strlen($imgRealName);

				if ($imgRealNameCount > 32)
				{
					$class = 'input-xxlarge';
				}
				elseif ($imgRealNameCount > 25)
				{
					$class = 'input-xlarge';
				}
				else
				{
					$class = '';
				}
				?>
				<input
					type="text" class="input <?php echo $class; ?>" disabled
					value="<?php if (!empty($imagePreview)) : echo $imgRealName; endif; ?>"
					placeholder="Use upload button to add a file."
					aria-describedby="<?php echo $fieldcode; ?>_name_desc"
					id="jform_fields_file_names_<?php echo $fieldcode; ?>"
				/>
				<input type="hidden" id="<?php echo $fieldId; ?>_rm" name="jform[fields][image][<?php echo $fieldcode; ?>]" value="<?php echo htmlspecialchars($value); ?>" />
				<input type="hidden" id="jform_fields_media_<?php echo $fieldcode; ?>" name="jform[fields][media][<?php echo $fieldcode; ?>]" />
				<button class="btn btn-info" type="button" onclick="jQuery('#<?php echo $fieldId; ?>').click();">
					<i class="icon-upload"></i>
				</button>
				<a class="btn btn-success modal-thumb" rel="{handler: 'iframe', size: {x: 1050, y: 450}}" title=""
				   href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=<?php echo $fieldcode; ?>">
					<i class="icon-upload-alt"></i>
					<?php echo JText::_('COM_REDITEM_ITEM_IMAGE_MEDIA');?>
				</a>
				<?php if (!empty($imagePreview)) : ?>
					<?php if ($isCroppingEnable):?>
				<button
					class="btn btn-warning" type="button"
					onclick="show_modal_<?php echo $fieldcode; ?>()"
					id="img-crop-<?php echo $fieldcode; ?>"
				>
					<i class="icon-crop"></i>
				</button>
					<?php endif; ?>
				<button
					class="btn btn-danger" type="button"
					onclick="clear_<?php echo $fieldcode; ?>()"
				>
					<i class="icon-remove"></i>
				</button>
				<?php endif; ?>
				<input type="file" name="<?php echo $fieldName; ?>" id="<?php echo $fieldId; ?>" <?php echo $attrString; ?> style="display: none" />
			</div>
			<?php if (!empty($imagePreview)) : ?>
			<div id="alt-<?php echo $fieldcode; ?>" style="padding: 5px 0px;">
				<label for="jform_fields_images_alt_<?php echo $fieldcode; ?>"><?php echo JText::_('COM_REDITEM_ITEM_IMAGE_ALT'); ?></label>
				<input
					type="text" class="input"
					value="<?php echo $alt; ?>"
					aria-describedby="<?php echo $fieldcode; ?>_name_desc"
					id="jform_fields_images_alt_<?php echo $fieldcode; ?>"
					name="jform[fields][images_alt][<?php echo $fieldcode; ?>]"
				/>
			</div>
			<?php endif; ?>
			<div id="imgfield_<?php echo $fieldcode; ?>_<?php echo $id ?>" class="dragndrop" upload-type="image"
				input-target="<?php echo $fieldId; ?>" target="<?php echo $fieldcode ?>"></div>
			<div id="<?php echo $fieldcode; ?>_media_preview"></div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php if (!empty($imagePreview) && $isCroppingEnable) : ?>
	<div class="modal hide fade reditem-cropper-modal" id="<?php echo $fieldcode; ?>-modal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="modal-<?php echo $fieldcode; ?>-title">Image crop</h3>
		</div>
		<div class="modal-body">
			<img src="<?php echo $imagePreview; ?>" id="modal-<?php echo $fieldcode?>-preview" width="100%" height="100%" />
		</div>
		<div class="modal-footer">
			<div>
				<p style="text-align: left">
					<span><?php echo JText::_('COM_REDITEM_FEATURE_CROP_SET_CROPPER_SIZE');?></span>
					<input type="text" class="input-small" id="<?php echo $fieldcode; ?>-cropper-width" />
					<span><?php echo JText::_('COM_REDITEM_FEATURE_CROP_WIDTH_IN_PIXELS');?></span>
					<input type="text" class="input-small" id="<?php echo $fieldcode; ?>-cropper-height" />
					<span><?php echo JText::_('COM_REDITEM_FEATURE_CROP_HEIGHT_IN_PIXELS');?></span>
					<button class="btn" type="button" onclick="setCropperSize('<?php echo $fieldcode; ?>')">
						<?php echo JText::_('COM_REDITEM_SET');?>
					</button>
				</p>
			</div>
			<span style="float:left" id="<?php echo $fieldcode; ?>-cropper-result"></span>
			<button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('COM_REDITEM_CLOSE');?></button>
			<button type="button" onclick="crop_<?php echo $fieldcode; ?>()" class="btn btn-primary"><?php echo JText::_('COM_REDITEM_FEATURE_CROP_BTN_LBL');?></button>
		</div>
	</div>
	<?php endif; ?>
	<small>
		<?php echo JText::sprintf('COM_REDITEM_FIELD_IMAGE_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
		( <?php echo JText::sprintf('COM_REDITEM_FIELD_IMAGE_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?> )
	</small>
</div>
