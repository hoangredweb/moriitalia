<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2017
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.632
 * @date		2017-06-01
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a standard button
 *
 * we cannot use Joomla's cancel button from a popup, as they use href="#" which causes the page to load in parallel with
 * closing of the popup. Need use href="javascript: void(0);"
 *
 *
 * @package 	Joomla.Framework
 * @subpackage		HTML
 * @since		1.5
 */
class JToolbarButtonj3popupstandardbutton extends JToolbarButtonStandard
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	var $_name = 'Standard';

	function fetchButton( $type='Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		$i18n_text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand($text, $task, $list, $hideMenu);

		$html	= "<button href=\"javascript: void(0);\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class btn btn-small\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html	.= "$i18n_text\n";
		$html	.= "</button>\n";
		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 * @since	1.5
	 */
	function fetchId( $type='Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		return $this->_parent->getName().'-'.$name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	string	$name	The task name as seen by the user
	 * @param	string	$task	The task used by the application
	 * @param	???		$list
	 * @param	boolean	$hide
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	function _getCommand($name, $task, $list, $hide)
	{
		$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);
		$hidecode	= $hide ? 'shHideMainMenu();' : '';

		if ($list) {
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ $hidecode Joomla.submitbutton('$task')}";
		} else {
			$cmd = "javascript:$hidecode Joomla.submitbutton('$task')";
		}


		return $cmd;
	}
}
