<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Slide
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.modal', 'a.modal-thumb');

?>
<?php foreach ($this->basicFields as $field) : ?>
<div class="control-group">
	<?php if ($field->type == 'Spacer') : ?>
		<?php if (!$firstSpacer) : ?>
			<hr />
		<?php else : ?>
			<?php $firstSpacer = false; ?>
		<?php endif; ?>
		<?php echo $field->label; ?>
	<?php elseif ($field->hidden) : ?>
		<?php echo $field->input; ?>
	<?php else : ?>
	<div class="control-label">
		<?php echo $field->label; ?>
	</div>
	<div class="controls">
		<?php echo $field->input; ?>
	</div>
	<?php endif; ?>
</div>
<?php endforeach; ?>

<ul class="nav nav-tabs" id="videoTab">
	<?php if (count($this->outputFields)): ?>
		<?php $first = true; ?>
		<?php foreach ($this->outputFields as $fkey => $fobject): ?>
			<li <?php echo $first?'class="active"':''; $first = false; ?>>
				<a href="#<?php echo $fkey ?>" data-toggle="tab"><strong><?php echo JText::_($fkey); ?></strong></a>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>
<div class="tab-content">
	<?php if (count($this->outputFields)): ?>
		<?php $first = true; ?>
		<?php foreach ($this->outputFields as $fkey => $fobject): ?>
			<div class="tab-pane<?php echo $first ? " active": ""; $first = false; ?>" id="<?php echo $fkey ?>">
			<?php foreach ($fobject as $field) : ?>
				<div class="control-group">
					<?php if ($field->type == 'Spacer') : ?>
						<?php if (!$firstSpacer) : ?>
							<hr />
						<?php else : ?>
							<?php $firstSpacer = false; ?>
						<?php endif; ?>
						<?php echo $field->label; ?>
					<?php elseif ($field->hidden) : ?>
						<?php echo $field->input; ?>
					<?php else : ?>
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->input; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
