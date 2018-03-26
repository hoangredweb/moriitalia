<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  RedITEM
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Category view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Html
 * @since       2.1.1
 */
class ReditemViewCategoryDetailGmap extends ReditemView
{
	protected $item;

	/**
	 * Display template
	 *
	 * @param   string  $tpl  [description]
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$app           = JFactory::getApplication();
		$params        = $app->getParams();
		$this->params  = $params;
		$this->item    = $this->get('Data');
		$this->canView = (boolean) ReditemHelperACL::checkCategoryPermission('category.view', $this->item->id);

		if (!$this->canView)
		{
			$this->setLayout('noaccess');
		}

		$content    = '';
		$this->list = array();

		if (isset($this->item->sub_categories) && !empty($this->item->sub_categories))
		{
			$this->list = $this->item->sub_categories;
		}

		foreach ($this->list as $category)
		{
			$itemsModel       = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
			$itemsOrdering    = 'i.' . $params->get('items_ordering', 'title');
			$itemsDestination = $params->get('items_destination', 'asc');
			$itemIds          = array();
			$itemsModel->setState('filter.catid', $category->id);
			$itemsModel->setState('filter.published', 1);
			$itemsModel->setState('list.ordering', $itemsOrdering);
			$itemsModel->setState('list.direction', $itemsDestination);

			// Get sub categories items
			$category->items  = $itemsModel->getItems();

			// Get sub categories fields
			$categoryModel    = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
			$category->fields = $categoryModel->getCustomFields($category->id, true);

			foreach ($category->items as $item)
			{
				$itemIds[] = $item->id;
			}

			$iCustomFields = ReditemHelperItem::getCustomFieldValues($itemIds);

			foreach ($category->items as $item)
			{
				if (isset($iCustomFields[$item->type_id][$item->id]))
				{
					$item->customfield_values = $iCustomFields[$item->type_id][$item->id];
				}
			}
		}

		if ($this->item)
		{
			// Check whether category access level allows access.
			$user = ReditemHelperSystem::getUser();

			if (!in_array($this->item->access, $user->getAuthorisedViewLevels()))
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			$this->prepareDocument();
			$content = ReditemHelperCategorygmap::prepareTemplate($this->item);
		}

		if (JPluginHelper::isEnabled('system', 'twig'))
		{
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger(
				'onTwigRender',
				array (
					&$content,
					'categorydetailgmap-' . $this->item->id . '.html',
					array (
						'items'         => isset($this->item->items) ? $this->item->items : null,
						'fields'        => ReditemHelperCustomfield::processValuesForTwig($this->item->fields),
						'page'          => $_SERVER,
						'subcategories' => $this->list
					)
				)
			);
		}

		$this->content = $content;

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app            = JFactory::getApplication();
		$menus          = $app->getMenu();
		$pathway        = $app->getPathway();
		$title          = null;
		$categoryParams = $this->item->params;
		$redConfig      = JComponentHelper::getParams('com_reditem');

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_REDITEM_GLOBAL_CATEGORY'));
		}

		$id = (int) @$menu->query['id'];

		if ($menu && (($menu->query['option'] != 'com_reditem') || ($menu->query['view'] != 'categorydetail') || ($id != $this->item->id)))
		{
			$pathway->addItem($this->item->title, '');
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$seoTitleConfig = $redConfig->get('seo_title_config', '');

		switch ($seoTitleConfig)
		{
			case 'append':
				$title = $title . " | " . $this->item->title;
				break;

			case 'prepend':
				$title = $this->item->title . " | " . $title;
				break;

			case 'replace':
				$title = $this->item->title;
				break;

			default:
				break;
		}

		$this->document->setTitle($title);

		// Meta description process
		if ($this->params->get('menu-meta_description'))
		{
			// Use Meta Description from menu
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		elseif (isset($categoryParams['meta_description']) && (!empty($categoryParams['meta_description'])))
		{
			// Use Meta Description of this item
			$this->document->setDescription($categoryParams['meta_description']);
		}

		// Meta keywords process
		if ($this->params->get('menu-meta_keywords'))
		{
			// Use Meta Keywords from menu
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		elseif (isset($categoryParams['meta_keywords']) && (!empty($categoryParams['meta_keywords'])))
		{
			// Use Meta Description of this item
			$this->document->setMetadata('keywords', $categoryParams['meta_keywords']);
		}

		// Robots process
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		elseif (isset($categoryParams['meta_robots']) && (!empty($categoryParams['meta_robots'])))
		{
			// Use Meta Description of this item
			$this->document->setMetadata('robots', $categoryParams['meta_robots']);
		}
	}
}
