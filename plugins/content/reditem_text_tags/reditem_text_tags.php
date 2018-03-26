<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

JLoader::import('reditem.library');

/**
 * Plugins RedITEM Tags
 *
 * @since  2.0
 */
class PlgContentreditem_Text_Tags extends JPlugin
{
	/**
	 * Method run on Content Prepare trigger
	 *
	 * @param   string  $context  Context
	 * @param   array   &$row     Data
	 * @param   array   &$params  Plugins parameters
	 * @param   int     $page     Page number
	 *
	 * @return  boolean
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$this->tagReditemText($row);

		return true;
	}

	/**
	 * Replace tag for items {text|$language_string}
	 *
	 * @param   array  &$row  Reference of row data
	 *
	 * @return  void
	 */
	public function tagReditemText(&$row)
	{
		$matches = array();

		if (!empty($row->content) && preg_match_all('/{reditem_text[^}]*}/i', $row->content, $matches) > 0)
		{
			RHelperAsset::load('reditem.min.js', 'com_reditem');

			$matches = $matches[0];

			foreach ($matches as $match)
			{
				$tagMatch = str_replace('{', '', str_replace('}', '', $match));
				$tagParams = explode('|', $tagMatch);
				$row->content = str_replace($match, JText::_($tagParams[1]), $row->content);
			}
		}
	}
}
