<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Element
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Renders Kerry express district List
 *
 * @since  1.1
 */
class JFormFieldKerryward extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'kerryward';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		JFactory::getDocument()->addScriptDeclaration("
			jQuery(document).ready(function(){
				kerrySelectWard(jQuery('#jform_params_district_code'));
			});
			function kerrySelectWard(el) {
				var district = jQuery(el).val();
				jQuery.ajax({
			        type: 'POST',
			        data: {district: district},
			        url: '" . JUri::root() . "index.php?option=com_ajax&plugin=GetKerryWard&group=redshop_checkout&format=raw',
			        success: function(data) {
			        	jQuery('select#jform_params_ward_code').html('');
			        	jQuery('select#jform_params_ward_code').append(data);
			   			jQuery('select#jform_params_ward_code').val('" . $this->value . "');
			        	jQuery('select#jform_params_ward_code').trigger('liszt:updated');
			        }
			    });
			}"
		);

		return parent::getInput();
	}
}
