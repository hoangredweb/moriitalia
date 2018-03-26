<?php
/**
 * @package     Reditem.Module
 * @subpackage  Frontend.mod_social_feed
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::script(JURI::base() . 'media/mod_social_feed/js/jquery.socialfeed.js');
JHtml::script(JURI::base() . 'media/mod_social_feed/js/doT/doT.min.js');
JHtml::script(JURI::base() . 'media/mod_social_feed/js/moment/moment.min.js');
JHtml::script(JURI::base() . 'media/mod_social_feed/js/moment/da.js');
?>
<div class="<?php echo trim('mod-social_feed ' . $moduleclass_sfx); ?>">
	<div class="mod-social_feed-container">
		<button class="" id="button-update"><i class="fa fa-refresh"></i></button>
        <div class="social-feed-container"></div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {

	    var updateFeed = function() {

	        var initialQueryFb = '<?php echo $params->get('fb_account'); ?>';
	        initialQueryFb = initialQueryFb.replace(" ", "");
	        var queryTagsFb = initialQueryFb.split(",");

	        var initialQueryIns = '<?php echo $params->get('ins_account'); ?>';
	        initialQueryIns = initialQueryIns.replace(" ", "");
	        var queryTagsIns = initialQueryIns.split(",");

	        jQuery('.social-feed-container').socialfeed({
	            // FACEBOOK
	            facebook: {
	                accounts: queryTagsFb,
	                limit: '<?php echo $params->get('fb_limit'); ?>',
	                access_token: '<?php echo $params->get('fb_app_id'); ?>|<?php echo $params->get('fb_app_secret'); ?>'
	            },
	            // INSTAGRAM
	            instagram: {
	                accounts: queryTagsIns,
	                limit: '<?php echo $params->get('ins_limit'); ?>',
	                client_id: '<?php echo $params->get('ins_client_id'); ?>',
	                access_token: '<?php echo $params->get('ins_client_secret_key'); ?>'
	            },

	            // GENERAL SETTINGS
	            length: '<?php echo $params->get('length'); ?>',
	            show_media: '<?php echo $params->get('show_media'); ?>',
	            // Moderation function - if returns false, template will have class hidden
	            moderation: function(content) {
	                return (content.text) ? content.text.indexOf('fuck') == -1 : true;
	            },
	            //update_period: 5000,
	            // When all the posts are collected and displayed - this function is evoked
	            callback: function() {
	                console.log('all posts are collected');
	            }
	        });
	    };

	    updateFeed();
	    jQuery('#button-update').click(function() {
	        //first, get rid of old data/posts.
	        jQuery('.social-feed-container').html('');

	        //then load new posts
	        updateFeed();
	    });

	});
</script>
