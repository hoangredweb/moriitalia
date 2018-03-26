<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

/**
 * object  $item     Item data object
 * array   $addThis  Array of addThis configuration
 */
extract($displayData);

$categoryId = !empty($item->categoryId) ? $item->categoryId : 0;
$categoryId = (!$categoryId && !empty($item->categories)) ? $item->categories[0]->id : 0;

$shareLink = ReditemHelperRouter::getItemRoute($item->id, $categoryId);
$shareLink = JRoute::_($shareLink, true, -1);
?>

<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js"></script>
<div class="addthis_toolbox addthis_default_style" addthis:url="<?php echo $shareLink; ?>">
	<?php if ($addThis['FBLike']) : ?>
	<a class="addthis_button_facebook_like addthis_button_facebook" fb:share:layout="icon_link"></a>
	<?php endif; ?>
	<?php if ($addThis['FBShare']) : ?>
	<a class="addthis_button_facebook_share" fb:share:layout="button"></a>
	<?php endif; ?>
	<?php if ($addThis['GooglePlus']) : ?>
	<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
	<?php endif; ?>
	<?php if ($addThis['Email']) : ?>
	<a class="addthis_button_email"></a>
	<a class="addthis_button_mailto"></a>
	<?php endif; ?>
	<?php if ($addThis['TweetIt']) : ?>
	<a class="addthis_button_tweet"></a>
	<?php endif; ?>
	<?php if ($addThis['LinkedIn']) : ?>
	<a class="addthis_button_linkedin_counter"></a>
	<?php endif; ?>
	<?php if ($addThis['Pinterest']) : ?>
	<a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"
		pi:pinit:url="http://www.addthis.com/features/pinterest"
		pi:pinit:media="http://www.addthis.com/cms-content/images/features/pinterest-lg.png"></a>
	<?php endif; ?>
	<?php if ($addThis['More']) : ?>
	<a class="addthis_button_compact"></a>
	<?php endif; ?>
</div>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			// Alert a message when the user shares somewhere
			function eventHandler(evt) {
			    switch (evt.type) {
			        case "addthis.menu.share":
			        	$.ajax({
			        		type: "POST",
							url: "<?php echo JUri::root() ?>index.php?option=com_reditem&task=item.ajaxItemShare",
							data: {
								id: "<?php echo $item->id ?>",
								service: evt.data.service,
								'<?php echo JSession::getFormToken(); ?>' : 1
							}
						});
			            break;
			        default:
			           break;
			    }
			}

			// Listen to various events
			addthis.addEventListener('addthis.menu.share', eventHandler);
		});
	})(jQuery);
</script>
