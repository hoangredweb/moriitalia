<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  CustomField.User
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Renders a User Custom field
 *
 * @package     RedITEM.Libraries
 * @subpackage  CustomField.User
 * @since       2.1.13
 *
 */
class ReditemCustomfieldUser extends ReditemCustomfieldGeneric
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
		$values        = array();
		$data          = array();
		$attributeHtml = '';
		$fieldConfig   = new JRegistry($this->params);
		$dispatcher    = RFactory::getDispatcher();
		$userModel     = RModel::getAdminInstance('Users', array('ignore_request' => true), 'com_users');
		JPluginHelper::importPlugin('reditem');
		$type          = null;

		if ($fieldConfig->get('multiple'))
		{
			$attributes['multiple'] = "multiple";
		}

		if ($fieldConfig->get('required'))
		{
			$attributes['required'] = "required";
		}

		if ($fieldConfig->get('blocked'))
		{
			$userModel->setState('filter.state', 1);
		}
		else
		{
			$userModel->setState('filter.state', 0);
		}

		if (!empty($fieldConfig['usergroups']))
		{
			$usergroups = $fieldConfig['usergroups'];
			JArrayHelper::toInteger($usergroups);
			$userModel->setState('filter.groups', $usergroups);
		}

		$includeCurrentUser = (boolean) $fieldConfig->get('includeCurrentUser', true);
		$users              = $userModel->getItems();
		$curUser            = ReditemHelperSystem::getUser();

		$data[] = array(
			'text'     => JText::_('COM_REDITEM_SELECT'),
			'value'    => '',
			'selected' => empty($this->value)
		);

		// Process on value
		if (!empty($this->value))
		{
			$values = json_decode($this->value, true);
		}

		// Process on option list
		if (!empty($users))
		{
			foreach ($users as $user)
			{
				if ((!$includeCurrentUser) && ($user->id == $curUser->id))
				{
					continue;
				}

				$selected    = false;
				$optionText  = $user->name . ' @ ' . $user->username;
				$optionValue = $user->id;

				if (!empty($values) && in_array($optionValue, $values))
				{
					$selected = true;
				}

				$data[] = array(
					'text'     => $optionText,
					'value'    => $optionValue,
					'selected' => $selected
				);
			}
		}

		$dispatcher->trigger('OnUserOptionsPrepare', array(&$data));

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
			'default'    => $this->default
		);

		if (isset($this->type_id) && !empty($this->type_id))
		{
			$type = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($this->type_id);
		}

		return ReditemHelperLayout::render($type, 'customfields.user.edit', $layoutData, array('component' => 'com_reditem'));
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
				$value = json_decode($customFieldValues[$field->fieldcode]);
			}
		}
		else
		{
			$value = json_decode($this->value, true);
		}

		if (is_array($value))
		{
			$value = $value[0];
		}

		foreach ($matches as $match)
		{
			$user          = JFactory::getUser($value);
			$layoutData    = array('tag' => $field, 'value' => $user, 'item' => $item);
			$layoutFile    = 'customfields.user.view';
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
