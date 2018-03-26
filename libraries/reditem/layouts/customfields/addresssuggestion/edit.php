<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$field      = $displayData['field'];
$attributes = $displayData['attributes'];
$attrString = '';
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;

if (!empty($field->default) && $isNew)
{
	$value = $field->default;
}

foreach ($attributes as $key => $val)
{
	$attrString .= $key . '="' . $val . '" ';
}
?>

<div class="reditem_customfield_addresssuggestion">
	<input type="text" name="jform[fields][addresssuggestion][<?php echo $field->fieldcode; ?>]" id="jform_fields_addresssuggestion_<?php echo $field->fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attrString; ?>/>
	<?php if ($field->isLimitGuideEnabled): ?>
		<span id="number_character_<?php echo $field->fieldcode; ?>"><?php echo JString::strlen($field->value) ?></span><span>/<?php echo $field->limit ?></span>
	<?php endif ?>
</div>

<?php if ($field->isLimitGuideEnabled): ?>
<script type='text/javascript'>
(function($){
	$(document).ready(function($){
		var reditem_address_suggestion_<?php echo $field->fieldcode; ?> = new google.maps.Geocoder();

		// Initial Google Maps API Autocomplete
		var reditem_address_suggestion_<?php echo $field->fieldcode; ?> = document.getElementById('jform_fields_addresssuggestion_<?php echo $field->fieldcode; ?>');

		var reditem_address_suggestion_option_<?php echo $field->fieldcode; ?> = {
			types: ['geocode'],
			componentRestrictions: {country: 'DK'}
		};

		var reditem_address_suggestion_autocomplete_<?php echo $field->fieldcode; ?> = new google.maps.places.Autocomplete(
			reditem_address_suggestion_<?php echo $field->fieldcode; ?>,
			reditem_address_suggestion_option_<?php echo $field->fieldcode; ?>
		);

		google.maps.event.addListener(reditem_address_suggestion_autocomplete_<?php echo $field->fieldcode; ?>, 'place_changed', function() {
			// Get the place details from the autocomplete object.
			var place = reditem_address_suggestion_autocomplete_<?php echo $field->fieldcode; ?>.getPlace();

			if (typeof place == "undefined")
				return false;

			if (typeof place.address_components == "undefined")
				return false;

			var streetNumber = '';
			var streetRoute = '';
			var city = '';
			var postalCode = '';
			var country = '';

			for (var i = 0; i < place.address_components.length; i++) {
				var addressType = place.address_components[i].types[0];

				switch (addressType) {
					case 'street_number':
						streetNumber = place.address_components[i]['short_name'];
						break;

					case 'route':
						streetRoute = place.address_components[i]['long_name'];
						break;

					case 'locality':
						city = place.address_components[i]['long_name'];
						break;

					case 'country':
						country = place.address_components[i]['long_name'];
						break;

					case 'postal_code':
						postalCode = place.address_components[i]['short_name'];
						break;

					default:
						break;
				}
			}

			var correctAddress = streetNumber + " " + streetRoute + ", " + postalCode + " " + city + ", " + country;

			$('#jform_fields_addresssuggestion_<?php echo $field->fieldcode; ?>').val(correctAddress);
		});

		$("#jform_fields_addresssuggestion_<?php echo $field->fieldcode; ?>").keyup(function(){
			$("#number_character_<?php echo $field->fieldcode; ?>").html($(this).val().length);
		});
	});
})(jQuery);
</script>
<?php endif ?>
