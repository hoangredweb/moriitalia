<?php
// Wright v.3 Override: Joomla 2.5.18
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;
?>
<div class="registration-complete<?php echo $this->pageclass_sfx;?> hidden">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">  <?php // Wright v.3: Added page header ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>  <?php // Wright v.3: Added page header ?>
	</div>
	<?php endif; ?>
</div>
