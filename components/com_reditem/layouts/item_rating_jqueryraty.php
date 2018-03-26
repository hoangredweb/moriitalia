<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$stars = $displayData['stars'];
$step  = $displayData['step'];
$size  = $displayData['size'];
$value = $displayData['value'];
$item  = $displayData['item'];

RHelperAsset::load('jquery/jquery.raty.customize.min.js', 'com_reditem');
RHelperAsset::load('jquery/jquery.raty.min.css', 'com_reditem');

$ratingLink = JUri::root() . 'index.php?option=com_reditem&task=item.ajaxItemRating&id=' . $item->id;
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#reditem_items_rating_<?php echo $item->id; ?>').raty({
				score: <?php echo (float) $value; ?>,
				numberMax: <?php echo $stars; ?>,
				number: <?php echo $stars * $step; ?>,
				half: false,
				round: { up: 0.9 },
				<?php if (ReditemHelperSystem::getUser()->guest) : ?>
				readOnly: true,
				<?php endif; ?>
				click: function(score, evt) {
					var url = "<?php echo $ratingLink; ?>&value=" + score;
					$.ajax({
						url: url,
						dataType: "json",
						cache: false
					})
					.done(function (data){
						if ((data.status != undefined) && (data.status == 1))
							$('#reditem_items_rating_<?php echo $item->id; ?>').raty('score', data.totalValue);
						else
							alert("<?php echo JText::_('COM_REDITEM_ITEM_RATING_FAIL'); ?>");
					});
				},
				target: "#reditem_items_rating_<?php echo $item->id; ?>_value",
				targetType : 'score',
				starOn: "<?php echo JHtml::_('image', 'com_reditem/jquery-raty/star-on.png', null, null, true, true); ?>",
				starOff: "<?php echo JHtml::_('image', 'com_reditem/jquery-raty/star-off.png', null, null, true, true); ?>",
				starHalf: "<?php echo JHtml::_('image', 'com_reditem/jquery-raty/star-half.png', null, null, true, true); ?>"
			});
		});
	}(jQuery));
</script>

<span class="reditem_items_rating reditem_items_rating_<?php echo $item->id; ?> <?php if (ReditemHelperSystem::getUser()->guest) : ?>readonly<?php endif; ?>">
	<div id="reditem_items_rating_<?php echo $item->id; ?>"></div>
	<input id="reditem_items_rating_<?php echo $item->id; ?>_value" type="hidden" value="<?php echo $value; ?>" />
</span>
