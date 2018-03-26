<?php
/**
 * @package     RedITEM.Library
 * @subpackage  Database.Filter
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Filterers interface
 *
 * @since  2.5.0
 */
interface ReditemDatabaseFilterInterface
{
	/**
	 * Filter data
	 *
	 * @return  mixed
	 */
	public function filter();
}
