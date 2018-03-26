<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopViewWedding_list extends RedshopView
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		// Request variables

		$params = $app->getParams('com_redshop');
		$task   = JRequest::getCmd('task', 'com_redshop');

		$Itemid = JRequest::getInt('Itemid');
		$pid    = JRequest::getInt('product_id');
		$layout = JRequest::getCmd('layout');
		$config = Redconfiguration::getInstance();
		$pageheadingtag = '';
		$params   = $app->getParams('com_redshop');

		$model = $this->getModel("wedding_list");

		if ($task == 'addtowedding')
		{
			$this->setlayout('addtowedding');
			$this->params = $params;
			parent::display($tpl);
		}
		else
		{
			$this->weddingList = $model->getWeddingListProduct();
			$this->params = $params;
			parent::display($tpl);
		}
	}
}
