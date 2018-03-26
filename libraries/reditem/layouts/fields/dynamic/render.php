<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts.fields.dynamic
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$options    = $displayData['options'];
$field      = $displayData['field'];
$attributes = $displayData['attributes'];

RHelperAsset::load('lib/jquery-ui/jquery-ui.min.js', 'redcore');
RHelperAsset::load('lib/jquery-ui/jquery-ui.custom.min.css', 'redcore');
?>

<script type="text/javascript">
	var reditemDynamicRowIndex = <?php echo count($options); ?>;

	(function($){
		$(document).ready(function(){
			$('#reditem-dynamic-table .dynamic-value, #reditem-dynamic-table .dynamic-text').change(function(event){
				event.preventDefault();
				reditemDynamicRefreshValue();
			});

			// Add option
			$('#reditem-dynamic-add').click(function(event){
				event.preventDefault();

				var index = reditemDynamicRowIndex;

				$('<tr>').attr('id', 'dynamic-row-' + index).attr('class', 'dynamic-row')
					.append(
						$('<td>').append(
							$('<input>').attr('type', 'text').addClass('input dynamic-value').change(function(event){
								event.preventDefault();
								reditemDynamicRefreshValue();
							})
						)
					)
					.append(
						$('<td>').append(
							$('<input>').attr('type', 'text').addClass('input dynamic-text').change(function(event){
								event.preventDefault();
								reditemDynamicRefreshValue();
							})
						)
					)
					.append(
						$('<td>').append(
							$('<a>').attr('href', 'javascript:void(0);')
								.addClass('btn btn-danger dynamic-remove icon-remove pull-right')
								.click(function(event){ reditemDynamicRowRemove(index); })
						)
					)
					.appendTo($('#reditem-dynamic-table tbody'));
				$('#reditem-dynamic-table').trigger('row-dynamic-added');

				reditemDynamicRowIndex++;
			});

			$('#reditem-dynamic-table').find('tbody').sortable({
				update : reditemDynamicRefreshValue
			});
		});
	})(jQuery);

	function reditemDynamicRowRemove(index)
	{
		(function($){
			var rowId = 'dynamic-row-' + index;
			var table = $('#reditem-dynamic-table');
			table.find('tr#' + rowId).fadeOut('slow', function(){
				this.remove();
				reditemDynamicRefreshValue();
				table.trigger('row-dynamic-removed');
			});
		})(jQuery);
	}

	function reditemDynamicRefreshValue()
	{
		(function($){
			var finalData = '';

			$('#reditem-dynamic-table tbody tr').each(function(){
				var value = $.trim($(this).find('input.dynamic-value').val());
				var text  = $.trim($(this).find('input.dynamic-text').val());

				if ((value != '') || (text != '')) {
					if (value == '') value = text;
					if (text  == '') text = value;

					finalData += value + "|" + text + "\n";
				}
			});

			$('#<?php echo $field->id; ?>').html(finalData);
		})(jQuery);
	}
</script>

<?php if (!empty($field->description)): ?>
	<p><?php echo $field->description; ?></p>
<?php endif; ?>
<div class="span6" style="margin-left: 0px;">
	<div class="block">
		<a class="btn btn-primary icon icon-plus" id="reditem-dynamic-add" href="javascript:void(0);"></a>
	</div>
	<div class="clear"></div>
	<table class="table table-hover table-striped" id="reditem-dynamic-table">
		<thead>
			<tr>
				<th width="40%"><?php echo JText::_('COM_REDITEM_FIELD_DYNAMIC_OPTION_VALUE'); ?></th>
				<th width="40%"><?php echo JText::_('COM_REDITEM_FIELD_DYNAMIC_OPTION_TEXT'); ?></th>
				<th width="20%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($options)): ?>
				<?php foreach ($options as $index => $option): ?>
				<tr id="dynamic-row-<?php echo $index; ?>" class="dynamic-row" style="cursor: move">
					<td>
						<input type="text" class="input dynamic-value" value="<?php echo $option['value']; ?>" />
					</td>
					<td>
						<input type="text" class="input dynamic-text" value="<?php echo $option['text']; ?>" />
					</td>
					<td>
						<a class="pull-right btn btn-danger dynamic-remove icon-remove" href="javascript:void(0);" onClick="reditemDynamicRowRemove(<?php echo $index; ?>);"></a>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<textarea id="<?php echo $field->id ?>" name="<?php echo $field->name ?>" class="hidden"
	<?php if ($attributes['required']): ?>
		required
	<?php endif; ?>><?php echo $field->value; ?></textarea>
