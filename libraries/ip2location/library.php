<?php
/**
 * IP2Location Library file.
 * Including this file into your application will make IP2Location available to use.
 *
 * @package    IP2Location.Library
 * @copyright  Copyright (C) 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

// JPATH ip2location defines
define('JPATH_IP2LOCATION_LIBRARY', __DIR__);

// Load class files
require_once JPATH_IP2LOCATION_LIBRARY . '/class/ip2locationrecord.php';
require_once JPATH_IP2LOCATION_LIBRARY . '/class/ip2location.php';
