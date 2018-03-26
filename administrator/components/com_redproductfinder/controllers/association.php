<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Association controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderControllerAssociation extends RControllerForm
{
	/**
	 * For auto-submit form when client choose category
	 *
	 * @return void
	 */
	public function setProduct()
	{
		$app = JFactory::getApplication();
		$data     = $app->input->get_Array('jform', null);

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . '&catid=' . $data['category_id'] . '&id=' . $data['id'] . $this->getRedirectToItemAppend($recordId), false));
	}
}
