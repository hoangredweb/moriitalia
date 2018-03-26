<?php
/**
 * @package     RedITEM.Front
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The item edit controller
 *
 * @package     RedITEM.Front
 * @subpackage  Controller.Item
 * @since       2.0.19
 */

class ReditemControllerItem extends RControllerForm
{
	/**
	 * Add an item.
	 *
	 * @return  mixed
	 */
	public function add()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.tid', '');
		$app->setUserState('com_reditem.global.itemId', '');

		return parent::add();
	}

	/**
	 * Edit an item.
	 *
	 * @param   int     $key     [description]
	 * @param   string  $urlVar  [description]
	 *
	 * @return  mixed
	 */
	public function edit($key = null, $urlVar = null)
	{
		$itemmodel = RModel::getAdminInstance('Item');

		$item = $itemmodel->getItem();

		$app = JFactory::getApplication();
		$app->setUserState('com_reditem.global.tid', $item->type_id);
		$app->setUserState('com_reditem.global.itemId', $item->id);

		return parent::edit($key, $urlVar);
	}

	/**
	 * For auto-submit form when client choose type
	 *
	 * @return void
	 */
	public function setType()
	{
		$app      = JFactory::getApplication();
		$recordId = $app->input->getInt('id', 0);
		$data     = $app->input->get('jform', array(), 'array');

		// Get default template from type configuration
		$typemodel = RModel::getAdminInstance('Type', array('ignore_request' => true));
		$typemodel->setState('list.select', 'params');
		$params = $typemodel->getItem($data['type_id'])->params;
		$data['template_id'] = $params['default_itemdetail_template'];

		// Check if default template has exist
		$templatemodel = RModel::getAdminInstance('Template', array('ignore_request' => true));
		$templatemodel->setState('list.select', 'id');
		$defaultTemplate = $templatemodel->getItem($params['default_itemdetail_template']);

		$data['template_id'] = ($defaultTemplate) ? $defaultTemplate->id : null;

		$app->setUserState('com_reditem.edit.item.data', $data);
		$app->setUserState('com_reditem.global.tid', $data['type_id']);

		$redirect = JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false);

		$this->setRedirect($redirect);
	}

	/**
	 * Method for run event after user share item
	 *
	 * @return  void
	 */
	public function ajaxItemShare()
	{
		ReditemHelperAjax::validateAjaxRequest();
		$user = ReditemHelperSystem::getUser();
		$app  = RFactory::getApplication();

		if ($user->guest)
		{
			$app->close();
		}

		$itemId        = $app->input->getInt('id', 0);
		$sharedService = $app->input->getString('service');
		$model         = $this->getModel();
		$result        = $model->itemShare($itemId, $sharedService);

		echo (int) $result;

		$app->close();
	}

	/**
	 * Archive a card
	 *
	 * @return  void
	 */
	public function ajaxArchive()
	{
		$app    = RFactory::getApplication();
		$model  = $this->getModel();
		$itemId = $app->input->getInt('id', 0);
		$result = $model->archiveItem($itemId);
		echo $result;

		$app->close();
	}

	/**
	 * Ajax function for adding task row in task list custom field.
	 *
	 * @return void
	 */
	public function ajaxCustomfieldTasklistAddTask()
	{
		ReditemHelperAjax::validateAjaxRequest();
		$app      = JFactory::getApplication();
		$input    = $app->input;
		$typeId   = $input->getInt('typeId', 0);
		$rowNo    = $input->getInt('rowNo', 0);
		$addTable = $input->getInt('addTable', 0);
		$type     = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($typeId);

		if ($addTable)
		{
			$html = '<table class="table table-bordered tasks"><thead><tr>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_TASKLIST_TITLE') . '</th>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_TASKLIST_DESCRIPTION') . '</th>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_TASKLIST_ACTIONS') . '</th></tr></thead><tbody>' .
				ReditemHelperLayout::render($type, 'customfields.tasklist.task', array('taskNo' => 1), array('component' => 'com_reditem')) .
				'</tbody></table>';
		}
		else
		{
			$html = ReditemHelperLayout::render($type, 'customfields.tasklist.task', array('taskNo' => $rowNo), array('component' => 'com_reditem'));
		}

		echo $html;
		$app->close();
	}

	/**
	 * Ajax function for adding text row in multitextarea custom field.
	 *
	 * @return void
	 */
	public function ajaxCustomfieldMultitextareaAddTextarea()
	{
		ReditemHelperAjax::validateAjaxRequest();
		$app      = JFactory::getApplication();
		$input    = $app->input;
		$typeId   = $input->getInt('typeId', 0);
		$rowNo    = $input->getInt('rowNo', 0);
		$addTable = $input->getInt('addTable', 0);

		$type     = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem')->getItem($typeId);

		if ($addTable)
		{
			$html = '<table class="table table-bordered texts"><thead><tr>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_USER') . '</th>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_CONTENT') . '</th>' .
				'<th>' . JText::_('COM_REDITEM_FIELD_MULTITEXTAREA_ACTIONS') . '</th></tr></thead><tbody>' .
				ReditemHelperLayout::render($type, 'customfields.multitextarea.textarea', array('textNo' => 1), array('component' => 'com_reditem')) .
				'</tbody></table>';
		}
		else
		{
			$html = ReditemHelperLayout::render($type, 'customfields.multitextarea.textarea', array('textNo' => $rowNo), array('component' => 'com_reditem'));
		}

		echo $html;
		$app->close();
	}
}
