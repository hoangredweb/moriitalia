<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');
?>

<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<div class="reditem">
	<?php if (!isset($this->item->id)) : ?>
	<p><?php echo JText::_('COM_REDITEM_ERROR_NO_ITEM_FOUND'); ?></p>
	<?php else : ?>
	<div class="reditem_content">
		<?php echo $this->content; ?>
	</div>
	<?php endif; ?>
</div>
