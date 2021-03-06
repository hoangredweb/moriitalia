<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.4.1
 * @build-date      2015/07/04
 */

defined('_JEXEC') or die('Restricted access');

class TableJFBConnectOpenGraphObject extends JTable
{
	var $id = null;
	var $plugin = null;
    var $system_name = null;
    var $display_name = null;
    var $type = null;
	var $published = 0;
    var $fb_built_in = false;
    var $params = "";
    var $created = null;
    var $modified = null;

	function TableJFBConnectOpenGraphObject(&$db)
	{
		parent::__construct('#__opengraph_object', 'id', $db);
	}
}