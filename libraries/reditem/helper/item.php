<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Item tags helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.ItemTags
 * @since       2.0
 *
 */
class ReditemHelperItem
{
	/**
	 * Table names by item id.
	 *
	 * @var array
	 */
	private static $tableNames = array();

	/**
	 * Replace tag on content of template -> Item TAG
	 * Replace for ReditemTagsHelper::tagReplaceItem() function
	 *
	 * @param   string  &$content    Content template
	 * @param   array   $item        Item data array
	 * @param   int     $categoryId  Parent category id
	 * @param   int     $itemId      Item ID default
	 * @param   bool    $basicMode   Only replace general tags
	 *
	 * @return  bool    True if success. False otherwise.
	 */
	public static function replaceTag(&$content, $item, $categoryId = 0, $itemId = 0, $basicMode = false)
	{
		JPluginHelper::importPlugin('reditem_item_tag');
		$dispatcher = RFactory::getDispatcher();

		// Check if content is empty of item object is null
		if (empty($content) || empty($item))
		{
			return false;
		}

		// Get categories
		if (empty($item->categories))
		{
			$categories = self::getCategories($item->id, false);

			if (isset($categories[$item->id]))
			{
				$item->categories = $categories[$item->id];
			}
		}
		elseif (!is_array($item->categories))
		{
			$item->categories = json_decode($item->categories);
		}

		if ($basicMode)
		{
			self::replaceGeneralTag($content, $item, $categoryId, $itemId);

			$dispatcher->trigger('onAfterReplaceItemTag', array(&$content, $item));

			return true;
		}

		// Get type parameters
		self::getTypeObject($item);

		// Item sharing tool
		ReditemHelperShare::replaceTag($content, $item);

		// Item General tag
		self::replaceGeneralTag($content, $item, $categoryId, $itemId);

		$dispatcher->trigger('onAfterReplaceItemTag', array(&$content, $item));

		return true;
	}

	/**
	 * Replace tag on content of template
	 * Replace for ReditemTagsHelper::tagReplaceItemCustomField() function
	 *
	 * @param   string  &$content         Content template
	 * @param   object  $item             Item data object
	 * @param   array   $customFieldTags  List of custom field available for this item
	 *
	 * @return  boolean
	 */
	public static function replaceCustomfieldsTag(&$content, $item, $customFieldTags = null)
	{
		// Check if item object has no custom fields.
		if (empty($content) || empty($item))
		{
			return false;
		}

		// Get customfield tags
		if (!isset($customFieldTags))
		{
			$customFieldTags = self::getCustomFieldTags($item->type_id);
		}

		foreach ($customFieldTags As $tag)
		{
			$fieldClass = ReditemHelperCustomfield::getCustomField($tag->type);
			$fieldClass->bind($tag);

			// Replace the title tag
			$fieldClass->replaceLabelTag($content, $tag);

			// Replace the value tag
			$fieldClass->replaceValueTag($content, $tag, $item);
		}

		return true;
	}

	/**
	 * Method for get categories of a list of item
	 *
	 * @param   int|array  $itemIds  List of item id
	 * @param   boolean    $idOnly   True is return array of ids. False is return array of objects
	 *
	 * @return  mixed
	 */
	public static function getCategories($itemIds, $idOnly = true)
	{
		$db = JFactory::getDbo();

		if (!is_array($itemIds))
		{
			$itemIds = array($itemIds);
		}

		$itemIds = array_filter($itemIds);
		$result = array();

		if (!empty($itemIds))
		{
			RModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_reditem/models', 'ReditemModel');
			$categoryModel = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

			// Get categories reference
			$query = $db->getQuery(true)
				->select('x.*')
				->from($db->qn('#__reditem_item_category_xref', 'x'))
				->where($db->qn('item_id') . ' IN(' . implode(',', $itemIds) . ')')
				->where($db->qn('category_id') . ' > 0');

			$db->setQuery($query);
			$refs = $db->loadObjectList();

			foreach ($refs as $ref)
			{
				if ($idOnly)
				{
					$result[$ref->item_id][] = $ref->category_id;
				}
				else
				{
					$category         = $categoryModel->getItemNoRelated($ref->category_id);
					$category->fields = $categoryModel->getCustomFields($ref->category_id, true);

					if ($category->id)
					{
						$result[$ref->item_id][] = $category;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Method for get custom fields list of specific type ID
	 *
	 * @param   int  $typeId  Id of type
	 *
	 * @return  array  List of fields. False otherwise.
	 */
	public static function getCustomFieldTags($typeId)
	{
		$typeId = (int) $typeId;

		if (!$typeId)
		{
			return false;
		}

		$fieldsmodel = RModel::getAdminInstance('Fields', array('ignore_request' => true), 'com_reditem');
		$fieldsmodel->setState('filter.types', $typeId);
		$fieldsmodel->setState('filter.published', 1);
		$fields = $fieldsmodel->getItems();

		return $fields;
	}

	/**
	 * Method for get custom fields value of an item object list
	 *
	 * @param   object|array  $itemIds  List of item ids
	 *
	 * @return  mixed
	 */
	public static function getCustomFieldValues($itemIds)
	{
		$db = JFactory::getDbo();

		if (is_object($itemIds))
		{
			$itemIds = array($itemIds->id);
		}
		elseif (!is_array($itemIds))
		{
			$itemIds = array($itemIds);
		}

		$itemIds = array_filter($itemIds);
		$group = array();

		foreach ($itemIds as $id)
		{
			if (is_object($id))
			{
				$id = $id->id;
			}

			$item = ReditemEntityItem::getInstance($id);

			if ($item->getItemId())
			{
				$tid = $item->getTypeId();
				$group[$tid][] = $id;
			}
		}

		$result = array();

		if ($group)
		{
			foreach ($group as $type => $ids)
			{
				$table = ReditemHelperType::getTableName($type);

				if ($table)
				{
					$query = $db->getQuery(true)
						->select('cf.*')
						->from($db->qn($table, 'cf'))
						->where($db->qn('cf.id') . ' IN (' . implode(',', $ids) . ')');
					$db->setQuery($query);

					$result[$type] = $db->loadAssocList('id');
				}
			}
		}

		return $result;
	}

	/**
	 * Method for get type object for item
	 *
	 * @param   object  $item  Item object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function getTypeObject($item)
	{
		if (empty($item) || !is_object($item) || !empty($item->type))
		{
			return false;
		}

		$typeModel  = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
		$item->type = $typeModel->getItem($item->type_id);

		return true;
	}

	/**
	 * Method for replace related items list
	 *
	 * @param   string  &$content  Template content
	 * @param   object  $item      Item object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function replaceRelatedItems(&$content, $item)
	{
		if (empty($content) || empty($item))
		{
			return false;
		}

		$relatedItems   = array();
		$preContentIf   = '';
		$postContentIf  = '';
		$subContent     = '';
		$preSubContent  = '';
		$postSubContent = '';

		// Prepare type object of item
		if (!isset($item->type))
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$item->type = $typeModel->getItem($item->type_id);

			// Clean up memory
			unset($typeModel);
		}

		// Prepare related items list
		$itemParams   = new JRegistry($item->params);
		$relatedItems = self::parseRelatedItems($itemParams->get('related_items', array()));

		// If has {if} tag, split the content
		if ((strpos($content, '{if_related_items}') == true) && (strpos($content, '{end_if_related_items') == true))
		{
			$tempContent = explode('{if_related_items}', $content);
			$preContentIf = (count($tempContent) > 1) ? $tempContent[0] : '';

			$tempContent = $tempContent[count($tempContent) - 1];
			$tempContent = explode('{end_if_related_items}', $tempContent);
			$subContent = $tempContent[0];

			$postContentIf = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

			// If no related items available
			if (empty($relatedItems))
			{
				$content = $preContentIf . $postContentIf;

				// Stop function
				return false;
			}
		}
		else
		{
			$subContent = $content;
		}

		// Split the for loop tag
		if ((strpos($subContent, '{related_items_loop_start}') == true) && (strpos($subContent, '{related_items_loop_end') == true))
		{
			$tempSubContent = explode('{related_items_loop_start}', $subContent);
			$preSubContent = (count($tempSubContent) > 1) ? $tempSubContent[0] : '';

			$tempSubContent = $tempSubContent[count($tempSubContent) - 1];
			$tempSubContent = explode('{related_items_loop_end}', $tempSubContent);
			$subContent = $tempSubContent[0];

			$postSubContent = (count($tempSubContent) > 1) ? $tempSubContent[count($tempSubContent) - 1] : '';

			// If no related items available
			if (empty($relatedItems))
			{
				$content = $preContentIf . $preSubContent . $postSubContent . $postSubContent;

				// Stop function
				return false;
			}

			// Process replace tag on related items
			$itemsContent = '';

			foreach ($relatedItems as $relatedItemId)
			{
				$itemModel = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
				$tempItem = $itemModel->getItem($relatedItemId);
				$tempItem->type = $item->type;
				$tempItemContent = $subContent;
				self::replaceTag($tempItemContent, $tempItem);
				self::replaceCustomfieldsTag($tempItemContent, $tempItem);
				$itemsContent .= $tempItemContent;

				// Clean up memory
				unset($tempItem);
			}

			$subContent = $itemsContent;
		}

		$content = $preContentIf . $preSubContent . $subContent . $postSubContent . $postContentIf;

		return true;
	}

	/**
	 * Parse related items to cast out blocked and unpublished ones.
	 *
	 * @param   array  $ids  Item ids.
	 *
	 * @return  array  Item ids.
	 */
	private static function parseRelatedItems($ids)
	{
		if (empty($ids) || !is_array($ids))
		{
			return array();
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('id'))
			->from($db->qn('#__reditem_items'))
			->where($db->qn('published') . ' = 1')
			->where($db->qn('blocked') . ' = 0')
			->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');

		return $db->setQuery($query)->loadColumn();
	}

	/**
	 * Method for replace location tag for item
	 *
	 * @param   string  &$content  Template content
	 * @param   object  $item      Item object
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function replaceLocationTag(&$content, $item)
	{
		if (empty($content) || empty($item))
		{
			return false;
		}

		// Get type parameters
		if (!isset($item->type))
		{
			$typeModel = RModel::getAdminInstance('Type', array('ignore_request' => true), 'com_reditem');
			$item->type = $typeModel->getItem($item->type_id);
		}

		$typeParams = new JRegistry($item->type->params);
		$useItemGmap = (boolean) $typeParams->get('item_gmap_field', false);

		// If "Item Gmap" option is not enabled in Type Configuration
		if (!$useItemGmap)
		{
			$content = str_replace('{item_location}', '', $content);
			$content = str_replace('{item_location_address}', '', $content);

			return false;
		}

		// Clean up memory
		unset($typeParams);
		unset($useItemGmap);

		$itemParams = new JRegistry($item->params);

		// Replace for {item_location_address} tag
		if (strpos($content, '{item_location_address}') == true)
		{
			$itemAddress = $itemParams->get('itemAddress', '');
			$content = str_replace('{item_location_address}', $itemAddress, $content);
		}

		// Replace for {item_location} tag
		if (strpos($content, '{item_location}') == true)
		{
			$itemLatLng = $itemParams->get('itemLatLng', '');
			$itemLatLng = explode(',', $itemLatLng);

			// Check if value of item location is right or not
			if (!isset($itemLatLng[1]))
			{
				$content = str_replace('{item_location}', '', $content);

				// Exit current function
				return false;
			}

			$itemLatitude = (float) $itemLatLng[0];
			$itemLongtitude = (float) $itemLatLng[1];

			$layoutData = array('item' => $item, 'latitude' => $itemLatitude, 'longtitude' => $itemLongtitude);
			$contentHtml = ReditemHelperLayout::render($item->type, 'item_location', $layoutData, array('component' => 'com_reditem'));
			$content = str_replace('{item_location}', $contentHtml, $content);
		}

		// Clean up memory
		unset($itemParams);

		return true;
	}

	/**
	 * Prepare the item edit tamplte
	 *
	 * @param   object  $template      template object
	 * @param   object  $form          form object
	 * @param   array   $customfields  customfields array
	 * @param   object  $item          item object
	 *
	 * @return mixed
	 */
	public static function prepareItemEditTemplate($template, $form, $customfields, $item)
	{
		$content = $template->content;

		// Standard fields
		$content = str_replace('{item_title_label}', $form->getLabel('title'), $content);
		$content = str_replace('{item_title}', $form->getInput('title'), $content);
		$content = str_replace('{item_alias_label}', $form->getLabel('alias'), $content);
		$content = str_replace('{item_alias}', $form->getInput('alias'), $content);
		$content = str_replace('{item_category_label}', $form->getLabel('categories'), $content);
		$content = str_replace('{item_category}', $form->getInput('categories'), $content);
		$content = str_replace('{item_access_label}', $form->getLabel('access'), $content);
		$content = str_replace('{item_access}', $form->getInput('access'), $content);
		$content = str_replace('{item_template_label}', $form->getLabel('template_id'), $content);
		$content = str_replace('{item_template}', $form->getInput('template_id'), $content);
		$content = str_replace('{item_featured_label}', $form->getLabel('featured'), $content);
		$content = str_replace('{item_featured}', $form->getInput('featured'), $content);
		$content = str_replace('{item_published_label}', $form->getLabel('published'), $content);
		$content = str_replace('{item_published}', $form->getInput('published'), $content);
		$content = str_replace('{item_link_label}', $form->getLabel(''), $content);
		$content = str_replace('{item_link}', $form->getInput(''), $content);
		$content = str_replace('{item_start_publishing_label}', $form->getLabel('publish_up'), $content);
		$content = str_replace('{item_start_publishing}', $form->getInput('publish_up'), $content);
		$content = str_replace('{item_finish_publishing_label}', $form->getLabel('publish_down'), $content);
		$content = str_replace('{item_finish_publishing}', $form->getInput('publish_down'), $content);

		$relatedItemsField = $form->getField('related_items', 'params');
		$content = str_replace('{item_related_items_label}', $relatedItemsField->label, $content);
		$content = str_replace('{item_related_items}', $relatedItemsField->input, $content);

		$metaDescriptionField = $form->getField('meta_description', 'params');
		$content = str_replace('{item_meta_description_label}', $metaDescriptionField->label, $content);
		$content = str_replace('{item_meta_description}', $metaDescriptionField->input, $content);

		$metaKeywordsField = $form->getField('meta_keywords', 'params');
		$content = str_replace('{item_meta_keywords_label}', $metaKeywordsField->label, $content);
		$content = str_replace('{item_meta_keywords}', $metaKeywordsField->input, $content);

		// Customfields
		foreach ($customfields as $field)
		{
			// Field title
			$tag = '{' . $field->fieldcode . '_text}';

			if (strstr($content, $tag))
			{
				$content = str_replace($tag, $field->getLabel(), $content);
			}

			// Field value
			$tag = '{' . $field->fieldcode . '_value}';

			if (strstr($content, $tag))
			{
				$content = str_replace($tag, $field->render(), $content);
			}
		}

		return $content;
	}

	/**
	 * Method for replace general tag for item
	 *
	 * @param   string  &$content  Template content
	 * @param   object  $item      Item object
	 * @param   object  $catid     Category Id
	 * @param   object  $itemId    Menu Item Id
	 *
	 * @return  boolean  True on success. False otherwise.
	 */
	public static function replaceGeneralTag(&$content, $item, $catid, $itemId)
	{
		$nullDate = RFactory::getDbo()->getNullDate();

		if (empty($content) || empty($item))
		{
			return false;
		}

		// Id tag
		$content = str_replace('{item_id}', $item->id, $content);

		// Title tag
		$matches = array();

		if (preg_match_all('/{item_title[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			foreach ($matches as $match)
			{
				$textParams = explode('|', $match);
				$tmpValue   = $item->title;

				if (isset($textParams[1]))
				{
					$tmpValue = JHTML::_('string.truncate', $tmpValue, (int) $textParams[1], true, false);
				}

				$content = str_replace($match, $tmpValue, $content);
			}
		}

		$content = str_replace('{item_title}', $item->title, $content);

		// In case, item has 1 category. Process it as main category
		if (!empty($item->categories) && count($item->categories) == 1)
		{
			if (is_numeric($item->categories[0]))
			{
				$categoryId = $item->categories[0];
			}
			elseif (!empty($item->categories[0]->id))
			{
				$categoryId = $item->categories[0]->id;
			}
		}
		else
		{
			// Item link
			$categoryId = (int) $catid;

			if ($categoryId === 0 && !empty($item->categories))
			{
				// Get first id of categories of item
				$categoryId = $item->categories[0]->id;
			}
		}

		if ($itemId)
		{
			$item->itemLink = 'index.php?option=com_reditem&view=itemdetail&id=' . $item->id . '&cid=' . $categoryId . '&Itemid=' . $itemId;
		}
		else
		{
			$item->itemLink = ReditemHelperRouter::getItemRoute($item->id, $categoryId);
		}

		$item->itemLink = JRoute::_($item->itemLink, false);

		$content = str_replace('{item_link}', $item->itemLink, $content);
		$content = str_replace('{item_author}', JFactory::getUser($item->created_user_id)->name, $content);

		// Replace start publishing tag
		if (preg_match_all('/{item_publishing_start[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			if (!empty($matches))
			{
				foreach ($matches as $match)
				{
					$tag     = str_replace(array('{', '}'), '', $match);
					$tmp     = explode('|', $tag);
					$format  = isset($tmp[1]) ? $tmp[1] : 'H:i d.m.Y';

					if ($item->publish_up > '0000-00-00 00:00:00')
					{
						$date = JFactory::getDate($item->publish_up)->format($format);
					}
					else
					{
						$date = JText::_('COM_REDITEM_DATE_NOT_SET');
					}

					$content = str_replace($match, $date, $content);
				}
			}
		}

		// Replace end publishing tag
		if (preg_match_all('/{item_publishing_end[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			if (!empty($matches))
			{
				foreach ($matches as $match)
				{
					$tag     = str_replace(array('{', '}'), '', $match);
					$tmp     = explode('|', $tag);
					$format  = isset($tmp[1]) ? $tmp[1] : 'H:i d.m.Y';

					if ($item->publish_down > '0000-00-00 00:00:00')
					{
						$date = JFactory::getDate($item->publish_down)->format($format);
					}
					else
					{
						$date = JText::_('COM_REDITEM_DATE_NOT_SET');
					}

					$content = str_replace($match, $date, $content);
				}
			}
		}

		// Item edit link
		$matches = array();

		if (preg_match_all('/{item_edit_link[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			if (!empty($matches))
			{
				$itemEditLink = '';
				$itemCanEdit  = false;
				$user         = ReditemHelperSystem::getUser();

				/*
				 * Make sure user is not Guest
				 * Check if user have Item.Edit permission on this type
				 * Or user have Item.Edit.Own permission on this type and this item is created by this user
				 */
				if (!$user->guest
					&& (ReditemHelperACL::checkItemPermission('item.edit', $item->id)
					|| (ReditemHelperACL::checkItemPermission('item.edit.own', $item->id) && ($item->created_user_id == $user->id))))
				{
					$itemCanEdit = true;
				}

				if ($itemCanEdit)
				{
					$itemEditLink = 'index.php?option=com_reditem&view=item&layout=edit&id=' . $item->id;

					if ($itemId)
					{
						$itemEditLink .= '&Itemid=' . $itemId;
					}

					$itemEditLink = JRoute::_($itemEditLink);
				}

				foreach ($matches as $match)
				{
					$generatedItemEdit = '';
					$tag		= str_replace('{', '', $match);
					$tag		= str_replace('}', '', $tag);
					$tagParams	= explode('|', $tag);

					if ($itemCanEdit)
					{
						$itemEditText	= '';
						$itemEditIcon	= '';
						$itemEditClass	= '';
						$itemEditHtml	= '';

						// Get "Text" parameter
						if (isset($tagParams[1]))
						{
							$itemEditText = $tagParams[1];
						}

						// Get "Icon" parameter
						if (isset($tagParams[2]) && !empty($tagParams[2]))
						{
							$itemEditIcon = '<img src="' . JURI::root() . $tagParams[2] . '" />';
						}

						// Get "Class" parameter
						if (isset($tagParams[3]))
						{
							$itemEditClass = $tagParams[3];
						}

						// Get "Html" parameter
						if (isset($tagParams[4]))
						{
							$itemEditHtml = $tagParams[4];
						}

						$generatedItemEdit = '<a href="' . $itemEditLink . '" class="' . $itemEditClass . '">'
							. $itemEditText
							. ' ' . $itemEditIcon
							. $itemEditHtml . '</a>';
					}

					$content = str_replace($match, $generatedItemEdit, $content);
				}
			}
		}

		$firstCatLinkMatches = array();
		$firstCatNameMatches = array();

		$itemFirstCatLinkCheck = preg_match_all('/{item_first_cat_link[^}]*}/i', $content, $firstCatLinkMatches);
		$itemFirstCatNameCheck = preg_match_all('/{item_first_cat_name[^}]*}/i', $content, $firstCatNameMatches);
		$itemFirstCatIdCheck   = strpos($content, '{item_first_cat_id}');

		// First category tags available in template
		if ($itemFirstCatLinkCheck || $itemFirstCatNameCheck || $itemFirstCatIdCheck !== false)
		{
			$parentCategory = null;

			if (!empty($item->categories))
			{
				$parentCategory = $item->categories[0];
			}

			if (!empty($firstCatLinkMatches))
			{
				$matches = $firstCatLinkMatches[0];

				foreach ($matches as $match)
				{
					$parentCategoryLink = '';

					if ($parentCategory)
					{
						$textParams = explode('|', $match);

						if (isset($textParams[1]))
						{
							$itemId = JString::str_ireplace('}', '', $textParams[1]);

							if (is_numeric($itemId))
							{
								$parentCategoryLink = JRoute::_(ReditemRouterHelper::getCategoryRoute($parentCategory->id, $itemId));
							}
						}
						else
						{
							$parentCategoryLink = JRoute::_(ReditemRouterHelper::getCategoryRoute($parentCategory->id));
						}
					}

					$content = str_replace($match, $parentCategoryLink, $content);
				}
			}

			if (!empty($firstCatNameMatches))
			{
				$matches = $firstCatNameMatches[0];

				foreach ($matches as $match)
				{
					$textContent = '';

					if ($parentCategory)
					{
						$textParams = explode('|', $match);
						$textContent = $parentCategory->title;

						if (isset($textParams[1]))
						{
							$limit = JString::str_ireplace('}', '', $textParams[1]);

							// Have param limit string
							$textContent = JHTML::_('string.truncate', $textContent, (int) $limit, true, false);
						}
					}

					$content = str_replace($match, $textContent, $content);
				}
			}

			if ($itemFirstCatIdCheck !== false)
			{
				$parentId = '';

				if ($parentCategory)
				{
					$parentId = $parentCategory->id;
				}

				$content = str_replace('{item_first_cat_id}', $parentId, $content);
			}
		}

		$preg = '/{item_files_info[^}]*}/i';
		$fileCount = 0;

		if (preg_match_all($preg, $content, $matches) > 0)
		{
			$cfValues = self::getCustomFieldValues($item->id);

			if (isset($cfValues[$item->type_id][$item->id]))
			{
				$item->customfield_values = $cfValues[$item->type_id][$item->id];
			}

			$matches = $matches[0];

			$customFieldValues = $item->customfield_values;

			foreach ($matches as $match)
			{
				$value		= '';

				$textParams	= str_replace('}', '', $match);
				$textParams	= explode('|', $textParams);

				if (count($textParams) > 1)
				{
					if (isset($customFieldValues[$textParams[1]]))
					{
						$value = $customFieldValues[$textParams[1]];
					}
				}

				if ($value)
				{
					$value = json_decode($value);
					$fileCount = count($value);
				}

				$layoutData = array('field' => $value, 'item' => $item);

				$contentHtml = '';

				if (count($value) && is_array($value))
				{
					$layoutFile    = 'item_files_info';
					$layoutOptions = array('component' => 'com_reditem');

					$contentHtml = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);
				}

				$content = str_replace($match, $contentHtml, $content);
			}
		}

		// Replace {item_created} tag
		$matches = array();

		if (preg_match_all('/{item_created[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			if (!empty($matches))
			{
				// Default format for display Created date
				$defaultDateFormat = 'd-m-Y H:i:s';

				foreach ($matches as $match)
				{
					$tagString = str_replace(array('{', '}'), '', $match);
					$tagString = explode('|', $tagString);
					$itemCreatedDate = ReditemHelperSystem::getDateWithTimezone($item->created_time);
					$dateFormat = $defaultDateFormat;

					if (isset($tagString[1]))
					{
						$dateFormat = (string) $tagString[1];
					}

					if (empty($item->created_time) || ($item->created_time == $nullDate))
					{
						$html = '';
					}
					else
					{
						$html = $itemCreatedDate->format($dateFormat, true);
					}

					$content = str_replace($match, $html, $content);

					unset($itemCreatedDate);
				}
			}
		}

		// Replace {item_modified} tag
		$matches = array();

		if (preg_match_all('/{item_modified[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			if (!empty($matches))
			{
				// Default format for display Created date
				$defaultDateFormat = 'd-m-Y H:i:s';

				foreach ($matches as $match)
				{
					$tagString = str_replace(array('{', '}'), '', $match);
					$tagString = explode('|', $tagString);
					$itemModifiedTime = ReditemHelperSystem::getDateWithTimezone($item->modified_time);
					$dateFormat = $defaultDateFormat;

					if (isset($tagString[1]))
					{
						$dateFormat = (string) $tagString[1];
					}

					if (empty($item->modified_time) || ($item->modified_time == $nullDate))
					{
						$html = '';
					}
					else
					{
						$html = $itemModifiedTime->format($dateFormat, true);
					}

					$content = str_replace($match, $html, $content);

					unset($itemModifiedTime);
				}
			}
		}

		// Item location
		self::replaceLocationTag($content, $item);

		// Item files if
		self::replaceFileIf($content, $fileCount);

		// Print icon
		$url  = '#';
		$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
		$attribs['title']   = JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.print(); return false;";
		$attribs['rel']     = 'nofollow';
		$printHtml = JHtml::_('link', JRoute::_($url), $text, $attribs);
		$content = str_replace('{print_icon}', $printHtml, $content);

		// Email icon
		if (preg_match_all('/{email_icon[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			// If we found this tag
			if (!empty($matches))
			{
				// Process all tags
				foreach ($matches as $match)
				{
					// Extract it into params
					$tmpMatch = explode('|', $match);
					$attribs = array ();

					switch (count($tmpMatch))
					{
						case 2:
							$mailTo  = $tmpMatch[0];
							$subject = $tmpMatch[1];
							break;
						default:
							$config  = JFactory::getConfig();
							$mailTo  = $config->get('mailfrom');
							$subject = '';
					}

					// Data prepare
					$layoutFile    = 'item.email.body';
					$layoutOptions = array('component' => 'com_reditem');
					$layoutData    = array(
						'item' => $item
					);

					// Prepare email body
					$body = ReditemHelperLayout::render($item->type, $layoutFile, $layoutData, $layoutOptions);

					$url = 'mailto:' . $mailTo;
					$url .= (empty($subject)) ? '?Subject=' . urlencode($subject) : '';
					$url .= '&body=' . urlencode($body);

					$text = JHtml::_('image', 'system/emailButton.png', JText::_('JGLOBAL_EMAIL'), null, true);
					$attribs['title']   = JText::_('JGLOBAL_PRINT');
					$attribs['rel']     = 'nofollow';
					$printHtml = JHtml::_('link', JRoute::_($url), $text, $attribs);
					$content = str_replace($match, $printHtml, $content);
				}
			}
		}

		return true;
	}

	/**
	 * Method for replace file information
	 *
	 * @param   string  &$content   HTML content of template
	 * @param   int     $fileCount  File count value
	 *
	 * @return  boolean            True on success. False otherwise.
	 */
	public static function replaceFileIf(&$content, $fileCount)
	{
		$fileCount = (int) $fileCount;

		if (empty($content))
		{
			return false;
		}

		if ((strpos($content, '{if_files}') == true) && (strpos($content, '{end_if_files}') == true))
		{
			$tempContent   = explode('{if_files}', $content);
			$preContentIf  = (count($tempContent) > 1) ? $tempContent[0] : '';
			$tempContent   = $tempContent[count($tempContent) - 1];
			$tempContent   = explode('{end_if_files}', $tempContent);
			$postContentIf = (count($tempContent) > 1) ? $tempContent[count($tempContent) - 1] : '';

			// Check if item has rating
			if ($fileCount <= 0)
			{
				$content = $preContentIf . $postContentIf;
			}
		}

		// Remove {if_files} & {end_if_files} from template
		$content = str_replace('{if_files}', '', $content);
		$content = str_replace('{end_if_files}', '', $content);

		return true;
	}

	/**
	 * Method to get type id by item id
	 *
	 * @param   int|array  $itemId  item id
	 *
	 * @return bool|mixed
	 */
	public static function getTypeIdByItemId($itemId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('type_id'))
			->from($db->qn('#__reditem_items'));

		if (is_array($itemId))
		{
			$query->where($db->qn('id') . ' IN (' . implode(',', $itemId) . ')');
			$db->setQuery($query);
			$result = $db->loadColumn();
		}
		else
		{
			$query->where($db->qn('id') . ' = ' . (int) $itemId);
			$db->setQuery($query, 0, 1);
			$result = $db->loadResult();
		}

		if ($result)
		{
			return $result;
		}

		return false;
	}

	/**
	 * Method to get list of item ids
	 *
	 * @param   array  $items  List of items
	 *
	 * @return  array
	 */
	public static function getItemIds($items)
	{
		$func = function($item) {
			return $item->id;
		};

		return array_map($func, $items);
	}

	/**
	 * Get item table name by item id.
	 *
	 * @param   int      $id        Item id.
	 * @param   boolean  $fullName  Return full name (ex: '#__reditem_types_TABLE_NAME') or just TABLE_NAME
	 *
	 * @return  string  Table name.
	 */
	public static function getTableName($id, $fullName = true)
	{
		if (empty(self::$tableNames[$id]))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('t.table_name'))
				->from($db->qn('#__reditem_items', 'i'))
				->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('t.id'))
				->where($db->qn('i.id') . ' = ' . (int) $id);

			if ($name = $db->setQuery($query)->loadResult())
			{
				self::$tableNames[$id] = '#__reditem_types_' . $name;
			}
			else
			{
				return false;
			}
		}

		if ($fullName)
		{
			return self::$tableNames[$id];
		}
		else
		{
			return str_replace('#__reditem_types_', '', self::$tableNames[$id]);
		}
	}

	/**
	 * Render item using it default template or one provided in function.
	 *
	 * @param   int  $itemId      Item id to render.
	 * @param   int  $templateId  Template id to use for render. If 0, item default template will be used.
	 *
	 * @return  string  Item render HTML code.
	 *
	 * @since   2.5.5
	 */
	public static function renderItem($itemId, $templateId = 0)
	{
		if (!$itemId)
		{
			return '';
		}

		$model    = RModel::getAdminInstance('Item', array('ignore_request' => true), 'com_reditem');
		$tplModel = RModel::getAdminInstance('Template', array('ignore_request' => true), 'com_reditem');
		$item     = $model->getItem($itemId);

		if (!$templateId)
		{
			if ($item)
			{
				$templateId = $item->template_id;
			}

			if (!$templateId)
			{
				return '';
			}
		}

		$template   = $tplModel->getItem($templateId);
		$categories = self::getCategories($item->id, false);
		$content    = $template->content;

		// Set item values
		$item->categories = $categories;
		$item->template   = $template;

		// Replace related items tag first
		self::replaceRelatedItems($content, $item);

		// Replace items data tag
		self::replaceTag($content, $item);

		// Replace item's custom fields data
		self::replaceCustomfieldsTag($content, $item);

		// Run dispatcher for content's plugins
		JPluginHelper::importPlugin('content');
		$content = JHtml::_('content.prepare', $content);

		if (JPluginHelper::isEnabled('system', 'twig'))
		{
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger(
				'onTwigRender',
				array (
					&$content,
					'itemdetail-' . $item->id . '.html',
					array (
						'fields'     => ReditemHelperCustomfield::processValuesForTwig($item->customfield_values),
						'page'       => $_SERVER,
						'categories' => $categories
					)
				)
			);
		}

		return $content;
	}
}
