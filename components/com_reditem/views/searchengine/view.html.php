<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  RedITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Search Engine manage view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Html
 * @since       2.1.15
 */
class ReditemViewSearchEngine extends ReditemView
{
	/**
	 * Display template
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();
		$app  = JFactory::getApplication();

		if (!$user->authorise('core.searchengine', 'com_reditem'))
		{
			$defaultMenu  = $app->getMenu()->getDefault();
			$redirectLink = JRoute::_($defaultMenu->link . '&Itemid=' . $defaultMenu->id, false);
			$app->redirect($redirectLink, JText::_('COM_REDITEM_SEARCH_ENGINE_ERROR_PERMISSION'));
		}

		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');

		if (!empty($this->items))
		{
			foreach ($this->items as $item)
			{
				$searchData = new JRegistry($item->search_data);
				$searchData = $searchData->toArray();

				$item->url = '';
				$item->searchData = array();

				if (isset($searchData['url']))
				{
					$item->url = $searchData['url'];
					unset($searchData['url']);
				}

				$item->url .= '&' . http_build_query($searchData);
				$item->searchData = $this->prepareFilterData($searchData);
			}
		}

		parent::display($tpl);
	}

	/**
	 * Method for prepare filter information text
	 *
	 * @param   array  $searchData  Array of filter data
	 * @param   int    $typeId      ID of type
	 *
	 * @return  string              HTML code of filter information.
	 */
	public function prepareFilterData($searchData, $typeId = 0)
	{
		if (empty($searchData) || !$typeId)
		{
			return false;
		}

		$result = array();

		foreach ($searchData as $key => $value)
		{
			if ($key == 'filter_category')
			{
				$tmpValue = array();
				$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

				foreach ($value as $categories)
				{
					if (is_array($categories))
					{
						foreach ($categories as $tmpCategory)
						{
							$category = $categoryModel->getItem($tmpCategory);
							$tmpValue[] = $category->title;
							unset($category);
						}

						continue;
					}

					$category = $categoryModel->getItem($categories);
					$tmpValue[] = $category->title;
					unset($category);
				}

				unset($categoryModel);
				$value = implode(', ', $tmpValue);

				if (empty($value))
				{
					continue;
				}

				$name = JText::_('COM_REDITEM_SEARCH_ENGINE_FILTER_' . strtoupper($key));
				$result[$name] = $value;

				continue;
			}
			elseif ($key == 'filter_customfield')
			{
				$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

				foreach ($value as $customfield => $customValue)
				{
					$field = $fieldModel->getItem($customfield);

					if (is_array($customValue))
					{
						$tmpValues = array();

						foreach ($customValue as $tmpCustomValue)
						{
							$tmpValues[] = str_replace('%', '', base64_decode($tmpCustomValue));
						}

						$result[$field->name] = implode(', ', $tmpValues);

						continue;
					}

					$customValue = str_replace('%', '', base64_decode($customValue));
					$result[$field->name] = $customValue;
				}

				unset($fieldModel);

				continue;
			}
			else
			{
				$name = JText::_('COM_REDITEM_SEARCH_ENGINE_FILTER_' . strtoupper($key));
				$result[$name] = $value;
			}
		}

		return $result;
	}
}
