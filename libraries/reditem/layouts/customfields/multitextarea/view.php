<?php
/**
 * @package     RedITEM.Layouts
 * @subpackage  Customfields.Multitextarea
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag  = $displayData['tag'];
$data = $displayData['data'];
$item = $displayData['item'];
?>

<?php if (!empty($data)) : ?>
<div class="reditem_multitextarea reditem_multitextarea_<?php echo $tag->id; ?>">
	<?php foreach ($data as $textarea) : ?>
	<?php $user = JFactory::getUser($textarea[0]);?>
	<p class="small"><?php echo JText::sprintf('COM_REDITEM_FIELD_MULTITEXTAREA_WRITTEN_BY', $user->name . ' (' . $user->username . ')');?></p>
	<p><?php echo $textarea[1];?></p>
	<?php endforeach;?>
</div>
<?php endif; ?>
