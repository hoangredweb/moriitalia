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
RHelperAsset::load('googlemaps/markerwithlabel.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/infobubble.min.js', 'com_reditem');
RHelperAsset::load('googlemaps/markerclusterer.min.js', 'com_reditem');

RHelperAsset::load('reditem.categories.gmap.min.js', 'mod_reditem_categories_gmap');
RHelperAsset::load('reditem.categories.gmap.min.css', 'mod_reditem_categories_gmap');
?>

<!-- Initialize --> 
<script type="text/javascript">
	var countryName = "<?php echo $country; ?>"; // "United States of America";
	var uniqueInforWindow = <?php echo ($gmapInfoWindowUnique) ? 'true' : 'false'; ?>;
	var reditemGlobalInforWindow = new InfoBubble();

	google.load('visualization', '1', {'packages':['corechart', 'table', 'geomap']});

	function reditemGmap_initialize() 
	{
		var centerPoint = new google.maps.LatLng(<?php echo (isset($category->params['categoryLatLng'])) ? $category->params['categoryLatLng'] : $gmapLatlng; ?>);

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


		};

		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		<?php
		$uri = JURI::getInstance();
		$i = 0;
		?>


			<?php
			$markerClass = 'gmap_hidden';
			$uriParams = $uri->getQuery(true);
			$uri->setQuery($uriParams);
			?>

			<?php if (isset($category->params['categoryLatLng'])) : ?>
			reditemGmapAddMarker(
				map,
				"<?php echo $category->params['categoryLatLng']; ?>",
				"<?php echo $category->title; ?>",
				"<?php echo ''; ?>",
				<?php echo $category->id; ?>,
				"<?php echo JHTML::_('image', 'mod_reditem_categories_gmap/pin.png', '', null, true, true); ?>",
				"<?php echo JRoute::_($uri->toString()); ?>",
				<?php echo $i; ?>,
				''<?php //echo $category->inforbox; ?>);
			<?php
			$i++;
			?>
			<?php endif; ?>


		var ftoptions = {
			query: {
				from: FT_TableID,
				select: 'kml_4326',
				where: "name_0 = '" + countryName + "'"
			},
			suppressInfoWindows:true,
			styles: [{
				polygonOptions: {
					fillColor: '#DDDDDD',
					fillOpacity: 1.0,
					strokeColor: '#DDDDDD',
					strokeOpacity: 0.0,
					strokeWeight: 0,
				}
			}]
		};

		//var layer = new google.maps.FusionTablesLayer(ftoptions);
		//layer.setMap(map);

		map.mapTypes.set('map_style', styledMap);
		//map.setMapTypeId('map_style');

		<?php if ($gmapCluterer == '1') : ?>
			//var markerCluster = new MarkerClusterer(map, markers);
		<?php endif; ?>
	}
</script>

<div id="map_canvas" style="<?php echo $gmapWidth . ';' . $gmapHeight; ?>">
</div>
