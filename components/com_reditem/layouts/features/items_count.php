<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$count    = $displayData['count'];
$category = $displayData['category'];
$prefix   = $displayData['prefix'];
?>

<span id="reditem-<?php echo $prefix ?>CategoryDetail-<?php echo $category->id ?>-itemsCount">
	<?php echo $count ?>
</span>
