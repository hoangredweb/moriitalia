<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedITEM Ajax Helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helpers
 * @since       2.4.0
 */
abstract class ReditemHelperAjax
{
	/**
	 * Check if we have received an AJAX request for security reasons
	 *
	 * @return  boolean
	 */
	public static function isAjaxRequest()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
			&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/**
	 * Verify that an AJAX request has been received
	 *
	 * @param   string  $method  Method to validate the ajax request
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public static function validateAjaxRequest($method = 'post')
	{
		if (!JSession::checkToken($method) || !static::isAjaxRequest())
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}
	}
}
