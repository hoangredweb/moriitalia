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
class RedproductfinderTableForm extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/** @var string The IP address or range to block */
	var $formname = null;

	/** @var string Whether or not the entry is published */
	var $published = null;

	/** @var string Whether or not the entry is published */
	var $publish_up = 0;

	/** @var string Whether or not the entry is published */
	var $publish_down = 0;

	/** @var string Whether or not the competition name is shown */
	var $showname = null;

	/** @var string CSS classname to allow individual styling */
	var $classname = null;

	/** @var string Whether or not the entry is dependency */
	var $dependency = 0;

	/** @var datetime default value is null*/
	var $checked_out = null;

	/** @var datetime default value is null*/
	var $checked_out_time = null;

	/** @var int default value is 0*/
	var $formexpires = 0;

	/** @var datetime default value is null*/
	var $created = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  Jdatabase object
	 */
	public function __construct($db)
	{
		parent::__construct('#__redproductfinder_forms', 'id', $db);
	}
}
