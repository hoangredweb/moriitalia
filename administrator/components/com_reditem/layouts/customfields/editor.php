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
$options	= $displayData['options'];

$editor = JFactory::getEditor();

?>

<div class="reditem_customfield_editor">
	<?php echo $editor->display('cform[editor][' . $fieldcode . ']', $value, 640, 400, 100, 30, $options); ?>
</div>
