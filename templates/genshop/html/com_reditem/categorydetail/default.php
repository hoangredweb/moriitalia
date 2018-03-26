<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$itemId = JFactory::getApplication()->input->getInt('Itemid', 0);

JHtml::_('rjquery.framework');
JHtml::_('rholder.image', '100x100');
JHtml::_('rjquery.select2', 'select');

// Load RedITEM stuff
RHelperAsset::load('reditem.min.js');
RHelperAsset::load('reditem.min.css');
?>

<script type="text/javascript">
	var holderlib = '<?php echo JURI::root(); ?>media/redcore/js/lib/holder.js';
	(function($){
		$('#reditemCategoryDetail').submit(function(event) {
			return false;
		});
	})(jQuery);
</script>

<?php if ($this->params->get('show_page_heading')) : ?>
<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php if (empty($this->content)) : ?>
<p><?php echo JText::_('COM_REDITEM_ERROR_NO_CATEGORY_FOUND'); ?></p>
<?php else: ?>
<div class="reditem">
	<div class="reditem_categories">
		<form action="index.php" class="admin" id="reditemCategoryDetail" method="get" name="adminForm">
			<input type="hidden" name="option" value="com_reditem" />
			<input type="hidden" name="view" value="categorydetail" />
			<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
			<input type="hidden" name="templateId" value="<?php echo $this->data->template->id; ?>" />
			<?php echo $this->content; ?>
			<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	</div>
</div>
<?php endif; ?>
