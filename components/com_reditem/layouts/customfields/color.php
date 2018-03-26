<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JHtml::_('script', 'system/html5fallback.js', false, true);
JHtml::_('behavior.colorpicker');

$data       = (object) $displayData;
$attributes = array();
$fieldcode  = $data->fieldcode;
$color      = strtolower($data->value);

$attributes['id']            = $data->id;
$attributes['class']         = $data->element['class'] ? (string) trim('minicolors ' . $data->element['class']) : 'minicolors';
$attributes['required']      = $data->required ? 'required' : null;
$attributes['aria-required'] = $data->required ? 'true' : null;
$attributes['placeholder']   = '#rrggbb';
$attributes['autocomplete']  = (!$data->element['autocomplete']) ? 'off' : 'on';

if ($data->element['readonly'])
{
	$attributes['readonly'] = ($data->element['readonly'] == 'true') ? 'true' : null;
}

$renderedAttributes = null;

if ($attributes)
{
	foreach ($attributes as $attribute => $value)
	{
		if (null !== $value)
		{
			$renderedAttributes .= ' ' . $attribute . '="' . (string) $value . '"';
		}
	}
}

if (!$color || in_array($color, array('none', 'transparent')))
{
	$color = 'none';
}
elseif ($color['0'] != '#')
{
	$color = '#' . $color;
}

$value = htmlspecialchars($color, ENT_COMPAT, 'UTF-8');
?>
<div class="reditem_customfield_color">
	<div class="input-append">
		<input type="text" style="padding: 4px 6px 4px 30px;"
			name="cform[color][<?php echo $fieldcode; ?>]"
			value="<?php echo $value; ?>"
			<?php echo $renderedAttributes; ?> />
		<a class="btn hasTooltip" title="" href="#"
			onclick="jQuery('#<?php echo $attributes['id']; ?>').minicolors('value',''); return false;"
			data-original-title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR') . ' (' . strtolower(JText::_('LIB_REDCORE_RCOLOR_SET_TRANSPARENT')) . ')'; ?>">
			<i class="icon-remove"></i>
		</a>
	</div>
</div>
