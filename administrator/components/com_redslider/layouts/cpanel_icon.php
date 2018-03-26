<?php
/**
 * @package     RedSLIDER
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


defined('JPATH_REDCORE') or die;

$modal      = isset($displayData['modal']) ? $displayData['modal'] : 0;
$link      = isset($displayData['link']) ? $displayData['link'] : '#';
$image      = isset($displayData['image']) ? $displayData['image'] : '';
$text      = isset($displayData['text']) ? $displayData['text'] : '';

?>
<div class="rbtn-group btn-group <?php echo(JFactory::getLanguage()->isRTL() ? 'pull-right' : 'pull-left'); ?>">
	<?php if ($modal == 1) : ?>
		<?php JHTML::_('behavior.modal'); ?>
		<a class="btn btn-default btn-lg cpanelicon"
		   href="<?php echo $link; ?>&amp;tmpl=component"
		   style="cursor:pointer"
		   class="modal"
		   rel="{handler: 'iframe', size: {x: 800, y: 650}}"
			>
	<?php else : ?>
		<a class="btn btn-default btn-lg cpanelicon" href="<?php echo $link; ?>">
	<?php endif; ?>
	<i class="<?php echo $image; ?> icon-3x"></i>
	<br/>
	<span><?php echo $text; ?></span>
	</a>
</div>
