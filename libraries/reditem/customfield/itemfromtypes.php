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
 * Renders a Item From Type custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.ItemFromType
 * @since       2.1.13
 *
 */
class ReditemCustomfieldItemFromTypes extends ReditemCustomfieldGeneric
{
	/**
	 * returns the html code for the form element
	 *
	 * @param   array  $attributes  HTML element attributes array.
	 *
	 * @return  string
	 */
	public function render($attributes = array())
	{
		$db            = RFactory::getDbo();
		$data          = array();
		$values        = array();
		$attributeHtml = '';
		$fieldConfig   = new JRegistry($this->params);
		$type          = null;

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

		if (!empty($attributes))
		{
			foreach ($attributes as $attrKey => $attrValue)
			{
				$attributeHtml .= ' ' . $attrKey . '="' . $attrValue . '"';
			}
		}

		// Process on value
		if (!empty($this->value))
		{
			$values = json_decode($this->value, true);
		}

		$ids = array();

		foreach ($values as $val)
		{
			$temp = explode('|', $val);

			if (isset($temp[1]))
			{
				$ids[] = (int) $temp[1];
			}
		}

		// Prepare items list from specific type
		$types = $fieldConfig->get('types');

		$query = $db->getQuery(true)
			->select(
				array(
					$db->qn('i.id'),
					$db->qn('i.title'),
					$db->qn('t.title', 'group'),
					$db->qn('t.table_name', 'table')
				)
			)
			->from($db->qn('#__reditem_items', 'i'))
			->leftJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('t.id'))
			->where($db->qn('i.type_id') . ' IN (' . implode(',', $types) . ')')
			->where($db->qn('i.published') . ' = 1')
			->where($db->qn('i.blocked') . ' = 0');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Process on option list
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$selected = false;

				if (!empty($ids) && in_array($item->id, $ids))
				{
					$selected = true;
				}

				$data[$item->group][] = array(
					'text'     => $item->title,
					'value'    => $item->table . '|' . $item->id,
					'selected' => $selected
				);
			}
		}

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		$default = ReditemHelperType::getTableName(ReditemHelperItem::getTypeIdByItemId($this->default), false) . '|' . $this->default;

		$layoutData = array(
			'fieldcode'  => $this->fieldcode,
			'data'       => $data,
			'attributes' => $attributeHtml,
			'default'    => $default
		);

		return ReditemHelperLayout::render($type, 'customfields.itemfromtypes.edit', $layoutData, array('component' => 'com_reditem'));
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
			$tmpValue   = json_decode($value);
			$templateId = 0;

			if (isset($textParams[1]))
			{
				$templateId = (int) $textParams[1];
			}

			$layoutData    = array('tag' => $field, 'value' => $tmpValue, 'item' => $item, 'templateId' => $templateId);
			$layoutFile    = 'customfields.itemfromtypes.view';
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
