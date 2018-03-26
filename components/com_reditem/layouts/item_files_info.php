<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$field = $displayData['field'];
$item = $displayData['item'];

?>
<div id="item_files_info_<?php echo $item->id ?>" class="item_files_info">
	<?php  echo count($field) ?>
</div>
