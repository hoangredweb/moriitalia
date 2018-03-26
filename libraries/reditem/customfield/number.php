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
 * Renders a Number Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Number
 * @since       2.1.13
 *
 */
class ReditemCustomfieldNumber extends ReditemCustomfieldGeneric
{
	/**
	 * Column data configuration
	 *
	 * @var  string
	 */
	public $sqlColumnData = ' DOUBLE NOT NULL DEFAULT 0.0';

	/**
	 * Returns the html code for the form element
	 *
	 * @param   array  $attributes  HTML element attributes array.
	 *
	 * @return string
	 */
	public function render($attributes = array())
	{
		$attributes['class'] = 'validate-numeric';
		$attributeHtml       = '';
		$config              = new JRegistry($this->params);
		$required            = (boolean) $config->get('required');
		$type                = null;

		if ($required)
		{
			$attributes['class'] .= ' required';
		}

		$attributes['placeholder'] = $this->name;

		if (!empty($attributes))
		{
			foreach ($attributes as $attrKey => $attrValue)
			{
				$attributeHtml .= ' ' . $attrKey . '="' . $attrValue . '"';
			}
		}

		$layoutData = array(
			'attributes'	=> $attributeHtml,
			'fieldcode'		=> $this->fieldcode,
			'value'			=> $this->value,
			'default'       => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.number.edit', $layoutData, array('component' => 'com_reditem'));
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

		$fieldParams       = new JRegistry($field->params);
		$decimalSepatator  = $fieldParams->get('number_decimal_sepatator', '.');
		$thousandSeparator = $fieldParams->get('number_thousand_separator', ',');
		$numberDecimals    = $fieldParams->get('number_number_decimals', 2);

		if (empty($this->value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;
			$value             = 0;

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = $customFieldValues[$field->fieldcode];
				$value = number_format(floatval($value), $numberDecimals, $decimalSepatator, $thousandSeparator);
			}
		}
		else
		{
			$value = number_format(floatval($this->value), $numberDecimals, $decimalSepatator, $thousandSeparator);
		}

		$layoutData    = array('tag' => $field, 'value' => $value, 'item' => $item);
		$layoutFile    = 'customfields.number.view';
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
