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

jimport('joomla.application.component.view');

/**
 * Item view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Html
 * @since       2.0
 */
class ReditemViewItemDetail extends ReditemView
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
		JPluginHelper::importPlugin('reditem');
		$dispatcher    = RFactory::getDispatcher();
		$app           = JFactory::getApplication();
		$this->params  = $app->getParams();
		$this->item    = $this->get('Data');
		$this->canView = (boolean) ReditemHelperACL::checkItemPermission('item.view', $this->item->id);

		if (!$this->canView)
		{
			$defaultMenu = $app->getMenu()->getDefault();
			$redirectLink = JRoute::_($defaultMenu->link . '&Itemid=' . $defaultMenu->id, false);
			$app->redirect($redirectLink, JText::_('COM_REDITEM_ITEM_ERROR_PERMISSION_VIEW_ITEM'));
		}

		$content = '';

		$dispatcher->trigger('onPrepareItemDetail', array($this->item));

		if ($this->item)
		{
			// Load categories of this item
			$categories = ReditemHelperItem::getCategories($this->item->id, false);

			if (isset($categories[$this->item->id]))
			{
				$this->item->categories = $categories[$this->item->id];
			}

			// Check whether category access level allows access.
			$user   = ReditemHelperSystem::getUser();
			$groups = $user->getAuthorisedViewLevels();

			if (!in_array($this->item->access, $groups))
			{
				JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}

			$this->prepareDocument();

			$content = $this->item->template->content;

			// Replace related items tag first
			ReditemHelperItem::replaceRelatedItems($content, $this->item);

			// Replace items data tag
			ReditemHelperItem::replaceTag($content, $this->item);

			// Replace item's custom fields data
			ReditemHelperItem::replaceCustomfieldsTag($content, $this->item);

			// Run dispatcher for content's plugins
			JPluginHelper::importPlugin('content');
			$content = JHtml::_('content.prepare', $content);
		}

		if (JPluginHelper::isEnabled('system', 'twig'))
		{
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger(
				'onTwigRender',
				array (
					&$content,
					'itemdetail-' . $this->item->id . '.html',
					array (
						'fields'     => ReditemHelperCustomfield::processValuesForTwig($this->item->customfield_values),
						'page'       => $_SERVER,
						'categories' => $this->item->categories
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
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;
		$itemParams	= $this->item->params;
		$redConfig	= JComponentHelper::getParams('com_reditem');

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

		if ($menu && (($menu->query['option'] != 'com_reditem') || ($menu->query['view'] != 'itemdetail') || ($id != $this->item->id)))
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

		$seoTitleConfig = !empty($itemParams['append_to_global_seo']) ? $itemParams['append_to_global_seo'] : 'append';
		$pageTitle = !empty($itemParams['page_title']) ? $itemParams['page_title'] : '';

		switch ($seoTitleConfig)
		{
			case 'append':
				$title = $title . " | " . $this->item->title;

				if ('' != trim($pageTitle))
				{
					$title = $pageTitle . " | " . $this->item->title;
				}

				break;

			case 'prepend':
				$title = $this->item->title . " | " . $title;

				if ('' != trim($pageTitle))
				{
					$title = $this->item->title . " | " . $pageTitle;
				}

				break;

			case 'replace':
				$title = $this->item->title;

				if ('' != trim($pageTitle))
				{
					$title = $pageTitle;
				}

				break;

			default:
				break;
		}

		$this->document->setTitle($title);
		$this->document->setMetadata('title', $title);
		$this->document->setMetadata('og:title', $title);

		// Meta description process
		if ($this->params->get('menu-meta_description'))
		{
			// Use Meta Description from menu
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		elseif (isset($itemParams['meta_description']) && (!empty($itemParams['meta_description'])))
		{
			// Use Meta Description of this item
			$this->document->setDescription($itemParams['meta_description']);
		}

		// Meta keywords process
		if ($this->params->get('menu-meta_keywords'))
		{
			// Use Meta Keywords from menu
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		elseif (isset($itemParams['meta_keywords']) && (!empty($itemParams['meta_keywords'])))
		{
			// Use Meta Description of this item
			$this->document->setMetadata('keywords', $itemParams['meta_keywords']);
		}

		// Robots process
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		elseif (isset($itemParams['meta_robots']) && (!empty($itemParams['meta_robots'])))
		{
			// Use Meta Description of this item
			$this->document->setMetadata('robots', $itemParams['meta_robots']);
		}

		if (isset($itemParams['meta_language']) && (!empty($itemParams['meta_language'])))
		{
			// Use Meta Language of this item
			$this->document->setLanguage($itemParams['meta_language']);
		}
	}
}
