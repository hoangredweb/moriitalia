<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$config             = $displayData['config'];
$javascriptCallback = $displayData['javascriptCallback'];
$value              = $displayData['value'];
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#reditem_filter_subcat_title").on("keypress", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					<?php echo $javascriptCallback; ?>();
					return false;
				}
			});

			<?php if ($config['autocomplete']) : ?>
			$("#reditem_filter_subcat_title").typeahead({
				source: function (query, process) {
					var items = new Array;
					items = [""];

					var form = document.getElementById("reditemCategoryDetail");
					form.task.value = "categorydetail.ajaxFilterSubCatTitle";
					var url = "index.php?" + $(form).serialize();

					return $.get(url, {}, function (data) {
						var objects = JSON.parse(data);
						items = objects;
						process(items);
					});
				},
				updater: function (item) {
					$("#reditem_filter_subcat_title").val(item);
					<?php echo $javascriptCallback; ?>();
					return item;
				},
				highlighter: function(item) {
					return "<span>" + item + "</span>";
				}
			});
			<?php endif; ?>
		});
	})(jQuery);
</script>

<input
	type="text"
	name="filter_subcat_title"
	id="reditem_filter_subcat_title"
	value="<?php echo $value; ?>"
	placeholder="<?php echo $config['hint']; ?>"
	<?php if ($config['autocomplete']) : ?>
	autocomplete="off"
	<?php endif; ?>
	/>