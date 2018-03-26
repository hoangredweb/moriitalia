<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag        = $displayData['tag'];
$value      = $displayData['value'];
$item       = $displayData['item'];
$templateId = $displayData['templateId'];
?>

<?php if (!empty($value)) : ?>
<span class="reditem_itemfromtypes reditem_itemfromtypes_<?php echo $tag->id; ?>">
	<?php foreach ($value as $val):?>
	<?php
		$tmp       = explode('|', $val);
		$tableName = $tmp[0];
		$itemId    = $tmp[1];
	?>
	<?php echo ReditemHelperItem::renderItem($itemId, $templateId); ?>
	<?php endforeach; ?>
</span>
<?php endif; ?>
