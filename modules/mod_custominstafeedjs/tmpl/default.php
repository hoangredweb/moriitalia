<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScript(JURI::root().'modules/'.$module->module.'/js/instafeed.min.js');
?>

<script type="text/javascript">
	var feed = new Instafeed({
		target: 'instafeed_<?php echo $module->id; ?>',
		get: 'user',
		userId: '<?php echo $params->get("userId");?>',
		clientId: '<?php echo $params->get("clientId");?>',
		accessToken:'<?php echo $params->get("accessToken");?>',

		resolution: '<?php echo $params->get("resolution");?>',
		<?php if( $params->get("template") != "" ): ?>
			template: "<?php echo addslashes($params->get("template"));?>",
		<?php else:?>
			template: "<div class='hover ehover1'><img src='{{image}}' class='img-responsive'><div class='overlay'><div class='inner'><h2>Ideas</h2><p>Follow us on instagram</p><a class='info' target='_blank' href='{{link}}'>Discover</a></div></div></div>",
		<?php endif;?>
		limit: <?php echo $params->get("limit");?>
	});
	jQuery(window).on('load',function(){
		feed.run();
	});
</script>
<div id="instafeed_<?php echo $module->id; ?>" class="instafeed custom">
</div>