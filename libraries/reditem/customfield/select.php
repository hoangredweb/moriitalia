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
 * Renders a Textbox Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Select
 * @since       2.1.13
 *
 */
class ReditemCustomfieldSelect extends ReditemCustomfieldGeneric
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
		$data          = array();
		$values        = array();
		$attributeHtml = '';
		$fieldConfig   = new JRegistry($this->params);
		$type          = null;

		if (!empty($this->value))
		{
			$values = json_decode($this->value);
		}

		// Required option
		if ($fieldConfig->get('required', false))
		{
			$attributes['class'] = 'required';
		}

		// Multiple option
		if ($fieldConfig->get('multiple', true))
		{
			$attributes['multiple'] = 'true';
		}

		// Prepare options list
		$options = trim($this->options);

		if (!empty($options))
		{
			$options = explode("\n", $options);

			// Sort options if needed
			if ($fieldConfig->get('sort_options', 1))
			{
				sort($options);
			}

			foreach ($options as $option)
			{
				$option        = explode('|', trim($option));
				$optionValue   = $option[0];
				$optionText    = $optionValue;
				$optionChecked = false;

				if (isset($option[1]))
				{
					$optionText = $option[1];
				}

				if (!empty($values) && in_array($optionValue, json_decode($this->value)))
				{
					$optionChecked = true;
				}

				$data[] = array(
					'text'     => $optionText,
					'value'    => $optionValue,
					'selected' => $optionChecked
				);
			}
		}

		// Prepare attributes
		if (!empty($attributes))
		{
			foreach ($attributes as $attribute => $attributeValue)
			{
				$attributeHtml .= ' ' . $attribute . '="' . $attributeValue . '"';
			}
		}

		$layoutData = array(
			'fieldcode'  => $this->fieldcode,
			'data'       => $data,
			'attributes' => $attributeHtml,
			'name'       => $this->name,
			'default'    => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.select.edit', $layoutData, array('component' => 'com_reditem'));
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

		$optionsData  = array();
		$fieldOptions = array();

		// Prepare for options list of field
		$temporaryFieldOptions = trim($field->options);
		$temporaryFieldOptions = explode("\n", $temporaryFieldOptions);

		foreach ($temporaryFieldOptions as $temporaryOption)
		{
			$temporaryOption = explode('|', trim($temporaryOption));
			$option          = new stdClass;
			$option->value   = $temporaryOption[0];
			$option->text    = $temporaryOption[0];

			if (isset($temporaryOption[1]))
			{
				$option->text = $temporaryOption[1];
			}

			$fieldOptions[$option->value] = $option;
		}

		if (empty($this->value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$itemValues     = $customFieldValues[$field->fieldcode];
				$itemsValueJSON = json_decode($itemValues);

				foreach ($itemsValueJSON as $itemValueJSON)
				{
					if (isset($fieldOptions[$itemValues]))
					{
						$optionsData[] = $fieldOptions[$itemValues];
					}
					elseif (isset($fieldOptions[$itemValueJSON]))
					{
						$optionsData[] = $fieldOptions[$itemValueJSON];
					}
				}
			}
		}
		else
		{
			$itemsValueJSON = json_decode($this->value);

			foreach ($itemsValueJSON as $itemValueJSON)
			{
				if (isset($fieldOptions[$this->value]))
				{
					$optionsData[] = $fieldOptions[$this->value];
				}
				elseif (isset($fieldOptions[$itemValueJSON]))
				{
					$optionsData[] = $fieldOptions[$itemValueJSON];
				}
			}
		}

		$layoutData    = array('tag' => $field, 'value' => $optionsData, 'item' => $item);
		$layoutFile    = 'customfields.select.view';
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
