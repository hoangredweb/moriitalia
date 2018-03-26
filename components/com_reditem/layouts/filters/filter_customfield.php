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
$filterValue        = $displayData['value'];
$javascriptCallback = $displayData['jsCallback'];

if (empty($category->id))
{
	$category->id = 0;
}

RHelperAsset::load('jquery/jquery.base64.min.js', 'com_reditem');

/** Filter stuff - DO NOT CHANGE THIS **/
$filterName = 'filter_customfield[' . $field->id . ']';
$filterId = 'filterCustomfieldText_' . $category->id . '_' . $field->id;
/** Filter stuff - END **/

$realValue = base64_decode($filterValue);
$realValue = str_replace('%', '', $realValue);
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#<?php echo $filterId; ?>").on("keypress", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					var fieldId    = $(this).attr("id") + "_hidden";
					var encodedStr = $.base64.btoa("%" + $(this).val() + "%", true);
					$("#" + fieldId).val(encodedStr);
					<?php echo $javascriptCallback; ?>();
				}
			});
		});
	})(jQuery);
</script>
<input type="hidden" name="<?php echo $filterName; ?>" id="<?php echo $filterId; ?>_hidden" value="<?php echo $filterValue; ?>" />
<input type="text" name="" id="<?php echo $filterId; ?>" value="<?php echo $realValue; ?>" />
