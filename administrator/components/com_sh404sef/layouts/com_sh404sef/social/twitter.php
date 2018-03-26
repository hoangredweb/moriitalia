<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2017
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.9.2.3552
 * @date        2017-06-01
 */

/**
 * Input:
 *
 * $displayData['viaAccount']
 * $displayData['tweetLayout']
 * $displayData['url']
 * $displayData['languageTag']
 */
// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<!-- Twitter share button -->
<a href="https://twitter.com/share" data-via="<?php echo $displayData['viaAccount']; ?>"
   data-url="<?php echo $displayData['url']; ?>"
   data-lang="<?php echo $displayData['languageTag']; ?>" class="twitter-share-button">Tweet</a>
<!-- End of Twitter share button -->
