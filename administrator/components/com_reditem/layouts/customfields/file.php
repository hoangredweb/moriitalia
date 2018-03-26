<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode		= $displayData['fieldcode'];
$id				= $displayData['id'];
$value			= $displayData['value'];
$basePath		= $displayData['basepath'];
$attributes		= $displayData['attributes'];
$filePreview	= $displayData['filepreview'];
$config			= $displayData['config'];

$uploadMaxFilesize			= (int) $config->get('upload_max_filesize', 2);
$uploadMaxFilesizeInByte	= $uploadMaxFilesize * 1024 * 1024;
$allowedFileExtension		= $config->get('allowed_file_extension', 'zip,doc,xls,pdf');
$allowedMime				= $config->get('allowed_file_mimetype', 'application/zip,application/doc,application/xls,application/pdf');

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

?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#dragndrop_filefield_<?php echo $fieldcode; ?>_<?php echo $id; ?>').dragndrop({
			url: "index.php?option=com_reditem&task=item.ajaxUpload",
			text: "<?php echo JText::_('COM_REDITEM_ITEM_DRAG_A_FILE') ?>",
			config: {
				size: "<?php echo $uploadMaxFilesizeInByte ?>",
				ext: "<?php echo $allowedFileExtension ?>",
				mime: "<?php echo $allowedMime ?>"
			}
		});
	});
</script>

<div class="reditem_customfield_file dragndrop">
	<?php if (!empty($filePreview)) : ?>
	<div class="reditem_customfield_file_preview">
		<h3 class="badge badge-info">
			<?php echo strtoupper(JFile::getExt($filePreview['filePath'])); ?>
		</h3>
		<span class="badge badge-success">
			<?php if (!empty($filePreview['fileName'])) : ?>
				<?php echo $filePreview['fileName']; ?>
			<?php else : ?>
				<?php echo JFile::getName($filePreview['filePath']); ?>
			<?php endif; ?>
		</span>
		<a href="<?php echo $filePreview['filePath']; ?>" target="_blank">
			<i class="icon-download"></i>
		</a>
	</div>
	<?php endif; ?>
	<p>
		<input type="file" class="input-large" name="cform[file][<?php echo $fieldcode; ?>_file]" id="cform_<?php echo $fieldcode; ?>" <?php echo $attributes; ?> />
		<input class="input-large" style="margin-left: 10px;" type="text" name="jform[cform][file][<?php echo $fieldcode; ?>]" value=""
			placeholder="<?php echo JText::_('COM_REDITEM_FIELD_FILE_FILENAME'); ?>" />
		<input type="hidden" name="cform[file][<?php echo $fieldcode; ?>]" id="cform_<?php echo $fieldcode; ?>_value" value="<?php echo htmlentities($value); ?>" />
		<span class="clearfix"></span>
	</p>
	<div id="dragndrop_filefield_<?php echo $fieldcode; ?>_<?php echo $id; ?>" upload-type="file" target="<?php echo $fieldcode; ?>" input-target="cform_<?php echo $fieldcode; ?>"></div>
	<small>
		<?php echo JText::sprintf('COM_REDITEM_FIELD_FILE_NOTICE_UPLOAD_FILESIZE', $config->get('upload_max_filesize', 2)); ?>
		( <?php echo JText::sprintf('COM_REDITEM_FIELD_FILE_NOTICE_ALLOWED_FILE_EXTENSION', $allowedFileExtension); ?> )
	</small>
</div>
