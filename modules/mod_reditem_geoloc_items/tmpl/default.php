<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_geoloc_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
?>

<div class="mod_reditem_geoloc_items_wrapper <?php echo $moduleclass_sfx; ?>">
	<?php if (!empty($items)) : ?>
	<div class="span12">
		<?php foreach ($items as $item):?>
		<div class="span3">
			<!-- ITEM OUTPUT -->
		</div>
		<?php endforeach; ?>
	</div>
	<?php else : ?>
		<p><?php echo JText::_('MOD_REDITEM_GEOLOC_ITEMS_ERROR_NO_ITEM_FOUND'); ?>
	<?php endif; ?>
</div>
