<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * RedITEM type select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RITypeList
 *
 * @since       2.0
 */
class JFormFieldRIFieldTypes extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RIFieldTypes';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$options = array();
		$currentOptions = parent::getOptions();

		// Prepare value list
		$items = array(
			'checkbox'          => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_CHECKBOX'),
			'date'              => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_DATE'),
			'daterange'         => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_DATERANGE'),
			'editor'            => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_EDITOR'),
			'gallery'           => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_GALLERY'),
			'googlemaps'        => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_GOOGLEMAPS'),
			'file'              => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_FILE'),
			'image'             => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_IMAGE'),
			'number'            => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_NUMBER'),
			'radio'             => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_RADIO'),
			'range'             => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_RANGE'),
			'select'            => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_SELECT'),
			'text'              => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_TEXT'),
			'textarea'          => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_TEXTAREA'),
			'url'               => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_URL'),
			'youtube'           => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_YOUTUBE'),
			'user'              => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_USER'),
			'itemfromtypes'     => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_ITEMFROMTYPES'),
			'tasklist'          => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_TASKLIST'),
			'multitextarea'     => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_MULTITEXTAREA'),
			'color'             => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_COLOR'),
			'addresssuggestion' => JText::_('COM_REDITEM_FIELD_SELECT_TYPE_OPTION_ADDRESSSUGGESTION')
		);

		asort($items);

		// Create options list
		foreach ($items as $value => $text)
		{
			$options[] = JHtml::_('select.option', $value, $text);
		}

		// If options has set in XML file, merge this
		if (!empty($currentOptions))
		{
			$options = array_merge($currentOptions, $options);
		}

		return $options;
	}
}
