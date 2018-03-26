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
 * RedITEM User Groups list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.reditemusergroups
 *
 * @since       2.1
 */
class JFormFieldRedItemUserGroups extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RedItemUserGroups';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$db      = JFactory::getDbo();
		$options = array();

		$query = $db->getQuery(true)
			->select($db->qn(array('id', 'parent_id', 'title')))
			->from($db->qn('#__usergroups'))
			->order($db->qn('lft'));
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$children = array();

		foreach ($options as $v)
		{
			$v->value = $v->id;
			$v->text = $v->title;
			$pt = $v->parent_id;

			if (isset($children[$pt]))
			{
				$list = $children[$pt];
			}
			else
			{
				$list = array();
			}

			array_push($list, $v);
			$children[$pt] = $list;
		}

		$userGroups = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);
		$options = array();
		$options = array_merge(parent::getOptions(), $options);

		foreach ($userGroups as $userGroup)
		{
			$userGroup->treename = JString::str_ireplace('&#160;', ' -', $userGroup->treename);
			$options[] = JHtml::_('select.option', $userGroup->id, $userGroup->treename);
		}

		return $options;
	}
}
