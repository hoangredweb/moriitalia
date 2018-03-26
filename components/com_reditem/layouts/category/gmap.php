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

extract($displayData);
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var latlng = new google.maps.LatLng(<?php echo $latitude ?>, <?php echo $longitude ?>);

			var mapOptions = {
				zoom: 8,
				center: latlng,
				panControl: false,
				zoomControl: false,
				mapTypeControl: false,
				scaleControl: false,
				streetViewControl: false,
				overviewMapControl: false,
			};

			var map = new google.maps.Map(document.getElementById("reditem-category-map-canvas-<?php echo $category->id ?>"), mapOptions);

			var marker = new google.maps.Marker({
				map: map,
				position: latlng
			});

			google.maps.event.addListener(marker, "click", function (e) {
				var infowindow = new google.maps.InfoWindow({
					content: "<?php echo $category->title ?>"
				});

				infowindow.open(map, this);
			});
		});
	})(jQuery);
</script>

<div class="reditem-category-gmap">
	<div id="reditem-category-map-canvas-<?php echo $category->id ?>" style="width: 100%; height: 300px;"></div>
</div>
