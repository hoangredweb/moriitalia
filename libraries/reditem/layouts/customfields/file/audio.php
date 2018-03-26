<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

RHelperAsset::load('mediaelement/mediaelement-and-player.js', 'com_reditem');
RHelperAsset::load('mediaelement/mediaelementplayer.css', 'com_reditem');

extract($displayData);

$filePath = !empty($value['filePath']) ? $value['filePath'] : '';
?>

<?php if (!empty($filePath)): ?>
<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#reditem-file-audio-<?php echo $item->id ?>-<?php echo $tag->id ?>").mediaelementplayer();
		});
	})(jQuery);
</script>
<audio
	id="reditem-file-audio-<?php echo $item->id ?>-<?php echo $tag->id ?>"
	type="audio/<?php echo strtolower(JFile::getExt($filePath)) ?>"
	controls="controls"
	src="<?php echo $value['filePath'] ?>">
</audio>
<?php endif; ?>
