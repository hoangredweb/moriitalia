<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode	= $displayData['fieldcode'];
$value		= $displayData['value'];
$attributes	= $displayData['attributes'];
$default    = $displayData['default'];
$isNew      = JFactory::getApplication()->input->getInt('id', 0) == 0;

if (!empty($default) && $isNew)
{
	$value = $default;
}
?>

<div class="reditem_customfield_number">
	<input type="text" name="jform[fields][number][<?php echo $fieldcode; ?>]" id="jform_fields_number_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?> />
</div>