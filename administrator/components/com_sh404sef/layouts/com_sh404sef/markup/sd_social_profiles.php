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
 * 'entity_type'
 * 'entity_url'
 * 'entity_name'
 * 'profiles'
 */
// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die();

$displayData['sameAs'] = array();
foreach ($displayData['profiles'] as $profile)
{
	$displayData['sameAs'][] = $profile;
}
unset($displayData['profiles']);
?>
<!-- Google social profiles markup-->
<script type="application/ld+json">
<?php echo ShlSystem_Convert::jsonEncode($displayData); ?>

</script>
<!-- End of Google social profiles markup-->
