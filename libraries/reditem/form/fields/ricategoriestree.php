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
JLoader::import('reditem.library');

/**
 * RedITEM category tree select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RICategoriesTree
 *
 * @since       2.0
 */
class JFormFieldRICategoriesTree extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RICategoriesTree';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 * If need hide all childs categories - use in field option unuse="category_id" ,
	 * where category_id - field id parent category for all childs
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		$db     = JFactory::getDBO();
		$user   = RFactory::getUser();
		$ignore = $this->getAttribute('ignoreCats', array());
		$allow  = $this->getAttribute('allow_cids', null);

		// Get the categories list
		$query = $db->getQuery(true)
			->select('c.*')
			->from($db->qn('#__reditem_categories', 'c'))
			->where($db->qn('c.published') . ' = ' . $db->q(1))
			->where($db->qn('c.level') . ' > 0')
			->order($db->qn('c.lft'));

		if (!empty($ignore))
		{
			if (is_string($ignore))
			{
				$query->where($db->qn('c.id') . ' NOT IN (' . $ignore . ')');
			}
			elseif (is_array($ignore))
			{
				$query->where($db->qn('c.id') . ' NOT IN (' . implode(',', $ignore) . ')');
			}
		}

		if (!empty($allow))
		{
			if (is_string($allow))
			{
				$query->where($db->qn('c.id') . ' IN (' . $allow . ')');
			}
			elseif (is_array($allow))
			{
				$query->where($db->qn('c.id') . ' IN (' . implode(',', $allow) . ')');
			}
		}

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		// Prepare options list
		$options    = array();
		$options    = array_merge(parent::getOptions(), $options);
		$permission = $this->getAttribute('permission');

		foreach ($categories as $category)
		{
			if (!empty($permission) && !ReditemHelperACL::checkCategoryPermission($permission, $category->id, $user))
			{
				continue;
			}

			$optionText  = str_repeat(' -', $category->level - 1) . ' ' . $category->title;
			$optionValue = $category->id;
			$options[] = JHTML::_('select.option', $optionValue, $optionText);
		}

		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$attr .= $this->placeholder ? ' placeholder="' . (string) $this->element['placeholder'] . '"' : '';

		if ($this->multiple && !is_array($this->value))
		{
			if ($value = ReditemHelperCustomfield::isJsonValue($this->value))
			{
				$this->value = $value;
			}
			else
			{
				$this->value = explode(",", $this->value);
			}
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return JHTML::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
	}
}
