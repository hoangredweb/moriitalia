<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;
jimport('joomla.filesystem.folder');

/**
 * Template helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Template
 * @since       2.0
 *
 */
class ReditemHelperTemplate
{
	/**
	 * Get template contets by template id.
	 *
	 * @param   int  $templateId  Template id.
	 *
	 * @return  string  Template contents.
	 */
	public static function getContent($templateId)
	{
		$db       = JFactory::getDbo();
		$query    = $db->getQuery(true);
		$contents = '';

		$query->select($db->qn('filename'))
			->from($db->qn('#__reditem_templates'))
			->where($db->qn('id') . ' = ' . (int) $templateId);
		$filename = $db->setQuery($query)->loadResult();

		if (JFile::exists(JPATH_REDITEM_TEMPLATES . $filename))
		{
			$contents = JFile::read(JPATH_REDITEM_TEMPLATES . $filename);

			if ($contents === false)
			{
				$contents = '';
			}
		}

		return $contents;
	}
}
