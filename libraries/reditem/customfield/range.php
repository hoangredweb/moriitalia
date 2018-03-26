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
 * Renders a Range number Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.Range
 * @since       2.1.13
 *
 */
class ReditemCustomfieldRange extends ReditemCustomfieldGeneric
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
		$data                = array();
		$attributes['class'] = 'validate-numeric';
		$config              = new JRegistry($this->params);
		$attributeHtml       = '';
		$type                = null;

		if ($config->get('required', false))
		{
			$attributes['class'] .= 'required';
		}

		// Prepare config data
		$config = new JRegistry($this->params);

		$data['min']               = (int) $config->get('min', 1);
		$data['max']               = (int) $config->get('max', 100);
		$data['step']              = (int) $config->get('step', 1);
		$data['tooltip']           = $config->get('tooltipText', '');
		$data['tooltipDisplay']    = $config->get('tooltipDisplay', '');
		$data['pointStyle']        = $config->get('pointStyle', 'round');
		$data['backgroundColor']   = $config->get('backgroundColor', '#f5f5f5');
		$data['selectionColor']    = $config->get('selectionColor', '#da4f49');
		$data['sliderOrientation'] = $config->get('sliderOrientation', '');

		// Prepare value
		$value = (int) $config->get('default', 20);

		if (!empty($attributes))
		{
			foreach ($attributes as $attrKey => $attrValue)
			{
				$attributeHtml .= ' ' . $attrKey . '="' . $attrValue . '"';
			}
		}

		if (!empty($this->value))
		{
			$value = (int) $this->value;
		}

		$layoutData = array(
			'fieldcode'  => $this->fieldcode,
			'value'      => $value,
			'data'       => $data,
			'attributes' => $attributeHtml
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.range.edit', $layoutData, array('component' => 'com_reditem'));
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
			$value             = '';

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
			$layoutFile    = 'customfields.range.view';
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
