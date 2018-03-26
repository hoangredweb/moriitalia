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
 * RedPRODUCTFINDER Form Table.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderTableKeyword extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__redproductfinder_keyword', 'id', $db);
	}
}
