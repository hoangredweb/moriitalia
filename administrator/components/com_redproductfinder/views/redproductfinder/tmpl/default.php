<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>

<form action="<?php echo JRoute::_('index.php?option=com_redproductfinder'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="span12">
		<div><?php echo JHTML::_('image', JURI::root().'media/com_redproductfinder/images/redproductfinder_logo_400width.png', JText::_('redPRODUCTFINDER')); ?></div>

		<table class="table table-striped" id="typeslist" class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_REDPRODUCTFINDER_VIEWS_REDPRODUCTFINDER_NAME'); ?></th>
				<th><?php echo JText::_('COM_REDPRODUCTFINDER_VIEWS_REDPRODUCTFINDER_TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->stats as $key => $stat) { ?>
				<tr>
					<td><?php echo JText::_($key); ?></td>
					<td><?php echo $stat['total']; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>

</form>