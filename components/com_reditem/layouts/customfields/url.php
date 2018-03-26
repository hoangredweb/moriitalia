<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode  = $displayData['fieldcode'];
$value      = $displayData['value'];
$attributes = $displayData['attributes'];
?>

<div class="reditem_customfield_url">
	<p>
		<input type="text"
			class="input-xlarge"
			name="cform[url][<?php echo $fieldcode; ?>]"
			id="cform_url_<?php echo $fieldcode; ?>"
			value="<?php echo $value['link']; ?>"
			placeholder="<?php echo JText::_('COM_REDITEM_FIELD_URL_LINK'); ?>"
			<?php echo $attributes; ?> />
	</p>
	<p>
		<input type="text"
			class="input-xlarge"
			name="jform[cform][url][<?php echo $fieldcode; ?>]"
			id="jform_cform_url_<?php echo $fieldcode; ?>"
			value="<?php echo $value['title']; ?>"
			placeholder="<?php echo JText::_('COM_REDITEM_FIELD_URL_TITLE'); ?>"
			<?php echo $attributes; ?> />
	</p>
</div>
