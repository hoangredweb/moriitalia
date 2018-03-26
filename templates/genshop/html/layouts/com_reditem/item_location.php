<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$item           = $displayData['item'];
$itemLatitude   = $displayData['latitude'];
$itemLongtitude = $displayData['longtitude'];

ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var reditemItemCenterPoint<?php echo $item->id; ?> = new google.maps.LatLng(<?php echo $itemLatitude; ?>,<?php echo $itemLongtitude; ?>);
			var reditemItemMapOptions<?php echo $item->id; ?> = {
				zoom: 16,
				center: reditemItemCenterPoint<?php echo $item->id; ?>,
				panControl: false,
				zoomControl: true,
				mapTypeControl: false,
				scaleControl: true,
				streetViewControl: false,
				overviewMapControl: false,
			}
			var reditemItemMapObj<?php echo $item->id; ?> = new google.maps.Map(document.getElementById('reditem_item_location_canvas<?php echo $item->id; ?>'), reditemItemMapOptions<?php echo $item->id; ?>);

			var image = "<?php echo JHtml::_('image', 'com_reditem/map-marker.png', null, null, true, true); ?>";
			var reditemItemMarker<?php echo $item->id; ?> = new google.maps.Marker({
				map: reditemItemMapObj<?php echo $item->id; ?>,
				position: reditemItemCenterPoint<?php echo $item->id; ?>,
				draggable: false,
				icon: image
			});
		});
	})(jQuery);
</script>

<div class="reditem_item_location" id="reditem_item_location<?php echo $item->id; ?>">
	<div
		class="reditem_item_location_canvas reditem_item_location_canvas<?php echo $item->id; ?>"
		id="reditem_item_location_canvas<?php echo $item->id; ?>">
	</div>
</div>
