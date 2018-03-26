<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

$url = JURI::base();
$wishlist = $this->wishlist;
$product_id = JRequest::getInt('product_id');
$flage = ($product_id && count($wishlist) > 0) ? true : false;
$Itemid = JRequest::getInt('Itemid', 903);
$isLogin = JFactory::getApplication()->input->getInt('loginwishlist', 0);

?>
<?php if ($flage) : ?>
	<input type="checkbox" name="chkNewwishlist" id="chkNewwishlist"
	       onchange="changeDiv(this);"/><?php echo JText::_('COM_REDSHOP_CREATE_NEW_WISHLIST'); ?>
<?php endif;
?>
<div id="newwishlist" style="display:<?php echo $flage ? 'none' : 'block'; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1))
	{
		$pagetitle = JText::_('COM_REDSHOP_CREATE_NEWWISHLIST');
		?>
		<h3 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<?php echo $pagetitle; ?>
		</h3>
	<?php
	}
	?>
	<form name="newwishlistForm" method="post" action="">
		<table>
			<tr>
				<td>
					<label for="txtWishlistname"><?php echo JText::_('COM_REDSHOP_WISHLIST_NAME');?> : </label>
				</td>
				<td>
					<input type="input" name="txtWishlistname" id="txtWishlistname"/>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_REDSHOP_CREATE_SAVE'); ?>"
					       onclick="checkValidation()"/>&nbsp;
					<?php
					if (JRequest::getInt('loginwishlist') == 1) : ?>
					<?php
						$mywishlist_link = JRoute::_('index.php?view=wishlist&task=viewwishlist&option=com_redshop&Itemid=' . $Itemid);
					?>
						<a href="<?php echo $mywishlist_link; ?>">
							<input type="button" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>"/>
						</a>
					<?php else : ?>
						<input type="button" class="btn btn-cancel" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>"
						       onclick="xClose();"/>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="view" value="wishlist"/>
		<input type="hidden" name="option" value="com_redshop"/>
		<input type="hidden" name="task" value="createsave"/>
		<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
		<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<?php
if ($flage) : ?>
	<div id="wishlist">
		<?php
		if ($this->params->get('show_page_heading', 1))
		{
			$pagetitle = JText::_('COM_REDSHOP_MY_WISHLIST');
			?>
			<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<?php echo $pagetitle; ?>
			</h1>
		<?php
		}
		?>
		<form name="adminForm" id="adminForm" method="post" action="">
			<table class="adminlist">
				<thead>
					<th width="5%" align="center">
						<?php echo JText::_('COM_REDSHOP_NUM'); ?>
					</th>
					<th width="5%" class="title" align="center">
						<?php echo JHtml::_('redshopgrid.checkall'); ?>
					</th>
					<th class="title" width="30%">
						<?php echo JText::_('COM_REDSHOP_WISHLIST_NAME'); ?>
					</th>
				</thead>
				<tbody>
				<?php
				$k = 0;
				$i = 0;

				foreach ($wishlist as $row)
				{
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center">
							<?php echo ($i + 1); ?>
						</td>
						<td align="center">
							<?php echo JHTML::_('grid.id', $i, $row->wishlist_id, false, 'wishlist_id'); ?>
						</td>
						<td>
							<?php echo $row->wishlist_name; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
					$i++;
				}
				?>
				<tr>
					<td colspan="3" align="center">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_REDSHOP_ADD_TO_WISHLIST'); ?>"
						       onclick="submitform();"/>&nbsp;
						<input type="button" class="btn btn-cancel" value="<?php echo JText::_('COM_REDSHOP_CANCEL'); ?>"
						       onclick="<?php echo $isLogin == 0 ? 'window.parent.SqueezeBox.close();' : 'window.location.href = \'' . JRoute::_('index.php?option=com_redshop&Itemid=940&view=account') . '\''; ?> "/>
					</td>
				</tr>
				</tbody>
			</table>
			<input type="hidden" name="view" value="wishlist"/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="option" value="com_redshop"/>
			<input type="hidden" name="task" value="savewishlist"/>
			<input type="hidden" name="product_id" value="<?php echo $product_id; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>

<script language="javascript" type="text/javascript">
	function submitform() {
		if (document.adminForm.boxchecked.value == '0')
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_SELECT_WISHLIST')?>");
		else
			document.adminForm.submit();
	}
	function changeDiv(element) {
		if (element.checked) {
			document.getElementById('newwishlist').style.display = 'block';
			document.getElementById('wishlist').style.display = 'none';
		}
		else {
			document.getElementById('newwishlist').style.display = 'none';
			document.getElementById('wishlist').style.display = 'block';
		}
	}
</script>
<?php endif; ?>
<script language="javascript" type="text/javascript">
	
	function checkValidation() {
		if (trim(document.newwishlistForm.txtWishlistname.value) == "")
			alert("<?php echo JText::_('COM_REDSHOP_PLEASE_ENTER_WISHLIST_NAME')?>");
		else
			document.newwishlistForm.submit();
	}
	function closeSqueeze() {
		//window.parent.SqueezeBox.close();
		jQuery('a').attr('aria-controls', 'sbox-window').each(function(i, el){
			window.parent.SqueezeBox.close();
			jQuery(el).trigger('click');
		});
	}

	function xClose()
	{
		var parentBody = window.parent.document.body;
		jQuery(parentBody).find('#sbox-overlay').remove();
		jQuery(parentBody).find('#sbox-overlay').trigger('click');
		jQuery(parentBody).find('#sbox-overlay').click();
	
	}
</script>
