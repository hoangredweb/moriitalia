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

<div class="reditem_customfield_number">
	<input type="text" name="cform[number][<?php echo $fieldcode; ?>]" id="cform_number_<?php echo $fieldcode; ?>" value="<?php echo $value; ?>" <?php echo $attributes; ?> />
</div>