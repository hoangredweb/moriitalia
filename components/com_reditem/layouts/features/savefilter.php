<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$type = $displayData['type'];
?>

<a class="reditem-save-filter btn btn-default" id="reditem_save_filter_<?php echo $type->id ?>"
	title="<?php echo JText::_('COM_REDITEM_SAVE_FILTER') ?>"
	href="javascript:void(0);" onClick="javascript:reditemSaveFilter();">
	<?php echo JText::_('COM_REDITEM_SAVE_FILTER') ?>
</a>
