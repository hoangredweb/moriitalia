<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag   = $displayData['tag'];
$value = $displayData['value'];
$item  = $displayData['item'];
?>

<?php if (!empty($value['filePath'])) : ?>
<span class="reditem_file reditem_file_<?php echo $tag->id; ?>">
	<a href="<?php echo $value['filePath']; ?>" target="_blank">
		<?php echo $value['fileName']; ?>
	</a>
</span>
<?php endif; ?>
