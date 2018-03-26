<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$fieldcode = $displayData['fieldcode'];
$value     = $displayData['value'];
$options   = $displayData['options'];
$editor    = JFactory::getEditor();
$default   = $displayData['default'];
$isNew     = JFactory::getApplication()->input->getInt('id', 0) == 0;

if ($isNew && !empty($default))
{
	$value = $default;
}
?>

<div class="reditem_customfield_editor">
	<?php echo $editor->display('jform[fields][editor][' . $fieldcode . ']', $value, 640, 400, 100, 30, $options); ?>
</div>
