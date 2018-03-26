<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts.Fields.Items.List
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDCORE') or die;

RHelperAsset::load('select2/select2.min.js', 'com_reditem');
RHelperAsset::load('select2/select2.min.css', 'com_reditem');

$exclude  = $displayData['exclude'];
$field    = $displayData['field'];
$ajaxUrl  = $displayData['ajaxUrl'];
$limit    = (int) $displayData['limit'];
$readOnly = false;

// Initialize some field attributes.
$attr  = !empty($field->class) ? ' class="' . $field->class . '"' : '';
$attr .= !empty($field->size) ? ' size="' . $field->size . '"' : '';
$attr .= !empty($field->autofocus) ? ' autofocus' : '';

if ((string) $field->required == '1' || (string) $field->required == 'true')
{
	$attr .= ' required aria-required="true"';
}

if ((string) $field->multiple == '1' || (string) $field->multiple == 'true')
{
	$attr .= ' multiple="true"';
}

// To avoid user's confusion, readonly="true" should imply disabled="true".
if ((string) $field->readonly == '1' || (string) $field->readonly == 'true' || (string) $field->disabled == '1'|| (string) $field->disabled == 'true')
{
	$attr .= ' disabled="disabled"';
}

// Initialize JavaScript field attributes.
$attr .= !empty($field->onchange) ? ' onchange="' . $field->onchange . '"' : '';

$attr = trim($attr);

if ((string) $field->readonly == '1' || (string) $field->readonly == 'true')
{
	$readOnly = true;
}

JFactory::getDocument()->addScriptDeclaration("
			(function($){
				function formatItem (item) {
					if (item.placeholder) return item.placeholder;
					var text = '<span class=\'ajax-option\' id=\'' + item.id + '\'>' + item.text + '</span>';

					return text;
			    }

				function formatItemSelection (item) {
					if (item.placeholder) return item.placeholder;
					var text = '<span class=\'ajax-option\' id=\'' + item.id + '\'>' + item.text + '</span>';

					return text;
				}

				$(document).ready(function () {
					$('#" . $field->id . "').select2({
						ajax: {
							url      : '" . $ajaxUrl . "',
							dataType : 'json',
							delay    : 250,
							data     : function(params) {
								return {
									search  : params.term,
					                page    : params.page,
					                exclude : '" . $exclude . "',
					                limit   : '" . $limit . "'
					            };
							},
					        processResults: function(data, params) {
								params.page = params.page || 1;

								return {
									results    : data.items,
									pagination : {
										more   : (params.page * " . $limit . ") < data.total
									}
								};
							},
					        cache : true
						},
						escapeMarkup       : function (markup){ return markup; },
						minimumInputLength : 1,
						templateResult     : formatItem,
						templateSelection  : formatItemSelection,
						allowClear         : true,
						placeholder        : {
							id          : '',
							placeholder : '" . JText::_('COM_REDITEM_SELECT_A_ITEM') . "'
						}
					});
				});
			})(jQuery);
		");
?>
<?php if (empty($ajaxUrl)) :?>
	<p><?php echo JText::_('COM_REDITEM_FIELD_AJAX_LIST_URL_NOT_SET'); ?></p>
<?php else : ?>
	<select <?php echo $attr;?>
	       id="<?php echo $field->id; ?>"
	       name="<?php echo $field->name; ?>"
		<?php if ($readOnly) : ?>disabled<?php endif; ?>
	>
		<?php foreach ($field->value as $val) : ?>
		<option value="<?php echo $val['val']; ?>" selected="selected"><?php echo $val['text']; ?></option>
		<?php endforeach; ?>
	</select>
<?php endif;?>
