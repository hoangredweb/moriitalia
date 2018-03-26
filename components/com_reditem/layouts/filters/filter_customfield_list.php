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
$field              = $displayData['field'];
$options            = $displayData['options'];
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

if (empty($category->id))
{
	$category->id = 0;
}

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName = 'filter_customfield[' . $field->id . ']';
/** Filter stuff - END **/
?>

<script type="text/javascript">
	if (typeof reditemFilterCustomFieldListChange != "function")
	{
		function reditemFilterCustomFieldListChange(element, id, value)
		{
			var objlist = jQuery("#reditem_filter_customfield_list_" + id);
			var hiddenobj = jQuery("#reditem_filter_customfield_" + id);

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

<ul class="reditem_filter_customfield_list" id="reditem_filter_customfield_list_<?php echo $field->id; ?>">
	<!-- Add "All" option -->
	<li class="list-item active">
		<a href="javascript:void(0);" onClick="reditemFilterCustomFieldListChange(this, '<?php echo $field->id; ?>', '');">
			<?php echo JText::_('JALL'); ?>
		</a>
	</li>
	<!-- Add "options" -->
	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $option) : ?>
		<li class="list-item">
			<a href="javascript:void(0);"
				onClick="reditemFilterCustomFieldListChange(this, '<?php echo $field->id; ?>', '<?php echo $option->value; ?>');">
				<span><?php echo $option->text; ?></span>
			</a>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
<input type="hidden" name="<?php echo $filterName; ?>" id="reditem_filter_customfield_<?php echo $field->id; ?>" value="<?php echo $filterValue; ?>" />
