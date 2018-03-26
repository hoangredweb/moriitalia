<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;


JHTML::_('behavior.tooltip', '.hasTooltip, .hasTip');
$editor = JFactory::getEditor();
JHTMLBehavior::modal();
$uri = JURI::getInstance();
$url = $uri->root();
?>

<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel' || pressbutton == 'save') {
			submitform(pressbutton);
			return;
		}
	}
</script>

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm"
      enctype="multipart/form-data">
	<div class="col50">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_REDSHOP_USER_POINT_CONFIG'); ?></legend>

			<table class="admintable">
				<?php foreach ($this->lists['groups'] as $key => $group) : ?>
					<tr>
						<td width="100" align="right" class="key">
							<label for="name">
								<?php echo $group->shopper_group_name; ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="point[<?php echo $group->shopper_group_id; ?>]" id="point_<?php echo $group->shopper_group_id; ?>"
							       value="<?php echo (!empty($group->point)) ? $group->point : ''; ?>"/>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</fieldset>

	</div>

	<div class="clr"></div>

	<input type="hidden" name="task" value="edit"/>
	<input type="hidden" name="view" value="user_point_config"/>
</form>
