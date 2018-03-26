<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * Plugins RedSLIDER section article
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Article extends JPlugin
{
	private $sectionId;

	private $sectionName;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->sectionId = "SECTION_ARTICLE";
		$this->sectionName = JText::_("PLG_SECTION_ARTICLE_NAME");
	}

	/**
	 * Get section name
	 *
	 * @return  array
	 */
	public function getSectionName()
	{
		$section = new stdClass;
		$section->id = $this->sectionId;
		$section->name = $this->sectionName;

		return $section;
	}

	/**
	 * Get section name by section Id
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  string
	 */
	public function getSectionNameById($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			return $this->sectionName;
		}
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  void/array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
					"{article_title}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_TITLE_DESC"),
					"{article_introtext|<em>limit</em>}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_INTROTEXT_DESC"),
					"{article_fulltext|<em>limit</em>}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_FULLTEXT_DESC"),
					"{article_date}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_DATE_DESC"),
					"{article_link}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_LINK_DESC"),
				);

			return $tags;
		}
	}

	/**
	 * Add forms fields of section to slide view
	 *
	 * @param   mixed   $form       joomla form object
	 * @param   string  $sectionId  section's id
	 *
	 * @return  boolean
	 */
	public function onSlidePrepareForm($form, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_article', false);
			}
		}

		return $return;
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object  $view       JView object
	 * @param   string  $sectionId  section's id
	 *
	 * @return boolean
	 */
	public function onSlidePrepareTemplate($view, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('article');
			}
		}

		return $return;
	}

	/**
	 * Event on store a slide
	 *
	 * @param   object  $jtable  JTable object
	 * @param   object  $jinput  JForm data
	 *
	 * @return boolean
	 */
	public function onSlideStore($jtable, $jinput)
	{
		return true;
	}

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string  $content  Template Content
	 * @param   object  $slide    Slide result object
	 *
	 * @return  string  $content  repaced content
	 */
	public function onPrepareTemplateContent($content, $slide)
	{
		// Check if we need to load component's CSS or not
		$useOwnCSS = JComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		if ($slide->section === $this->sectionId)
		{
			// Load stylesheet for each section
			$css = 'redslider.' . JString::strtolower($this->sectionId) . '.min.css';

			if (!$useOwnCSS)
			{
				RHelperAsset::load($css, 'redslider_sections/' . JString::strtolower($this->sectionId));
			}

			$params = new JRegistry($slide->params);

			$id = (int) $params->get('article_id', 0);

			if (!empty($id))
			{
				$articleModel = RModel::getFrontInstance('Article', array('ignore_request' => false), 'com_content');
				$countArticle = $this->countArticle();
				
				if ($countArticle > 0)
				{
					$article = $articleModel->getItem($id);

					$matches = array();

					if (preg_match_all('/{article_title[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], $article->title, $content);
							}
						}
					}

					if (preg_match_all('/{article_introtext[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = RedsliderHelper::replaceTagsHTML($match[0], $article->introtext, $content);
							}
						}
					}

					if (preg_match_all('/{article_fulltext[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = RedsliderHelper::replaceTagsHTML($match[0], $article->fulltext, $content);
							}
						}
					}

					if (preg_match_all('/{article_date[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], $article->created, $content);
							}
						}
					}

					if (preg_match_all('/{article_link[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid)), $content);
							}
						}
					}
				}
				else
				{
					$matches = array();

					if (preg_match_all('/{article_title[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], "", $content);
							}
						}
					}

					if (preg_match_all('/{article_introtext[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = RedsliderHelper::replaceTagsHTML($match[0], "", $content);
							}
						}
					}

					if (preg_match_all('/{article_fulltext[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = RedsliderHelper::replaceTagsHTML($match[0], "", $content);
							}
						}
					}

					if (preg_match_all('/{article_date[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], "", $content);
							}
						}
					}

					if (preg_match_all('/{article_link[^}]*}/i', $content, $matches) > 0)
					{
						foreach ($matches as $match)
						{
							if (count($match))
							{
								$content = JString::str_ireplace($match[0], "", $content);
							}
						}
					}
				}
			}

			return $content;
		}
	}

	/**
	 * Count article
	 *
	 * @return  int
	 */
	public function countArticle()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__content'))
			->where($db->qn('state') . ' = 1');

		return $db->setQuery($query)->loadResult();
	}
}
