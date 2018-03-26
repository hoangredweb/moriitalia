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
 * RedITEM related item list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.reditemlist
 *
 * @since       2.0
 */
class JFormFieldRedItemList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RedItemList';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$app         = JFactory::getApplication();
		$currentItem = (int) $this->element['exclude'];
		$db          = JFactory::getDbo();
		$query       = $db->getQuery(true);
		$currentOptions = parent::getOptions();

		$query->select(
			array(
				$db->qn('i.id', 'id'),
				$db->qn('i.title', 'title'),
				$db->qn('t.title', 'type')
			)
		)
			->from($db->qn('#__reditem_items', 'i'))
			->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('t.id'))
			->where($db->qn('i.published') . ' = 1')
			->where($db->qn('i.blocked') . ' = 0')
			->where($db->qn('i.id') . ' != ' . (int) $currentItem)
			->order($db->qn('i.title'));
		$db->setQuery($query);

		$items = $db->loadObjectList();

		// Clean up the options
		$options = array();

		if (!empty($items))
		{
			$groups = array();

			foreach ($items as $item)
			{
				if (!isset($groups[$item->type]))
				{
					$groups[$item->type] = array($item);
				}
				else
				{
					$groups[$item->type][] = $item;
				}
			}

			ksort($groups, SORT_ASC);

			foreach ($groups as $group => $items)
			{
				$options[] = JHtml::_(
					'select.optgroup',
					$group
				);

				foreach ($items as $item)
				{
					$options[] = JHtml::_('select.option', $item->id, $item->title);
				}

				$options[] = JHtml::_(
					'select.optgroup',
					$group
				);
			}
		}

		if (!empty($currentOptions))
		{
			$options = array_merge($currentOptions, $options);
		}

		return $options;
	}
}
