<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldCode            = $displayData['fieldcode'];
$index                = $displayData['index'];
$divId                = $displayData['divId'];
$imageData            = $displayData['imageData'];
$config               = $displayData['config'];
$defaultImage         = $displayData['defaultImage'];
$value                = $displayData['value'];
$basePath             = $displayData['basepath'];
$uploadMaxFilesize    = (int) $config->get('upload_max_filesize', 2);
$allowedFileExtension = $config->get('allowed_file_extension', 'jpg,jpeg,gif,png');
$isCroppingEnable     = $config->get('enable_cropping_image', '1');
$cropKeepRatio        = (boolean) $config->get('crop_keep_ratio', 0);
$previewWidth         = $config->get('preview_image_width', '300');
$previewHeight        = $config->get('preview_image_height', '300');
$jId                  = JFactory::getApplication()->input->getInt('id', 0);
$dropZoneFileExts     = explode(',', $allowedFileExtension);

for ($i = 0; $i < count($dropZoneFileExts); $i++)
{
	$dropZoneFileExts[$i] = 'image/' . $dropZoneFileExts[$i];
}

$dropZoneFileExts = implode(',', $dropZoneFileExts);

RHelperAsset::load('dropzone/dropzone.min.js', 'com_reditem');
RHelperAsset::load('dropzone/dropzone.min.css', 'com_reditem');
RHelperAsset::load('cropper/cropper.min.js', 'com_reditem');
RHelperAsset::load('cropper/cropper.min.css', 'com_reditem');
RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');

$document = JFactory::getDocument();
$script = "
jQuery(document).ready(function() {
	var previewNode_" . $fieldCode . " = jQuery(\"#template-" . $fieldCode . "\");
	previewNode_" . $fieldCode . ".attr(\"id\", \"\");
	var previewTemplate_" . $fieldCode . " = previewNode_" . $fieldCode . ".parent().html();
	previewNode_" . $fieldCode . ".remove();
	var order = 0;
	
	jQuery(\"#" . $fieldCode . "-dropzone\").dropzone(
		{
			url: \"index.php?option=com_reditem&task=field.ajaxUpload\",
			thumbnailWidth: " . $previewWidth . ",
			thumbnailHeight: " . $previewHeight . ",
			maxFilesize: " . $uploadMaxFilesize . ",
			acceptedFiles: \"" . $dropZoneFileExts . "\",
			parallelUploads: 20,
			previewTemplate: previewTemplate_" . $fieldCode . ",
			previewsContainer: \"#previews-" . $fieldCode . "\",
			paramName: \"dragFile\",
			params: {uploadType: \"gallery\", uploadTarget: \"" . $fieldCode . "\"},
			dictRemoveFileConfirmation: \"" . JText::_('COM_REDITEM_FIELD_GALLERY_REMOVE_IMAGE_CONFIRM') . "\",
			init: function() {
				var instance = this;
				instance.on(\"success\", function (file, response) {
					if (response.length > 0)
					{
						var template = jQuery(file.previewElement);
						var value    = jQuery(\"#jform_fields_gallery_" . $fieldCode . "\");

						function getValues()
						{
							var values = [];
							var tmp    = value.val();
	
							if (tmp.length > 0)
							{
								values = jQuery.parseJSON(tmp);
							}

							return values;
						}
						

						if (file.preload)
						{
							template.find(\".tempFile\").val(\"/media/com_reditem/images/customfield/\" + response);
							template.find(\".order\").val(order++);

							if (file.default)
							{
								template.find(\"button.default\").css(\"display\", \"none\");
								template.find(\".thumbnail\").css(\"border-color\", \"#006dcc\");
							}

							var iAlt  = template.find(\".alt-text\");
							var iName = template.find(\".name\");
							iAlt.val(file.name);

							iName.on(\"click\", function() {
								iName.hide();
								iAlt.show();
								iAlt.focus();
							});

							function updateName()
							{
								iName.html(iAlt.val());
								iAlt.hide();
								iName.show();
								var values = getValues();
								values.each(function(e, i) {
									if (e.path == response)
									{
										e.alt = iAlt.val();
									}
								});
								value.val(JSON.stringify(values));
							}

							iAlt.on(\"blur\", updateName);
							iAlt.on(\"keypress\", function(e) { if (e.which === 13 || e.keyCode === 13) { e.preventDefault(); updateName(); } });
							iName.tooltip({trigger : \"manual\", title: \"" . JText::_('COM_REDITEM_FIELD_GALLERY_ALT_CLICK_TO_EDIT') . "\"});
							iName.mouseover(function() {
								template.find(\"span.pencil\").show();
								iName.tooltip(\"toggle\");
							}).mouseout(function() {
								template.find(\"span.pencil\").hide();
								iName.tooltip(\"toggle\");
							});
						}
						else
						{
							template.find(\".dragable\").remove();
							template.find(\".tempFile\").val(\"/media/com_reditem/files/customfield/temporary/\" + response);
							jQuery(template).addClass(\"not-sortable\");
							
							var drags = jQuery(\"#jform_fields_drangdrop_" . $fieldCode . "\");
							var val   = drags.val();
	
							if (val.length > 0)
							{
								var values = val.split(\",\");
								values.push(response);
								drags.val(values.join());
							}
							else
							{
								drags.val(response);
							}

							template.find(\"button.default\").remove();
						}

						template.find(\".info\").tooltip({
							title : \"" . JText::_('COM_REDITEM_FIELD_GALLERY_ORIGINAL_FILE_NAME') . "\" + response.split(\"/\").pop()
						});

						template.find(\".progress\").remove();
						template.find(\"img\").attr(\"id\", response.replace(/\\W/g, ''));

						template.find(\".crop\").on(\"click\", function() {
							var tempFile = template.find(\".tempFile\").val();
							var fName    = tempFile.split(\"/\").pop();
							var uq       = new Date().getTime();
							jQuery(\"#modal-" . $fieldCode . "-preview\").attr(
								\"src\",
								tempFile + \"?t=\" + uq
							).cropper(\"destroy\").cropper({
								minContainerWidth: 810,
								minContainerHeight: 515,
								viewMode: 2
							});

							jQuery(\"#" . $fieldCode . "-modal\").modal('show').draggable({scroll: false, handle: \".modal-header\"});
							var mCrop = jQuery(\"#modal-" . $fieldCode . "-crop\");
							var src   = jQuery(\"#modal-" . $fieldCode . "-preview\").attr(\"src\");
	
							if (file.preload)
							{
								mCrop.unbind(\"click\").on(\"click\", function(){ reditemCropImage(fName, \"" . $fieldCode . "\", \"images/" . $basePath . "/" . $jId . "/\", src, \"#\" + response.replace(/\\W/g, '')); });
							}
							else
							{
								mCrop.unbind(\"click\").on(\"click\", function(){ reditemCropImage(fName, \"" . $fieldCode . "\", \"files/customfield/temporary/\", src, \"#\" + response.replace(/\\W/g, '')); });
							}
						});

						template.find(\"button.default\").on(\"click\", function() {
							var galleryImages = jQuery(\".thumbnails\");
							galleryImages.find(\".thumbnail\").removeAttr(\"style\");
							galleryImages.find(\"button.default\").removeAttr(\"style\");
							template.find(\".thumbnail\").css(\"border-color\", \"#006dcc\");
							template.find(\"button.default\").css(\"display\", \"none\");
							var values = getValues();

							values.each(function(ele, i) {
								if (ele.path.search(response) < 0)
								{
									ele.default = 0;
								}
								else
								{
									ele.default = 1;
								}
							});

							if (values.length > 0)
							{
								value.val(JSON.stringify(values));
							}
						});
					}
				});
				
				instance.on(\"sending\", function (file) {
					jQuery(file.previewElement).find(\".start\").attr(\"disabled\", \"disabled\");
				});

				instance.on(\"removedfile\", function (file) {
					var tempFile = jQuery(file.previewElement).find(\".tempFile\").val();
					var fName    = tempFile.split(\"/\").pop();
					var drags    = jQuery(\"#jform_fields_drangdrop_" . $fieldCode . "\");
					var values   = drags.val().split(\",\");
					var index    = jQuery.inArray(fName, values);
					var vals     = jQuery(\"#jform_fields_gallery_" . $fieldCode . "\");

					if (index != -1)
					{
						values.splice(index, 1);
						drags.val(values.join());
					}
					
					var tmp = vals.val();

					if (tmp.length > 0)
					{
						values        = jQuery.parseJSON(tmp);
						var newValues = [];
	
						values.each(function(ele, i) {
							if (ele.path.search(fName) < 0)
							{
								newValues.push(ele);
							}
						});
	
						vals.val(JSON.stringify(newValues));
					}

					jQuery.ajax({
						url  : \"index.php?option=com_reditem&task=item.ajaxRemove\",
						data : {
							file      : tempFile,
							fieldCode : \"" . $fieldCode . "\",
							updateVal : file.preload,
							basePath  : \"" . $basePath . "\",
							id        : " . $jId . ",
							value     : JSON.stringify(newValues)
						}
					});
				});

				" . $imageData . "
			}
		}
	);

	var gValues  = jQuery(\"#jform_fields_gallery_" . $fieldCode . "\");
	var tmp      = gValues.val();
	var orgOrder = [];

	if (tmp.length > 0)
	{
		orgOrder = jQuery.parseJSON(tmp);
	}

	jQuery(\"#" . $divId . "\").find(\".thumbnails\").sortable({
		items    : \"li:not(.not-sortable)\",
		handle   : \".dragable\",
		cursorAt : { left: 0, top: 0 },
		update   : function(e, ui) {
			newOrder = [];
			jQuery(\".order\").each(function (i, e) {
				var index = parseInt(jQuery(e).val());
				newOrder.push(orgOrder[index]);
			});

			gValues.val(JSON.stringify(newOrder));
		}
	});
});";

$document->addScriptDeclaration($script);
?>
<div class="reditem_customfield_gallery" id="<?php echo $divId;?>">
	<div class="container-fluid">
		<div id="<?php echo $fieldCode; ?>-add-files">
			<div class="row-fluid">
				<div class="gallery-dragndrop" id="<?php echo $fieldCode?>-dropzone">
					<?php echo JText::_('COM_REDITEM_FIELD_GALLERY_DROP_OR_CLICK_HERE');?>
				</div>
			</div>
			<div class="row-fluid">
				<ul class="thumbnails" id="previews-<?php echo $fieldCode; ?>">
					<li id="template-<?php echo $fieldCode; ?>" class="span3" style="min-height: 420px">
						<div class="thumbnail" style="padding: 10px">
							<div class="pull-right dragable">
								<span style="cursor: move;"><i class="icon icon-move"></i></span>
							</div>
							<div class="preview" style="text-align: center;">
								<img data-dz-thumbnail width="<?php echo $previewWidth;?>px" height="<?php echo $previewHeight; ?>px" />
							</div>
							<div class="center">
								<div style="margin: 10px">
									<strong class="name" data-dz-name></strong>
									<span style="margin:3px; display: none" class="pencil"><i class="icon icon-pencil"></i></span>
									<input type="text" style="display: none;" class="input-medium alt-text" />
									<div>
										<span class="dz-size" data-dz-size></span>
										<span class="label label-info info" style="cursor: help">
											<i class="icon icon-info" style="padding: 2px; margin: 0px;"></i>
										</span>
									</div>
								</div>
								<div>
									<strong class="error text-danger" data-dz-errormessage></strong>
								</div>
								<div class="progress progress-striped active" >
									<div class="bar bar-success" style="width:0%;" data-dz-uploadprogress></div>
								</div>
								<div>
									<button class="btn btn-primary default" type="button">
										<i class="icon icon-check"></i>
										<span><?php echo JText::_('COM_REDITEM_CUSTOMFIELD_GALLERY_SET_DEFAULT'); ?></span>
									</button>
									<?php if ($isCroppingEnable) : ?>
									<button class="btn btn-warning crop" type="button">
										<i class="icon icon-crop"></i>
										<span><?php echo JText::_('COM_REDITEM_FEATURE_CROP_BTN_LBL');?></span>
									</button>
									<?php endif; ?>
									<button data-dz-remove class="btn btn-danger delete" type="button">
										<i class="icon icon-trash"></i>
										<span><?php echo JText::_('COM_REDITEM_CUSTOMFIELD_IMAGE_REMOVE');?></span>
									</button>
								</div>
							</div>
							<input type="hidden" class="tempFile" />
							<input type="hidden" class="order" />
						</div>
					</li>
				</ul>
			</div>
			<div class="row-fluid">
				<div>
					<small><?php echo JText::_('COM_REDITEM_FIELD_GALLERY_SAVE_FIRST');?></small>
				</div>
				<div>
					<small>
					<?php echo JText::sprintf('COM_REDITEM_FIELD_GALLERY_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
					(<?php echo JText::sprintf('COM_REDITEM_FIELD_GALLERY_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?>)
					</small>
				</div>
			</div>
		</div>
	</div>
	<?php if ($isCroppingEnable) : ?>
	<div class="modal hide fade reditem-cropper-modal" id="<?php echo $fieldCode; ?>-modal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="modal-<?php echo $fieldCode; ?>-title">Image crop</h3>
		</div>
		<div class="modal-body">
			<img src="" id="modal-<?php echo $fieldCode?>-preview" width="100%" height="100%" />
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('COM_REDITEM_CLOSE');?></button>
			<button type="button" id="modal-<?php echo $fieldCode?>-crop" class="btn btn-primary"><?php echo JText::_('COM_REDITEM_FEATURE_CROP_BTN_LBL');?></button>
		</div>
	</div>
	<?php endif; ?>
	<input type="hidden" id="jform_fields_drangdrop_<?php echo $fieldCode; ?>" name="jform[fields][dragndrop][<?php echo $fieldCode; ?>]" value="" />
	<input type="hidden" name="jform[fields][gallery][<?php echo $fieldCode; ?>]" id="jform_fields_gallery_<?php echo $fieldCode; ?>" value="<?php echo htmlspecialchars($value); ?>" />
</div>
