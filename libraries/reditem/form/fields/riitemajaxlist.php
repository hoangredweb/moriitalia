<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Form.Fields
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

/**
 * RedITEM related item list
 *
 * @package     RedITEM.Backend
 * @subpackage  Form.Fields.RIItemAjaxList
 *
 * @since       2.2.0
 */
class JFormFieldRIItemAjaxList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'riitemajaxlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	public function getInput()
	{
		if (!empty($this->value))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(
				array (
					$db->qn('i.id', 'val'),
					'CONCAT (' . $db->qn('i.title') . ', \' (\',' . $db->qn('t.title') . ', \')\') AS ' . $db->qn('text')
				)
			)
				->from($db->qn('#__reditem_items', 'i'))
				->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('t.id'))
				->where($db->qn('i.published') . ' = 1')
				->where($db->qn('i.blocked') . ' = 0');

			if (is_array($this->value))
			{
				$query->where($db->qn('i.id') . ' IN (' . implode(',', $this->value) . ')');
				$query->order('FIELD(' . $db->qn('i.id') . ',' . implode(',', $this->value) . ')');
			}
			else
			{
				$query->where($db->qn('i.id') . ' = ' . $db->q($this->value));
			}

			$db->setQuery($query);
			$this->value = $db->loadAssocList();
		}

		$layoutData = array(
			'exclude' => $this->element['exclude'],
			'field'   => $this,
			'ajaxUrl' => JRoute::_('index.php?option=com_reditem&task=items.ajaxGetRelatedItems', false),
			'limit'   => (!empty($this->element['limit'])) ? $this->element['limit'] : 20
		);

		$layoutOptions = array(
			'component' => 'com_reditem',
			'debug'     => false
		);

		return ReditemHelperLayout::render(null, 'fields.item.list.ajax', $layoutData, $layoutOptions);
	}
}
