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

/**
 * Categories select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RedTypeTemplate
 *
 * @since       2.0
 */
class JFormFieldRedTypeTemplate extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RedTypeTemplate';

	/**
	 * Method to get the field input markup for a modal select form.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		$db = JFactory::getDBO();
		$options = array();
		$groups = array();

		// Default section is 'module_items' for redITEM Module Items templates
		$section = 'module_items';

		if (!empty($this->element['section']))
		{
			$section = $this->element['section'];
		}

		$query = $db->getQuery(true)
			->select(array($db->qn('ty.id'), $db->qn('ty.title')))
			->from($db->qn('#__reditem_types', 'ty'))
			->order($db->qn('ty.title'));
		$db->setQuery($query);

		$types = $db->loadObjectList();

		if ($section != 'view_search')
		{
			$groups[0] = array();
			$groups[0]['id'] = '';
			$groups[0]['text'] = '';
			$groups[0]['items'] = array(JHtml::_('select.option', '', '--' . JText::_('COM_REDITEM_USE_ASSIGNED_TEMPLATE') . '--'));
		}

		if ($types)
		{
			foreach ($types as $type)
			{
				$groups[$type->id] = array();
				$groups[$type->id]['id'] = $type->id . '__';
				$groups[$type->id]['text'] = $type->title;
				$groups[$type->id]['items'] = array();

				$query = $db->getQuery(true)
					->select(array($db->qn('tmpl.id'), $db->qn('tmpl.name')))
					->from($db->qn('#__reditem_templates', 'tmpl'))
					->where($db->qn('tmpl.published') . ' = ' . $db->quote('1'))

					->where($db->qn('tmpl.typecode') . ' = ' . $db->quote($section))
					->order($db->qn('tmpl.name'));

				if (!in_array(trim($section), array('view_categorydetail', 'view_categorydetailgmap')))
				{
					$query->where($db->qn('tmpl.type_id') . ' = ' . (int) $type->id);
				}

				$db->setQuery($query);
				$templates = $db->loadObjectList();

				if ($templates)
				{
					foreach ($templates as $template)
					{
						/*$options[] = JHTML::_('select.option', $template->id, $template->name);*/
						$groups[$type->id]['items'][] = JHtml::_('select.option', $template->id, $template->name);
					}
				}
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Prepare HTML code
		$html = array();

		// Compute the current selected values
		$selected = array($this->value);

		// Add a grouped list
		$html[] = JHtml::_(
			'select.groupedlist', $groups, $this->name,
			array('id' => $this->id, 'group.id' => 'id', 'list.attr' => $attr, 'list.select' => $selected)
		);

		return implode($html);
	}
}
