var ytPlayers = [];

(function($){
	// OnLoad initialize
	$(document).ready(function($){
		// Make youtube video div
		var yVideos = $('.youtube');

		if (yVideos.length > 0)
		{
			$.getScript('https://www.youtube.com/player_api', function(data, textStatus, jqxhr) {
				if (jqxhr.status == 200)
				{
					// Load the IFrame Player API code asynchronously.
					setTimeout(function (){
						yVideos.each(function(index){
							ytPlayers.push(youtube_create($(this)));
						});

						$('.reditem_youtube_modal').on('hidden', function() {
							var targetDiv = $(this).find('.reditem_youtube_video');
							var target    = $(targetDiv[0]).attr('id');

							$(ytPlayers).each(function (){
								if (this.id == target)
								{
									this.player.stopVideo();
								}
							})
						});
					}, 100);
				}
			});
		}

		// Apply AJAX call for items pagination
		$('#reditemItemsPagination').on('click', 'a', function(event){
			event.preventDefault();
			var form = $('#reditemCategoryDetail');
			$('#reditemCategoryDetail input[name="task"]').val('categorydetail.ajaxFilter');
			$('#reditemCategoryDetail input[name="'+$(this).attr('data-prefix')+'"]').val($(this).attr('data-limitstart'));

			reditemLoadAjaxData(form);
		});

		// Apply AJAX call for sub-categories pagination
		$('#reditemCategoriesPagination').on('click', 'a', function(event){
			event.preventDefault();
			var form = $('#reditemCategoryDetail');
			$('#reditemCategoryDetail input[name="task"]').val('categorydetail.ajaxCatExtraFilter');
			$('#reditemCategoryDetail input[name="'+$(this).attr('data-prefix')+'"]').val($(this).attr('data-limitstart'));

			reditemLoadAjaxCatExtraData(form);
		});
	});
})(jQuery);

/**
 * Method for run filter item
 *
 * @return  void
 */
function reditemFilterAjax()
{
	(function($){
		var form = $('#reditemCategoryDetail');
		$('#reditemCategoryDetail input[name="task"]').val('categorydetail.ajaxFilter');
		$('#reditemCategoryDetail input[name="'+$(this).attr('data-prefix')+'"]').val($(this).attr('data-limitstart'));

		reditemLoadAjaxData(form);
	})(jQuery);
}

/**
 * Method for get data from AJAX and replace in div
 *
 * @param   object  form  Form object
 *
 * @return  void
 */
function reditemLoadAjaxData(form)
{
	(function($){
		$('#reditemsItems').html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
		$.ajax({
			url: 'index.php?option=com_reditem&tmpl=component',
			method: "POST",
			data: form.serialize(),
			dataType: 'json',
			cache: false
		})
		.done(function (data){
			afterRunAjaxFilter(data);

			// Replace Items data
			$('#reditemsItems').empty().append(data.category);

			// Replace items pagination
			$('#reditemItemsPagination').empty().append(data.pagination);

			// Replace items count
			if (!(typeof data.itemsCount == 'undefined'))
			{
				if ($('#reditem-CategoryDetail-' + data.categoryId + '-itemsCount').length > 0) {
					$('#reditem-CategoryDetail-' + data.categoryId + '-itemsCount').empty().append(data.itemsCount);
				}
			}

			// Related Categories filter
			if (!(typeof data.relatedCategories == 'undefined'))
			{
				for (parentCat in data.relatedCategories)
				{
					var relatedCategories = data.relatedCategories[parentCat];
					var selectbox = $('#filter_related_' + parentCat);
					var selectAllText = selectbox.find("option[value='']").text();

					if ((selectbox.length > 0) && (!$.isEmptyObject(relatedCategories)))
					{
						selectbox.empty();

						selectbox.append("<option value=''>" + selectAllText + "</option>");

						for (relatedCat in relatedCategories)
						{
							var relatedCategory = relatedCategories[relatedCat];
							var attr = "";

							if (relatedCategory.filter == true)
							{
								attr = " selected='selected'";
							}

							selectbox.append("<option value='"+ relatedCategory.id +"'" + attr + ">" + relatedCategory.title + "</option>");
						}

						selectbox.select2();
					}
				}
			}
			else
			{
				$('select.reditemFilterRelated').each(function(index){
					var selectbox = $(this);
					var tmp = $('#' + selectbox.attr('id') + '_tmp');

					if (tmp.length > 0)
					{
						selectbox.empty();

						tmp.find('option').each(function(){
							selectbox.append(new Option($(this).text(), $(this).val()));
 						})

 						selectbox.select2();
					}
				});
			}

			$('img[src^="holder.js"]').addClass('reditem-holder');
			Holder.run({
				'images' : '.reditem-holder'
			});

			afterReditemLoadAjaxData();
		});
	})(jQuery);
}

/**
 * Function for create youtube block.
 *
 * @param obj
 *
 * @returns {{id: *, player: YT.Player}}
 */
function youtube_create(obj)
{
	var video     = obj.attr('youtube');
	var href      = obj.attr('href');
	var split     = href.split('_');
	var target    = 'reditem_youtube_video_' + split[3];
	var player    = new YT.Player(target, {
		height  : '390',
		width   : '640',
		videoId : video
	});

	jQuery('<img/>', {
		src   : 'http://i.ytimg.com/vi/' + video + '/hqdefault.jpg',
		style : obj.attr('style'),
		class : 'thumb'
	}).appendTo(obj);
	jQuery('<div/>', {
		class : 'play'
	}).appendTo(obj);

	return {'id' : target, 'player' : player};
}

/**
 * Function create map for items custom fields
 *
 * @param  field  Field selector to use for creating the map.
 */
function reditem_customfield_googlemaps_init(field)
{
	var mapid = jQuery(field).find('input[id="mapid"]').val();
	var markerLatLng = jQuery(field).find('input[id="maplatlng"]').val();
	var infor = jQuery(field).find('input[id="mapinfor"]').val();

	var markerLatLngArray = markerLatLng.split(',');
	var latlng = new google.maps.LatLng(markerLatLngArray[0], markerLatLngArray[1]);

	var mapOptions = {
		zoom: 8,
		center: latlng,
		panControl: false,
		zoomControl: false,
		mapTypeControl: false,
		scaleControl: false,
		streetViewControl: false,
		overviewMapControl: false
	};

	var map = new google.maps.Map(document.getElementById(mapid), mapOptions);

	var marker = new google.maps.Marker({
		map: map,
		position: latlng
	});

	google.maps.event.addListener(marker, "click", function (e) {
		var infowindow = new google.maps.InfoWindow({
			content: infor
		});

		infowindow.open(map, this);
	});
}

/**
 * Method for get data from AJAX and replace in div
 *
 * @param   object  form  Form object
 *
 * @return  void
 */
function reditemLoadAjaxCatExtraData(form)
{
	(function($){
		$('#reditemCategories').html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');

		$.ajax({
			url: 'index.php?option=com_reditem&tmpl=component',
			method: "POST",
			data: form.serialize(),
			cache: false
		})
		.done(function (data){

			// Replace sub-categories data
			$('#reditemCategories').empty().append(data.content);

			// Replace sub-categories pagination
			$('#reditemCategoriesPagination').empty().append(data.pagination);

			$('img[src^="holder.js"]').addClass('reditem-holder');
			Holder.run({
				'images' : '.reditem-holder'
			});
		});
	})(jQuery);
}

/**
 * Method for process on Search view
 *
 * @return  void
 */
function reditemSearchCallback()
{
	(function($){
		document.getElementById('reditemSearch').submit();
	})(jQuery);
}

/**
 * Method for run save search engine
 *
 * @return  void
 */
function reditemSaveFilter()
{
	(function($){
		if ($('#reditemCategoryDetail').length > 0) {
			var form = document.getElementById("reditemCategoryDetail");
		}
		else if ($('#reditemSearch').length > 0) {
			var form = document.getElementById("reditemSearch");
		}
		else {
			return false;
		}
		form.view.value = '';
		form.task.value = 'searchengine.ajaxSaveFilter';
		form.submit();
	})(jQuery);
}

/**
 * Check if element is in viewport.
 *
 * @param e
 *
 * @returns {boolean}
 */
function isElementInViewport (e)
{
    var win = jQuery(window);
	var el  = jQuery(e + ':visible');

	if (el.length)
	{
		var ele      = el.get(0);
		var vpWidth  = win.width();
		var vpHeight = win.height();

		if (typeof ele.getBoundingClientRect === 'function')
		{
			// Use this native browser method, if available.
			var rec = ele.getBoundingClientRect(),
				tViz = rec.top    >= 0 && rec.top    <  vpHeight,
				bViz = rec.bottom >  0 && rec.bottom <= vpHeight,
				lViz = rec.left   >= 0 && rec.left   <  vpWidth,
				rViz = rec.right  >  0 && rec.right  <= vpWidth;
			var vVisible = tViz || bViz;
			var hVisible = lViz || rViz;

			return vVisible && hVisible;
		}
		else
		{
			var viewTop = win.scrollTop(),
				viewBottom = viewTop + vpHeight,
				viewLeft   = win.scrollLeft(),
				viewRight  = viewLeft + vpWidth,
				offset     = el.offset(),
				_top       = offset.top,
				_bottom    = _top + el.height(),
				_left      = offset.left,
				_right     = _left + el.width();

			return (_top <= viewBottom) && (_bottom >= viewTop) && (_left <= viewRight) && (_right >= viewLeft);
		}
	}

    return false;
}

/**
 * Image crop function for cropping custom field images.
 * @param name
 * @param fieldcode
 * @param path
 * @param src
 * @param preview
 * @param modalPreview
 */
function reditemCropImage(name, fieldcode, path, src, preview, modalPreview)
{
	if (confirm(Joomla.JText._('COM_REDITEM_FEATURE_CROP_CONFIRM')))
	{
		var image      = jQuery('#modal-' + fieldcode + '-preview');
		var crop       = image.cropper('getCropBoxData');
		var canvas     = image.cropper('getCanvasData');
		var widthRate  = canvas.naturalWidth / canvas.width;
		var heightRate = canvas.naturalHeight / canvas.height;
		var left       = (crop.left - canvas.left) * widthRate;
		var top        = (crop.top - canvas.top) * heightRate;
		var width      = crop.width * widthRate;
		var height     = crop.height * widthRate;

		jQuery.ajax({
			url  : 'index.php?option=com_reditem&task=field.ajaxCropImage',
			data : {
				left       : left,
				top        : top,
				width      : width,
				height     : height,
				path       : path,
				image_name : name
			}
		}).done(function (res){
			if (res)
			{
				var uq = new Date().getTime();
				jQuery('#' + fieldcode + '-modal').modal('hide');
				jQuery(preview).attr('src', src + '?t=' + uq);
				jQuery(modalPreview).attr('src', src + '?t=' + uq);
			}
		});
	}
}

/**
 * Hook method for fire after run ajax filter
 *
 * @param   json  data  Data in JSON format
 *
 * @return  void
 */
function afterRunAjaxFilter(data) {}

/**
 * Hook method to fire after date update on ajax filtering.
 *
 * @return  void
 */
function afterReditemLoadAjaxData() {}
