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

if (isset($this->item->useItemGmapField) && ($this->item->useItemGmapField == true)) :
	ReditemHelperSystem::loadGoogleMapJavascriptLibrary();
endif;

$currentUrl = 'index.php?option=com_reditem&view=categorydetail&id=' . $this->data->id . '&templateId=' . $this->data->template->id . '&Itemid=' . $itemId;
$scripts [] = '(function($){$("#reditemCategoryDetail").submit(function(event) {return false;});})(jQuery);';

JFactory::getDocument()->addScriptDeclaration(implode(PHP_EOL, $scripts));
?>


<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php if (empty($this->content)) : ?>
<p><?php echo JText::_('COM_REDITEM_ERROR_NO_CATEGORY_FOUND'); ?></p>
<?php else: ?>
<div class="reditem">
	<div class="reditem_categories">
		<form action="index.php" class="admin" id="reditemCategoryDetail" method="post" name="adminForm">
			<input type="hidden" name="option" value="com_reditem" />
			<input type="hidden" name="view" value="categorydetail" />
			<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
			<input type="hidden" name="templateId" value="<?php echo $this->data->template->id; ?>" />
			<?php echo $this->content; ?>
			<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>" />
			<input type="hidden" name="current_url" value="<?php echo $currentUrl; ?>" />
			<?php echo JHtml::_('form.token') ?>
			<input type="hidden" name="task" value="" />
		</form>
	</div>
</div>
<?php endif; ?>
