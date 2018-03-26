<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$itemId = JFactory::getApplication()->input->getInt('Itemid', 0);

JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');

ReditemHelperSystem::loadGoogleMapJavascriptLibrary();

RHelperAsset::load('reditem.categorydetailgmap.min.css', 'com_reditem');
RHelperAsset::load('reditem.categorydetailgmap.min.js', 'com_reditem');

RHelperAsset::load('googlemaps/markerwithlabel.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/infobubble.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/markerclusterer.min.js', 'com_reditem');

$gmapZoom = 8;
$gmapLatlng = JText::_('COM_REDITEM_ITEM_LATITUDE_AND_LONGTITUDE_NUMBER_DEFAULT');

if (!empty($this->item->settingDistance)) :
	$gmapLatlng = $this->item->settingDistance['location'];
endif;
?>

<script type="text/javascript">
	var holderlib = '<?php echo JURI::root(); ?>media/redcore/js/lib/holder.js';
	var map = null;
	var markers = new Array();
	var mapBounds = new google.maps.LatLngBounds();
	var circleOptions = null;
	var circleSetting = null;
	var markerCluster = null;

	function reditemCategoryDetailGmapInitialize()
	{
		var centerPoint = new google.maps.LatLng(<?php echo $gmapLatlng ?>);

		myOptions = {
			zoom: <?php echo $gmapZoom ?>,
			center: centerPoint,
			panControl: false,
			zoomControl: false,
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			overviewMapControl: false,
			draggable: true,
			scrollwheel: true,
			disableDoubleClickZoom: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControlOptions: {
				mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
			}//,
			//styles: [{"featureType":"water","stylers":[{"color":"#0e171d"}]},{"featureType":"landscape","stylers":[{"color":"#1e303d"}]},{"featureType":"road","stylers":[{"color":"#1e303d"}]},{"featureType":"poi.park","stylers":[{"color":"#1e303d"}]},{"featureType":"transit","stylers":[{"color":"#182731"},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"color":"#f0c514"},{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1e303d"},{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#e77e24"},{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#94a5a6"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#e84c3c"}]},{"featureType":"poi","stylers":[{"color":"#e84c3c"},{"visibility":"off"}]}]
		};

		map = new google.maps.Map(document.getElementById("reditemCategoryDetailGmapCanvas"), myOptions);

		var message = document.getElementById("system-message-container");
		<?php if (!empty($this->item->items)) : ?>
			// var alert = document.createTextNode('<?php echo JText::_("FOUND_ITEM")?>');
			// message.appendChild(alert);
			<?php $markersCount = 0; ?>
			<?php foreach ($this->item->items as $item) : ?>
				<?php if (!empty($item->itemLatLng)) : ?>
					reditemCategoryDetailGmapAddMarker(
						map,
						"<?php echo $item->itemLatLng; ?>",
						"<?php echo $item->title; ?>",
						"gmap_labels",
						"",
						<?php echo $item->id; ?>,
						<?php echo json_encode($item->content); ?>
					);
					<?php $markersCount++; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ($markersCount > 0) : ?>
			map.fitBounds(mapBounds);
			<?php else : ?>
			map.panTo(centerPoint);
			<?php endif; ?>
		<?php else: ?>
			// var alert = document.createTextNode("<div class='alert notice'><?php echo JText::_('NOT_FOUND_ITEM')?></div>");
			// message.appendChild(alert);
		<?php endif; ?>

		<?php if (!empty($this->item->settingDistance)) : ?>
		reditemCategoryDetailGmapDrawCircle(centerPoint, <?php echo $this->item->settingDistance['distance']; ?> * 1000);
		<?php endif; ?>

		var options = {
            imagePath: '<?php echo JUri::root() ?>templates/genshop/images/googlemap/m'
        };

		markerCluster = new MarkerClusterer(map, markers, options);
	}

	function reditemCategoryDetailGmapMarkerRemake(lbl)
	{
		for (var i = 0; i < markers.length; i++)
		{
			if ((lbl != '') && (markers[i].labelContent == lbl))
			{
				markers[i].labelClass = 'gmap_active';
			}
			else
			{
				markers[i].labelClass = 'gmap_labels';
			}

			markers[i].label.setStyles();
		}
	}

	function reditemCategoryDetailGmapAddMarker(map, markerLatLng, markerTitle, markerClass, pinIcon, index, inforbox)
	{
		var markerLatLngArray = markerLatLng.split(',');

		var location = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);

		mapBounds.extend(location);
		var image = "<?php echo JUri::base();?>templates/genshop/images/googlemap/pin.png";
		var icon = {
			url: image
			//scaledSize: new google.maps.Size(iconSize, iconSize)
		}
		var marker = new MarkerWithLabel({
			position: location,
			draggable: false,
			raiseOnDrag: false,
			map: map,
			labelContent: markerTitle,
			labelAnchor: new google.maps.Point(28, 0),
			labelClass: markerClass,
			labelStyle: {opacity: 1.0},
			itemId: index,
			icon: icon
		});

		// Create information box
		var iw = new InfoBubble({
			content: inforbox
		});

		google.maps.event.addListener(marker, "click", function (e) {
			// reditemCategoryDetailGmapMarkerRemake(markerTitle);
			iw.open(map, marker);
		});

		google.maps.event.addListener(marker, "mouseover", function (e) {
			if (this.labelClass != 'gmap_active')
			{
				this.labelClass = 'gmap_active';
				this.label.setStyles();
			}
		});

		google.maps.event.addListener(marker, "mouseout", function (e) {
			if (this.labelClass != 'gmap_labels')
			{
				this.labelClass = 'gmap_labels';
				this.label.setStyles();
			}
		});

		markers.push(marker);
	}

	/**
	 * Method for draw a circle on map
	 *
	 * @param   GoogleMapPoint  locationPoint  Location point for center of circle
	 * @param   int             distanceValue  Distance radius for this circle (in meters)
	 *
	 * @return  void
	 */
	function reditemCategoryDetailGmapDrawCircle(locationPoint, distanceValue)
	{
		if (circleSetting != null)
		{
			// Clear old circle
			circleSetting.setMap(null);
		}

		circleOptions = {
			strokeColor: '#FFFFFF',
			strokeOpacity: 0.7,
			strokeWeight: 2,
			fillColor: '#808080',
			fillOpacity: 0.5,
			map: map,
			center: locationPoint,
			radius: distanceValue
		};

		// Add the circle to the map.
		circleSetting = new google.maps.Circle(circleOptions);
		map.fitBounds(circleSetting.getBounds());
		map.panTo(locationPoint);
	}

	/**
	 * Method for remove a circle on map
	 *
	 * @return  void
	 */
	function reditemCategoryDetailGmapRemoveCircle()
	{
		if (circleSetting != null)
		{
			// Clear old circle
			circleSetting.setMap(null);
		}

		map.fitBounds(mapBounds);
		map.panToBounds(mapBounds);
	}

	google.maps.event.addDomListener(window, 'load', reditemCategoryDetailGmapInitialize);
</script>

<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php if (empty($this->content)) : ?>
<p><?php echo JText::_('COM_REDITEM_ERROR_NO_CATEGORY_FOUND'); ?></p>
<?php else: ?>

<div class="reditem">
	<div class="reditem_categories_gmap">
		<form action="index.php" class="admin" id="reditemCategoryDetail" method="get" name="adminForm">
			<?php echo $this->content; ?>
			<div id="reditemCategoryDetailGmapCanvas" style="width: 100%;"></div>
			<input type="hidden" name="option" value="com_reditem" />
			<input type="hidden" name="view" value="categorydetailgmap" />
			<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="templateId" value="<?php echo $this->item->template->id; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	</div>
</div>
<?php endif; ?>
