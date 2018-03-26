<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_categories
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rholder.image', '100x100');
?>

<div class="mod_reditem_categories_wrapper">
	<?php if ($categories) : ?>
	<ul>
		<?php foreach ($categories as $category) : ?>
		<li>
			<a href="<?php echo JRoute::_($category->link); ?>">
				<?php echo $category->title; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>	
	<?php endif; ?>
	<?php if ($params->get('show_readmore')) : ?>
		<?php if ($parentDetail) : ?>
			<a class="read_more" href="<?php echo $parentDetail->readmoreLink; ?>">
				<img src="<?php echo $parentDetail->readmoreImage; ?>" />
				<?php echo JText::_('MOD_REDITEM_CATEGORIES_FIELD_READMORE'); ?>
				<?php echo $parentDetail->title; ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>
</div>
