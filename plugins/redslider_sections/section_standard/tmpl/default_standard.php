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

?>
<?php foreach ($this->form->getGroup('params') as $field) : ?>
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
