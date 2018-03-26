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

jimport('joomla.application.component.controller');

/**
 * redPRODUCTFINDER Controller
 */
class RedproductfinderControllerRedproductfinder extends JControllerForm
{
	/**
    * Method to display the view
    *
    * @access   public
    */
   function __construct()
   {
      parent::__construct();

      /* Redirect templates to templates as this is the standard call */
      // $this->registerTask('findproducts','redproductfinder');
   }


	/**
	 * Method to show the Redproductfinder view
	 *
	 * @access	public
	 * @todo rename duplicate model name redproductfinder
	 */
	public function Redproductfinder()
	{
		/* Create the view object */
		$view = $this->getView('redproductfinder', 'html');

		/* Set model paths */
		$this->addModelPath(JPATH_COMPONENT.DS.'models');

		/* Standard model */
		$view->setModel( $this->getModel( 'Redproductfinder', 'RedproductfinderModel' ), true );

		/* Backend models */
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$view->setModel( $this->getModel( 'types', 'RedproductfinderModel' ));
		$view->setModel( $this->getModel( 'tags', 'RedproductfinderModel' ));
		$view->setModel( $this->getModel( 'associations', 'RedproductfinderModel' ));

		/* Set the layout */
		$view->setLayout('redproductfinder');

		/* Now display the view */
		$view->display();
	}
	/**
	 * Method to show the Redproductfinder view for ajax call
	 *
	 * @access	public
	 * @todo rename duplicate model name redproductfinder
	 */
	public function Redproductfinder_ajax()
	{
		/* Create the view object */
		$view = $this->getView('redproductfinder', 'html');

		/* Set model paths */
		$this->addModelPath(JPATH_COMPONENT.DS.'models');

		/* Standard model */
		$view->setModel( $this->getModel( 'Redproductfinder', 'RedproductfinderModel' ), true );

		/* Backend models */
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$view->setModel( $this->getModel( 'types', 'RedproductfinderModel' ));
		$view->setModel( $this->getModel( 'tags', 'RedproductfinderModel' ));
		$view->setModel( $this->getModel( 'associations', 'RedproductfinderModel' ));

		/* Set the layout */
		$view->setLayout('redproductfinder_ajax');

		/* Now display the view */
		$view->display();
	}
	public function Findproducts()
	{
		/* Set a default view if none exists */
		JRequest::setVar('view', 'redproductfinder' );
		JRequest::setVar('layout', 'searchresult' );

		parent::display();
	}
}

?>