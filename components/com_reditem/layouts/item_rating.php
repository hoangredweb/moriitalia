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

RHelperAsset::load('star-rating.min.js', 'com_reditem');
RHelperAsset::load('star-rating.min.css', 'com_reditem');

$ratingLink = JUri::root() . 'index.php?option=com_reditem&task=item.ajaxItemRating&id=' . $item->id;
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$('#reditem_items_rating_<?php echo $item->id; ?>').rating({
				'showCaption': true,
				'defaultCaption': '',
				'starCaptions': {},
				'starCaptionsClasses': {}
			})
			.on('rating.change', function(event, value, caption) {
				var url = "<?php echo $ratingLink; ?>&value=" + value;
				$.ajax({
					url: url,
					dataType: "json",
					cache: false
				})
				.done(function (data){
					if ((data.status != undefined) && (data.status == 1))
						$('#reditem_items_rating_<?php echo $item->id; ?>').rating('update', data.totalValue.rating_value);
					else
						alert("<?php echo JText::_('COM_REDITEM_ITEM_RATING_FAIL'); ?>");
				});
			});
		});
	}(jQuery));
</script>

<div class="reditem_items_rating reditem_items_rating_<?php echo $item->id; ?> <?php if (ReditemHelperSystem::getUser()->guest) : ?>readonly<?php endif; ?>" style="line-height:37px">
	<input id="reditem_items_rating_<?php echo $item->id; ?>"
		type="number"
		class="rating"
		min=0
		max=<?php echo $stars; ?>
		step=<?php echo $step; ?>
		data-stars=<?php echo $stars; ?>
		data-glyphicon="false"
		data-size="<?php echo $size; ?>"
		value="<?php echo $value; ?>"
		<?php if (ReditemHelperSystem::getUser()->guest) : ?>
		data-readonly="true"
		<?php endif; ?>
		/>
</div>
