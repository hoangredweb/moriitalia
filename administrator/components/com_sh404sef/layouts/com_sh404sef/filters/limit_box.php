<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2017
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.9.2.3552
 * @date		2017-06-01
 */

defined('JPATH_BASE') or die;

/**
 * Displays a drop-down select list with values for numer of items per page
 */
?>
<div class="btn-group pull-right hidden-phone">
	<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
	<?php echo $displayData->getLimitBox(); ?>
</div>
