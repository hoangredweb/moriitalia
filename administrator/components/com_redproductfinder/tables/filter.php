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
 * RedPRODUCTFINDER Filter Table.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderTableFilter extends JTable
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

	/** @var string Tag type 1 */
	var $filter_name = null;

	/** @var varchar of the free text */
	var $type_select = null;

	/** @var varchar of the free text */
	var $tag_id = null;

	/** @var varchar of the free text */
	var $select_name = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  a database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__redproductfinder_filters', 'id', $db);
	}
}
