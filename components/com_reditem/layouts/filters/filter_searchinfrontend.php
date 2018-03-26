<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;
$config				= $displayData['config'];
$javascriptCallback	= $displayData['javascriptCallback'];
$value				= $displayData['value'];
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#reditem_filter_searchinfrontend").on("keydown", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					<?php echo $javascriptCallback; ?>();
				}
			});
		});
	})(jQuery);
	function reditemFilterSearchInFrontendReset()
	{
		jQuery('#reditem_filter_searchinfrontend').val('');
		<?php echo $javascriptCallback; ?>();
	}
</script>

<input
		type="text"
		name="filter_searchinfrontend"
		id="reditem_filter_searchinfrontend"
		value="<?php echo $value; ?>"
		placeholder="<?php echo $config['hint']; ?>"
/>
<a class="btn" href="javascript:void(0);" onclick="javascript:reditemFilterSearchInFrontendReset();">Reset</a>
