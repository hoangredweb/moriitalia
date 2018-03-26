<?php
/**
 * @package     RedITEM.Libarries
 * @subpackage  CustomField
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Renders a Text Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Color
 * @since       2.1.13
 *
 */
class ReditemCustomfieldColor extends ReditemCustomfieldGeneric
{
	/**
	 * Returns the html code for the form element
	 *
	 * @param   array  $attributes  HTML element attributes array.
	 *
	 * @return string
	 */
	public function render($attributes = array())
	{
		$params                     = new JRegistry($this->params);
		$required                   = (boolean) $params->get('required');
		$attributes['autofocus']    = (boolean) $params->get('autofocus');
		$attributes['autocomplete'] = (boolean) $params->get('autocomplete');
		$attributes['readonly']     = (boolean) $params->get('readonly');
		$attributes['class']        = $params->get('class');
		$type                       = null;

		$layoutData = array(
			'id'        => $this->divId,
			'name'      => $this->name,
			'fieldcode' => $this->fieldcode,
			'value'     => $this->value,
			'element'   => $attributes,
			'required'  => $required,
			'default'   => $this->default
		);

		if (!empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.color.edit', $layoutData, array('component' => 'com_reditem'));
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
		$value   = $this->value;

		if (preg_match_all('/{' . $field->fieldcode . '_value[^}]*}/i', $content, $matches) <= 0)
		{
			return false;
		}

		$matches = $matches[0];

		if (empty($value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = $customFieldValues[$field->fieldcode];
			}
		}

		foreach ($matches as $match)
		{
			$textParams = explode('|', $match);
			$tmpValue   = $value;

			if (isset($textParams[1]))
			{
				$tmpValue = JHTML::_('string.truncate', $tmpValue, (int) $textParams[1], true, false);
			}

			$layoutData    = array('tag' => $field, 'value' => $tmpValue, 'item' => $item);
			$layoutFile    = 'customfields.color.view';
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
