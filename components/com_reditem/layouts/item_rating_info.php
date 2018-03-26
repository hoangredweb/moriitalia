<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$stars = $displayData['stars'];
$step  = $displayData['step'];
$size  = $displayData['size'];
$value = $displayData['value'];
$item  = $displayData['item'];
?>
<div id="item_rating_info_<?php echo $item->id ?>" class="item_rating_info">
	<?php echo round($value, 2) ?>
</div>
