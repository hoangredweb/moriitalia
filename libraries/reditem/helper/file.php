<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

/**
 * File helper.
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.File
 *
 * @since       2.5.0
 */
class ReditemHelperFile
{
	/**
	 * Uploads file to the given folder.
	 *
	 * @param   array    $file               The file descriptor returned by PHP
	 * @param   string   $destinationFolder  Name of a folder in media/com_reditem/.
	 * @param   int      $maxFileSize        Maximum allowed file size.
	 * @param   string   $okFileExtensions   Comma separated string list of allowed file extensions.
	 * @param   boolean  $customName         If true, system will auto create file name. If false, filename is original name
	 * @param   boolean  $override           Override file if exists.
	 *
	 * @return  array|bool  Array with file data. False if upload failed.
	 */
	public static function upload(
		$file, $destinationFolder, $maxFileSize = 2,
		$okFileExtensions = null, $customName = true, $override = false)
	{
		$app           = JFactory::getApplication();
		$fileExtension = JFile::getExt($file['name']);

		// Can we upload this file type?
		if (!self::canUpload($file, $maxFileSize, $okFileExtensions))
		{
			return false;
		}

		if (!$customName)
		{
			$mangledName = JFilterOutput::stringURLSafe(JFile::stripExt($file['name']));
		}
		else
		{
			$mangledName = self::getUniqueName($file['name']);
		}

		$filePath = JPath::clean($destinationFolder . '/' . $mangledName . '.' . $fileExtension);

		// If we have a name clash, abort the upload
		if (JFile::exists($filePath) && !$override)
		{
			$app->enqueueMessage(JText::sprintf('COM_REDITEM_FILE_HELPER_FILENAMEALREADYEXIST', $filePath), 'error');

			return false;
		}

		// Do the upload
		if (!JFile::upload($file['tmp_name'], $filePath))
		{
			$app->enqueueMessage(JText::_('COM_REDITEM_FILE_HELPER_CANTJFILEUPLOAD'), 'error');

			return false;
		}

		$resultFile = array(
			'original' => $file['name'],
			'name'     => $mangledName . '.' . $fileExtension,
			'ext'      => $fileExtension,
			'path'     => $filePath
		);

		// Return the file info
		return $resultFile;
	}

	/**
	 * Checks if the file can be uploaded.
	 *
	 * @param   string  $name  Additional string you want to put into hash
	 *
	 * @return  boolean
	 */
	public static function getUniqueName($name = '')
	{
		// Get a (very!) randomised name
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$serverKey = JFactory::getConfig()->get('secret', '');
		}
		else
		{
			$serverKey = JFactory::getConfig()->getValue('secret', '');
		}

		$sig = $name . microtime() . $serverKey;

		if (function_exists('sha256'))
		{
			$mangledName = sha256($sig);
		}
		elseif (function_exists('sha1'))
		{
			$mangledName = sha1($sig);
		}
		else
		{
			$mangledName = md5($sig);
		}

		return $mangledName;
	}

	/**
	 * Checks if the file can be uploaded.
	 *
	 * @param   array   $file              File information.
	 * @param   int     $maxFileSize       Maximum allowed file size.
	 * @param   string  $okFileExtensions  Comma separated string list of allowed file extensions.
	 *
	 * @return  boolean
	 */
	private static function canUpload($file, $maxFileSize = 2, $okFileExtensions = null)
	{
		$app = JFactory::getApplication();

		if (empty($file['name']))
		{
			$app->enqueueMessage(JText::_('COM_REDITEM_FILE_HELPER_FILE_NAME_EMPTY'), 'error');

			return false;
		}

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$app->enqueueMessage(JText::sprintf('COM_REDITEM_FILE_HELPER_ERROR_FILE_NAME', $file['name']), 'error');

			return false;
		}

		// Allowed file extensions
		if (!empty($okFileExtensions))
		{
			$format    = strtolower(JFile::getExt($file['name']));
			$allowable = array_map('trim', explode(",", $okFileExtensions));

			if (!in_array($format, $allowable))
			{
				$app->enqueueMessage(JText::sprintf('COM_REDITEM_FILE_HELPER_ERROR_WRONG_FILE_EXTENSION', $format, $okFileExtensions), 'error');

				return false;
			}
		}

		// Max file size is set by config.xml
		$maxSize = (int) ($maxFileSize * 1024 * 1024);

		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$app->enqueueMessage(JText::sprintf('COM_REDITEM_FILE_HELPER_ERROR_FILE_TOOLARGE', $maxFileSize), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Function serve for upload from dragndrop ajax
	 *
	 * @param   array   $file       File posted
	 * @param   string  $type       Field type category/item
	 * @param   string  $fieldCode  Field code
	 *
	 * @return  string  path of uploaded files, '' if not in types [file/image/gallery]
	 */
	public static function dragNDropUpload($file, $type, $fieldCode)
	{
		$fileExtension = JFile::getExt($file['name']);
		$fileName      = JFilterOutput::stringURLSafe(JFile::stripExt($file['name']));
		$file['name']  = $fileName . '.' . $fileExtension;
		$tmp           = JPATH_ROOT . '/media/com_reditem/files/customfield/temporary/';
		$defaults      = ReditemHelperCustomfield::getDefaultParams();
		$field         = ReditemHelperCustomfield::getFieldByFieldCode($fieldCode, $type);

		$result = self::upload(
			$file,
			$tmp,
			$field->params->get('upload_max_filesize', $defaults[$field->type]['upload_max_filesize']),
			$field->params->get('allowed_file_extension', $defaults[$field->type]['allowed_file_extension']),
			true,
			false
		);

		if (!empty($result) && !empty($result['name']))
		{
			$path = $result['name'];
		}
		else
		{
			$path = '';
		}

		return $path;
	}
}
