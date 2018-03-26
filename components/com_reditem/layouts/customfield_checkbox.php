<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag  = $displayData['tag'];
$data = $displayData['value'];
$item = $displayData['item'];
?>

<?php if (!empty($data)) : ?>
<div class="reditem_checkbox reditem_checkbox_<?php echo $tag->id; ?>">
	<ul>
		<?php foreach ($data as $value) : ?>
		<li><?php echo $value->text; ?></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>
