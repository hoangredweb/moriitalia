<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  CustomField
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Renders a Google Maps Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.GoogleMaps
 * @since       2.1.13
 *
 */
class ReditemCustomfieldGooglemaps extends ReditemCustomfieldGeneric
{
	/**
	 * returns the html code for the form element
	 *
	 * @param   array  $attributes  HTML element attributes array.
	 *
	 * @return string
	 */
	public function render($attributes = array())
	{
		// Default value is Denmark
		$defaultLagLng = JText::_('COM_REDITEM_ITEM_LATITUDE_AND_LONGTITUDE_NUMBER_DEFAULT');
		$type          = null;

		if ($this->value)
		{
			$defaultLagLng = $this->value;
		}

		$config   = new JRegistry($this->params);
		$required = (boolean) $config->get('required');

		if ($required)
		{
			$attributes['class'] = "required";
		}

		$attributes['placeholder'] = $this->name;

		$layoutData = array(
			'fieldcode'      => $this->fieldcode,
			'value'          => $this->value,
			'defaultlatlong' => $defaultLagLng,
			'attributes'     => $attributes,
			'default'        => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.googlemaps.edit', $layoutData, array('component' => 'com_reditem'));
	}

	/**
	 * Method for replace value tag of customfield
	 *
	 * @param   string  &$content  HTML content
	 * @param   object  $field     Field object of customfield
	 * @param   object  $item      Item object
	 *
	 * @return  boolean            True on success. False otherwise.
	 */
	public function replaceValueTag(&$content, $field, $item)
	{
		if (empty($content) || empty($field) || !is_object($field) || empty($item))
		{
			return false;
		}

		$tagString = '{' . $field->fieldcode . '_value}';

		if (strpos($content, $tagString) === false)
		{
			return false;
		}

		if (empty($this->value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;

			$value = '';

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = $customFieldValues[$field->fieldcode];
			}
		}
		else
		{
			$value = $this->value;
		}

		$layoutData = array(
			'tag'          => $field,
			'value'        => $value,
			'reditemId'    => $item->id,
			'reditemTitle' => $item->title,
			'item'         => $item
		);
		$layoutFile    = 'customfields.googlemaps.view';
		$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

		if (isset($item->type) && is_object($item->type))
		{
			$contentHtml = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);
		}
		else
		{
			$contentHtml = ReditemHelperLayout::render(null, $layoutFile, $layoutData, $layoutOptions);
		}

		$content = str_replace($tagString, $contentHtml, $content);

		return true;
	}
}
