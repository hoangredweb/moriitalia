<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$javascriptCallback = $displayData['javascriptCallback'];
$category           = $displayData['category'];

ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
?>

<script type="text/javascript">
	var reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> = null;
	var reditemGmapFilterDistanceMap<?php echo $category->id; ?> = null;
	var reditemGmapFilterDistanceMarker<?php echo $category->id; ?> = null;
	var reditemGmapFilterDistanceCenterDefault<?php echo $category->id; ?> = new google.maps.LatLng(<?php echo JText::_('COM_REDITEM_ITEM_LATITUDE_AND_LONGTITUDE_NUMBER_DEFAULT'); ?>);
	var reditemGmapFilterDistanceCenter<?php echo $category->id; ?> = reditemGmapFilterDistanceCenterDefault<?php echo $category->id; ?>;

	(function($){
		$(document).ready(function(){
			$("#reditemGmapFilterDistanceAddressInput<?php echo $category->id; ?>").on("keypress", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					reditemGmapFilterDistanceGetAddress<?php echo $category->id; ?>();
				}
			})
			.on("focus", function(event){
				$(this).popover({
					'placement': 'right',
					'title': "<strong><?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_ADDRESS_HELPER_TITLE'); ?></strong>",
					'content': "<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_ADDRESS_HELPER_CONTENT'); ?>"
				});
			})
			.on("blur", function(event){
				$(this).popover('destroy');
			});

			$("#reditemGmapFilterDistanceValue<?php echo $category->id; ?>").on("keypress", function(event){
				if (event.which == 13 || event.keyCode == 13)
				{
					event.preventDefault();
					reditemGmapFilterDistanceProcess<?php echo $category->id; ?>();
				}
			});

			$("#filterDistancePickonmapTabLink<?php echo $category->id; ?>").on("shown", function(){
				// Avoid init google map error inside Tab content
				reditemFilterDistanceInitialize<?php echo $category->id; ?>();
			});

			$("#reditemGmapFilterDistanceAlertButton<?php echo $category->id; ?>").on('click', function(event){
				$("#reditemGmapFilterDistanceAlert<?php echo $category->id; ?>").addClass('hidden');
			})

			$('#reditemGmapFilterDistanceReset<?php echo $category->id; ?>').on('click', function(event){
				$('#reditemGmapFilterDistanceValue<?php echo $category->id; ?>').val('');
				$('#reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>').val('');
				<?php echo $javascriptCallback; ?>();
			})

			google.maps.event.addDomListener(window, 'load', reditemFilterDistanceInitialize<?php echo $category->id; ?>);
		});
	})(jQuery);

	/**
	 * Method for init Google Map
	 *
	 * @return  void
	 */
	function reditemFilterDistanceInitialize<?php echo $category->id; ?>()
	{
		// Init geocoder
		if (reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> == null)
		{
			reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> = new google.maps.Geocoder();
		}

		// Init map
		if (reditemGmapFilterDistanceMap<?php echo $category->id; ?> == null)
		{
			// Get center point from main Map if available
			if (typeof map != 'undefined')
			{
				reditemGmapFilterDistanceCenter<?php echo $category->id; ?> = map.getCenter();
			}

			var mapOptions = {
				zoom: 5,
				center: reditemGmapFilterDistanceCenter<?php echo $category->id; ?>,
				panControl: false,
				zoomControl: false,
				mapTypeControl: false,
				scaleControl: false,
				streetViewControl: false,
				overviewMapControl: false,
			}
			reditemGmapFilterDistanceMap<?php echo $category->id; ?> = new google.maps.Map(document.getElementById('reditemGmapFilterDistanceMapCanvas<?php echo $category->id; ?>'), mapOptions);

			// Add marker
			reditemGmapFilterSetMarker<?php echo $category->id; ?>();
		}
		else
		{
			// Avoid init google map error inside Tab content
			google.maps.event.trigger(reditemGmapFilterDistanceMap<?php echo $category->id; ?>, 'resize');

			reditemGmapFilterDistanceMap<?php echo $category->id; ?>.setCenter(reditemGmapFilterDistanceCenter<?php echo $category->id; ?>);
		}
	}

	/**
	 * Method for get location point base on address
	 *
	 * @return  void
	 */
	function reditemGmapFilterDistanceGetAddress()
	{
		var address = document.getElementById('reditemGmapFilterDistanceAddressInput<?php echo $category->id; ?>').value;

		if (reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> == null)
		{
			reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> = new google.maps.Geocoder();
		}

		reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?>.geocode({'address': address}, function(results, status){
			if (status == google.maps.GeocoderStatus.OK)
			{
				reditemGmapFilterDistanceCenter<?php echo $category->id; ?> = results[0].geometry.location;

				// Add marker
				reditemGmapFilterSetMarker<?php echo $category->id; ?>();

				// Set position value in input field
				document.getElementById('reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>').value = reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lat() + ',' + reditemGmapFilterDistanceCenter.lng();
				reditemGmapFilterDistanceProcess<?php echo $category->id; ?>();
			}
		});
	}

	/**
	 * Method for get current location of client base on browser navigator
	 *
	 * @return  void
	 */
	function reditemGmapFilterGetCurrentLocation<?php echo $category->id; ?>()
	{
		if (reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> == null)
		{
			reditemGmapFilterDistanceGeocoder<?php echo $category->id; ?> = new google.maps.Geocoder();
		}

		if (navigator.geolocation)
		{
			// Try W3C Geolocation (Preferred)
			navigator.geolocation.getCurrentPosition(function(position) {
					reditemGmapFilterDistanceCenter<?php echo $category->id; ?> = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
					// Add marker
					reditemGmapFilterSetMarker<?php echo $category->id; ?>();
					document.getElementById('reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>').value = reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lat() + ',' + reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lng();
					reditemGmapFilterDistanceProcess<?php echo $category->id; ?>();
				},
				function() {
					reditemGmapFilterDistanceShowAlert<?php echo $category->id; ?>("<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_GET_CURRENT_LOCATION_ERROR_SERVICE_FAIL'); ?>");
				}
			);
		}
		else
		{
			reditemGmapFilterDistanceShowAlert<?php echo $category->id; ?>("<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_GET_CURRENT_LOCATION_ERROR_BROWSER_FAIL'); ?>");
		}
	}

	/**
	 * Method for set marker of Google Maps
	 *
	 * @return  void
	 */
	function reditemGmapFilterSetMarker<?php echo $category->id; ?>()
	{
		if (reditemGmapFilterDistanceMarker<?php echo $category->id; ?> == null)
		{
			// If marker doesn't exist, create marker
			reditemGmapFilterDistanceMarker<?php echo $category->id; ?> = new google.maps.Marker({
				map: reditemGmapFilterDistanceMap<?php echo $category->id; ?>,
				position: reditemGmapFilterDistanceCenter<?php echo $category->id; ?>,
				draggable: true
			});

			// User start drag pin icon, clear address input field
			google.maps.event.addListener(reditemGmapFilterDistanceMarker<?php echo $category->id; ?>, 'dragstart', function() {
				document.getElementById('reditemGmapFilterDistanceAddressInput').value = '';
			});

			// When user stop drag, get current position
			google.maps.event.addListener(reditemGmapFilterDistanceMarker<?php echo $category->id; ?>, 'dragend', function() {
				reditemGmapFilterDistanceCenter<?php echo $category->id; ?> = reditemGmapFilterDistanceMarker<?php echo $category->id; ?>.getPosition();
				reditemGmapFilterDistanceMap<?php echo $category->id; ?>.panTo(reditemGmapFilterDistanceCenter<?php echo $category->id; ?>);

				// Set position value in input field
				document.getElementById('reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>').value = reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lat() + ',' + reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lng();
				reditemGmapFilterDistanceProcess<?php echo $category->id; ?>();
			});
		}
		else
		{
			reditemGmapFilterDistanceMarker<?php echo $category->id; ?>.setPosition(reditemGmapFilterDistanceCenter<?php echo $category->id; ?>);
			reditemGmapFilterDistanceMap<?php echo $category->id; ?>.panTo(reditemGmapFilterDistanceCenter<?php echo $category->id; ?>);
		}
	}

	/**
	 * Method for process data of filter and call javascript call back
	 *
	 * @return  void
	 */
	function reditemGmapFilterDistanceProcess<?php echo $category->id; ?>()
	{
		var distanceValue = document.getElementById('reditemGmapFilterDistanceValue<?php echo $category->id; ?>').value;
		var check = isPositiveInteger(distanceValue);

		if (!check)
		{
			reditemGmapFilterDistanceShowAlert<?php echo $category->id; ?>('<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_VALUE_MUST_BE_POSITIVE_INTEGER'); ?>');
			return;
		}

		var distanceFrom = document.getElementById('reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>');

		if (distanceFrom.value == '')
		{
			distanceFrom.value = reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lat() + ',' + reditemGmapFilterDistanceCenter<?php echo $category->id; ?>.lng();
		}

		<?php echo $javascriptCallback; ?>();
	}

	/**
	 * Method for check if input is positive integer
	 *
	 * @param   string  value  String value for check
	 *
	 * @return  boolean     True if value is positive integer
	 */
	function isPositiveInteger(value) {
	    return 0 === value % (!isNaN(parseFloat(value)) && 0 <= ~~value);
	}

	/**
	 * Show alert div
	 *
	 * @param   string  alertString  Alert string
	 *
	 * @return  void
	 */
	function reditemGmapFilterDistanceShowAlert<?php echo $category->id; ?>(alertString)
	{
		(function($){
			$('#reditemGmapFilterDistanceAlertContent<?php echo $category->id; ?>').html(alertString);
			$('#reditemGmapFilterDistanceAlert<?php echo $category->id; ?>').removeClass('hidden');
		})(jQuery);
	}
</script>

<div class="reditemGmapFilterDistance">
	<div id="reditemGmapFilterDistanceAlert<?php echo $category->id; ?>" class="alert alert-danger hidden">
		<button id="reditemGmapFilterDistanceAlertButton<?php echo $category->id; ?>" type="button" class="close">&times;</button>
		<span id="reditemGmapFilterDistanceAlertContent<?php echo $category->id; ?>"></span>
	</div>
	<h3><?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_LOCATION_AREA'); ?></h3>
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#filterDistanceCurrentLocationTab<?php echo $category->id; ?>" data-toggle="tab">
				<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_GET_CURRENT_LOCATION_TAB'); ?>
			</a>
		</li>
		<li>
			<a href="#filterDistanceAddressTab<?php echo $category->id; ?>" data-toggle="tab">
				<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_ADDRESS_TAB'); ?>
			</a>
		</li>
		<li>
			<a href="#filterDistancePickonmapTab<?php echo $category->id; ?>" data-toggle="tab" id="filterDistancePickonmapTabLink<?php echo $category->id; ?>">
				<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_PICK_ON_MAP_TAB'); ?>
			</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="filterDistanceCurrentLocationTab<?php echo $category->id; ?>">
			<a href="javascript:void(0);" class="btn" onclick="javascript:reditemGmapFilterGetCurrentLocation<?php echo $category->id; ?>();">
				<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_GET_CURRENT_LOCATION_GET'); ?>
			</a>
		</div>
		<div class="tab-pane" id="filterDistanceAddressTab<?php echo $category->id; ?>">
			<input type="text" class="input-xlarge" id="reditemGmapFilterDistanceAddressInput<?php echo $category->id; ?>" value=""
				placeholder="<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_ADDRESS_INPUT_HELP'); ?>" />
		</div>
		<div class="tab-pane" id="filterDistancePickonmapTab<?php echo $category->id; ?>">
			<div id="reditemGmapFilterDistanceMapCanvas<?php echo $category->id; ?>" style="width: 100%; height: 300px;"></div>
		</div>
	</div>
	<h3><?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_AREA'); ?></h3>
	<div>
		<input type="text" name="gmap_filter_distance" class="input-xlarge" id="reditemGmapFilterDistanceValue<?php echo $category->id; ?>" value="100" /> Km
	</div>
	<a href="javascript:void(0);" class="btn" id="reditemGmapFilterDistanceReset<?php echo $category->id; ?>">
		<?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_GMAP_FILTER_DISTANCE_RESET'); ?>
	</a>
	<input type="text" name="gmap_filter_distance_from" id="reditemGmapFilterDistanceFromValue<?php echo $category->id; ?>" value="" />
</div>
