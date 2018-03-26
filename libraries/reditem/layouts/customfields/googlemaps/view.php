<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

// Add Google Maps script
ReditemHelperSystem::loadGoogleMapJavascriptLibrary();

$tag          = $displayData['tag'];
$value        = $displayData['value'];
$reditemId    = $displayData['reditemId'];
$reditemTitle = $displayData['reditemTitle'];
$item         = $displayData['item'];
?>

<?php if (!empty($value)) : ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function($){
			reditem_customfield_googlemaps_init('#gmap-<?php echo $tag->fieldcode; ?>');
		});
	})(jQuery);
</script>

<div class="reditem_googlemap reditem_googlemap_<?php echo $reditemId; ?> reditem_googlemap_<?php echo $tag->fieldcode; ?>" id="gmap-<?php echo $tag->fieldcode; ?>">
	<div class="reditem_custom_googlemaps">
		<div id="reditem_customfield_googlemaps_<?php echo $tag->fieldcode; ?>_canvas" class="reditem_custom_googlemaps_canvas"></div>
		<input type="hidden" id="mapid" value="reditem_customfield_googlemaps_<?php echo $tag->fieldcode; ?>_canvas" />
		<input type="hidden" id="maplatlng" value="<?php echo $value; ?>" />
		<input type="hidden" id="mapinfor" value="<h3><?php echo $reditemTitle; ?></h3>" />
	</div>
</div>
<?php endif; ?>
