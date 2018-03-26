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
 * @subpackage  CustomField.TextBox
 * @since       2.1.13
 *
 */
class ReditemCustomfieldText extends ReditemCustomfieldGeneric
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
		$params                    = new JRegistry($this->params);
		$this->isLimitGuideEnabled = $params->get('enable_limit_guide', '1');
		$this->limit               = (int) $params->get('limit', '100');
		$required                  = (boolean) $params->get('required');
		$attributeHtml             = '';
		$type                      = null;

		if ($required)
		{
			$attributes['class'] = 'required';
		}

		if ($this->isLimitGuideEnabled && ($this->type != 'youtube'))
		{
			$attributes['maxlength'] = $this->limit;
		}

		$attributes['placeholder'] = $this->name;

		// Prepare attributes
		if (!empty($attributes))
		{
			foreach ($attributes as $attribute => $attributeValue)
			{
				$attributeHtml .= ' ' . $attribute . '="' . $attributeValue . '"';
			}
		}

		$layoutData = array(
			'fieldcode'           => $this->fieldcode,
			'value'               => $this->value,
			'attributes'          => $attributeHtml,
			'isLimitGuideEnabled' => $this->isLimitGuideEnabled,
			'limit'               => $this->limit,
			'default'             => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.text.edit', $layoutData, array('component' => 'com_reditem'));
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
		$value   = '';

		if (empty($this->value))
		{
			$this->prepareData($item);
			$customFieldValues = $item->customfield_values;

			if (isset($customFieldValues[$field->fieldcode]))
			{
				$value = $customFieldValues[$field->fieldcode];
			}
		}
		else
		{
			$value = $this->value;
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
			$layoutFile    = 'customfields.text.view';
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
