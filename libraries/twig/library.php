<?php
/**
 * Twig library file.
 * Including this file into your application will make Twig available to use.
 *
 * @package    Twig.Library
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

// Define library path
define('JPATH_TWIG_LIBRARY', __DIR__);

// Load Twig template system
require_once JPATH_TWIG_LIBRARY . '/lib/Autoloader.php';

// Register Twig for use
Twig_Autoloader::register();
