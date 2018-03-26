<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Category Controller.
 *
 * @package     RedITEM.Frontend
 * @subpackage  Controller
 * @since       2.1.15
 */
class ReditemControllerSearchEngine extends JControllerLegacy
{
	/**
	 * Method for save filter
	 *
	 * @return  void
	 */
	public function ajaxSaveFilter()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app  = JFactory::getApplication();
		$user = ReditemHelperSystem::getUser();

		// Check user permission
		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			$defaultMenu  = $app->getMenu()->getDefault();
			$redirectLink = JRoute::_($defaultMenu->link . '&Itemid=' . $defaultMenu->id, false);
			$app->redirect($redirectLink, JText::_('COM_REDITEM_SEARCH_ENGINE_ERROR_PERMISSION'), 'error');
		}

		$data       = $app->input->getArray(array());
		$model      = $this->getModel('SearchEngine');
		$currentUrl = $data['current_url'];

		// Save filter data for user
		if (!$model->saveFilter($data))
		{
			$app->redirect(JRoute::_($currentUrl), JText::_('COM_REDITEM_SEARCH_ENGINE_ERROR_COULD_NOT_SAVE_FILTER'), 'error');
		}

		$redirectLink = ReditemHelperSearchengine::getSearchEngineManagePage();
		$app->redirect($redirectLink, JText::_('COM_REDITEM_SEARCH_ENGINE_SUCCESS_SAVE_FILTER'), 'success');
	}

	/**
	 * Method for remove an search engine from user
	 *
	 * @return  void
	 */
	public function ajaxRemove()
	{
		$app    = JFactory::getApplication();
		$user   = ReditemHelperSystem::getUser();
		$id     = $app->input->getInt('id', 0);
		$return = array('status' => 0, 'msg' => '');

		// Check user permission
		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			$return['msg'] = JText::_('COM_REDITEM_SEARCH_ENGINE_ERROR_PERMISSION');

			echo json_encode($return);
			$app->close();
		}

		$model = $this->getModel('SearchEngine');

		if (!$model->removeFilter($id, $user->id))
		{
			$return['msg'] = JText::_('COM_REDITEM_SEARCH_ENGINE_ERROR_REMOVE_SEARCH_ENGINE');

			echo json_encode($return);
			$app->close();
		}

		$return['status'] = 1;
		$return['msg']    = JText::sprintf('COM_REDITEM_SEARCH_ENGINE_SUCCESS_REMOVE_SEARCH_ENGINE', $id);

		echo json_encode($return);
		$app->close();
	}
}
