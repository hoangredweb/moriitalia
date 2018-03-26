<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$category           = $displayData['category'];
$subCategories      = $displayData['subCategories'];
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

/** Filter stuff - DO NOT CHANGE THIS **/
$inputId   = 'reditem_filter_category_' . $category->id;
$inputName = 'filter_category[' . $category->id . ']';
/** Filter stuff - END **/
?>

<script type="text/javascript">
	if (typeof reditemFilterCategoryListChange == "undefined")
	{
		function reditemFilterCategoryListChange(element, id, value)
		{
			var objlist = jQuery("#reditem_filter_category_list_" + id);
			var hiddenobj = jQuery("#reditem_filter_category_" + id);

			objlist.find("li").each(function (index)
			{
				jQuery(this).removeClass("active");
			});

			element = jQuery(element);
			element.parent().addClass("active");

			hiddenobj.val(value);

			<?php echo $javascriptCallback; ?>();
		}
	}
</script>

<ul class="reditem_filter_category_list" id="reditem_filter_category_list_<?php echo $category->id; ?>">
	<li class="list-item active">
		<a href="javascript:void(0);" onClick="reditemFilterCategoryListChange(this, '<?php echo $category->id; ?>', '');">
			<?php echo JText::_('JALL'); ?>
		</a>
	</li>
	<?php if (!empty($subCategories)) : ?>
		<?php foreach ($subCategories as $subCategory) : ?>
			<?php if ($subCategory->id != $category->id) : ?>
				<?php $imageSmall = ReditemHelperImage::getImageLink($subCategory, 'category', $subCategory->category_image, 'small'); ?>
				<li class="list-item">
					<a href="javascript:void(0);" onClick="reditemFilterCategoryListChange(this, '<?php echo $category->id; ?>', '<?php echo $subCategory->id; ?>');">
						<?php echo $imageSmall; ?>
						<span><?php echo $subCategory->title; ?></span>
					</a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
<input type="hidden" name="<?php echo $inputName; ?>" id="<?php echo $inputId; ?>" value="<?php echo $filterValue; ?>" />
