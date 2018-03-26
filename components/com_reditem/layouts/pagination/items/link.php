<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_REDCORE') or die;

$item = $displayData['data'];
$display = $item->text;
$class   = '';

switch ((string) $item->text) :
	// Check for "Start" item
	case JText::_('JLIB_HTML_START') :
		$icon = "icon-backward";
		break;

	// Check for "Prev" item
	case $item->text == JText::_('JPREV') :
		$item->text = JText::_('JPREVIOUS');
		$icon = "icon-step-backward";
		break;

	// Check for "Next" item
	case JText::_('JNEXT') :
		$icon = "icon-step-forward";
		break;

	// Check for "End" item
	case JText::_('JLIB_HTML_END') :
		$icon = "icon-forward";
		break;

	default:
		$icon = null;
		break;
endswitch;

if ($icon !== null) :
	$display = '<i class="' . $icon . '"></i>';
endif;

if ($displayData['active']) :
	if ($item->base > 0) :
		$limit = 'limitstart.value=' . $item->base;
	else :
		$limit = 'limitstart.value=0';
	endif;

	$cssClasses = array();
	$title      = '';

	if (!is_numeric($item->text)) :
		JHtml::_('rbootstrap.tooltip');
		$cssClasses[] = 'hasTooltip';
		$title = ' title="' . $item->text . '" ';
	endif;
else :
	$class = (property_exists($item, 'active') && $item->active) ? 'active' : 'disabled';
endif;

// If the display object isn't set already, just render the item with its text
if (!isset($display)) :
	$class   = ' class="hidden-phone"';
endif;
?>

<li class="<?php echo $class; ?>">
<?php if ($displayData['active']) : ?>
	<a data-limitstart="<?php echo $item->base?>" data-prefix="<?php echo $item->prefix?>limitstart" class="pageNav <?php echo implode(' ', $cssClasses); ?>" <?php echo $title; ?> href="<?php echo JRoute::_($item->link); ?>"><?php echo $display; ?></a>
<?php else : ?>
	<span><?php echo $display; ?></span>
<?php endif; ?>
</li>
