<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$tag   = $displayData['tag'];
$user  = $displayData['value'];
$item  = $displayData['item'];
?>

<?php if (!empty($user)) : ?>
<span class="reditem_user reditem_user_<?php echo $tag->id; ?>"><?php echo $user->name . ' (' . $user->username . ')'; ?></span>
<?php endif; ?>
