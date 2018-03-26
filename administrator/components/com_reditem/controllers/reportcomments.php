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
 * The comment reports controller
 *
 * @package     RedITEM.Backend
 * @subpackage  Controller.ReportComments
 * @since       2.1.3
 */
class ReditemControllerReportComments extends RControllerAdmin
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
		$model    = $this->getModel('ReportComments');

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
		$reportId  = $input->getInt('report_id', 0);
		$commentId = $input->getInt('comment_id', 0);
		$model     = $this->getModel('ReportComments');

		echo (int) $model->ignoreReport($commentId, $reportId);

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
		$reportId  = $input->getInt('report_id', 0);
		$commentId = $input->getInt('comment_id', 0);
		$model     = $this->getModel('ReportComments');

		echo (int) $model->approveReport($commentId, $reportId);

		RFactory::getApplication()->close();
	}
}
