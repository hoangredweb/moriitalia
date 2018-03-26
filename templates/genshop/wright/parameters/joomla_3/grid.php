<?php
/**
 * @package     Wright
 * @subpackage  Parameters
 *
 * @copyright   Copyright (C) 2005 - 2015 redCOMPONENT.com.  All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Restrict Access to within Joomla
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Wright field to create the grid width input
 *
 * @package     Wright
 * @subpackage  Parameters
 * @since       2.0
 */
class JFormFieldGrid extends JFormFieldList
{
	public $type = 'Grid';

	/**
	 * Creates the options of the grid width
	 *
	 * @return  array  Options
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$template = $this->form->getValue('template');
		$size = 12;

		if (file_exists(JPATH_ROOT . '/templates/' . $template . '/wrighttemplate.php'))
		{
			require_once JPATH_ROOT . '/templates/' . $template . '/wrighttemplate.php';
			$wrightTemplate = WrightTemplate::getInstance();
			$size = $wrightTemplate->getTemplate()->params->get('columnsNumber', 12);
		}

		$options = array ();

		for ($i = 1; $i <= $size; $i++)
		{
			$val	= $i;
			$text	= $i;
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}

		return $options;
	}
}
