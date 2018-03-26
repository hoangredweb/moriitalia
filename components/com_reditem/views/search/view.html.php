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
 * Search view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Html
 * @since       2.0
 */
class ReditemViewSearch extends ReditemView
{
	protected $data;

	protected $content;

	/**
	 * Display template
	 *
	 * @param   string  $tpl  [description]
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		$this->params = $params;
		$this->data = $this->get('Data');
		$content = '';

		if ($this->data)
		{
			$this->prepareDocument();
			$content = $this->prepareContent($this->data);

			if (JPluginHelper::isEnabled('system', 'twig'))
			{
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger(
					'onTwigRender',
					array (
						&$content,
						'search_results.html',
						array (
							'items' => isset($this->data->items) ? $this->data->items : null,
							'page'  => $_SERVER
						)
					)
				);
			}
		}

		$this->content = $content;

		parent::display($tpl);
	}

	/**
	 * Method for replace tag of template
	 *
	 * @param   object  $template  Template object
	 *
	 * @return  string  HTML code after replace tag.
	 */
	protected function prepareContent($template)
	{
		$mainContent = '';

		if (!$template)
		{
			return $mainContent;
		}

		$mainContent = $template->content;

		// Items array
		if ((strpos($mainContent, '{items_loop_start}') !== false) && (strpos($mainContent, '{items_loop_end}') !== false))
		{
			$tempContent = explode('{items_loop_start}', $mainContent);
			$preContent  = (count($tempContent) > 1) ? $tempContent[0] : '';
			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{items_loop_end}', $tempContent);
			$subTemplate = $tempContent[0];
			$postContent = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';
			$subContent  = '';

			if ($template->items)
			{
				// Has sub categories
				foreach ($template->items as $item)
				{
					$subContentSub = $subTemplate;

					ReditemHelperItem::replaceTag($subContentSub, $item);
					ReditemHelperItem::replaceCustomfieldsTag($subContentSub, $item);

					$subContent .= '<div class="reditemItem">' . $subContentSub . '</div>';
				}
			}

			$mainContent = $preContent . '<div id="reditemsItems">' . $subContent . '</div>' . $postContent;
		}

		// Filter tag
		ReditemHelperTags::tagReplaceFilter($mainContent, $template, 'reditemSearchCallback');

		JPluginHelper::importPlugin('content');
		$mainContent = JHtml::_('content.prepare', $mainContent);

		return $mainContent;
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

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

		/*if ($menu && (($menu->query['option'] != 'com_reditem') || ($menu->query['view'] != 'search')))
		{
			$pathway->addItem($this->item->title, '');
		}*/

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

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
