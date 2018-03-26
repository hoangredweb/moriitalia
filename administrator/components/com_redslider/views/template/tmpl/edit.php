<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHtml::_('rjquery.chosen', 'select');

$isNew = true;

if ($this->item->id)
{
	$isNew = false;
}
?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		// Disable click function on btn-group
		jQuery(".btn-group").each(function(index){
			if (jQuery(this).hasClass('disabled'))
			{
				jQuery(this).find("label").off('click');
			}
		});
		jQuery('.tab-pane .btn-tag').on('click', function (e) {
			var $button = jQuery(this);
			var tag = $button.html().trim().replace(/<em>|<\/em>/g, "");
			var cm = jQuery('.CodeMirror')[0].CodeMirror;
			var doc = cm.getDoc();
			var cursor = doc.getCursor();
			var pos = {
				line: cursor.line,
				ch: cursor.ch
			}
			doc.replaceRange(tag, pos);
			cm.focus();
			doc.setSelection(pos, {line: cursor.line, ch: cursor.ch + tag.length});
		});
	});
</script>
<form enctype="multipart/form-data"
	action="index.php?option=com_redslider&task=template.edit&id=<?php echo $this->item->id; ?>"
	method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">
	<div class="row-fluid">
		<div class="span7">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('section'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('section'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('title'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('title'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('alias'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('alias'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('content'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('content'); ?>
				</div>
			</div>
		</div>
		<div class="span5">
			<div class='template_tags'>
				<?php if (count($this->templateTags)): ?>
				<div class='template_tags'>
					<div class='well'>
						<div class="accordion" id="accordion_tag_default">
							<div class="accordion-heading">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_tag_default" href="#collapseOne">
									<?php echo JText::_('COM_REDSLIDER_TEMPLATE_TAGS_DEFAULT_LBL'); ?>
								</a>
							</div>
							<div id="collapseOne" class="accordion-body collapse in">
								<div class="accordion-inner">
									<div class="tab-pane" id="tag_related">
										<ul>
										<?php foreach ($this->templateTags as $tag => $tagDesc) : ?>
											<li class="block">
												<button type="button" class="btn-tag btn btn-small"><?php echo $tag ?></button>&nbsp;&nbsp;<?php echo $tagDesc ?>
											</li>
										<?php endforeach; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				<div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
