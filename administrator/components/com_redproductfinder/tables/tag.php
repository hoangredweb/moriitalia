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
class RedproductfinderTableTag extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/** @var string Whether or not a tag is published */
	var $published = null;

	/** @var integer Whether or not a tag is published_up */
	var $publish_up = null;

	/** @var integer Whether or not a tag is published_down */
	var $publish_down = null;

	/** @var string Whether or not a tag is checked out */
	var $checked_out = null;

	/** @var string When a tag is checked out */
	var $checked_out_time = null;

	/** @var integer The order of the tag */
	var $ordering = 0;

	/** @var integer The order of the tag */
	var $type_id = 0;

	/** @var string Tag type 1 */
	var $tag_name = null;

	/** @var varchar of the free text */
	var $aliases = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  A database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__redproductfinder_tags', 'id', $db);
	}
}
