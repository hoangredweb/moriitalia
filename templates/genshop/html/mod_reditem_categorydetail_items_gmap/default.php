<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');

ReditemHelperSystem::loadGoogleMapJavascriptLibrary();

RHelperAsset::load('reditem.categorydetailgmap.min.css', 'com_reditem');
RHelperAsset::load('reditem.categorydetailgmap.min.js', 'com_reditem');

RHelperAsset::load('googlemaps/markerwithlabel.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/infobubble.min.js', 'com_reditem');


//RHelperAsset::load('googlemaps/markerwithlabel.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/markerclusterer.min.js', 'com_reditem');


$ts = array("/[À-Å]/","/Æ/","/Ç/","/[È-Ë]/","/[Ì-Ï]/","/Ð/","/Ñ/","/[Ò-ÖØ]/","/×/","/[Ù-Ü]/","/[Ý-ß]/","/[à-å]/","/æ/","/ç/","/[è-ë]/","/[ì-ï]/","/ð/","/ñ/","/[ò-öø]/","/÷/","/[ù-ü]/","/[ý-ÿ]/");
$tn = array("A","AE","C","E","I","D","N","O","X","U","Y","a","ae","c","e","i","d","n","o","x","u","y");

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

	var modRICIGAllItems        = [];

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
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

	function reditemCategoryDetailItemsGmap_initialize()
	{
		var centerPoint = new google.maps.LatLng(55.3906821,10.437969000000066);
		//var isDraggable = screen.width > 480 ? true : false;
		//var isscrollwheel = screen.width > 480 ? false : true;
		var myOptions = {
			zoom: 6,
			center: centerPoint,
			gestureHandling: 'cooperative',
			panControl: false,
			zoomControl: true,
			mapTypeControl: true,
			scaleControl: true,
			streetViewControl: true,
			overviewMapControl: true,
			draggable: true,
			scrollwheel: false,
			//clickable: true,
			disableDoubleClickZoom: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
		};

		return new google.maps.Map(document.getElementById("reditem-categorydetail-items-gmap"), myOptions);


	}

	/**
	 * Adds clustered marks on Google map.
	 *
	 * @param init
	 * @param category
	 */
	function reditemCategoryDetailItemsGmap_addMarkers(category, cb)
	{
		var iconBase = '<?php echo JUri::base();?>images/icons/';

		for (var i = 0; i < modRICIGItems.length; i++)
		{
			var marketLatLng = modRICIGItems[i].itemLatLng;
			var markerLatLngArray = marketLatLng.split(',');
			var location = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);
			// var isDraggable = screen.width > 480 ? true : true;
			//var isscrollwheel = screen.width > 480 ? false : true;
			var marker = new MarkerWithLabel({
				position: location,
				draggable: false,
				raiseOnDrag: false,
				/*icon: iconBase + 'find-synshal-large.png',*/
				icon: '<?php echo JUri::base();?>templates/genshop/images/googlemap/pin3.png',
				map: modRICIGMap,
				labelContent: modRICIGItems[i].title,
				labelAnchor: new google.maps.Point(28, 0),
				labelStyle: {opacity: 1.0}
			});
			marker.itemId     = modRICIGItems[i].itemId;
			marker.itemLink   = modRICIGItems[i].itemLink;
			marker.itemTypes  = modRICIGItems[i].itemTypes;
			marker.title      = modRICIGItems[i].title;
			marker.cleanTitle = modRICIGItems[i].cleanTitle;

			google.maps.event.addListener(marker, 'click', function() {
				window.location.href = this.itemLink;
			});


			modRICIGMarkers.push(marker);
		}

		if (category > 1)
		{
			var center = new google.maps.LatLng(10.8230989,106.6296638);
			var zoom   = 6;

			switch (category)
			{
				case 18:
					center = new google.maps.LatLng(10.8230989,106.6296638);
					zoom   = 8;
					break;
				case 19:
					zoom   = 8;
					center = new google.maps.LatLng(21.0277644,105.83415979999995);
					break;
				case 20:
					zoom = 12;
					center = new google.maps.LatLng(9.1526728,105.1960795);
				case 21:
					zoom = 12;
					center = new google.maps.LatLng(11.3254024,106.47701699999993);
				case 22:
					zoom = 12;
					center = new google.maps.LatLng(11.0686305,107.16759760000002);
				case 23:
					zoom = 12;
					center = new google.maps.LatLng(10.5215836,105.12589550000007);
				case 24:
					zoom = 12;
					center = new google.maps.LatLng(10.0451618,105.74685350000004);
				default:
					break;
			}

			modRICIGMap.setCenter(center);
			modRICIGMap.setZoom(zoom);
		}
			<?php if (!$isMobile) :?>
		else
		{

			//  Fit these bounds to the map
			if (category < 2 && modRICIGLatitude > 0.0 && modRICIGLongitude > 0.0)
			{
				var closest = findClosest(modRICIGLatitude, modRICIGLongitude, 3);
				var bounds  = new google.maps.LatLngBounds();

				for (i = 0; i < closest.length; i++)
				{
					bounds.extend(closest[i].getPosition());
				}

				modRICIGMap.setCenter(new google.maps.LatLng(modRICIGLatitude, modRICIGLongitude));
				modRICIGMap.fitBounds(bounds);
			}
		}
		<?php endif; ?>

		var mcOptions = {
			styles: [{
				textColor: 'white',
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
	function reditemCategoryDetailItemsGmap_filter(ele, filter)
	{
		jQuery('.reditem_filter_customfield_list').find('li.active').removeClass('active');
		ele.parent().addClass('active');
		modRICIGMarkerClusterer.removeMarkers(modRICIGMarkers, true);

		for (var i = 0; i < modRICIGMarkers.length; i++)
		{
			if (jQuery.inArray(filter, modRICIGMarkers[i].itemTypes) < 0)
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
				//jQuery('.scrollbar-inner').mCustomScrollbar();
				//jQuery('.ps-container').perfectScrollbar('update');
			}
		});
	}

	function string_to_slug(str) {
		str = str.replace(/^\s+|\s+$/g, ''); // trim
		str = str.toLowerCase();

		// remove accents, swap ñ for n, etc
		var from = "àáạäâậấầèéëêìíïîơởờòóöôộồùúủüûưứñçýđ·/_,:;";
		var to   = "aaaaaaaaeeeeiiiiooooooooouuuuuuuncyd------";
		for (var i=0, l=from.length ; i<l ; i++) {
			str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		}

		// str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		// .replace(/\s+/g, '-') // collapse whitespace and replace by -
		// .replace(/-+/g, '-'); // collapse dashes

		return str;
	}

	/**
	 * Check near by.
	 *
	 * @param lat
	 * @param lng
	 * @param distance
	 *
	 * @return array
	 */
	function isNearBy(center, targetLocation, distance)
	{
		var R = 6378.1; // radius of earth in km

		var mlat = targetLocation.lat();
		var mlng = targetLocation.lng();
		var dLat  = (mlat - center.lat()) * Math.PI/180;
		var dLong = (mlng - center.lng()) * Math.PI/180;
		var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
			Math.cos(center.lat() * Math.PI/180) * Math.cos(center.lat() * Math.PI/180) * Math.sin(dLong/2) * Math.sin(dLong/2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
		var d = R * c;

		if (d <= distance)
		{
			return true;
		}

		return false;
	}

	function getBoundsRadius(bounds){
		// r = radius of the earth in km
		var r = 6378.1

		// degrees to radians (divide by 57.2958)
		var ne_lat = bounds.getNorthEast().lat() / 57.2958
		var ne_lng = bounds.getNorthEast().lng() / 57.2958
		var c_lat = bounds.getCenter().lat() / 57.2958
		var c_lng = bounds.getCenter().lng() / 57.2958

		// distance = circle radius from center to Northeast corner of bounds
		var r_km = r * Math.acos(
			Math.sin(c_lat) * Math.sin(ne_lat) +
			Math.cos(c_lat) * Math.cos(ne_lat) * Math.cos(ne_lng - c_lng)
		);

		return r_km
	}

	function reditemCategoryDetailItemsGmap_search()
	{
		var search = jQuery('#mod_ricatdigmap_search').val();

		//add

		// if (!isNaN(search))
		// {
		// 	var zip = parseInt(search);

		// 	if (zip > 100000 && zip < 450000)
		// 	{
		// 		search = 'Ha noi';
		// 	}
		// 	else if (zip > 460000 && zip < 660000)
		// 	{
		// 		search = 'Da nang';
		// 	}
		// 	else if (zip > 660000 && zip < 850000)
		// 	{
		// 		search = 'Ho chi minh';
		// 	}
		// 	else{
		// 		search = 'Can tho';
		// 	}
		// }

		if (search == '')
		{
			modRICIGItems = modRICIGAllItems;
			modRICIGMap = reditemCategoryDetailItemsGmap_initialize();
			reditemCategoryDetailItemsGmap_addMarkers(0);

			jQuery('#items_gmap_results .infoBoxItem').each(function() {
				jQuery(this).removeClass('hidden');
			});

			return;
		}

		for (i = 0; i < modRICIGMarkers.length; i++)
		{
			modRICIGMarkers[i].setMap(null);
		}

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(
			{
				'address': search,
				'region' : 'VN'
			},
			function(results, status)
			{
				if (status == google.maps.GeocoderStatus.OK)
				{
					var loc = results[0].geometry.location;
					var lat = loc.lat();
					var lng = loc.lng();

					var boundRadius = getBoundsRadius(results[0].geometry.bounds);

					// process
					modRICIGMarkers = [];
					modRICIGItems = [];

					var titles = '';

					for (i = 0; i < modRICIGAllItems.length; i++)
					{
						var marketLatLng = modRICIGAllItems[i].itemLatLng;
						var markerLatLngArray = marketLatLng.split(',');
						var location = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);

						if (isNearBy(loc, location, boundRadius))
						{
							modRICIGItems.push(modRICIGAllItems[i]);
							titles += modRICIGAllItems[i].title;
						}
					}

					jQuery('#items_gmap_results .infoBoxItem').each(function() {
						if (titles.indexOf(jQuery(this).find('.title').text()) > -1)
						{
							jQuery(this).removeClass('hidden');
						}
						else
						{
							jQuery(this).addClass('hidden');
						}
					});

					if (search == '')
					{
						modRICIGItems = modRICIGAllItems;
					}

					if (modRICIGItems.length > 0)
					{
						modRICIGMap = reditemCategoryDetailItemsGmap_initialize();
						reditemCategoryDetailItemsGmap_addMarkers(0);
					}
					else
					{
						var pos = new google.maps.LatLng(loc.lat(), loc.lng());
						modRICIGMap.setCenter(pos);
					}
					// end


					// if (lat > 10.04 || lat < 10.82 || lng > 105.74 || lng < 106.62)
					// {
					// 	var centerPoint = new google.maps.LatLng(10.8230989,106.6296638);
					// 	modRICIGMap.setCenter(centerPoint);
					// 	modRICIGMap.setZoom(8);
					// }
					// else
					// {
					// 	var closest = findClosest(loc.lat(), loc.lng(), 5);
					// 	console.log(closest);
					// 	var bounds  = new google.maps.LatLngBounds();
					// 	var pos     = new google.maps.LatLng(loc.lat(), loc.lng());
					// 	modRICIGMap.setCenter(pos);
					// 	bounds.extend(pos);
					//
					// 	for (var i = 0; i < closest.length; i++)
					// 	{
					// 		bounds.extend(closest[i].getPosition());
					// 	}
					//
					// 	modRICIGMap.fitBounds(bounds);
					// }
				}
			}
		);
		//end

		// for (i = 0; i < modRICIGMarkers.length; i++)
		// {
		// 	modRICIGMarkers[i].setMap(null);
		// }
		//
		// modRICIGMarkers = [];
		// modRICIGItems = [];
		//
		// var titles = '';
		//
		// for (i = 0; i < modRICIGAllItems.length; i++)
		// {
		// 	search = string_to_slug(search);
		// 	const regex = new RegExp(search,"i");
		// 	const title = string_to_slug(modRICIGAllItems[i].title);
		//
		// 	if (regex.test(title))
		// 	{
		// 		modRICIGItems.push(modRICIGAllItems[i]);
		// 		titles += modRICIGAllItems[i].title;
		// 	}
		// }
		//
		// // if (modRICIGItems.length == 0)
		// // {
		// // 	modRICIGItems = modRICIGAllItems;
		// // }
		//
		// jQuery('#items_gmap_results .infoBoxItem').each(function() {
		// 	if (titles.indexOf(jQuery(this).find('.title').text()) > -1)
		// 	{
		// 		jQuery(this).removeClass('hidden');
		// 	}
		// 	else
		// 	{
		// 		jQuery(this).addClass('hidden');
		// 	}
		// });
		//
		// if (modRICIGItems.length > 0)
		// {
		// 	modRICIGMap = reditemCategoryDetailItemsGmap_initialize();
		// 	reditemCategoryDetailItemsGmap_addMarkers(0);
		// }
		//
		// // if (modRICIGItems.length == 0)
		// // {
		// // 	modRICIGMap.setCenter(new google.maps.LatLng(modRICIGLatitude, modRICIGLongitude));
		// // }
	}

	(function($){
		function mapLoad()
		{
			<?php foreach ($items as $item) :?>
			var item = {
				title      : '<?php echo $item->title; ?>',
				itemLatLng : '<?php echo $item->itemLatLng; ?>',
				itemId     : <?php echo $item->id; ?>,
				itemLink   : '<?php echo $item->link?>',
				cleanTitle : "<?php echo mb_convert_encoding($item->title, 'US-ASCII', 'UTF-8'); ?>",
				itemTypes  : ["<?php echo implode(',', $item->types) ?>"]
			};


			modRICIGItems.push(item);
			modRICIGAllItems.push(item);
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
						if ($('.reditem_filter_customfield_list li.active').length > 0)
						{
							var bounds = modRICIGMap.getBounds();
							boundsChanged(bounds, category);
						}
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

			$('#searchsubmit').click(function(){
				reditemCategoryDetailItemsGmap_search(modRICIGMap, modRICIGMarkers);
			});

			$('.Personbil a').click();

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
			modRICIGMapLoaded = true;
		}

		<?php if (!$lazyLoad) : ?>
		google.maps.event.addDomListener(window, 'load', mapLoad);
		<?php else: ?>
		$(document).ready(function() {
			// if (!modRICIGMapLoaded && isElementInViewport(jQuery('#reditem-categorydetail-items-gmap')))
			// {
				//mapLoad();
			//}
			jQuery(window).bind('scroll', function() {
	                if(jQuery(window).scrollTop() >= jQuery('.googleMap').offset().top + jQuery('.googleMap').outerHeight() - window.innerHeight -500) {
	                    mapLoad();
	                    jQuery(window).unbind('scroll').delay(5000);
	              	}
			});
		//$(window).on('scroll', function() {
			// if (!modRICIGMapLoaded && isElementInViewport(jQuery('#reditem-categorydetail-items-gmap')))
			// {
				//mapLoad();
			//}
		//});
		});
		<?php endif; ?>
	})(jQuery);
</script>
<div class="googleMap">
	<div id="reditem-categorydetail-items-gmap" style="width: 100%; height: 479px;"></div>
	<?php if (!$isMobile) :?>
	<div class="container">
	<div class="gMapinfoBox ui-widget-content">

			<div class="row">
				<div class="col-xs-12">
					<div class="infoBox">
						<div class="infoBoxHeadline">
							<h2><?php echo JText::_('MOD_REDITEM_GMAP_ITEMS_INFOBOX'); ?></h2>
						</div>
						<div class="reditem_categorydetail_items_gmap">
							<div class="infoBoxSearch input-append">
								<input
									type="text"
									placeholder="<?php echo JText::_('SEARCH_TEXT_PLACEHOLDER'); ?>"
									id="mod_ricatdigmap_search" />
								<button type="submit" class="btn" id="searchsubmit">

								</button>
							</div>
							<?php
							/*
							<div class="infoBoxTypes">
								<ul class="reditem_filter_customfield_list">
									<?php foreach($filters as $filter) :?>
										<li class="list-item <?php if ($filter->active): $active = $filter->value;?>active<?php endif;?> <?php echo $filter->value; ?>">
											<a href="#" onclick="reditemCategoryDetailItemsGmap_filter(jQuery(this),'<?php echo $filter->value; ?>'); return false;"><?php echo $filter->text;?></a>
										</li>
									<?php endforeach;?>
								</ul>
							</div>
							*/
							?>
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
