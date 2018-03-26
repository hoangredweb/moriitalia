<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

/**
 * The item reports controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.ReportItems
 * @since       2.1.3
 */
class ReditemControllerReportItems extends RControllerAdmin
{
	/**
	 * Method for add point to reporters
	 *
	 * @return  void
	 */
	public function addPoint()
	{
		$input    = RFactory::getApplication()->input;
		$reportId = $input->getInt('report_id', 0);
		$userId   = $input->getInt('user_id', 0);
		$point    = $input->getInt('value', 0);
		$model    = $this->getModel('ReportItems');

		echo (int) $model->addPoint($userId, $reportId, $point);

		RFactory::getApplication()->close();
	}

	/**
	 * Method for ignore an report
	 *
	 * @return  void
	 */
	public function ignoreReport()
	{
		$input = RFactory::getApplication()->input;
		$reportId = $input->getInt('report_id', 0);
		$itemId   = $input->getInt('item_id', 0);
		$model    = $this->getModel('ReportItems');

		echo (int) $model->ignoreReport($itemId, $reportId);

		RFactory::getApplication()->close();
	}

	/**
	 * Method for approve an report
	 *
	 * @return  void
	 */
	public function approveReport()
	{
		$input = RFactory::getApplication()->input;
		$reportId = $input->getInt('report_id', 0);
		$itemId   = $input->getInt('item_id', 0);
		$model    = $this->getModel('ReportItems');

		echo (int) $model->approveReport($itemId, $reportId);

		RFactory::getApplication()->close();
	}
}
