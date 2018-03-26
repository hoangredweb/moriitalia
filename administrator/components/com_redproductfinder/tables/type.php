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
class RedproductfinderTableType extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/** @var string Whether or not a tag is published */
	var $published = null;

	/** @var string Whether or not a tag is checked out */
	var $checked_out = null;

	/** @var string When a tag is checked out */
	var $checked_out_time = null;

	/** @var integer The order of the tag */
	var $ordering = 0;

	/** @var string Type name */
	var $type_name = null;

	/** @var string Type selection box */
	var $type_select = null;

	/** @var string Tooltip to show with type */
	var $tooltip = null;

	/** @var int ID of the form the type belongs to */
	var $form_id = null;

	/** @var int is show picker or no*/
	var $picker = null;

	/** @var int extrafield of type*/
	var $extrafield = null;

	/** @var int ID of the form the type belongs to */
	var $publish_up = null;

	/** @var int ID of the form the type belongs to */
	var $publish_down = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__redproductfinder_types', 'id', $db);
	}
}
