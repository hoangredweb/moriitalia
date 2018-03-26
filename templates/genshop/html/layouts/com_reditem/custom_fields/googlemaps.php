<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode      = $displayData['fieldcode'];
$value          = $displayData['value'];
$defaultLatLong = $displayData['defaultlatlong'];
$attributes     = $displayData['attributes'];

// Add Google Maps script
ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
?>

<style type="text/css">
	.gmap_field
	{
		display: block;
		position: relative;
		margin: 20px 0px 0px 0px;
		width: 500px;;
	}
	.gmap_field .gmap_field_canvas
	{
		width: 100%;
		height: 350px;
	}
	.gmap_field .gmap_field_panel
	{
		position: absolute;
		top: 10px;
		left: 50%;
		margin-left: -30%;
		z-index: 5;
		background-color: #fff;
		padding: 5px;
		border: 1px solid #999;
		border-radius: 5px;
	}
</style>

<script type="text/javascript">
	var geocoder_<?php echo $fieldcode; ?>;
	var map_<?php echo $fieldcode; ?>;
	var marker_<?php echo $fieldcode; ?>;

	function initialize_<?php echo $fieldcode; ?>()
	{
		geocoder_<?php echo $fieldcode; ?> = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(<?php echo $defaultLatLong; ?>);
		var mapOptions = {
			zoom: 16,
			center: latlng,
			panControl: false,
			zoomControl: false,
			mapTypeControl: false,
			scaleControl: false,
			streetViewControl: false,
			overviewMapControl: false,
		}
		map_<?php echo $fieldcode; ?> = new google.maps.Map(document.getElementById("gmap_field_canvas_<?php echo $fieldcode; ?>"), mapOptions);

		<?php if ($value) : ?>
		marker = new google.maps.Marker({
				map: map_<?php echo $fieldcode; ?>,
				position: latlng
		});
		<?php endif; ?>
	}

	function codeAddress_<?php echo $fieldcode; ?>()
	{
		var address = document.getElementById("gmap_field_address_<?php echo $fieldcode; ?>").value;
		geocoder_<?php echo $fieldcode; ?>.geocode( { "address": address}, function(results, status){
			if (status == google.maps.GeocoderStatus.OK) {
				map_<?php echo $fieldcode; ?>.setCenter(results[0].geometry.location);
				marker_<?php echo $fieldcode; ?> = new google.maps.Marker({
					map: map_<?php echo $fieldcode; ?>,
					position: results[0].geometry.location
				});
				document.getElementById("<?php echo $fieldcode; ?>").value = results[0].geometry.location.lat() + "," + results[0].geometry.location.lng();
			}
		});
	}

	// Add fix code for load Goole map on tab.
	jQuery(document).ready(function(){
		initialize_<?php echo $fieldcode; ?>();
		
		jQuery("#additional-link").on("shown", function(){
			initialize_<?php echo $fieldcode; ?>();
		});
	});
</script>

<div class="gmap_field_input">
	<?php $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES); ?>
	<input type="text" name="cform[googlemaps][<?php echo $fieldcode; ?>]" id="<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?> />
</div>

<div class="gmap_field">
	<div class="gmap_field_panel input-append">
		<input id="gmap_field_address_<?php echo $fieldcode; ?>" type="text" class="input" value="" placeholder="Odense, Denmark" />
		<input type="button" class="btn"
			value="<?php echo JText::_('COM_REDITEM_CUSTOMFIELD_GOOGLEMAPS_GEOCODE'); ?>"
			onclick="codeAddress_<?php echo $fieldcode; ?>()" />
	</div>
	<div id="gmap_field_canvas_<?php echo $fieldcode; ?>" class="gmap_field_canvas"></div>
</div>
