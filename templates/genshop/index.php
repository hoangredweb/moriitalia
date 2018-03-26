<?php
/**
 * @package    Template.Function
 *
 * @copyright  Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the framework
require dirname(__FILE__) . '/wright/wright.php';

// Initialize the framework and
$tpl = Wright::getInstance();
$tpl->addJSScript(JURI::root() . 'templates/' . $this->template . '/js/jquery.elevatezoom.js');
$tpl->addJSScript(JURI::root() . 'templates/' . $this->template . '/js/js.js');
$tpl->addJSScript(JURI::root() . 'templates/' . $this->template . '/js/grids.js');
$tpl->addJSScript(JURI::root() . 'templates/' . $this->template . '/js/select2.min.js');
$tpl->addJSScript(JURI::root() . 'templates/' . $this->template . '/js/customjs.js');

unset($this->_scripts[JURI::root(true).'/media/jui/js/bootstrap.min.js']);

$tpl->display();
