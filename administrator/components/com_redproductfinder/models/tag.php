<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Association controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderModelTag extends RModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			// Remove related table here
			$id = $record->id;

			$db = JFactory::getDbo();

			$query = $db->getQuery(true);

			// Delete all custom keys for user 1001.
			$conditions = array(
				$db->quoteName('tag_id') . ' = ' . $id
			);

			$query->delete($db->quoteName('#__redproductfinder_tag_type'));
			$query->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();

			if ($result === true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Tag', $prefix = 'RedproductfinderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_redproductfinder/models/fields');

		// Get the form.
		$form = $this->loadForm('com_redproductfinder.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();

		$post = $input->post->get("jform", null, null);

		// Edit
		if (parent::save($data))
		{
			if ($post["id"] != 0)
			{
				// Save tag type into table tag_type
				if (count($post["type_id"]) > 0)
				{
					// Delete tag
					$r = $this->deleteTagType($post["id"]);

					// Insert tag type
					$a = $this->insertTagType($post, $post["id"]);
				}
			}
			else
			{
				$idTag			= $db->insertid();

				// Save tag type into table tag_type
				if (count($post["type_id"]) > 0)
				{
					$a = $this->insertTagType($post, $idTag);
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * This method will insert both tag and type into table map
	 *
	 * @param   array  $data   array data to insert
	 * @param   int    $idTag  tag id
	 *
	 * @return mixed
	 */
	public function insertTagType($data, $idTag)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->insert($db->quoteName('#__redproductfinder_tag_type'))
			->columns($db->quoteName(array('tag_id', 'type_id')));

		foreach ($data["type_id"] as $key => $value)
		{
			$values = $db->quote($idTag) . ',' . $db->quote($value);
			$query->values($values);
		}

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	 * This method will delete both tag_type in table tag_type
	 *
	 * @param   int  $idTag  id of tag that need to be removed
	 *
	 * @return boolean
	 */
	protected function deleteTagType($idTag)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Delete all custom keys for user 1001.
		$conditions = array(
			$db->quoteName('tag_id') . ' = ' . $idTag
		);

		$query->delete($db->quoteName('#__redproductfinder_tag_type'));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $result;
	}
}
