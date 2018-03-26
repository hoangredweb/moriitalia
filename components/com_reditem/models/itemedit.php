<?php
/**
 * @package     RedITEM.Front
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');

/**
 * RedITEM ItemEdit Model
 *
 * @package     RedITEM.Component
 * @subpackage  Models.Item
 * @since       2.0.19
 *
 */
class ReditemModelItemEdit extends RModelAdmin
{
	public $item = null;
	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$input = $app->input;
		$item = parent::getItem($pk);
		$published = $this->getState('filter.published');

		if (isset($item->id))
		{
			$app->setUserState('com_reditem.global.tid', $item->type_id);

			if ($item->blocked || $item->published != 1)
			{
				$app->enqueueMessage(JText::_('COM_REDITEM_ERROR_NO_ITEM_FOUND'), 'error');
				$app->redirect(JRoute::_('index.php?option=com_reditem&view=items'));
			}

			// Get categories of item
			$query = $db->getQuery(true);
			$query->select($db->qn('category_id'));
			$query->from($db->qn('#__reditem_item_category_xref'));
			$query->where($db->qn('item_id') . ' = ' . $db->quote($item->id));

			if ($published)
			{
				$query->where($db->qn('published') . ' = ' . (int) $published);
			}
			elseif ($published === '')
			{
				$query->where($db->qn('published') . ' IN (0, 1)');
			}

			$db->setQuery($query);
			$result = $db->loadObjectList();

			if ($result)
			{
				$item->categories = array();

				foreach ($result as $category)
				{
					$item->categories[] = $category->category_id;
				}
			}

			// Get custom field values
			$query = $db->getQuery(true);
			$query->select($db->qn('table_name'))
				->from('#__reditem_types')
				->where($db->qn('id') . ' = ' . $db->quote($item->type_id));
			$db->setQuery($query);
			$rs = $db->loadObject();
			$tb = '#__reditem_types_' . $rs->table_name;

			$query = $db->getQuery(true);
			$query->select('cf.*');
			$query->from($db->qn($tb, 'cf'));
			$query->where($db->qn('cf.id') . ' = ' . $db->quote($item->id));
			$db->setQuery($query);

			$item->customfield_values = (array) $db->loadObject();

			// Remove the id column of custom fields value
			array_shift($item->customfield_values);
		}

		$this->item = $item;

		return $this->item;
	}

	/**
	 * Method to get template
	 *
	 * @return mixed
	 */
	public function getTemplate()
	{
		$app    = JFactory::getApplication();
		$input  = $app->input;

		$typeModel  = RModel::getAdminInstance('Type', array('ignore_request' => true));
		$typeId     = $input->getInt('typeId', 0);

		if (!$typeId)
		{
			$typeId = JFactory::getApplication()->getUserState('com_reditem.global.tid', '0');
		}

		$templateModel  = RModel::getAdminInstance('Template', array('ignore_request' => true));
		$templateId     = $input->getInt('templateId', 0);

		if (!$templateId)
		{
			$type = $typeModel->getItem($typeId);
			$templateId = $type->params['default_itemedit_template'];
		}

		return $templateModel->getItem($templateId);
	}

	/**
	 * Method to get custom field.
	 *
	 * @return  array
	 */
	public function getCustomFields()
	{
		$app = RFactory::getApplication();
		$typeId = $app->input->getInt('tid', null);

		if (!$typeId)
		{
			$typeId = JFactory::getApplication()->getUserState('com_reditem.global.tid', '0');
		}

		if (!$typeId)
		{
			return false;
		}

		$customfields = RModel::getAdminInstance('Fields', array('ignore_request' => true));
		$customfields->setState('filter.types', $typeId);
		$customfields->setState('filter.published', 1);
		$rows = $customfields->getItems();

		$fields = array();

		foreach ($rows as $row)
		{
			if ($row->published == 1)
			{
				$field = ReditemHelperCustomfield::getCustomField($row->type);
				$field->bind($row);

				if ((isset($this->item->customfield_values)) && isset($this->item->customfield_values[$row->fieldcode]))
				{
					$field->value = $this->item->customfield_values[$row->fieldcode];
				}

				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Method to set featured of item.
	 *
	 * @param   int  $id     Id of item
	 * @param   int  $state  featured state of item
	 *
	 * @return  boolean
	 */
	public function featured($id = null, $state = 0)
	{
		$db = JFactory::getDbo();

		if ($id)
		{
			$query = $db->getQuery(true);

			$query->update($db->qn('#__reditem_items', 'i'))
				->set($db->qn('i.featured') . ' = ' . (int) $state)
				->where($db->qn('i.id') . ' = ' . (int) $id);

			$db->setQuery($query);

			if (!$db->execute())
			{
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = parent::getForm($data, $loadData);
		$user = ReditemHelperSystem::getUser();

		if (!$user->authorise('core.edit.state', 'com_reditem'))
		{
			// Disable change publish state
			$form->setFieldAttribute('published', 'readonly', true);
			$form->setFieldAttribute('published', 'class', 'btn-group disabled');

			// Disable change feature state
			$form->setFieldAttribute('featured', 'readonly', true);
			$form->setFieldAttribute('featured', 'class', 'btn-group disabled');

			// Disable change access state
			$form->setFieldAttribute('access', 'disabled', true);
		}

		return $form;
	}

	/**
	 * Get the associated JTable
	 *
	 * @param   string  $name    Table name
	 * @param   string  $prefix  Table prefix
	 * @param   array   $config  Configuration array
	 *
	 * @return  JTable
	 */
	public function getTable($name = 'Item', $prefix = '', $config = array())
	{
		$class = get_class($this);

		if (empty($prefix))
		{
			$prefix = strstr($class, 'Model', true) . 'Table';
		}

		return parent::getTable($name, $prefix, $config);
	}
}
