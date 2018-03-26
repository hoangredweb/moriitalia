<?php
/**
 * @package     Webservices
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Api Helper class for overriding default methods
 *
 * @package     Redcore
 * @subpackage  Api Helper
 * @since       1.2
 */
class RApiHalHelperSiteReditemitem
{
	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @return object This method may be chained.
	 *
	 * @throws  RuntimeException
	 */
	/* public function isOperationAllowed(RApiHalHal $apiHal){} */

	/**
	 * Set resources from configuration if available
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	/* public function setResources(RApiHalHal $apiHal){} */

	/**
	 * Execute the Api Default Page operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	/* public function apiDefaultPage(RApiHalHal $apiHal){} */

	/**
	 * Execute the Api Create operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	/* public function apiCreate(RApiHalHal $apiHal){} */

	/**
	 * Execute the Api Delete operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	/* public function apiDelete(RApiHalHal $apiHal){} */

	/**
	 * Execute the Api Update operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	/* public function apiUpdate(RApiHalHal $apiHal){} */

	/**
	 * Execute the Api Task operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	/* public function apiTask(RApiHalHal $apiHal){} */

	/**
	 * Process posted data from json or object to array
	 *
	 * @param   mixed             $data           Raw Posted data
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return  mixed  Array with posted data.
	 *
	 * @since   1.2
	 */
	/* public function processPostData($data, $configuration, RApiHalHal $apiHal){} */

	/**
	 * Set document content for List view
	 *
	 * @param   array             $items          List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	/* public function setForRenderList($items, $configuration, RApiHalHal $apiHal){} */

	/**
	 * Set document content for Item view
	 *
	 * @param   object            $item           List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	/* public function setForRenderItem($item, $configuration, RApiHalHal $apiHal){} */

	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.2
	 */
	/* public function prepareBody($message, RApiHalHal $apiHal){} */

	/**
	 * Load model class for data manipulation
	 *
	 * @param   string            $elementName    Element name
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  mixed  Model class for data manipulation
	 *
	 * @since   1.2
	 */
	/* public function loadModel($elementName, $configuration, RApiHalHal $apiHal){} */

	/**
	 * Set Method for Api to be performed
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	/* public function setApiOperation(RApiHalHal $apiHal){} */

	/**
	 * Method to get the row form.
	 *
	 * @param   int  $pk  Primary key
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.4
	 */
	public function getItem($pk = null)
	{
		/** @var ReditemModelItem $itemModel */
		$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
		$item = $itemModel->getItem($pk);

		if (!$item)
		{
			return false;
		}

		$cfValues = ReditemHelperItem::getCustomFieldValues($item->id);

		if (isset($cfValues[$item->type_id][$item->id]))
		{
			$item->customfield_values = $cfValues[$item->type_id][$item->id];
		}

		if (isset($item->customfield_values))
		{
			$item->customfield_values = json_encode($item->customfield_values);
		}

		return $item;
	}

	/**
	 * Method for save item from webservice
	 *
	 * @return  boolean   True on success. False otherwise.
	 */
	public function save()
	{
		$input    = RFactory::getApplication()->input;
		$nullDate = RFactory::getDbo()->getNullDate();
		$title  = $input->getString('title', '');
		$typeId = $input->getInt('type_id', 0);

		if (empty($title) || !$typeId)
		{
			return false;
		}

		$jform        = array();
		$access       = $input->getInt('access', 1);
		$ordering     = $input->getInt('ordering', 0);
		$blocked      = $input->getInt('blocked', 0);
		$published    = $input->getInt('published', 1);
		$publishUp    = $input->getString('publish_up', $nullDate);
		$publishDown  = $input->getString('publish_down', $nullDate);
		$featured     = $input->getInt('featured', 0);
		$templateId   = $input->getInt('template_id', 0);
		$categories   = $input->getString('categories', '');
		$customfields = $input->getString('customfields', '');
		$id           = $input->getInt('id', null);

		// Check if this is edit --> Check permission of current user.
		if (!empty($id) && !ReditemHelperACL::checkItemPermission('item.edit.own', $id))
		{
			return false;
		}

		// Template process
		if (!$templateId)
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$type = $typeModel->getItem($typeId);
			$typeParams = new JRegistry($type->params);
			$defaultItemTemplate = $typeParams->get('default_itemdetail_template');

			if (!$defaultItemTemplate)
			{
				return false;
			}

			$templateId = $defaultItemTemplate;
		}

		$itemTable = RTable::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
		$itemTable->id           = $id;
		$itemTable->title        = $title;
		$itemTable->access       = $access;
		$itemTable->ordering     = $ordering;
		$itemTable->blocked      = $blocked;
		$itemTable->published    = $published;
		$itemTable->publish_up   = $publishUp;
		$itemTable->publish_down = $publishDown;
		$itemTable->featured     = $featured;
		$itemTable->template_id  = $templateId;
		$itemTable->type_id      = $typeId;

		if (!empty($categories))
		{
			$categories = explode(',', $categories);
			$jform['categories'] = $categories;
		}

		if (!empty($customfields))
		{
			$jform['fields'] = json_decode($customfields);
		}

		$input->set('jform', $jform);

		if (!$itemTable->store())
		{
			return false;
		}

		return true;
	}
}
