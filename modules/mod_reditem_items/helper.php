<?php
/**
 * @package     RedITEM.Frontend
 * @subpackage  mod_reditem_items
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('reditem.library');
require_once JPATH_SITE . '/modules/mod_reditem_items/helper.php';

/**
 * Categories helper
 *
 * @since  1.0
 */
class ModredITEMItemsHelper
{
	/**
	 * Get list of categories
	 *
	 * @param   array  &$params  Module parameters
	 *
	 * @return  array
	 */
	public static function getList(&$params)
	{
		$paramCategories    = $params->get('categoriesIds', array());
		$paramSubCat        = $params->get('include_sub', 0);
		$paramOrdering      = $params->get('items_ordering', 'i.alias');
		$paramDirection     = $params->get('items_direction', 'asc');
		$paramLimit         = $params->get('limit', '10');
		$paramFeaturedItems = $params->get('featured_items', '0');
		$paramItemId        = $params->get('setItemId', 0);
		$moduleTemplate     = $params->get('moduleTemplateInstance', null);
		$moduleId           = $params->get('moduleId', 0);

		if ($paramSubCat)
		{
			$categories = array_unique(array_merge(ReditemHelper::getSubCategories($paramCategories), $paramCategories));
		}
		else
		{
			$categories = $paramCategories;
		}

		// Get items
		$itemsModel = RModel::getAdminInstance('Items', array('ignore_request' => true), 'com_reditem');
		$itemsModel->setState('filter.published', 1);
		$itemsModel->setState('filter.catid', $categories);
		$itemsModel->setState('list.select',
			'DISTINCT (i.id), i.title, i.published, i.type_id, i.template_id, i.created_user_id,
			i.created_time, i.modified_user_id, i.modified_time, i.params');
		$itemsModel->setState('list.ordering', $paramOrdering);
		$itemsModel->setState('list.direction', $paramDirection);
		$itemsModel->setState('list.limit', (int) $paramLimit);
		$itemsModel->setState('filter.featured', (int) $paramFeaturedItems);
		$items = $itemsModel->getItems();

		// Process check view permission for items list.
		ReditemHelperACL::processItemACL($items);

		if (empty($items))
		{
			return false;
		}

		$ids = ReditemHelperItem::getItemIds($items);
		$cfValues = ReditemHelperItem::getCustomFieldValues($ids);
		$categories = ReditemHelperItem::getCategories($ids, false);
		$cTags = ReditemHelperItem::getCustomFieldTags($moduleTemplate->type_id);

		foreach ($items as $item)
		{
			if (isset($cfValues[$item->type_id][$item->id]))
			{
				$item->customfield_values = $cfValues[$item->type_id][$item->id];
			}

			if (isset($categories[$item->id]))
			{
				$item->categories = $categories[$item->id];
			}

			if ($moduleTemplate && ($moduleTemplate->typecode == 'module_items'))
			{
				$item->content = $moduleTemplate->content;
				ReditemHelperItem::replaceTag($item->content, $item, 0, $paramItemId, true);
				ReditemHelperItem::replaceCustomfieldsTag($item->content, $item, $cTags);

				JPluginHelper::importPlugin('content');
				$item->content = JHtml::_('content.prepare', $item->content);

				if (JPluginHelper::isEnabled('system', 'twig'))
				{
					$fields = array();

					foreach ($item->customfield_values as $key => $value)
					{
						if (($decode = ReditemHelperCustomfield::isJsonValue($value)) !== false)
						{
							if (is_array($decode))
							{
								if (count($decode) == 1)
								{
									$fields[$key] = $decode[0];
								}
								elseif (count($decode) == 0)
								{
									$fields[$key] = '';
								}
								else
								{
									$fields[$key] = $decode;
								}
							}
							else
							{
								$fields[$key] = $decode;
							}
						}
						else
						{
							$fields[$key] = $value;
						}
					}

					$loader = new Twig_Loader_Array(
						array (
							'mod_reditem_items-item-' . $item->id . '_' . $moduleId . '.html' => $item->content
						)
					);
					$twig = new Twig_Environment($loader);
					$twig->addExtension(ReditemHelperTwig::getExtension());
					$item->content = $twig->render(
						'mod_reditem_items-item-' . $item->id . '_' . $moduleId . '.html',
						array (
							'fields' => $fields
						)
					);
				}
			}
		}

		return $items;
	}
}
