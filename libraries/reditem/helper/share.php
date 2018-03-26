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
 * Share helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Share
 * @since       2.1
 *
 */
class ReditemHelperShare
{
	/**
	 * Replace {item_sharing} tag
	 *
	 * @param   string  &$content  Template content
	 * @param   object  $item      Item object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function replaceTag(&$content, $item)
	{
		// Check if item object is null
		if (empty($content) || empty($item))
		{
			return false;
		}

		// Get configuration of component
		$redItemConfiguration = JComponentHelper::getParams('com_reditem');

		$addThis = array();
		$addThis['FBLike']     = (boolean) $redItemConfiguration->get('addThisFBLike', 0);
		$addThis['FBShare']    = (boolean) $redItemConfiguration->get('addThisFBShare', 0);
		$addThis['GooglePlus'] = (boolean) $redItemConfiguration->get('addThisGooglePlus', 0);
		$addThis['Email']      = (boolean) $redItemConfiguration->get('addThisEmail', 0);
		$addThis['TweetIt']    = (boolean) $redItemConfiguration->get('addThisTweetIt', 0);
		$addThis['LinkedIn']   = (boolean) $redItemConfiguration->get('addThisLinkedIn', 0);
		$addThis['Pinterest']  = (boolean) $redItemConfiguration->get('addThisPinterest', 0);
		$addThis['More']       = (boolean) $redItemConfiguration->get('addThisMore', 0);

		$layoutData  = array('item' => $item, 'addThis' => $addThis);
		$contentHtml = ReditemHelperLayout::render($item->type, 'item_share', $layoutData, array('component' => 'com_reditem'));
		$content     = str_replace('{item_sharing}', $contentHtml, $content);

		return true;
	}
}
