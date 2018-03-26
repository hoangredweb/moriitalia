var map = null;
var geoXml = null;
var geoXmlDoc = null;
var myOptions = null;
var mapCenter = null;
var gpolygons = [];
var geocoder = null;
var maptype = google.maps.MapTypeId.ROADMAP;
var markers = new Array();

// Fusion Table data ID
var FT_TableID = 420419;

var styles = [
		{
			"stylers": [{
				"visibility": "off"
			}]
		},
		{
			"featureType": "water",
			"stylers": [
				{ "visibility": "on" },
				{ "color": "#ffffff" }
			]
		},
		{
			"featureType": "landscape",
			"stylers": [
				{ "visibility": "on" },
			{ "color": "#ffffff" }
			]
		}
	];

var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});

function reditemGmapRemake(lbl)
{
	for (var i = 0; i < markers.length; i++)
	{
		if ((lbl != '') && (markers[i].labelContent == lbl))
		{
			markers[i].labelClass = 'gmap_active';
		}
		else
		{
			markers[i].labelClass = 'gmap_hidden';
		}

		markers[i].label.setStyles();
	}
}

function reditemGmapAddMarker(map, markerLatLng, markerTitle, markerClass, categoryId, pinIcon, url, index, inforbox)
{
	var markerLatLngArray = markerLatLng.split(',');

	var location = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);

	var _marker = new MarkerWithLabel({
		position: location,
		draggable: false,
		raiseOnDrag: false,
		map: map,
		labelContent: markerTitle,
		labelAnchor: new google.maps.Point(28, 0),
		labelClass: markerClass,
		labelStyle: {opacity: 1.0},
		icon: pinIcon
	});

	google.maps.event.addListener(_marker, "click", function (e) {

		reditemGmapRemake(markerTitle);

		// Check if AJAX function is here
		if (typeof reditemLoadAjaxData == 'function')
		{
			var form = document.getElementById("reditemCategoryDetail");

			// Check if form reditemCategoryDetail exists?
			if (typeof form == 'object')
			{
				// Check if filter exists
				if (typeof form.elements['filter_category[' + parentCategory + ']'] == 'object')
				{
					var selectFilter = form.elements['filter_category[' + parentCategory + ']'];

					jQuery(selectFilter).select2('val', categoryId);
				}

				form.task.value = 'categorydetail.ajaxFilter';
				var url = 'index.php?' + jQuery(form).serialize();
				reditemLoadAjaxData(url);
			}
		}
		else
		{
			if (uniqueInforWindow)
			{
				var iw = new InfoBubble({
					content: inforbox
				});
				iw.open(map, this);
			}
			else
			{
				reditemGlobalInforWindow.setContent(inforbox);
				reditemGlobalInforWindow.open(map, this);
			}
		}
	});

	google.maps.event.addListener(_marker, "mouseover", function (e) {

		if (this.labelClass != 'gmap_active')
		{
			this.labelClass = 'gmap_labels';
			this.label.setStyles();
		}
	});

	google.maps.event.addListener(_marker, "mouseout", function (e) {
		if (this.labelClass != 'gmap_active')
		{
			this.labelClass = 'gmap_hidden';
			this.label.setStyles();
		}
	});

	markers[index] = _marker;
}

jQuery(function ($){

	reditemGmap_initialize();

	// Override function for mod_reditem_categories/reditemFilterAjax function
	if (typeof reditemFilterAjax == 'function')
	{
		reditemFilterAjax = function()
		{
			var form = document.getElementById("reditemCategoryDetail");

			// Check if form reditemCategoryDetail exists?
			if (typeof form == 'object')
			{
				// Check if filter exists
				if (typeof form.elements['filter_category[' + parentCategory + ']'] == 'object')
				{
					var selectFilter = form.elements['filter_category[' + parentCategory + ']'];
					var title = '';

					if (jQuery(selectFilter).select2('val') != '')
					{
						title = jQuery(selectFilter).select2('data').text.trim().replace('- ', '');
					}

					reditemGmapRemake(title);
				}

				form.task.value = 'categorydetail.ajaxFilter';
				var url = 'index.php?' + jQuery(form).serialize();
				
				reditemLoadAjaxData(url);
			}
		}
	}
});