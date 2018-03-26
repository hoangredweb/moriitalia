<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 */
class RedproductfinderViewRedproductfinder extends JViewLegacy
{
	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$input	  	= $app->input;

		$redshop_css_link = JURI::root().'components/com_redshop/assets/css/redshop.css';
		$document->addStyleSheet($redshop_css_link);

		$redfinder_js = JURI::root().'components/com_redproductfinder/helpers/redproductfinder.js';
		$document->addScript($redfinder_js);

// 		if (PRODUCT_HOVER_IMAGE_ENABLE)
// 			$document->addStyleSheet ( 'components/com_redshop/assets/css/style.css' );

		$model = $this->getModel();
		$tags = $model->getType('id, tag_name', 'tags', 1);

		//$type_model = $this->getModel('Redproductfinder');
		// Load the types
		$types = $this->get('Types');
		$post = JRequest::get('post');

		// Assign the necessary data
		$this->assignRef('lists', $lists);
		$this->assignRef('types', $types);

		parent::display($tpl);
	}
}
?>