<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$cate_arr = array();
foreach ($list as $item) {
	if ($item->displayCategoryTitle) {
		if (!in_array($item->displayCategoryTitle, $cate_arr)) {
			array_push($cate_arr, $item->displayCategoryTitle);
		}
	}
}
?>
<ul class="category-module<?php echo $moduleclass_sfx; ?>">


	<?php
	foreach ($cate_arr as $catetitle) {
		echo '<li class="articleitem">';
		echo $catetitle;
		echo '</li>';
	}
	?>
</ul>
