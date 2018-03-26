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
 * Select field listing all templates available in a specific section
 *
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @since       2.0
 */
class JFormFieldRedtemplate extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var                string
	 */
	protected $type = 'Redtemplate';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$options = array();

		// Get the templates based on a specificed 'section' defined in the xml form field.
		$section = '';

		if (!empty($this->element['section']))
		{
			$section = $this->element['section'];
		}

		$templates = $this->getTemplates($section);

		// Clean up the options
		$options = array();

		if (!empty($templates))
		{
			foreach ($templates as $template)
			{
				$options[] = JHtml::_('select.option', $template->value, $template->text);
			}
		}

		return $options;
	}

	/**
	 * Method to get field input
	 *
	 * @return  HTMLCode
	 */
	public function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		$placeholder = JText::_((string) ($this->element['placeholder']) ? $this->element['placeholder'] : $this->element['label']);

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		$attr .= $this->element['required'] ? ' required' : '';

		// Put placeholder
		$attr .= ' placeholder="' . $placeholder . '"';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	/**
	 * Method to get the list of templates of a specific section.
	 *
	 * @param   string  $section  the templates section
	 *
	 * @return  array  An array of templates.
	 */
	protected function getTemplates($section)
	{
		$typeId  = JFactory::getApplication()->getUserState('com_reditem.global.tid', '0');
		$options = parent::getOptions();

		if (!$section)
		{
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('t.id', 'value'))
			->select($db->qn('t.name', 'text'))
			->from($db->qn('#__reditem_templates', 't'))
			->where($db->qn('typecode') . '=' . $db->quote($section))
			->order($db->qn('t.name'));

		if ($typeId && !in_array($section, array('view_categorydetail', 'view_categorydetailgmap')))
		{
			$query->where($db->qn('type_id') . ' = ' . $db->quote($typeId));
		}

		$db->setQuery($query);
		$templates = $db->loadObjectList();
		$options   = array_merge($options, $templates);

		return $options;
	}
}
