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
 * login Controller.
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Controller
 * @since       1.0
 */
class RedshopControllerLogin extends RedshopControllerLoginDefault
{
	/**
	 *  Setlogin function
	 *
	 * @return  void
	 */
	public function setlogin()
	{
		$username     = JRequest::getVar('username', '', 'method', 'username');
		$password     = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$Itemid       = JRequest::getVar('Itemid');
		$returnitemid = JRequest::getVar('returnitemid');
		$mywishlist   = JRequest::getVar('mywishlist');
		$wedding      = JRequest::getVar('wedding');
		$menu         = JFactory::getApplication()->getMenu();
		$item         = $menu->getItem($returnitemid);
		$productId    = JRequest::getVar('product_id');

		JLoader::load('RedshopHelperHelper');

		$redhelper = new redhelper;

		$model = $this->getModel('login');

		$shoppergroupid = JRequest::getInt('protalid', '', 'post', 0);

		$msg = "";

		if ($shoppergroupid != 0)
		{
			$check = $model->CheckShopperGroup($username, $shoppergroupid);
			$link  = "index.php?option=com_redshop&view=login&layout=portal&protalid=" . $shoppergroupid;

			if ($check > 0)
			{
				$isLogin = $model->setlogin($username, $password);
				$return = JRequest::getVar('return');
			}
			else
			{
				$msg    = JText::_("COM_REDSHOP_SHOPPERGROUP_NOT_MATCH");
				$return = "";
			}
		}
		else
		{
			$isLogin = $model->setlogin($username, $password);
			$return = JRequest::getVar('return');
		}

		if ($wedding == 1)
		{
			?>
			<script language="javascript">
				var t = setTimeout("window.parent.SqueezeBox.close();window.parent.location.reload();", 2000);
			</script>
			<?php
		}
		elseif ($mywishlist == 1)
		{
			if ($isLogin == true)
			{
				$wishreturn = JRoute::_('index.php?loginwishlist=1&option=com_redshop&view=wishlist&product_id=' . $productId . ' &Itemid=' . 903, false);
			}
			else
			{
				$wishreturn = JRoute::_('index.php?loginwishlist=1&option=com_redshop&view=login&wishlist=1&product_id=' . $productId . '&Itemid=' . 903, false);
			}

			$this->setRedirect($wishreturn);

		}
		else
		{
			if ($item)
			{
				$link = $item->link . '&Itemid=' . $returnitemid;
			}
			else
			{
				$link = 'index.php?option=com_redshop&view=account&logout=133';
			}

			if (!empty($return))
			{
				$s_Itemid = $redhelper->getCheckoutItemid();
				$Itemid   = $s_Itemid ? $s_Itemid : $Itemid;
				$return   = JRoute::_('index.php?option=com_redshop&view=checkout&Itemid=' . $Itemid, false);

				$this->setRedirect($return);
			}
			else
			{
				$this->setRedirect(JRoute::_($link), $msg);
			}
		}
	}
}