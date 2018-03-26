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

require_once JPATH_ADMINISTRATOR . '/components/com_redslider/helpers/helper.php';

/**
 * Plugins RedSLIDER section standard
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Standard extends JPlugin
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
		$this->sectionId = "SECTION_STANDARD";
		$this->sectionName = JText::_("PLG_SECTION_STANDARD_NAME");
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
					'{standard_description}' => JText::_("COM_REDSLIDER_SECTION_STANDARD_TAG_DESCRIPTION_DESC"),
					'{standard_link}' => JText::_("COM_REDSLIDER_SECTION_STANDARD_TAG_LINK_DESC"),
					'{standard_linktext}' => JText::_("COM_REDSLIDER_SECTION_STANDARD_TAG_LINKTEXT_DESC"),
					'{standard_title}' => JText::_("COM_REDSLIDER_SECTION_STANDARD_TAG_TITLE_DESC"),
					'{standard_caption}' => JText::_("COM_REDSLIDER_SECTION_STANDARD_TAG_CAPTION_DESC"),
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
		if ($sectionId === $this->sectionId)
		{
			$return = false;

			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_standard', false);
			}

			return $return;
		}
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
		if ($sectionId === $this->sectionId)
		{
			$return = false;

			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('standard');
			}

			return $return;
		}
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

		// Load stylesheet for each section
		$css = 'redslider.' . JString::strtolower($this->sectionId) . '.min.css';

		if (!$useOwnCSS)
		{
			RHelperAsset::load($css, 'redslider_sections/' . JString::strtolower($this->sectionId));
		}

		if ($slide->section === $this->sectionId)
		{
			$params = new JRegistry($slide->params);

			$standard = new stdClass;
			$standard->description = $params->get('description', '');
			$standard->link = $params->get('link', '');
			$standard->linktext = $params->get('linktext', '');
			$standard->suffixClass = $params->get('suffix_class', 'standard_slide');
			$standard->title = $slide->title;
			$standard->caption = $params->get('caption', '');

			$matches = array();

			if (preg_match_all('/{standard_description[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $standard->description, $content);
					}
				}
			}

			if (preg_match_all('/{standard_link[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $standard->link, $content);
					}
				}
			}

			if (preg_match_all('/{standard_linktext[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $standard->linktext, $content);
					}
				}
			}

			if (preg_match_all('/{standard_title[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $standard->title, $content);
					}
				}
			}

			if (preg_match_all('/{standard_caption[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $standard->caption, $content);
					}
				}
			}

			return $content;
		}
	}
}
