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
 * Renders a Multi Textarea field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Task
 * @since       2.1.13
 *
 */
class ReditemCustomfieldMultitextarea extends ReditemCustomfieldGeneric
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
		$params        = new JRegistry($this->params);
		$required      = (boolean) $params->get('required');
		$type          = null;

		if (!empty($this->value))
		{
			$values = json_decode($this->value);
		}

		if (!empty($values))
		{
			foreach ($values as $value)
			{
				$textarea   = explode('|', trim($value));
				$userId 	= $textarea[0];
				$content  	= $textarea[1];

				$data[] = array(
					'userId'  => $userId,
					'content' => $content
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

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		$layoutData = array(
			'fieldcode'  => $this->fieldcode,
			'data'       => $data,
			'attributes' => $attributeHtml,
			'name'       => $this->name,
			'type'       => $type,
			'value'      => $this->value,
			'required'   => $required,
			'default'    => $this->default
		);

		return ReditemHelperLayout::render($type, 'customfields.multitextarea.edit', $layoutData, array('component' => 'com_reditem'));
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

		$matches = array();

		if (preg_match_all('/{' . $field->fieldcode . '_value[^}]*}/i', $content, $matches) <= 0)
		{
			return false;
		}

		$matches = $matches[0];

		if (empty($this->value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;
			$data              = array();

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = json_decode($customFieldValues[$field->fieldcode]);

				foreach ($value as $val)
				{
					$data[] = explode('|', $val);
				}
			}
		}
		else
		{
			$value = json_decode($this->value);

			foreach ($value as $val)
			{
				$data[] = explode('|', $val);
			}
		}

		foreach ($matches as $match)
		{
			$layoutData    = array('tag' => $field, 'data' => $data, 'item' => $item);
			$layoutFile    = 'customfields.multitextarea.view';
			$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

			if (isset($item->type) && is_object($item->type))
			{
				$contentHtml = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);
			}
			else
			{
				$contentHtml = ReditemHelperLayout::render(null, $layoutFile, $layoutData, $layoutOptions);
			}

			$content = str_replace($match, $contentHtml, $content);
		}

		return true;
	}
}
