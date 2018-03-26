<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * wishlist Controller.
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 * @since       1.0
 */
class RedshopControllerWishlist extends RedshopControllerWishlistDefault
{
	/**
	 * Save wishlist function
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function savewishlist()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		/** @var RedshopModelWishlist $model */
		$model = $this->getModel("wishlist");

		$data = JFactory::getApplication()->input->post->getArray();

		if ($model->savewishlist($data))
		{
			$msg = JText::_('COM_REDSHOP_PRODUCT_SAVED_IN_WISHLIST_SUCCESSFULLY');
			echo "<div>" . $msg . "</div>";
		}
		else
		{
			$msg = JText::_('COM_REDSHOP_PRODUCT_SAVED_IN_WISHLIST_SUCCESSFULLY');
			echo "<div>" . $msg . "</div>";
		}

		if (JRequest::getVar('loginwishlist') == 1)
		{
			$wishreturn = JRoute::_('index.php?option=com_redshop&view=wishlist&task=viewwishlist&Itemid=' . JRequest::getVar('Itemid', 903), false);
			$this->setMessage($msg);
			$this->setRedirect($wishreturn);
		}
		else
		{
			?>
			<script language="javascript">
				var t = setTimeout("window.parent.SqueezeBox.close();window.parent.location.reload();", 2000);
			</script>
		<?php
		}
	}
}
