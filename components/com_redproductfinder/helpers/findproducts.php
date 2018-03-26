<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('template', JPATH_SITE . '/components/com_redshop/helpers');

/**
 * Redproductfinder Component Form Helper
 *
 * @since  3.0
 */
class RedproductfinderFindProducts
{
	/**
	 * This method will filter type and tag to array
	 *
	 * @param   int  $templateId  template id
	 *
	 * @return array
	 */
	public static function getTemplate($templateId)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("template_desc");
		$query->from("#__redshop_template");
		$query->where("template_id = " . $db->q($templateId));

		$db->setQuery($query);

		// Get template name
		return $db->loadResult();
	}

	/**
	 * This method get name of tag
	 *
	 * @param   int  $id  tag id
	 *
	 * @return array
	 */
	public static function getTagName($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("tag_name");
		$query->from("#__redproductfinder_tags");
		$query->where("id = " . $db->q($id));

		$db->setQuery($query);

		// Get tag name
		return $db->loadResult();
	}

	/**
	 * This method get name of type
	 *
	 * @param   int  $id  type id
	 *
	 * @return array
	 */
	public static function getTypeName($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("type_name");
		$query->from("#__redproductfinder_types");
		$query->where("id = " . $db->q($id));

		$db->setQuery($query);

		// Get type name
		return $db->loadResult();
	}
}
