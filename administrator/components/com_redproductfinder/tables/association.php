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
 * RedPRODUCTFINDER Association Table.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderTableAssociation extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/** @var string Whether or not a product is published */
	var $published = null;

	/** @var string Whether or not a product is checked out */
	var $checked_out = null;

	/** @var string When a product is checked out */
	var $checked_out_time = null;

	/** @var integer The order of the product */
	var $ordering = 0;

	/** @var integer The ID of the Redshop product */
	var $product_id = 0;

	/** @var varchar of the free text */
	var $aliases = null;

	/**
	 * Database A database connector object
	 *
	 * @param   JDatabase  $db  Jdatabase object
	 */
	function __construct($db)
	{
		parent::__construct('#__redproductfinder_associations', 'id', $db);
	}
}
