<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * System helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.System
 * @since       2.1
 *
 */
class ReditemHelperSystem
{
	/**
	 * Get the current redITEM version
	 *
	 * @return  string  The redITEM version
	 *
	 * @since   2.1.9
	 */
	public static function getVersion()
	{
		$xmlfile = JPATH_SITE . '/administrator/components/com_reditem/reditem.xml';
		$version = JText::_('COM_REDITEM_FILE_NOT_FOUND');

		if (file_exists($xmlfile))
		{
			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			$version = $data['version'];
		}

		return $version;
	}

	/**
	 * Get the current redITEM version
	 *
	 * @return  string  The redITEM version
	 *
	 * @since   2.1.9
	 */
	public static function getStats()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			array (
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_items') . ') AS items',
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_categories') . ' WHERE ' . $db->qn('level') . ' > 0) AS categories',
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_types') . ') AS types',
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_fields') . ') AS fields',
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_category_fields') . ') AS category_fields',
				'(SELECT COUNT(' . $db->qn('id') . ') FROM ' . $db->qn('#__reditem_templates') . ') AS templates'
			)
		);

		$stats = $db->setQuery($query, 0, 1)->loadAssoc();

		return $stats;
	}

	/**
	 * Method to get templates by Section code and Type Id
	 *
	 * @param   string   $section  Section code
	 * @param   integer  $type     Type Id
	 *
	 * @return  mixed
	 */
	public static function getTemplatesBySection($section = 'view_itemdetail', $type = 0)
	{
		$templatesModel = RModel::getAdminInstance('Templates', array('ignore_request' => true), 'com_reditem');
		$templatesModel->setState('filter.section', $section);

		if (!empty($type))
		{
			$templatesModel->setState('filter.filter_types', (int) $type);
		}

		return $templatesModel->getItems();
	}

	/**
	 * Method for create JDate class with applied timezone of current user.
	 *
	 * @param   string   $date          Date string of specific time.
	 * @param   boolean  $convertToUTC  True to convert local time into UTC.
	 * @param   int      $userId        ID of user.
	 *
	 * @return  JDate            JDate class.
	 */
	public static function getDateWithTimezone($date = 'now', $convertToUTC = false, $userId = 0)
	{
		$tz = JFactory::getConfig()->get('offset', 'UTC');
		$tz = self::getUser($userId)->getParam('timezone', $tz);
		$timezone = new DateTimeZone($tz);

		if (empty($date))
		{
			return JFactory::getDate('0000-00-00');
		}

		if ($convertToUTC && $tz != 'UTC')
		{
			$tmpDate = new DateTime($date, $timezone);
			$tmpDate->setTimeZone(new DateTimeZone('UTC'));
			$date = $tmpDate->format('Y-m-d H:i:s');

			return JFactory::getDate($date);
		}

		$date = JFactory::getDate($date);
		$date->setTimeZone($timezone);

		return $date;
	}

	/**
	 * Method for get user data. Avoid the JUser::_load warning message
	 *
	 * @param   int  $userId  ID of user
	 *
	 * @return  JUser         Object user data
	 */
	public static function getUser($userId = null)
	{
		$userId = (int) $userId;

		if (!$userId || !JUser::getInstance()->load($userId))
		{
			return JFactory::getUser();
		}

		return JFactory::getUser($userId);
	}

	/**
	 * Method for load GoogleMap Javascript library v3
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function loadGoogleMapJavascriptLibrary()
	{
		$doc       = JFactory::getDocument();
		$settings  = JComponentHelper::getParams('com_reditem');
		$gKey      = $settings->get('googleApiKey', '');

		if (!empty($gKey))
		{
			$jsLibrary = 'https://maps.googleapis.com/maps/api/js?key=' . $gKey;

			// If this library not load, load it
			if (!array_key_exists($jsLibrary, $doc->_scripts))
			{
				$doc->addScript($jsLibrary);

				return true;
			}
		}

		return false;
	}
}
