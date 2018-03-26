<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Redshop configuration model
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Model.Configuration
 * @since       2.0
 */
class RedSliderModelCpanel extends RModelAdmin
{
	/**
	 * Get the current redSLIDER version
	 *
	 * @return  string  The redSLIDER version
	 *
	 * @since   2.0
	 */
	public function getVersion()
	{
		$xmlfile = JPATH_SITE . '/administrator/components/com_redslider/redslider.xml';
		$version = JText::_('COM_REDSLIDER_FILE_NOT_FOUND');

		if (file_exists($xmlfile))
		{
			$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
			$version = $data['version'];
		}

		return $version;
	}

	/**
	 * Get the current redSLIDER version
	 *
	 * @return  string  The redSLIDER version
	 *
	 * @since   2.0.0
	 */
	public function getStats()
	{
		$stats = new stdClass;

		// Get count of slides
		$slidesModel = RModel::getAdminInstance('Slides', array('ignore_request' => true), 'com_redslider');
		$slidesModel->setState('list.select', 'count(*) as count');
		$slides = $slidesModel->getItems();
		$stats->slides = $slides[0]->count;

		// Get count of galleries
		$galleriesModel = RModel::getAdminInstance('Galleries', array('ignore_request' => true), 'com_redslider');
		$galleriesModel->setState('list.select', 'count(*) as count');
		$galleries = $galleriesModel->getItems();
		$stats->galleries = $galleries[0]->count;

		// Get count of templates
		$templatesModel = RModel::getAdminInstance('Templates', array('ignore_request' => true), 'com_redslider');
		$templatesModel->setState('list.select', 'count(*) as count');
		$templates = $templatesModel->getItems();
		$stats->templates = $templates[0]->count;

		return $stats;
	}

	/**
	 * Install demo content
	 *
	 * @return   boolean  Always returns true
	 *
	 * @since	2.0
	 */
	public function demoContentInsert()
	{
		// Add Include path
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redslider/tables');
		/*
		 * Insert demo template for Gallery
		 */
		$gallery = JTable::getInstance('Gallery', 'RedsliderTable', array('ignore_request' => true));
		$gallery->id = null;
		$gallery->title = 'Sample Gallery';
		$gallery->access = 1;
		$gallery->published = 1;
		$gallery->store();
		$galleryId = $gallery->id;

		/*
		 * Insert demo template for Article section
		 */
		$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
		$templateTable->id = null;
		$templateTable->title = 'Template Article';
		$templateTable->section = 'SECTION_ARTICLE';
		$templateTable->published = 1;
		$templateTable->content = '<div class="articleSlide">
				<div class="slideTitle">
					<h3>
						<a href="{article_link}">{article_title}</a>
					</h3>
				</div>
				<div class="slideText">
					{article_introtext|250}
				</div></div>';
		$templateTable->store();
		$templateId = (int) $templateTable->id;
		/*
		 * Insert demo slide for Article section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = $galleryId;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample Article';
		$slideTable->section = 'SECTION_ARTICLE';
		$slideTable->published = 1;
		$slideTable->params = '{"article_id":"1","background_image":"images/stories/redslider/article_slider.jpg","slide_class":"article_slide"}';
		$slideTable->store();
		/*
		 * Insert demo template for Standard section
		 */
		$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
		$templateTable->id = null;
		$templateTable->title = 'Template Standard';
		$templateTable->section = 'SECTION_STANDARD';
		$templateTable->published = 1;
		$templateTable->content = '<div class="eachSlide">
			<div class="slideTitle">
				<h3>
					<a href="{standard_link}">
						{standard_linktext}
					</a>
				</h3>
			</div>
			<div class="slideText">
				{standard_description}
			</div></div>';
		$templateTable->store();
		$templateId = (int) $templateTable->id;
		/*
		 * Insert demo slide for Standard section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = $galleryId;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample Standard';
		$slideTable->section = 'SECTION_STANDARD';
		$slideTable->published = 1;
		$slideTable->params = '{"background_image":"images/stories/redslider/standard_slider.jpg","caption":"Sample Standard","description":"redSLIDER is a Joomla extension for creating continuous horizontal scroller of images and videos in a module","link":"#","linktext":"Sample Standard","slide_class":"standard_slide"}';
		$slideTable->store();
		/*
		 * Insert demo slide 2 for Standard section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = $galleryId;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample Standard 2';
		$slideTable->section = 'SECTION_STANDARD';
		$slideTable->published = 1;
		$slideTable->params = '{"background_image":"images/stories/redslider/standard_slider2.jpg","caption":"Sample Standard","description":"redSLIDER is a Joomla extension for creating continuous horizontal scroller of images and videos in a module","link":"#","linktext":"Sample Standard","slide_class":"standard_slide2"}';
		$slideTable->store();
		/*
		 * Insert demo template for Video section
		 */
		$templateTable = JTable::getInstance('Template', 'RedsliderTable', array('ignore_request' => true));
		$templateTable->id = null;
		$templateTable->title = 'Template Video';
		$templateTable->section = 'SECTION_VIDEO';
		$templateTable->published = 1;
		$templateTable->content = '<div class="video_Slide"><div class="videoSlide">{youtube}<div class="slideTitle"><h3>{caption}</h3></div></div></div>';
		$templateTable->store();
		$templateId = (int) $templateTable->id;
		/*
		 * Insert demo slide for Video section
		 */
		$slideTable = JTable::getInstance('Slide', 'RedsliderTable', array('ignore_request' => true));
		$slideTable->gallery_id = $galleryId;
		$slideTable->template_id = $templateId;
		$slideTable->title = 'Sample Video';
		$slideTable->section = 'SECTION_VIDEO';
		$slideTable->published = 1;
		$slideTable->params = '{"local_media":"","local_width":"500","local_height":"315","background_image":"images/stories/redslider/bg_redshop_slider.png","slide_class":"video_slide","vimeo_id":"","vimeo_width":"500","vimeo_height":"281","vimeo_portrait":"0","vimeo_title":"0","vimeo_byline":"0","vimeo_autoplay":"0","vimeo_loop":"0","vimeo_color":"#FFFFFF","youtube_id":"Qjnc0H8utks","youtube_width":"500","youtube_height":"315","youtube_suggested":"0","youtube_privacy_enhanced":"0","other_iframe":""}';
		$slideTable->store();

		unset($gallery);
		unset($templateTable);
		unset($slideTable);

		return true;
	}
}
