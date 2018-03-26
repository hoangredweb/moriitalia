<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$id         = $displayData["item_id"];
$user       = ReditemHelperSystem::getUser();
$isWatching = ReditemHelperWatch::isUserWatching($user->id, $id);
$btnLabel   = '';

if ($isWatching)
{
	$btnLabel = JText::_("COM_REDITEM_ITEM_UNWATCH_BTN");
}
else
{
	$btnLabel = JText::_("COM_REDITEM_ITEM_WATCH_BTN");
}

?>
<div class="watch_btn_info">
	<?php echo $btnLabel ?>
</div>
