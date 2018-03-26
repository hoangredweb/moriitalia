<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$comments = $displayData['comments'];
$item = $displayData['item'];
?>
<div id="item_comment_info_<?php echo $item->id ?>" class="item_comment_info">
	<?php echo count($comments) ?>
</div>
