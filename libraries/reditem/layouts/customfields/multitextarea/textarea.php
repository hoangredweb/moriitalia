<?php
/**
 * @package     RedITEM.Layouts
 * @subpackage  Customfields.Tasklist
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$user = ReditemHelperSystem::getUser();

if (empty($displayData))
{
	$textNo      = 1;
	$title       = '';
	$description = '';
	$isDone      = 0;
	$willBePaid  = 0;
}
else
{
	if (isset($displayData['textNo']) && !empty($displayData['textNo']))
	{
		$textNo = (int) $displayData['textNo'];
	}
	else
	{
		$textNo = 1;
	}

	if (isset($displayData['userId']) && !empty($displayData['userId']))
	{
		$userId = (string) $displayData['userId'];
		$userName = ReditemHelperSystem::getUser($userId)->name;
	}
	else
	{
		$userId = $user->id;
		$userName = $user->name;
	}

	if (isset($displayData['content']) && !empty($displayData['content']))
	{
		$content = (string) $displayData['content'];
	}
	else
	{
		$content = '';
	}
}
?>
<tr id="text-<?php echo $textNo; ?>">
	<td>
		<input type="text" value="<?php echo $userName; ?>" disabled >
		<input type="hidden" value="<?php echo $userId;?>" class="text-vals-<?php echo $textNo; ?> text-userid" id="text-<?php echo $textNo; ?>-userid"/>
	</td>
	<td>
		<textarea class="text-vals-<?php echo $textNo; ?> text-content"
			id="text-<?php echo $textNo; ?>-content" cols="50" rows="20"
			onchange="textareaUpdate()"
			<?php echo $user->id != $userId ? 'disabled' : ''; ?> ><?php echo $content;?></textarea>
	</td>
	<td>
		<div class="btn-group">
			<button class="btn btn-danger" id="text-del-<?php echo $textNo; ?>" onclick="deleteTextarea(<?php echo $textNo; ?>); return false;">
				<i class="icon icon-trash"></i>
				<span><?php echo JText::_('JTOOLBAR_DELETE');?></span>
			</button>
		</div>
	</td>
</tr>