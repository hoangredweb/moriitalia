<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Item
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

JHtml::_('rbootstrap.tooltip', '.hasTooltip', array('placement' => 'right'));

$customfields = array();
$customfieldGroups = array();
$customfieldUnGroups = array();

// Group custom fields by group name.
if ($this->customfields)
{
	foreach ($this->customfields as $customfield)
	{
		$fieldParams = new JRegistry($customfield->params);
		$fieldGroup = $fieldParams->get('group');
		$fieldGroupAlias = JFilterOutput::stringURLSafe($fieldGroup);

		if (!$fieldGroup)
		{
			$customfieldUnGroups[] = $customfield;
		}
		else
		{
			if (!isset($customfields[$fieldGroupAlias]))
			{
				$customfields[$fieldGroupAlias] = array();
				$customfieldGroups[$fieldGroupAlias] = $fieldGroup;
			}

			$customfields[$fieldGroupAlias][] = $customfield;
		}
	}
}

ksort($customfieldGroups);
?>
<div class="row-fluid reditem-admin">
	<?php if ($customfieldGroups) : ?>
	<div class="span2">
		<ul class="nav nav-pills nav-stacked">
			<li class="active">
				<a href="#ungroup" data-toggle="tab"><i><?php echo JText::_('COM_REDITEM_FIELDS_UNGROUP'); ?></i></a>
			</li>
			<?php foreach ($customfieldGroups as $id => $name) : ?>
			<li>
				<a href="#<?php echo $id; ?>" data-toggle="tab"><?php echo $name; ?></a>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<?php if ($customfieldGroups) : ?>
	<div class="span10">
	<?php endif; ?>
		<fieldset class="form-horizontal">
			<div class="tab-content">
				<div class="tab-pane active" id="ungroup">
					<?php foreach ($customfieldUnGroups as $customfield) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $customfield->getLabel(); ?>
						</div>
						<div class="controls">
							<?php echo $customfield->render(); ?>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php foreach ($customfieldGroups as $id => $name) : ?>
				<div class="tab-pane" id="<?php echo $id; ?>">
					<?php if (isset($customfields[$id]) && !empty($customfields[$id])) : ?>
						<?php foreach ($customfields[$id] as $customfield) : ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $customfield->getLabel(); ?>
							</div>
							<div class="controls">
								<?php echo $customfield->render(); ?>
							</div>
						</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
	<?php if ($customfieldGroups) : ?>
	</div>
	<?php endif; ?>
</div>
