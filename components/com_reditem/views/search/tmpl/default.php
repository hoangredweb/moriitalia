<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();

$itemId = $app->input->getInt('Itemid', 0);
$typeId = $app->input->getInt('typeId', 0);
$templateId = $app->input->getInt('templateId', 0);

JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');
JHtml::_('rjquery.chosen', 'select');

$action = 'index.php?option=com_reditem&view=search&typeId=' . $typeId . '&templateId=' . $templateId . '&Itemid=' . $itemId;
?>

<script type="text/javascript">
	(function($){
		$('#reditemSearch').submit(function(event) {
			event.preventDefault();
			return false;
		});
	})(jQuery);
</script>

<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<div class="reditem">
	<?php if (empty($this->content)) : ?>
	<p><?php echo JText::_('COM_REDITEM_ERROR_NO_ITEMS_FOUND'); ?></p>
	<?php endif; ?>
	<div class="reditem_search">
		<form action="<?php echo JRoute::_($action) ?>" class="admin" id="reditemSearch" method="POST" name="adminForm">
			<?php echo $this->content; ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="view" value="search" />
			<input type="hidden" name="<?php echo $this->data->paginationPrefix; ?>limitstart" />
			<input type="hidden" name="current_url" value="<?php echo $action ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
