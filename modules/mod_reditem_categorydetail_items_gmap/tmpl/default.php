<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

RHelperAsset::load('googlemaps/markerwithlabel.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/markerclusterer.min.js', 'com_reditem');

JHtml::_('script', 'com_redshopretail/select2.js', false, true, false, false);
JHtml::_('stylesheet', 'com_redshopretail/select2.css', false, true);

$items   = $data['items'];
$filters = $data['filters'];
$active  = '';
$app     = JFactory::getApplication();
$menu    = $app->getMenu();
$active  = $menu->getActive();

if (isset($active))
{
	$itemID = $active->id;
}
else
{
	$itemID = 0;
}

$root = JUri::root();

// NOTE: This is applus specific module atm. Idea is to generalize this module later on.
?>

<!-- Initialize -->
<script type="text/javascript">
	var modRICIGItems           = [];
	var modRICIGMap             = null;
	var modRICIGMarkers         = [];
	var modRICIGMarkerClusterer = null;
	var modRICIGLatitude        = <?php echo $data['lat']; ?>;
	var modRICIGLongitude       = <?php echo $data['lng']; ?>;
	var modRICIGMapLoaded       = false;

	/**
	 * Find closest markers.
	 *
	 * @param lat
	 * @param lng
	 * @param closestCount
	 *
	 * @return array
	 */
	function findClosest(lat, lng, closestCount)
	{
		var R = 6371; // radius of earth in km
		var distances = [];
		var closest = [];
		var temp = null;

		for(var i = 0; i < modRICIGMarkers.length; i++)
		{
			if (modRICIGMarkers[i].visible)
			{
				var mlat = modRICIGMarkers[i].position.lat();
				var mlng = modRICIGMarkers[i].position.lng();
				var dLat  = (mlat - lat) * Math.PI/180;
				var dLong = (mlng - lng) * Math.PI/180;
				var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
					Math.cos(lat * Math.PI/180) * Math.cos(lat * Math.PI/180) * Math.sin(dLong/2) * Math.sin(dLong/2);
				var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
				var d = R * c;
				distances.push({index: i, distance: d});
			}
		}

		if (closestCount > distances.length)
		{
			closestCount = distances.length;
		}

		for (i = 0; i < closestCount; i++)
		{
			for (var j = i+1; j < distances.length; j++)
			{
				if (distances[i].distance > distances[j].distance)
				{
					temp = distances[i];
					distances[i] = distances[j];
					distances[j] = temp;
				}
			}

			closest.push(modRICIGMarkers[distances[i].index]);
		}

		return closest;
	}

	/**
	 * Create Google map function.
	 *
	 * @returns {.extraWords.Map}
	 */
	function reditemCategoryDetailItemsGmap_initialize()
	{
		var centerPoint = new google.maps.LatLng(55.3906821,10.437969000000066);

		var myOptions = {
			zoom: 6,
			center: centerPoint,
			panControl: false,
			zoomControl: true,
			mapTypeControl: true,
			scaleControl: true,
			streetViewControl: true,
			overviewMapControl: true,
			draggable: true,
			scrollwheel: false,
			disableDoubleClickZoom: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		return new google.maps.Map(document.getElementById("reditem-categorydetail-items-gmap"), myOptions);
	}

	/**
	 * Adds clustered marks on Google map.
	 *
	 * @param init
	 * @param category
	 */
	function reditemCategoryDetailItemsGmap_addMarkers(category)
	{
		var iconBase = '<?php echo JUri::base();?>templates/redcomponent/images/';

		for (var i = 0; i < modRICIGItems.length; i++)
		{
			var marketLatLng = modRICIGItems[i].itemLatLng;
			var markerLatLngArray = marketLatLng.split(',');
			var location = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);
			var marker = new MarkerWithLabel({
				position: location,
				draggable: false,
				raiseOnDrag: false,
				//icon: iconBase + 'marker1.png',
				icon: '<?php echo JUri::base();?>templates/genshop/images/googlemap/pin3.png',
				map: modRICIGMap,
				labelContent: modRICIGItems[i].title,
				labelAnchor: new google.maps.Point(28, 0),
				labelStyle: {opacity: 1.0},
				labelClass: "labels"
			});
			marker.itemId    = modRICIGItems[i].itemId;
			marker.itemLink  = modRICIGItems[i].itemLink;
			marker.itemTypes = modRICIGItems[i].itemTypes;
			marker.itemBy 	 = modRICIGItems[i].itemBy;


			google.maps.event.addListener(marker, 'click', function() {

				if (this.itemLink)
				{
					window.location.href = this.itemLink;
				}

			});

			modRICIGMarkers.push(marker);
		}

		var mcOptions = {
			styles: [{
				textColor: 'white',
				//textSize: '13',
				height: 32,
				url: '<?php echo JUri::base();?>templates/genshop/images/googlemap/pin3.png',
				width: 21
			}],
			gridSize: 40,
			maxZoom: 10,
			averageCenter: true
		};

		modRICIGMarkerClusterer = new MarkerClusterer(modRICIGMap, modRICIGMarkers, mcOptions);
	}

	/**
	 * Clear search and filters.
	 */
	function reditemCategoryDetailItemsGmap_clear()
	{
		jQuery('.reditem_filter_customfield_list').find('li:first').click();
	}

	/**
	 * Apply filters function.
	 */
	function reditemCategoryDetailItemsGmap_filter(filter)
	{
		modRICIGMarkerClusterer.removeMarkers(modRICIGMarkers, true);

		for (var i = 0; i < modRICIGMarkers.length; i++)
		{
			if (filter == "")
			{
				modRICIGMarkers[i].setVisible(true);
				modRICIGMarkerClusterer.addMarker(modRICIGMarkers[i], true);
			}
			else if (jQuery.inArray(filter, modRICIGMarkers[i].itemTypes) < 0)
			{
				modRICIGMarkers[i].setVisible(false);
			}
			else
			{
				modRICIGMarkers[i].setVisible(true);
				modRICIGMarkerClusterer.addMarker(modRICIGMarkers[i], true);
			}
		}

		modRICIGMarkerClusterer.repaint();
		google.maps.event.trigger(modRICIGMap, 'idle');
	}

	function boundsChanged(bounds, category)
	{
		var search       = jQuery('#mod_ricatdigmap_search').val();
		var markersOnMap = [];

		for (var i = 0; i < modRICIGMarkers.length; i++)
		{

			if (bounds.contains(modRICIGMarkers[i].getPosition()) && modRICIGMarkers[i].visible)
			{
				markersOnMap.push(modRICIGMarkers[i].itemId);
			}
		}

		if (markersOnMap.length < 1)
		{
			var center    = modRICIGMap.getCenter();
			var markers   = findClosest(center.lat, center.lng, 3);
			var newBounds = new google.maps.LatLngBounds();

			for (i = 0; i < markers.length; i++)
			{
				newBounds.extend(markers[i].getPosition());
			}

			modRICIGMap.fitBounds(newBounds);

			return;
		}

		jQuery.ajax({
			url : '<?php echo $root;?>' + 'index.php?option=com_ajax&module=reditem_categorydetail_items_gmap&method=getOnMapResults&format=json&Itemid=' + '<?php echo $itemID;?>',
			data : {
				ids      : markersOnMap,
				search   : search,
				category : category
			},
			dataType: 'json'
		}).done(function(e) {
			if (e.success)
			{
				jQuery('#items_gmap_results').html(e.data.html);
				//jQuery('.ps-container').perfectScrollbar('update');
			}
		});
	}

	function reditemCategoryDetailItemsGmap_search()
	{
		var search = jQuery('#mod_ricatdigmap_search').val();

		search = search.toLowerCase();

		modRICIGMarkerClusterer.removeMarkers(modRICIGMarkers, true);

		for (var i = 0; i < modRICIGMarkers.length; i++)
		{
			var labelContent = modRICIGMarkers[i].labelContent.toLowerCase();
			var itemBy = modRICIGMarkers[i].itemBy.toLowerCase();
			console.log(modRICIGMarkers[i]);
			;
			if (search == "" || labelContent.indexOf(search) !== -1 || itemBy.indexOf(search) !== -1)
			{
				modRICIGMarkers[i].setVisible(true);
				modRICIGMarkerClusterer.addMarker(modRICIGMarkers[i], true);
			}
			else
			{
				modRICIGMarkers[i].setVisible(false);
			}
		}

		modRICIGMarkerClusterer.repaint();
		google.maps.event.trigger(modRICIGMap, 'idle');
	}

	(function($){
		function mapLoad()
		{
			<?php foreach ($items as $item) :?>
			<?php if (!empty($item->itemLatLng)) : ?>
			var item = {
				title      : '<?php echo htmlentities($item->title, ENT_QUOTES); ?>',
				itemLatLng : '<?php echo $item->itemLatLng; ?>',
				itemId     : <?php echo $item->id; ?>,
				itemLink   : '<?php echo $item->link?>',
				itemTypes  : ['<?php echo implode('\',\'', $item->types)?>']
			};
			modRICIGItems.push(item);
			<?php endif; ?>
			<?php endforeach; ?>

			var category = 0;
			var option   = jQuery('input[name="option"]').val();
			var view     = jQuery('input[name="view"]').val();
			var id       = jQuery('input[name="id"]').val();

			if (option == 'com_reditem' && view == 'categorydetail')
			{
				category = parseInt(id);
			}

			modRICIGMap = reditemCategoryDetailItemsGmap_initialize();
			reditemCategoryDetailItemsGmap_addMarkers(category);
			var timer = 0;
			var refreshing = false;

			google.maps.event.addListener(modRICIGMap, 'idle', function() {
				if (refreshing)
				{
					clearTimeout(timer);
					refreshing = false;
				}
				else
				{
					refreshing = true;
				}

				timer = setTimeout(
					function(){
						var bounds = modRICIGMap.getBounds();
						boundsChanged(bounds, category);
					}, 800
				);
			});

			$('#mod_ricatdigmap_search').keydown(function(event){
				if (event.keyCode == 13 || event.which == 13)
				{
					event.preventDefault();
					reditemCategoryDetailItemsGmap_search(modRICIGMap, modRICIGMarkers);
				}
			});



			<?php if ($isMobile) :?>
			if (navigator.geolocation)
			{
				navigator.geolocation.getCurrentPosition(function(position) {
					modRICIGLatitude  = position.coords.latitude;
					modRICIGLongitude = position.coords.longitude;

					var closest = findClosest(modRICIGLatitude, modRICIGLongitude, 2);
					var bounds  = new google.maps.LatLngBounds();

					for (var i = 0; i < closest.length; i++)
					{
						bounds.extend(closest[i].getPosition());
					}

					modRICIGMap.setCenter(new google.maps.LatLng(modRICIGLatitude, modRICIGLongitude));
					modRICIGMap.fitBounds(bounds);
				}, function() {
					if (category < 2 && modRICIGLatitude > 0.0 && modRICIGLongitude > 0.0)
					{
						var closest = findClosest(modRICIGLatitude, modRICIGLongitude, 3);
						var bounds  = new google.maps.LatLngBounds();

						for (var i = 0; i < closest.length; i++)
						{
							bounds.extend(closest[i].getPosition());
						}

						modRICIGMap.setCenter(new google.maps.LatLng(modRICIGLatitude, modRICIGLongitude));
						modRICIGMap.fitBounds(bounds);
					}
				},{
					enableHighAccuracy : true,
					maximumAge         : 30000,
					timeout            : 27000
				});
			}
			<?php endif; ?>

			<?php if (!$isMobile) :?>
			//  Fit these bounds to the map
			if (modRICIGLatitude > 0.0 && modRICIGLongitude > 0.0)
			{
				var closest = findClosest(modRICIGLatitude, modRICIGLongitude, 3);

				console.log(closest);
				var bounds  = new google.maps.LatLngBounds();

				for (i = 0; i < closest.length; i++)
				{
					bounds.extend(closest[i].getPosition());
				}

				modRICIGMap.setCenter(new google.maps.LatLng(modRICIGLatitude, modRICIGLongitude));
				modRICIGMap.fitBounds(bounds);
			}
			<?php endif; ?>

			modRICIGMapLoaded = true;
		}

		<?php if (!$lazyLoad) : ?>
		google.maps.event.addDomListener(window, 'load', mapLoad);
		<?php else: ?>
		$(document).ready(function() {
			// if (!modRICIGMapLoaded && isElementInViewport(jQuery("#reditem-categorydetail-items-gmap")))
			// {
			mapLoad();
			// }
		});
		//$(window).on('scroll', function() {
			// if (!modRICIGMapLoaded && isElementInViewport(jQuery("#reditem-categorydetail-items-gmap")))
			// {
			// 	mapLoad();
			// }
		//});
		<?php endif; ?>



	})(jQuery);
</script>
<div class="googleMap">
	<div id="reditem-categorydetail-items-gmap" style="width: 100%;"></div>
	<?php if (!$isMobile) :?>
	<div class="gMapinfoBox ui-widget-content">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-xs-12">
					<div class="infoBox">
						<div class="infoBoxHeadline">
							<h2><?php echo JText::_('MOD_REDITEM_GMAP_ITEMS_INFOBOX'); ?></h2>
						</div>
						<div class="reditem_categorydetail_items_gmap">
							<div class="infoBoxSearch input-append">
								<input
									type="text"
									placeholder="Indtast bynavn eller forhandlernavn"
									id="mod_ricatdigmap_search" />
								<button type="submit" class="btn" id="searchsubmit">

								</button>
							</div>
							<div class="infoBoxTypes">
								<select class="reditem_filter_customfield_list" onchange="reditemCategoryDetailItemsGmap_filter(this.value)">
									<?php foreach($filters as $filter) :?>
										<option value="<?php echo $filter->value; ?>" <?php if ($filter->active): $active = $filter->value;?>selected<?php endif;?>>
											<?php echo $filter->text;?>
										</option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
						<?php if (!empty($items)): ?>
							<div class="reditem_categorydetail_items_gmap_results">
								<div class="scrollbar-inner" id="items_gmap_results">
									<?php foreach ($items as $item): ?>
										<?php echo $item->html; ?>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>



<script type="text/javascript">
	jQuery(document).ready(function($){
		$('select').select2({ width: 'resolve' });
	});
</script>
