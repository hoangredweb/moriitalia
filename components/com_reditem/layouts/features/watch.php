<?php
/**
 * @package     RedITEM
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$id         = $displayData["item_id"];
$user       = ReditemHelperSystem::getUser();
$isWatching = ReditemHelperWatch::isUserWatching($user->id, $id);
$btnLabel   = $isWatching ? JText::_("COM_REDITEM_ITEM_UNWATCH_BTN") : JText::_("COM_REDITEM_ITEM_WATCH_BTN");
$func       = $isWatching? "ajaxUnwatch" : "ajaxWatch";
?>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$("#watch_<?php echo $id; ?>").click(function(event){
			event.preventDefault();

			var func = $(this).attr('func');
			var btnText = $(this).attr('btnText');
			var watchText = "<?php echo JText::_('COM_REDITEM_ITEM_WATCH_BTN') ?>";
			var unwatchText = "<?php echo JText::_('COM_REDITEM_ITEM_UNWATCH_BTN') ?>";

			$.ajax({
				type: "POST",
				url: "<?php echo JUri::root() ?>index.php?option=com_reditem&task=item." + func,
				data: {
					id: "<?php echo $id; ?>",
				},
				success: function(e){
					func = (func == "ajaxWatch")? "ajaxUnwatch" : "ajaxWatch";
					$("#watch_<?php echo $id; ?>").attr("func", func);

					btnText = (btnText == watchText)? unwatchText : watchText;
					$("#watch_<?php echo $id; ?>").attr("btnText", btnText);
					$("#watch_<?php echo $id; ?>").text(btnText);
				}
			});
		});
	});
</script>

<div class="watch_btn">
	<a href="#" id="watch_<?php echo $id; ?>" func="<?php echo $func ?>" btnText="<?php echo $btnLabel ?>"><?php echo $btnLabel ?></a>
</div>
