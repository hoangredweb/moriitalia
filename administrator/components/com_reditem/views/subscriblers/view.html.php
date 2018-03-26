<?php
/**
 * @package     RedITEM
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Subscriblers list view
 *
 * @package     RedITEM
 * @subpackage  View
 * @since       2.1.9
 */
class RedItemViewSubscriblers extends ReditemViewAdmin
{
	/**
	 * Display the reports list
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return   string
	 */
	public function display($tpl = null)
	{
		$user = ReditemHelperSystem::getUser();

		$this->items         = $this->get('Items');
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('Form');
		$this->activeFilters = $this->get('ActiveFilters');

		parent::display($tpl);
	}

	/**
	 * Get the page title
	 *
	 * @return  string  The title to display
	 */
	public function getTitle()
	{
		return JText::_('COM_REDITEM_SUBSCRIBLERS_TITLE');
	}
}
