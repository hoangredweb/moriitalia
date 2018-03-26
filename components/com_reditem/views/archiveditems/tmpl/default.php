<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$itemId = JFactory::getApplication()->input->getInt('Itemid', 0);

JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');
?>

<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<div class="archived-items-wrapper">
	<div class="archived-items-inner">
		<?php if (count($this->items)): ?>
			<?php foreach ($this->items as $item): ?>
				<div class="reditem">
					<?php echo $item->replacedContent; ?>	
				</div>
			<?php endforeach ?>
			<div class="pagination" id="reditemArchiveditemsPagination">
				<?php echo $this->pagination; ?>
			</div>
		<?php else: ?>
			<?php echo JText::_('COM_REDITEM_ERROR_NO_ITEM_FOUND'); ?>
		<?php endif ?>
	</div>
</div>