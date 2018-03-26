<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$item = $displayData['item'];

?>
<span class="readmore">
    <?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_READMORE'); ?>
</span>
<a href="<?php echo $item->itemLink;?>"><?php echo $item->title; ?></a>