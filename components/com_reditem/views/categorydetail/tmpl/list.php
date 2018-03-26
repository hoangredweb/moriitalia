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
<?php if ($this->list): ?>
	<div class="reditem">
		<div class="reditem_categories">
			<?php foreach ($this->list as $category): ?>
				<?php if ($category->items): ?>
					<h3><?php echo $category->title; ?></h3>
					<ul class="reditem_category">
						<?php foreach ($category->items as $item): ?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_reditem&view=itemdetail&id=' . $item->id . '&cid=' . $category->id)?>">
									<?php echo $item->title; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<a href="<?php echo JRoute::_('index.php?option=com_reditem&view=categorydetail&id=' . $category->id)?>">
						<?php echo JText::sprintf('COM_REDITEM_CATEGORYDETAIL_VIEW_ALL_ITEMS', count($category->items)); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>
