<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
JHtml::_('rjquery.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		submitbutton(pressbutton);
	};
</script>
<div id="rcCpanel-main-container" class="row-fluid">
	<div class="span8 rcCpanelMainIcons">
		<?php $iconsRow = array_chunk($this->iconArray, 6); ?>
		<?php foreach ($iconsRow as $row) : ?>
		<p></p>
		<div class="row-fluid">
			<?php foreach ($row as $icon) : ?>
			<div class="span2">
                <?php $target = ''; ?>
                <?php if (isset($icon['target']) && $icon['target']): ?>
                    <?php $target = 'target="' . $icon['target'] . '"'; ?>
                <?php endif; ?>
				<a class="rcCpanelIcons" href="<?php echo $icon['link']; ?>" <?php echo $target; ?>>
					<div class="row-fluid pagination-centered">
						<span class="dashboard-icon-link-icon">
							<i class="<?php echo $icon['icon']; ?> icon-5x"></i>
						</span>
					</div>
					<div class="row-fluid pagination-centered">
						<p class="dashboard-icon-link-text">
							<strong><?php echo $icon['title']; ?></strong>
						</p>
					</div>
				</a>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="span4 rcCpanelSideIcons">
		<div class="well">
			<div class="pull-right">
				<strong class="row-title">
					<?php echo JText::_('COM_REDSLIDER_VERSION'); ?>
				</strong>
				<span class="badge badge-success" title="<?php echo JText::_('COM_REDSLIDER_VERSION'); ?>">
					<?php echo $this->redsliderversion; ?>
				</span>
			</div>
			<p class="clearfix"></p>
			<table class="table table-striped adminlist">
			<tr>
				<td>
					<strong><?php echo JText::_('COM_REDSLIDER_GALLERIES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->galleries; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_REDSLIDER_SLIDES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->slides; ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_REDSLIDER_TEMPLATES'); ?></strong>
				</td>
				<td>
					<span class="badge"><?php echo $this->stats->templates; ?></span>
				</td>
			</tr>
		</table>
		</div>
	</div>
</div>
