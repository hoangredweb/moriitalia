<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Customfield
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM Customfield generic class
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers.Customfield
 * @since       2.1.13
 *
 */
class ReditemHelperCustomfield
{
	/**
	 * Method to return a custom field object according to type
	 *
	 * @param   string  $type  Type of field. Default is "Textbox"
	 *
	 * @return  object         Object class of field
	 */
	public static function getCustomField($type)
	{
		if (empty($type))
		{
			$type = 'Text';
		}

		$className = 'ReditemCustomfield' . ucfirst($type);
		$class = new $className;

		return $class;
	}

	/**
	 * Checks if string is in JSON format and decodes it.
	 *
	 * @param   string  $string  String for checking.
	 *
	 * @return  mixed  json_decode result if string is in JSON format, false otherwise.
	 */
	public static function isJsonValue($string)
	{
		$temp = json_decode($string);

		if (phpversion() > '5.3.0')
		{
			return (json_last_error() == JSON_ERROR_NONE) ? $temp : false;
		}
		else
		{
			return ((is_string($string) && (is_object($temp) || is_array($temp)))) ? $temp : false;
		}
	}

	/**
	 * Convert date format from php/jQuery datepicker to jQuery datepicker/php.
	 *
	 * @param   string  $format  Format for convert.
	 * @param   string  $to      To which format (php/jquery/moment).
	 * @param   string  $from    From which format (php/jquery/moment).
	 *
	 * @return  string  Date format.
	 */
	public static function convertDateFormat($format, $to, $from)
	{
		$momentToPhp = array(
			// Time
			'HH'   => 'H',
			'H'    => 'H',
			'hh'   => 'h',
			'h'    => 'h',
			'mm'   => 'i',
			'm'    => 'i',
			'ss'   => 's',
			's'    => 's',
			'ZZ'   => 'P',
			'Z'    => 'P',
			// Day
			'e'    => 'w',
			'E'    => 'N',
			'dddd' => 'l',
			'ddd'  => 'l',
			'DD'   => 'd',
			'D'    => 'd',
			// Week
			'gggg' => 'Y',
			'gg'   => 'W',
			'GGGG' => 'Y',
			'GG'   => 'W',
			'w'    => 'W',
			'ww'   => 'W',
			'WW'   => 'W',
			'W'    => 'W',
			// Month
			'MMMM' => 'F',
			'MMM'  => 'F',
			'MM'   => 'm',
			'M'    => 'm',
			// Year
			'YYYY' => 'Y',
			'YY'   => 'y'
		);

		$jQueryToPhp = array_merge(
			$momentToPhp,
			array(
				// Day
				'dd' => 'd',
				'd'  => 'j',
				'oo' => 'z',
				'o'  => 'z',
				'DD' => 'l',
				'D'  => 'D',
				// Month
				'mm' => 'm',
				'm'  => 'n',
				'MM' => 'F',
				'M'  => 'M',
				// Year
				'yy' => 'Y',
				'y'  => 'y'
			)
		);

		$momentToJquery = array(
			'DD'   => 'dd',
			'D'    => 'dd',
			'dddd' => 'DD',
			'ddd'  => 'DD',
			'MMMM' => 'MM',
			'MMM'  => 'MM',
			'MM'   => 'mm',
			'M'    => 'mm',
			'YYYY' => 'yy',
			'YY'   => 'y'
		);

		switch ($to)
		{
			case 'php':
				if ($from == 'jquery')
				{
					$format = str_replace(array_keys($jQueryToPhp), array_values($jQueryToPhp), $format);
				}
				else
				{
					$format = str_replace(array_keys($momentToPhp), array_values($momentToPhp), $format);
				}

				break;
			case 'moment':
				$jQueryToMoment = array_flip($momentToJquery);
				$phpToMoment    = array_flip($momentToPhp);

				if ($from == 'jquery')
				{
					$format = str_replace(array_keys($jQueryToMoment), array_values($jQueryToMoment), $format);
				}
				else
				{
					$format = str_replace(array_keys($phpToMoment), array_values($phpToMoment), $format);
				}

				break;
			case 'jquery':
				$phpTojQuery = array_flip($jQueryToPhp);

				if ($from == 'moment')
				{
					$format = str_replace(array_keys($momentToJquery), array_values($momentToJquery), $format);
				}
				else
				{
					$format = str_replace(array_keys($phpTojQuery), array_values($phpTojQuery), $format);
				}

				break;
			default:
				break;
		}

		return $format;
	}

	/**
	 * Method to get custom field's filters in backend
	 *
	 * @return  array
	 */
	public static function getFieldFilters()
	{
		$fieldsModel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsModel->setState('filter.backendFilter', 1);
		$fieldFilters = $fieldsModel->getItems();

		return $fieldFilters;
	}

	/**
	 * Method to get available fields based on view_itemdetail template
	 *
	 * @param   string  $template  Template content
	 * @param   array   $fields    List of all custom fields
	 *
	 * @return  mixed
	 */
	public static function getAvailableFields($template, $fields)
	{
		if (empty($template) || empty($fields))
		{
			return false;
		}

		$available = array();

		foreach ($fields as $k => $field)
		{
			$fc   = $field->fieldcode;
			$preg = "/{+(.)*$fc(.)*}+/i";

			if (preg_match($preg, $template, $matches) > 0)
			{
				$available[] = $field;
			}
		}

		return $available;
	}

	/**
	 * Function for getting list of possible fields to use
	 *
	 * @param   string  $fieldType  Field type to use for getting fields list.
	 * @param   array   $fields     Fields array to search in.
	 *
	 * @return  array  Array of possible convert fields.
	 */
	public static function getPossibleConvertFields($fieldType, $fields)
	{
		$fieldsByFieldType = array();
		$result            = array();

		foreach ($fields as $field)
		{
			if (!isset($fieldsByFieldType[$field->type]))
			{
				$fieldsByFieldType[$field->type] = array($field);
			}
			else
			{
				$fieldsByFieldType[$field->type][] = $field;
			}
		}

		/**
		 * We will do 1 to 1 fields convert for now.
		 * Later on, we can expand this and support convert
		 * between different field types.
		 */
		switch ($fieldType)
		{
			case 'addresssuggestion':
			case 'checkbox':
			case 'color':
			case 'date':
			case 'daterange':
			case 'editor':
			case 'file':
			case 'gallery':
			case 'googlemaps':
			case 'image':
			case 'itemfromtypes':
			case 'multitextarea':
			case 'number':
			case 'radio':
			case 'range':
			case 'select':
			case 'tasklist':
			case 'text':
			case 'textarea':
			case 'url':
			case 'user':
			case 'youtube':
				if (isset($fieldsByFieldType[$fieldType]))
				{
					$result = $fieldsByFieldType[$fieldType];
				}

				break;
			default:
				break;
		}

		return $result;
	}

	/**
	 * Method for storing fields files.
	 *
	 * @param   array   $files   Files to process.
	 * @param   array   $data    Data for table storage.
	 * @param   int     $id      Type id.
	 * @param   string  $type    Custom fields type to store.
	 * @param   bool    $update  Update table after the file saving.
	 *
	 * @return  array  Data for future processing.
	 */
	public static function storeFilesFromRequest($files, $data, $id, $type = 'item', $update = false)
	{
		$uploadedFiles = self::storeFiles($id, $files, $type);
		$db            = JFactory::getDbo();
		$query         = $db->getQuery(true);
		$fieldValues   = array();
		$itemUpdate    = false;

		if ($type == 'category')
		{
			if ($update)
			{
				// Get custom field ids for files and images
				$types = ReditemHelperDatabase::filterString(array('file', 'image'));
				$query->select(
					array (
						$db->qn('cf.id', 'id'),
						$db->qn('cf.fieldcode', 'fieldcode')
					)
				)
					->from($db->qn('#__reditem_category_fields', 'cf'))
					->innerJoin($db->qn('#__reditem_category_category_field_xref', 'xref') . ' ON ' . $db->qn('xref.category_field_id') . ' = ' . $db->qn('cf.id'))
					->where($db->qn('cf.type') . ' IN (' . implode(',', $types) . ')')
					->where($db->qn('xref.category_id') . ' = ' . (int) $id);
				$fieldIds = $db->setQuery($query)->loadAssocList('fieldcode');

				// Something went wrong in xref creation, we can't add files
				if (empty($fieldIds))
				{
					return array();
				}
			}
		}
		else
		{
			if ($update)
			{
				$tableName = ReditemHelperType::getTableName($data['type_id']);
				$query->update($db->qn($tableName))
					->where($db->qn('id') . ' = ' . (int) $id);
			}
		}

		// Update field file value
		if (!empty($uploadedFiles))
		{
			foreach ($uploadedFiles as $fieldType => $files)
			{
				foreach ($files as $fieldCode => $file)
				{
					if (!empty($file))
					{
						if ($fieldType == 'file')
						{
							if (!empty($data['fields']['file_names'][$fieldCode]))
							{
								$value = json_encode(array($id . '/' . $file['name'], $data['fields']['file_names'][$fieldCode]), true);
							}
							else
							{
								$value = json_encode(array($id . '/' . $file['name'], $file['original']), true);
							}
						}
						elseif ($fieldType == 'image')
						{
							if (!empty($data['fields']['images_alt'][$fieldCode]))
							{
								$value = json_encode(array($id . '/' . $file['name'] . '|' . $data['fields']['images_alt'][$fieldCode]), true);
							}
							else
							{
								$value = json_encode(array($id . '/' . $file['name']), true);
							}
						}
						else
						{
							continue;
						}

						$data['fields'][$fieldType][$fieldCode] = $value;

						if ($update)
						{
							if ($type == 'category')
							{
								if (!empty($fieldIds[$fieldCode]['id']))
								{
									$fieldId = $fieldIds[$fieldCode]['id'];
									$fieldValues[$fieldId] = $value;
								}
							}
							else
							{
								$query->set($db->qn($fieldCode) . ' = ' . $db->q($value));
								$itemUpdate = true;
							}
						}
					}
					// File is ready for delete or value is not changed
					else
					{
						// If -1 is set, delete that file
						if (!empty($data['fields'][$fieldType][$fieldCode]) && $data['fields'][$fieldType][$fieldCode] == '-1')
						{
							self::deleteFiles($id, array($fieldCode), $type);
							$data['fields'][$fieldType][$fieldCode] = '';

							if ($update)
							{
								if ($type == 'category')
								{
									if (!empty($fieldIds[$fieldCode]['id']))
									{
										$fieldId = $fieldIds[$fieldCode]['id'];
										$fieldValues[$fieldId] = '';
									}
								}
								else
								{
									$query->set($db->qn($fieldCode) . ' = ' . $db->q(''));
									$itemUpdate = true;
								}
							}
						}
					}
				}
			}
		}

		// Process media upload for images
		if (!empty($data['fields']['media']))
		{
			foreach ($data['fields']['media'] as $fieldCode => $media)
			{
				$orgFile = JPATH_ROOT . '/' . $media;
				$name    = JFile::getName($orgFile);

				if (JFile::exists($orgFile))
				{
					if ($type == 'item')
					{
						$location = JPATH_REDITEM_CUSTOMFIELD_IMAGES;
					}
					else
					{
						$location = JPATH_REDITEM_CATEGORY_IMAGES;
					}

					$field = self::getFieldByFieldCode($fieldCode, $type);

					// Try deleting old files.
					if (in_array($field->type, array('image', 'file')) && (int) $field->params->get('wipe_on_upload', 0))
					{
						self::deleteFiles($id, array($fieldCode), $type);
					}

					if ($field->params->get('use_mangled_name', 0))
					{
						$ext  = JFile::getExt($name);
						$name = ReditemHelperFile::getUniqueName($name);
						$name .= '.' . $ext;
					}

					if (JFile::exists($location . $id . '/' . $name) && !$field->params->get('allow_override', 0))
					{
						JFactory::getApplication()->enqueueMessage(
							JText::sprintf('COM_REDITEM_FILE_HELPER_FILENAMEALREADYEXIST', $location . $id . '/' . $name),
							'error'
						);

						continue;
					}

					if (!JFolder::exists($location . $id))
					{
						JFolder::create($location . $id);
					}

					if (JFile::copy($orgFile, $location . $id . '/' . $name))
					{
						if (!empty($data['fields']['images_alt'][$fieldCode]))
						{
							$value = json_encode(array($id . '/' . $name . '|' . $data['fields']['images_alt'][$fieldCode]), true);
						}
						else
						{
							$value = json_encode(array($id . '/' . $name), true);
						}

						$data['fields']['image'][$fieldCode] = $value;

						if ($update)
						{
							if ($type == 'category')
							{
								if (!empty($fieldIds[$fieldCode]['id']))
								{
									$fieldId = $fieldIds[$fieldCode]['id'];
									$fieldValues[$fieldId] = $value;
								}
							}
							else
							{
								$query->set($db->qn($fieldCode) . ' = ' . $db->q($value));
								$itemUpdate = true;
							}
						}
					}
				}
			}
		}

		if ($update)
		{
			if ($type == 'category')
			{
				foreach ($fieldValues as $fId => $val)
				{
					$query->clear()
						->update($db->qn('#__reditem_category_category_field_xref'))
						->set($db->qn('value') . ' = ' . $db->q($val))
						->where($db->qn('category_id') . ' = ' . (int) $id)
						->where($db->qn('category_field_id') . ' = ' . (int) $fId);
					$db->setQuery($query)->execute();
				}
			}
			elseif ($itemUpdate)
			{
				$db->setQuery($query)->execute();
			}
		}

		return $data;
	}

	/**
	 * Function for storing custom field files.
	 *
	 * @param   int     $id          Category/Item id.
	 * @param   array   $fieldFiles  Array of uploaded files.
	 * @param   string  $type        Custom field type (category or item).
	 *
	 * @return  array  Array of the new file names, index by field codes.
	 */
	public static function storeFiles($id, $fieldFiles, $type = 'item')
	{
		$result   = array();
		$defaults = self::getDefaultParams();

		if ($type == 'category')
		{
			$fieldPath = '/categoryfield/';
		}
		else
		{
			$fieldPath = '/customfield/';
		}

		if (!empty($fieldFiles))
		{
			foreach ($fieldFiles as $fieldType => $files)
			{
				$storePath = JPATH_REDITEM_MEDIA . $fieldType . $fieldPath . $id . '/';

				if ($fieldType == 'files')
				{
					$fType = 'file';
				}
				else
				{
					$fType = 'image';
				}

				if (!isset($result[$fType]))
				{
					$result[$fType] = array();
				}

				foreach ($files as $fieldCode => $file)
				{
					if (empty($file['name']) || !($field = self::getFieldByFieldCode($fieldCode, $type)))
					{
						$result[$fType][$fieldCode] = false;

						continue;
					}

					// Try deleting old files.
					if (in_array($field->type, array('image', 'file')) && (int) $field->params->get('wipe_on_upload', 0))
					{
						self::deleteFiles($id, array($fieldCode), $type);
					}

					// Upload new file
					$uploadedFile = ReditemHelperFile::upload(
						$file,
						$storePath,
						$field->params->get('upload_max_filesize', $defaults[$field->type]['upload_max_filesize']),
						$field->params->get('allowed_file_extension', $defaults[$field->type]['allowed_file_extension']),
						(int) $field->params->get('use_mangled_name', $defaults[$field->type]['mangled_filename']),
						(int) $field->params->get('allow_override', 0)
					);

					$result[$field->type][$field->fieldcode] = $uploadedFile;
				}
			}
		}

		return $result;
	}

	/**
	 * Function for coping all files from one to another custom field.
	 *
	 * @param   int     $fromId    Type id from which should copy all the files.
	 * @param   int     $toId      Type id where we should copy all the files.
	 * @param   string  $fileType  Files type to copy.
	 * @param   string  $type      Custom field type, can be 'item' or 'category'.
	 * @param   bool    $update    Update table values.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function copyFiles($fromId, $toId, $fileType = 'images', $type = 'item', $update = true)
	{
		$fieldType   = ($type == 'category') ? 'categoryfield' : 'customfield';
		$fromPath    = JPATH_REDITEM_MEDIA . $fileType . '/' . $fieldType . '/' . $fromId . '/';
		$toPath      = JPATH_REDITEM_MEDIA . $fileType . '/' . $fieldType . '/' . $toId . '/';
		$copiedFiles = array();

		// Copy files
		if (JFolder::exists($fromPath))
		{
			$files = JFolder::files($fromPath, '.', false, true, array(), array());

			foreach ($files as $file)
			{
				$fileName    = basename($file);
				$fileWrapper = new JFilesystemWrapperPath;
				$from        = $fileWrapper->clean($file);
				$to          = $fileWrapper->clean($toPath . $fileName);

				if (!JFolder::exists($toPath))
				{
					JFolder::create($toPath);
				}

				if (!JFile::copy($from, $to))
				{
					return false;
				}

				$copiedFiles[] = basename($to);
			}
		}

		// Update values
		if ($update && !empty($copiedFiles))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			if ($fileType == 'files')
			{
				$types = array($db->q('file'));
			}
			else
			{
				$types = array($db->q('image'));
			}

			if ($type == 'category')
			{
				// Get custom field ids for files and images
				$query->select(
					array (
						$db->qn('cf.id', 'id'),
						$db->qn('xref.value', 'value')
					)
				)
					->from($db->qn('#__reditem_category_fields', 'cf'))
					->innerJoin($db->qn('#__reditem_category_category_field_xref', 'xref') . ' ON ' . $db->qn('xref.category_field_id') . ' = ' . $db->qn('cf.id'))
					->where($db->qn('cf.type') . ' IN (' . implode(',', $types) . ')')
					->where($db->qn('xref.category_id') . ' = ' . (int) $fromId);
				$fields = $db->setQuery($query)->loadObjectList();
				$table  = RTable::getAdminInstance('Category_Category_Field_Xref', array(), 'com_reditem');

				foreach ($fields as $field)
				{
					$table->reset();
					$fromVal = json_decode($field->value, true);
					$arr     = explode('/', $fromVal[0]);
					$value   = $arr[1];

					// Process only copied files
					if (in_array($value, $copiedFiles))
					{
						// Format for db saving
						if ($fileType == 'files')
						{
							if (isset($fromVal[1]))
							{
								$value = json_encode(array($toId . '/' . $value, $fromVal[1]));
							}
							else
							{
								$value = json_encode(array($toId . '/' . $value, $value));
							}
						}
						else
						{
							$value = json_encode(array($toId . '/' . $value));
						}

						$table->save(
							array (
								'category_id'       => $toId,
								'category_field_id' => $field->id,
								'value'             => $value
							)
						);
					}
				}
			}
			else
			{
				$typeId = ReditemHelperItem::getTypeIdByItemId($fromId);
				$table  = ReditemHelperType::getTableName($typeId);
				$query->select('*')
					->from($db->qn($table))
					->where($db->qn('id') . ' = ' . (int) $fromId);
				$allFields = $db->setQuery($query, 0, 1)->loadAssoc();
				$query->clear()
					->select($db->qn('fieldcode'))
					->from($db->qn('#__reditem_fields'))
					->where($db->qn('type') . ' IN (' . implode(',', $types) . ')')
					->where($db->qn('type_id') . ' = ' . (int) $typeId);
				$fileTypeFieldCodes = $db->setQuery($query)->loadColumn();

				$updates = array();

				foreach ($fileTypeFieldCodes as $fieldCode)
				{
					if (!empty($allFields[$fieldCode]))
					{
						$fromVal = json_decode($allFields[$fieldCode], true);
						$arr     = explode('/', $fromVal[0]);
						$value   = $arr[1];

						// Process only copied files
						if (in_array($value, $copiedFiles))
						{
							// Format for db saving
							if ($fileType == 'files')
							{
								if (isset($fromVal[1]))
								{
									$value = json_encode(array($toId . '/' . $value, $fromVal[1]));
								}
								else
								{
									$value = json_encode(array($toId . '/' . $value, $value));
								}
							}
							else
							{
								$value = json_encode(array($toId . '/' . $value));
							}

							$updates[] = $db->qn($fieldCode) . ' = ' . $db->q($value);
						}
					}
				}

				if (!empty($updates))
				{
					$query->clear()
						->update($db->qn($table))
						->where($db->qn('id') . ' = ' . $toId);

					foreach ($updates as $updateSql)
					{
						$query->set($updateSql);
					}

					$db->setQuery($query)->execute();
				}
			}
		}

		return true;
	}

	/**
	 * Get custom field by it's field code from the database.
	 *
	 * @param   string  $fieldCode  Field code to filter custom field.
	 * @param   string  $type       Custom field type (category/item)
	 *
	 * @return  object|null  Custom field from the database or null.
	 */
	public static function getFieldByFieldCode($fieldCode, $type = 'item')
	{
		$db = JFactory::getDbo();

		if ($type == 'category')
		{
			$model = RModelList::getInstance('Category_Fields', 'ReditemModel', array('ignore_request' => true));
		}
		else
		{
			$model = RModelList::getInstance('Fields', 'ReditemModel', array('ignore_request' => true));
		}

		$model->setState('filter.fieldcode', $fieldCode);
		$query = $model->getListQuery();
		$field = $db->setQuery($query, 0, 1)->loadObject();

		if ($field)
		{
			$field->params = new JRegistry($field->params);
		}

		return $field;
	}

	/**
	 * Delete field files for given path or field id.
	 *
	 * @param   int     $id          Item/Category id.
	 * @param   array   $fieldCodes  Field codes to delete files.
	 * @param   string  $type        Item/Category
	 *
	 * @return  boolean  True on success. False and warning in $app que if any exception occurs.
	 */
	public static function deleteFiles($id, $fieldCodes, $type = 'item')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($type == 'category')
		{
			$fieldCodes = ReditemHelperDatabase::filterString($fieldCodes);
			$query->select(
				array (
					$db->qn('ccfx.value', 'value'),
					$db->qn('cf.type', 'type'),
					$db->qn('cf.fieldcode', 'fieldcode')
				)
			)
				->from($db->qn('#__reditem_category_category_field_xref', 'ccfx'))
				->innerJoin($db->qn('#__reditem_category_fields', 'cf') . ' ON ' . $db->qn('ccfx.category_field_id') . ' = ' . $db->qn('cf.id'))
				->where($db->qn('cf.fieldcode') . ' IN (' . implode(',', $fieldCodes) . ')')
				->where($db->qn('ccfx.category_id') . ' = ' . (int) $id);
			$tmp   = $db->setQuery($query)->loadObjectList();
			$files = array();

			foreach ($tmp as $file)
			{
				$decode = json_decode($file, true);

				// Check if value represents gallery images
				if (count($decode) > 1 || is_array($decode[0]))
				{
					foreach ($decode as $d)
					{
						$file->value = $d['path'];
						$files[]     = $file;
					}
				}
				else
				{
					$files[] = $file;
				}
			}
		}
		else
		{
			$typeId = ReditemHelperItem::getTypeIdByItemId($id);
			$table  = ReditemHelperType::getTableName($typeId);

			foreach ($fieldCodes as $fieldCode)
			{
				$query->select($db->qn($fieldCode));
			}

			$query->from($db->qn($table))
				->where($db->qn('id') . ' = ' . (int) $id);
			$values = $db->setQuery($query)->loadAssoc();
			$fieldCodes = ReditemHelperDatabase::filterString($fieldCodes);

			$query->clear()
				->select(
					array (
						$db->qn('fieldcode'),
						$db->qn('type')
					)
				)
				->from($db->qn('#__reditem_fields'))
				->where($db->qn('fieldcode') . ' IN (' . implode(',', $fieldCodes) . ')')
				->where($db->qn('type_id') . ' = ' . (int) $typeId);
			$tmp   = $db->setQuery($query)->loadObjectList('fieldcode');
			$files = array();

			foreach ($values as $fieldCode => $value)
			{
				$decode = json_decode($value, true);

				// Check if value represents gallery images
				if (count($decode) > 1 || is_array($decode[0]))
				{
					foreach ($decode as $d)
					{
						$tmp[$fieldCode]->value = $d['path'];
						$files[]                = $tmp[$fieldCode];
					}
				}
				else
				{
					$tmp[$fieldCode]->value = $value;
					$files[]                = $tmp[$fieldCode];
				}
			}
		}

		foreach ($files as $file)
		{
			$fType = ($type == 'item') ? 'customfield' : 'categoryfield';

			if (empty($file->value))
			{
				continue;
			}

			if ($file->type == 'gallery')
			{
				$filePath = JPATH_REDITEM_MEDIA . 'images/' . $fType . '/' . $id . '/';
				$images   = json_decode($file->value, true);

				foreach ($images as $image)
				{
					$value = explode('/', $image['path']);
					$tmp   = $value[1];
					$tmp   = explode('.', $tmp);
					$name  = $tmp[0];

					if (!self::fDelete($filePath, $name))
					{
						return false;
					}
				}
			}
			else
			{
				$path     = ($file->type == 'file') ? 'files' : 'images';
				$filePath = JPATH_REDITEM_MEDIA . $path . '/' . $fType . '/' . $id . '/';
				$decode   = json_decode($file->value, true);
				$value    = explode('/', $decode[0]);
				$tmp      = $value[1];
				$tmp      = explode('.', $tmp);
				$name     = $tmp[0];

				if (!self::fDelete($filePath, $name))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * File delete function.
	 *
	 * @param   string  $filePath  Folder location.
	 * @param   string  $name      File name like.
	 *
	 * @return  bool  True on success, false otherwise.
	 */
	private static function fDelete($filePath, $name)
	{
		if (empty($name))
		{
			return false;
		}

		if (!empty($filePath) && JFolder::exists($filePath))
		{
			try
			{
				$files = JFolder::files($filePath, '.?' . $name . '\.(.{3})', false, true, array(), array());

				if (!empty($files))
				{
					JFile::delete($files);
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');

				return false;
			}
		}

		return true;
	}

	/**
	 * Get field default params.
	 *
	 * @param   string  $fieldType  Field type.
	 *
	 * @return  array  Field defaults.
	 */
	public static function getDefaultParams($fieldType = '')
	{
		$config   = JComponentHelper::getParams('com_reditem');
		$defaults = array (
			'file'    => array (
				'upload_max_filesize'    => $config->get('customfieldFileUploadMaxSize', 2),
				'allowed_file_extension' => $config->get('customfieldFileUploadExtensions', 'zip,doc,xls,pdf'),
				'mangled_filename'       => (boolean) $config->get('customfieldFileUploadUseCustomName', true)
			),
			'image'   => array (
				'upload_max_filesize'    => $config->get('customfieldImageUploadMaxSize', 2),
				'allowed_file_extension' => $config->get('customfieldImageUploadExtensions', 'jpg,jpeg,gif,png'),
				'mangled_filename'       => false
			),
			'gallery' => array (
				'upload_max_filesize'    => $config->get('customfieldGalleryUploadMaxSize', 2),
				'allowed_file_extension' => $config->get('customfieldGalleryUploadExtensions', 'jpg,jpeg,gif,png'),
				'mangled_filename'       => false
			)
		);

		if (!empty($fieldType) && isset($defaults[$fieldType]))
		{
			return $defaults[$fieldType];
		}

		return $defaults;
	}

	/**
	 * Process drag and drop data function. Stores all files which are 'dropped' for upload.
	 *
	 * @param   int     $id     Item id.
	 * @param   int     $type   Item type.
	 * @param   array   $drags  Drag and drops to store.
	 * @param   array   $data   Request data for store.
	 * @param   string  $fType  Field type.
	 *
	 * @return  void
	 */
	public static function processDragNDrop($id, $type, $drags, $data, $fType = 'item')
	{
		$fieldType       = ($fType == 'category') ? 'categoryfield' : 'customfield';
		$temporaryFolder = JPATH_REDITEM_MEDIA . 'files/customfield/temporary/';
		$update          = false;
		$db              = JFactory::getDbo();
		$query           = $db->getQuery(true);
		$fieldValues     = array();
		$gUpdate         = false;

		if ($fType == 'category')
		{
			// Get custom field ids for files and images
			$types = ReditemHelperDatabase::filterString(array('file', 'image'));
			$query->select(
				array(
					$db->qn('cf.id', 'id'),
					$db->qn('cf.fieldcode', 'fieldcode')
				)
			)
				->from($db->qn('#__reditem_category_fields', 'cf'))
				->innerJoin($db->qn('#__reditem_category_category_field_xref', 'xref') . ' ON ' . $db->qn('xref.category_field_id') . ' = ' . $db->qn('cf.id'))
				->where($db->qn('cf.type') . ' IN (' . implode(',', $types) . ')')
				->where($db->qn('xref.category_id') . ' = ' . (int) $id);
			$fieldIds = $db->setQuery($query)->loadAssocList('fieldcode');
		}

		if (!empty($drags))
		{
			$galleries = array();

			if (!empty($data['fields']['gallery']))
			{
				foreach ($data['fields']['gallery'] as $fc => $gallery)
				{
					$galleries[$fc] = json_decode($gallery);
				}
			}

			foreach ($drags as $fieldCode => $files)
			{
				$field    = self::getFieldByFieldCode($fieldCode, $fType);
				$unique   = $field->params->get('use_mangled_name', 0);
				$override = $field->params->get('allow_override', 0);

				switch ($field->type)
				{
					case 'file':
						if (!empty($files) && !empty($files[0]))
						{
							self::deleteFiles($id, array($fieldCode), $fType);
							$file       = JString::trim($files[0]);
							$fileFolder = JPATH_REDITEM_MEDIA . 'files/' . $fieldType . '/' . $id . '/';
							$update     = true;

							if ($unique)
							{
								$ext  = JFile::getExt($file);
								$file = ReditemHelperFile::getUniqueName($file);
								$file .= '.' . $ext;
							}

							if (JFile::exists($fileFolder . $file) && !$override)
							{
								JFactory::getApplication()->enqueueMessage(
									JText::sprintf('COM_REDITEM_FILE_HELPER_FILENAMEALREADYEXIST', $fileFolder . $file),
									'error'
								);

								continue;
							}

							// Move file from temporary folder to file folder
							if (!JFolder::exists($fileFolder))
							{
								JFolder::create($fileFolder);
							}

							if (JFile::move($temporaryFolder . $file, $fileFolder . $file))
							{
								if (!empty($data['fields']['file_names'][$fieldCode]))
								{
									$value = json_encode(array($id . '/' . $file, $data['fields']['file_names'][$fieldCode]));
								}
								else
								{
									$value = json_encode(array($id . '/' . $file));
								}

								if ($fType == 'category')
								{
									if (!empty($fieldIds[$fieldCode]['id']))
									{
										$fieldId               = $fieldIds[$fieldCode]['id'];
										$fieldValues[$fieldId] = $value;
									}
								}
								else
								{
									$query->set($db->qn($fieldCode) . ' = ' . $db->q($value));
								}
							}
						}

						break;
					case 'image':
						if (!empty($files) && !empty($files[0]))
						{
							self::deleteFiles($id, array($fieldCode), $fType);
							$file       = JString::trim($files[0]);
							$fileFolder = JPATH_REDITEM_MEDIA . 'images/' . $fieldType . '/' . $id . '/';
							$update     = true;

							if ($unique)
							{
								$ext  = JFile::getExt($file);
								$file = ReditemHelperFile::getUniqueName($file);
								$file .= '.' . $ext;
							}

							if (JFile::exists($fileFolder . $file) && !$override)
							{
								JFactory::getApplication()->enqueueMessage(
									JText::sprintf('COM_REDITEM_FILE_HELPER_FILENAMEALREADYEXIST', $fileFolder . $file),
									'error'
								);

								continue;
							}

							// Move file from temporary folder to file folder
							if (!JFolder::exists($fileFolder))
							{
								JFolder::create($fileFolder);
							}

							if (JFile::move($temporaryFolder . $file, $fileFolder . $file))
							{
								if (!empty($data['fields']['images_alt'][$fieldCode]))
								{
									$value = json_encode(array($id . '/' . $file . '|' . $data['fields']['images_alt'][$fieldCode]));
								}
								else
								{
									$value = json_encode(array($id . '/' . $file));
								}

								if ($fType == 'category')
								{
									if (!empty($fieldIds[$fieldCode]['id']))
									{
										$fieldId               = $fieldIds[$fieldCode]['id'];
										$fieldValues[$fieldId] = $value;
									}
								}
								else
								{
									$query->set($db->qn($fieldCode) . ' = ' . $db->q($value));
								}
							}
						}

						break;
					case 'gallery':
						$files = explode(',', $files);

						if (!empty($files))
						{
							foreach ($files as $file)
							{
								if (!empty($file))
								{
									$gUpdate        = true;
									$fileFolder     = JPATH_REDITEM_MEDIA . 'images/' . $fieldType . '/' . $id . '/';
									$update         = true;
									$temporaryFile  = JString::trim($file);
									$file           = $temporaryFile;

									if ($unique)
									{
										$ext  = JFile::getExt($file);
										$file = ReditemHelperFile::getUniqueName($file);
										$file .= '.' . $ext;
									}

									if (JFile::exists($fileFolder . $file) && !$override)
									{
										JFactory::getApplication()->enqueueMessage(
											JText::sprintf('COM_REDITEM_FILE_HELPER_FILENAMEALREADYEXIST', $fileFolder . $file),
											'error'
										);

										continue;
									}

									// Move file from temporary folder to file folder
									if (!JFolder::exists($fileFolder))
									{
										JFolder::create($fileFolder);
									}

									if (JFile::move($temporaryFolder . $temporaryFile, $fileFolder . $file))
									{
										$image          = new stdClass;
										$image->path    = $id . '/' . $file;
										$image->default = 0;

										if (empty($galleries[$fieldCode]))
										{
											$galleries[$fieldCode] = array($image);
										}
										else
										{
											$galleries[$fieldCode][] = $image;
										}
									}
								}
							}
						}

						break;
					default:

						break;
				}
			}

			if (!empty($galleries) && $gUpdate)
			{
				foreach ($galleries as $fieldCode => $images)
				{
					if ($fType == 'category')
					{
						if (!empty($fieldIds[$fieldCode]['id']))
						{
							$fieldId               = $fieldIds[$fieldCode]['id'];
							$fieldValues[$fieldId] = json_encode($images);
						}
					}
					else
					{
						$query->set($db->qn($fieldCode) . ' = ' . $db->q(json_encode($images)));
					}
				}
			}
		}

		if ($update)
		{
			if ($fType == 'category')
			{
				$table = RTable::getInstance('Category_Category_Field_Xref', 'ReditemTable');

				foreach ($fieldValues as $fId => $val)
				{
					$table->reset();

					$src = array(
						'category_id'       => $id,
						'category_field_id' => $fId,
						'value'             => $val
					);

					$table->save($src);
				}
			}
			else
			{
				$tableName = ReditemHelperType::getTableName($type);

				$query->update($db->qn($tableName))
					->where($db->qn('id') . ' = ' . (int) $id);

				$db->setQuery($query)->execute();
			}
		}

		if (JFolder::exists($temporaryFolder))
		{
			JFolder::delete($temporaryFolder);
		}
	}

	/**
	 * Process custom field values for Twig usage.
	 *
	 * @param   array  $fields  Array of fields and its values.
	 *
	 * @return  array  Values for Twig usage.
	 */
	public static function processValuesForTwig($fields)
	{
		$values = array();

		if (is_array($fields))
		{
			foreach ($fields as $key => $value)
			{
				if (($decode = self::isJsonValue($value)) !== false)
				{
					if (is_array($decode))
					{
						if (count($decode) == 1)
						{
							$values[$key] = $decode[0];
						}
						elseif (count($decode) == 0)
						{
							$values[$key] = '';
						}
						else
						{
							$values[$key] = $decode;
						}
					}
					else
					{
						$values[$key] = $decode;
					}
				}
				else
				{
					$values[$key] = $value;
				}
			}
		}

		return $values;
	}

	/**
	 * Function for processing fields data for table store.
	 *
	 * @param   array  $data  Fields data for store.
	 *
	 * @return  array  Associative array of fields data (['FIELDCODE'=>VALUE])
	 */
	public static function processFieldsDataForStore($data)
	{
		$values = array();

		foreach ($data as $type => $fields)
		{
			if ($type == 'file_names' || $type == 'dragndrop' || $type == 'media' || $type == 'images_alt')
			{
				continue;
			}
			else
			{
				foreach ($fields as $fieldCode => $value)
				{
					$fieldCodes[] = $fieldCode;

					if ($type == 'file' && !empty($data['file_names'][$fieldCode]))
					{
						if (is_array($value))
						{
							$value[]               = $data['file_names'][$fieldCode];
							$values[$fieldCode] = json_encode(array_values($value));
						}
						else
						{
							$val                   = json_decode($value, 'true');
							$val[1]                = $data['file_names'][$fieldCode];
							$values[$fieldCode] = json_encode(array_values($val));
						}
					}
					elseif ($type == 'image' && !empty($data['images_alt'][$fieldCode]))
					{
						if (is_array($value))
						{
							$val                   = explode('|', $value[0]);
							$val[1]                = $data['images_alt'][$fieldCode];
							$values[$fieldCode] = json_encode(array(implode('|', $val)));
						}
						else
						{
							$value                 = json_decode($value, true);
							$val                   = explode('|', $value[0]);
							$val[1]                = $data['images_alt'][$fieldCode];
							$values[$fieldCode] = json_encode(array(implode('|', $val)));
						}
					}
					elseif ($type == 'date')
					{
						$date = ReditemHelperSystem::getDateWithTimezone($value, true);
						$values[$fieldCode] = $date->toSql();
					}
					elseif($type == 'daterange')
					{
						if (is_string($value))
						{
							$value = json_decode($value, true);
						}

						$start              = ReditemHelperSystem::getDateWithTimezone($value['start'], true)->toSql();
						$end                = ReditemHelperSystem::getDateWithTimezone($value['end'], true)->toSql();
						$values[$fieldCode] = json_encode(array('start' => $start, 'end' => $end));
					}
					elseif (is_array($value))
					{
						$values[$fieldCode] = json_encode(array_values($value));
					}
					else
					{
						$values[$fieldCode] = $value;
					}
				}
			}
		}

		return $values;
	}
}
