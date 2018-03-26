<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode               = $displayData['fieldcode'];
$id                      = $displayData['id'];
$value                   = $displayData['value'];
$basePath                = $displayData['basepath'];
$attributes              = $displayData['attributes'];
$filePreview             = $displayData['filepreview'];
$config                  = $displayData['config'];
$default                 = $displayData['default'];
$isNew                   = JFactory::getApplication()->input->getInt('id', 0) == 0;
$uploadMaxFilesize       = (int) $config->get('upload_max_filesize', 2);
$uploadMaxFilesizeInByte = $uploadMaxFilesize * 1024 * 1024;
$allowedFileExtension    = $config->get('allowed_file_extension', 'zip,doc,xls,pdf');

// Load string for javascripts
JText::script('COM_REDITEM_UPLOAD_1_FILE_ONLY');
JText::script('COM_REDITEM_ITEM_DRAG_AN_IMAGE');
JText::script('COM_REDITEM_ITEM_DRAG_IMAGES');
JText::script('COM_REDITEM_ITEM_DRAG_FEATURE_NOT_SUPPORT');
JText::script('COM_REDITEM_UPLOAD_FILE_INVALID');
JText::script('COM_REDITEM_UPLOAD_FILE_TOO_BIG');
JText::script('COM_REDITEM_UPLOAD_ABORT');

// Load dragndrop scripts
RHelperAsset::load('jquery/jquery.ajaxfileupload.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.js', 'com_reditem');
RHelperAsset::load('dragndrop.min.css', 'com_reditem');

if ($isNew && !empty($default))
{
	$value = $default;
}
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#dragndrop_filefield_<?php echo $fieldcode; ?>_<?php echo $id; ?>').dragndrop({
			url: "index.php?option=com_reditem&task=field.ajaxUpload",
			text: "<?php echo JText::_('COM_REDITEM_ITEM_DRAG_A_FILE') ?>",
			config: {
				size: "<?php echo $uploadMaxFilesizeInByte ?>",
				ext: "<?php echo $allowedFileExtension ?>"
			}
		});

		$('#jform_fields_<?php echo $fieldcode; ?>').change(function() {
			var file = $(this).val().replace(/^.*[\\\/]/, '');
			$('#jform_fields_file_<?php echo $fieldcode; ?>').val(file);
		});
	});

	function clear_<?php echo $fieldcode; ?>()
	{
		jQuery('#jform_fields_<?php echo $fieldcode; ?>').val('');
		jQuery('#jform_fields_file_names_<?php echo $fieldcode; ?>').val('');
		jQuery('#<?php echo $fieldcode; ?>_rm').val('-1');
		jQuery('.reditemDragnDrop-single-element').remove();
		jQuery('input[name="jform[fields][dragndrop][file_upload][]"]');
		jQuery('#<?php echo $fieldcode;?>_current').remove();
	}
</script>
<div class="row-fluid">
	<div class="reditem_customfield_file">
		<div class="input-prepend input-append">
			<?php if (!empty($filePreview['filePath'])):?>
			<span class="label label-info add-on" id="<?php echo $fieldcode; ?>_name_desc" style="background-color: #3a87ad">
				<i class="icon-file-text-alt"></i>
				<?php echo strtoupper(JFile::getExt($filePreview['filePath'])); ?>
			</span>
			<?php endif; ?>
			<input
				type="text" class="input"
				value="<?php if (!empty($filePreview['fileName'])) : ?><?php echo $filePreview['fileName']; ?><?php elseif (!empty($filePreview['filePath'])) : ?><?php echo JFile::getName($filePreview['filePath']); ?><?php endif; ?>"
				placeholder="Use upload button to add a file."
				aria-describedby="<?php echo $fieldcode; ?>_name_desc"
				id="jform_fields_file_names_<?php echo $fieldcode; ?>"
			    name="jform[fields][file_names][<?php echo $fieldcode; ?>]"
			/>
			<input type="hidden" id="<?php echo $fieldcode; ?>_rm" name="jform[fields][file][<?php echo $fieldcode; ?>]" value="<?php echo htmlspecialchars($value); ?>" />
			<?php if (!empty($filePreview['filePath'])):?>
			<a class="btn btn-success" href="<?php echo $filePreview['filePath']; ?>" target="_blank">
				<i class="icon-download"></i>
			</a>
			<?php endif; ?>
			<button class="btn btn-info" type="button" onclick="jQuery('#jform_fields_<?php echo $fieldcode; ?>').click();">
				<i class="icon-upload"></i>
			</button>
			<?php if (!empty($filePreview['filePath'])):?>
			<button
				class="btn btn-danger" type="button"
				onclick="clear_<?php echo $fieldcode; ?>()"
			>
				<i class="icon-remove"></i>
			</button>
			<?php endif; ?>
		</div>
		<input type="file" style="display: none" name="jform[fields][files][<?php echo $fieldcode; ?>]" id="jform_fields_<?php echo $fieldcode; ?>" <?php echo $attributes; ?> />
		<div>
			<div id="dragndrop_filefield_<?php echo $fieldcode; ?>_<?php echo $id; ?>" upload-type="file" target="<?php echo $fieldcode; ?>" input-target="jform_fields_<?php echo $fieldcode; ?>"></div>
			<p class="help-block">
				<?php echo JText::sprintf('COM_REDITEM_FIELD_FILE_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
				( <?php echo JText::sprintf('COM_REDITEM_FIELD_FILE_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?> )
			</p>
			<?php if (!empty($filePreview['filePath'])) : ?>
			<p class="help-block" id="<?php echo $fieldcode;?>_current">
				<?php echo JText::_('COM_REDITEM_FIELD_FILE_FILENAME');?>: <?php echo JFile::getName($filePreview['filePath']); ?>
			</p>
			<?php endif; ?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>
