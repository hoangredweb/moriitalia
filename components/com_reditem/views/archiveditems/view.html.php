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

require_once JPATH_ADMINISTRATOR . '/components/com_reditem/helpers/helper.php';

/**
 * Item view.
 *
 * @package     RedITEM.Frontend
 * @subpackage  View.Html
 * @since       2.0
 */
class ReditemViewArchiveditems extends ReditemView
{
	/**
	 * Display template
	 *
	 * @param   string  $tpl  [description]
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$this->params = $mainframe->getParams();
		$input = $mainframe->input;
		$data = $this->get('Data');
		$this->items = $data['items'];
		$this->pagination = $data['pagination'];
		$this->prepareDocument();
		$templateId = $input->getInt('templateId', 0);

		if ($templateId)
		{
			$templatemodel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
			$this->template = $templatemodel->getItem($templateId);

			if (count($this->items))
			{
				foreach ($this->items as &$item)
				{
					$item->replacedContent = $this->template->content;

					ReditemHelperItem::replaceTag($item->replacedContent, $item);
					ReditemHelperItem::replaceCustomfieldsTag($item->replacedContent, $item);

					// Run dispatcher for content's plugins
					JPluginHelper::importPlugin('content');

					if (JPluginHelper::isEnabled('system', 'twig'))
					{
						$dispatcher = JDispatcher::getInstance();
						$dispatcher->trigger(
							'onTwigRender',
							array (
								&$item->replacedContent,
								'archiveditems-' . $item->id . '.html',
								array (
									'fields' => ReditemHelperCustomfield::processValuesForTwig($item->customfield_values),
									'page'   => $_SERVER
								)
							)
						);
					}
				}
			}
		}

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
		$redConfig	= JComponentHelper::getParams('com_reditem');

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
				$title = $title . " | " . JText::_('COM_REDITEM_ARCHIVED_ITEMS_LAYOUT');
				break;

			case 'prepend':
				$title = JText::_('COM_REDITEM_ARCHIVED_ITEMS_LAYOUT') . " | " . $title;
				break;

			case 'replace':
				$title = JText::_('COM_REDITEM_ARCHIVED_ITEMS_LAYOUT');
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
	}
}
