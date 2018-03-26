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
 * Tags helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Helper
 * @since       2.0
 *
 */
class ReditemHelperTags
{
	/**
	 * Replace filter tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Object data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function tagReplaceFilter(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object available or not
		if (empty($content) || empty($mainCategory))
		{
			return false;
		}

		// Replace {filter_category} tag
		self::replaceFilterCategory($content, $mainCategory, $javascriptCallback);

		// Replace {filter_searchinfrontend} tag
		self::replaceFilterSearchInFrontend($content, $mainCategory, $javascriptCallback);

		// Replace {filter_title} tag
		self::replaceFilterTitle($content, $mainCategory, $javascriptCallback);

		// Replace {filter_alphabe} tag
		self::replaceFilterAlphabet($content, $mainCategory, $javascriptCallback);

		// Replace {filter_customfield} tag
		self::replaceFilterCustomfield($content, $mainCategory, $javascriptCallback);

		// Replace {filter_relatedcategory} tag
		self::replaceFilterRelatedCategory($content, $mainCategory, $javascriptCallback);

		// Replace {filter_ranges} tag
		self::replaceFilterRanges($content, $mainCategory, $javascriptCallback);

		// Replace {filter_distance} tag
		self::replaceFilterDistance($content, $mainCategory, $javascriptCallback);

		return true;
	}

	/**
	 * Replace {filter_category} filter tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterCategory(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}

		/*
		 * {filter_category|<categoryID>|<generatedFilterHTML>|<featuredCategory>}
		 *
		 * <categoryID>				int 	ID of category 					Default: If not set, it's will get current category ID
		 * <generatedFilterHTML> 	string 	Type of generated filter 		Default: select [select, radio, checkbox, list]
		 * <featuredCategory>		int 	Show only featured category 	Default: 0 [0, 1]
		 */
		if (preg_match_all('/{filter_category[^}]*}/i', $content, $matches) > 0)
		{
			$matches              = $matches[0];
			$app                  = JFactory::getApplication();
			$filters              = $app->input->get('filter_category', array(), 'array');
			$value                = '';
			$availableDisplayType = array('radio', 'checkbox', 'list');
			$categoryModel        = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');

			foreach ($matches as $match)
			{
				$categoryId          = 0;
				$featuredCategory    = 0;
				$generatedFilterType = 'select';
				$params              = explode('|', $match);
				$params              = str_replace('{', '', $params);
				$params              = str_replace('}', '', $params);
				$layoutFile          = 'filters.filter_category';

				// Get param categoryID
				if (isset($params[1]))
				{
					$categoryId = (int) $params[1];
				}

				if ($categoryId)
				{
					$category = $categoryModel->getItem($categoryId);
				}
				else
				{
					$category = $mainCategory;
				}

				// Get param generatedFilterHTML
				if (isset($params[2]))
				{
					if (in_array($params[2], $availableDisplayType))
					{
						$generatedFilterType = $params[2];
					}
				}

				// Get param featuredCategory
				if (isset($params[3]))
				{
					$featuredCategory = (int) $params[3];
				}

				// Get sub-Categories deeper
				$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');
				$categoriesModel->setState('filter.published', 1);
				$categoriesModel->setState('filter.lft', $category->lft + 1);
				$categoriesModel->setState('filter.rgt', $category->rgt - 1);
				$categoriesModel->setState('list.select', 'DISTINCT (c.id), c.title, c.parent_id, c.level, c.type_id, c.category_image');
				$categoriesModel->setState('list.ordering', 'c.lft');
				$categoriesModel->setState('list.direction', 'asc');

				if ($featuredCategory == 1)
				{
					$categoriesModel->setState('filter.featured', 1);
				}

				$subCategories = $categoriesModel->getItems();

				// Process check view permission for sub-categories list.
				ReditemHelperACL::processCategoryACL($subCategories);

				// Clean up memory
				unset($categoriesModel);

				// Get value of filter
				if (!empty($filters))
				{
					if (isset($filters[$category->id]))
					{
						$value = $filters[$category->id];
					}
				}

				if (in_array($generatedFilterType, $availableDisplayType))
				{
					$layoutFile .= '_' . $generatedFilterType;
				}

				$layoutData = array(
					'category'      => $category,
					'subCategories' => $subCategories,
					'value'         => $value,
					'jsCallback'    => $javascriptCallback
				);

				$html = ReditemHelperLayout::render(null, $layoutFile, $layoutData, array('component' => 'com_reditem'));
				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Replace {filter_title} filter tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterTitle(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}

		/*
		 * {filter_title | <autocomplete> | <hint> }
		 *
		 * <autocomplete>  int     Autocomplete option     [1]: enable. [other] disable   Default: 0
		 * <hint>          string  Hints for input field
		 */
		if (preg_match_all('/{filter_title[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			$filterValue = JFactory::getApplication()->input->getRaw('filter_title', null);

			foreach ($matches as $match)
			{
				// Default config for filter
				$filterConfig = array('autocomplete' => false, 'hint' => '');

				// Get config from template
				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);
				$filterParams = explode('|', $tmpMatch);

				// If autocomplete has been set
				if (isset($filterParams[1]))
				{
					$filterConfig['autocomplete'] = (boolean) $filterParams[1];
				}

				// If Hint has been set
				if (isset($filterParams[2]))
				{
					$filterConfig['hint'] = $filterParams[2];
				}

				// Prepare data for layout
				$layoutData = array(
					'config'             => $filterConfig,
					'javascriptCallback' => $javascriptCallback,
					'value'              => $filterValue
				);

				$html = ReditemHelperLayout::render(null, 'filters.filter_title', $layoutData, array('component' => 'com_reditem'));
				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Replace {filter_searchInFrontend} filter tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterSearchInFrontend(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}
		/*
		 * {filter_searchinfrontend | <hint> }
		 *
		 * <hint>          string  Hints for input field
		 */
		if (preg_match_all('/{filter_searchinfrontend[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];
			$filterValue = JFactory::getApplication()->input->getRaw('filter_searchinfrontend', null);

			foreach ($matches as $match)
			{
				// Default config for filter
				$filterConfig = array('autocomplete' => false, 'hint' => '');

				// Get config from template
				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);
				$filterParams = explode('|', $tmpMatch);

				// If Hint has been set
				if (isset($filterParams[1]))
				{
					$filterConfig['hint'] = $filterParams[1];
				}
				// Prepare data for layout
				$layoutData = array(
						'config'             => $filterConfig,
						'javascriptCallback' => $javascriptCallback,
						'value'              => $filterValue
				);

				$html = ReditemHelperLayout::render(null, 'filters.filter_searchinfrontend', $layoutData, array('component' => 'com_reditem'));
				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Replace {filter_customfield} tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterCustomfield(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}

		/*
		 * filter_customfield | <customfieldId> | <generatedFilterType>
		 *
		 * <customfieldId>  int           ID of customfield
		 * <generatedFilterType>  string  Generated type for filter  [text, select, radio, checkbox, list]  Default: select
		 */
		if (preg_match_all('/{filter_customfield[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			$app = JFactory::getApplication();
			$db  = JFactory::getDbo();

			// Get filter value
			$filter = $app->input->get('filter_customfield', array(), 'array');

			// Allowed type of customfield can be use to generate filter tool.
			$allowedFieldType = array('text', 'textarea', 'editor', 'select', 'radio', 'checkbox');

			// Get object of field
			$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');

			foreach ($matches as $match)
			{
				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);

				$params = explode('|', $tmpMatch);
				$value  = '';
				$customfieldId = 0;
				$generatedFilterType = 'select';

				// Param: Customfield Id
				if (isset($params[1]))
				{
					$customfieldId = (int) $params[1];
				}

				// Check if customfield Id is not exist
				if (!$customfieldId)
				{
					// Skip this tag, go to next one
					$content = str_replace($match, '', $content);
					continue;
				}

				// Get field object base on customfield Id input
				$field = $fieldModel->getItem($customfieldId);

				// Check if field exist or not
				if (!$field)
				{
					// Skip this tag, go to next one
					$content = str_replace($match, '', $content);
					continue;
				}

				// Check this type in allowed type
				if (!in_array($field->type, $allowedFieldType))
				{
					// Skip this tag, go to next one
					$content = str_replace($match, '', $content);
					continue;
				}

				// Param: Get genereted filter type
				if (isset($params[2]))
				{
					$generatedFilterType = $params[2];
				}

				$columnName  = $field->fieldcode;

				// Get the filter value
				if (!empty($filter) && isset($filter[$columnName]))
				{
					$value = $filter[$columnName];
				}

				// Prepare table name for type data
				$tableName = ReditemHelperType::getTableName($field->type_id);

				// If generated just a input text, simple generated here.
				if ($generatedFilterType == 'text')
				{
					$layoutData = array(
						'field'      => $field,
						'value'      => $value,
						'category'   => $mainCategory,
						'jsCallback' => $javascriptCallback
					);

					$html = ReditemHelperLayout::render(
						ReditemHelperType::getType($field->type_id),
						'filters.filter_customfield',
						$layoutData,
						array('component' => 'com_reditem')
					);
				}
				else
				{
					$options = array();

					// Create the list option
					if (($field->type == 'text') || ($field->type == 'textarea') || ($field->type == 'editor'))
					{
						// If custom field is a textfield, textarea, editor. Values list will be the data which has entered already
						$query = $db->getQuery(true)
							->select('DISTINCT (' . $db->qn($columnName) . ')')
							->from($db->qn($tableName))
							->where($db->qn($columnName) . ' <> ' . $db->quote(''))
							->order($db->qn($columnName));
						$db->setQuery($query);
						$lists = $db->loadColumn();
					}
					else
					{
						// If custom field is other (radio, checkbox, select). Values list will be the option list of custom fields.
						$lists = explode("\n", $field->options);

						$fieldParams = new JRegistry($field->params);

						if ($fieldParams->get('sort_options', 1))
						{
							// Sort this array (A-Z)
							asort($lists);
						}
					}

					if (!empty($mainCategory->all_items))
					{
						$fieldCode = $field->fieldcode;
						$active    = array();

						foreach ($mainCategory->all_items as $item)
						{
							if ($vals = ReditemHelperCustomfield::isJsonValue($item->customfield_values[$fieldCode]))
							{
								foreach ($vals as $val)
								{
									$active[] = $val;
								}
							}
							else
							{
								$active[] = $item->customfield_values[$fieldCode];
							}
						}
					}

					if (!empty($lists))
					{
						// Prepare option value
						foreach ($lists as $option)
						{
							if (is_string($option) && !empty($option))
							{
								$tmpOption = new stdClass;
								$option    = explode('|', trim($option));

								if (!empty($active) && !in_array($option[0], $active))
								{
									continue;
								}

								$tmpOption->value = $option[0];
								$tmpOption->text  = $tmpOption->value;

								if (isset($option[1]))
								{
									$tmpOption->text = $option[1];
								}

								// Use base64 encode for value of option to avoid error in multiple browser
								$tmpOption->value = base64_encode($tmpOption->value);
								$options[]        = $tmpOption;
							}
						}
					}

					$layoutData = array(
						'category'   => $mainCategory,
						'field'      => $field,
						'value'      => $value,
						'options'    => $options,
						'jsCallback' => $javascriptCallback
					);

					$layoutFile = 'filters.filter_customfield_' . $generatedFilterType;
					$html = ReditemHelperLayout::render(ReditemHelperType::getType($field->type_id), $layoutFile, $layoutData, array('component' => 'com_reditem'));
				}

				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Replace {filter_relatedcategory} tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterRelatedCategory(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}

		/*
		 * filter_relatedcategory | <related_category_id>
		 *
		 * <related_category_id>  int  ID of category   Default: If id not set, get current category
		 */
		if (preg_match_all('/{filter_relatedcategory[^}]*}/i', $content, $matches) > 0)
		{
			$matches = $matches[0];

			$db = JFactory::getDbo();
			$categoryModel   = RModel::getAdminInstance('Category', array('ignore_request' => true), 'com_reditem');
			$categoriesModel = RModel::getAdminInstance('Categories', array('ignore_request' => true), 'com_reditem');

			foreach ($matches as $match)
			{
				$category   = null;
				$categoryId = 0;
				$html       = '';

				// Get params of tag
				$params = explode('|', $match);

				// Get category ID from params
				if (isset($params[1]))
				{
					$categoryId = (int) $params[1];
				}

				// Check if category ID has been set or not
				if ($categoryId)
				{
					$category = $categoryModel->getItem($categoryId);
				}
				else
				{
					$category = $mainCategory;
				}

				// Get sub-Categories deeper
				$categoriesModel->setState('filter.published', 1);
				$categoriesModel->setState('filter.lft', $category->lft);
				$categoriesModel->setState('filter.rgt', $category->rgt);
				$categoriesModel->setState('list.select', 'DISTINCT (c.id), c.title, c.parent_id, c.level');
				$categoriesModel->setState('list.ordering', 'c.title');
				$categoriesModel->setState('list.direction', 'asc');
				$query = $categoriesModel->getListQuery();
				$db->setQuery($query);

				$subCategories = $db->loadObjectList();

				if ($subCategories)
				{
					$options  = array();
					$children = array();
					$value    = '';

					// Create tree list
					foreach ($subCategories as $subCategory)
					{
						$pt = $subCategory->parent_id;
						$list = (isset($children[$pt])) ? $children[$pt] : array();
						array_push($list, $subCategory);
						$children[$pt] = $list;
					}

					$treeCategories = JHTML::_('menu.treerecurse', $category->id, ' ', array(), $children, 9999, 0, 0);

					// Add tree list to options list
					foreach ($treeCategories as $node)
					{
						$options[] = array('text' => $node->treename, 'value' => $node->id);
					}

					// Clean up memory
					unset($treeCategories);
					unset($children);

					// Get value for filter
					$filters = JFactory::getApplication()->input->get('filter_category', array(), 'array');

					if (!empty($filters))
					{
						if (isset($filters[$category->id]))
						{
							$value = $filters[$category->id];
						}
					}

					$layoutData = array(
						'category'   => $category,
						'options'    => $options,
						'value'      => $value,
						'jsCallback' => $javascriptCallback
					);

					$html = ReditemHelperLayout::render(
						null,
						'filters.filter_relatedcategory',
						$layoutData,
						array('component' => 'com_reditem')
					);
				}

				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Replace {filter_ranges} tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $mainCategory        Current category object
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterRanges(&$content, $mainCategory, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$mainCategory)
		{
			return false;
		}

		/*
		 * filter_ranges | <custom_field_id> | <number_of_range>;<minVal>:<maxVal>;<name_of_range> | <default_select>
		 *
		 * <custom_field_id> 	int 	Id of customfield
		 * <number_of_range> 	int 	Number of range (options)	Default: 4
		 * <minVal>				int 	Min value of range			Default: Get lowest value from custom field data
		 * <maxVal>				int 	Max value of range			Default: Get highest value from custom field data
		 * <name_of_range>		string 	Array of text 				Default: Generate by min-max value
		 *
		 * <default_select>		string  Default selected option 	Default: null
		 */
		if (preg_match_all('/{filter_ranges[^}]*}/i', $content, $matches) > 0)
		{
			$fieldModel = RModel::getAdminInstance('Field', array('ignore_request' => true), 'com_reditem');
			$matches    = $matches[0];
			$db         = JFactory::getDBO();

			foreach ($matches as $match)
			{
				$value         = '';
				$customFieldId = 0;
				$options       = array();
				$rangeCount    = 4;
				$minValue      = false;
				$maxValue      = false;
				$rangeText     = array();
				$defaultSelect = '';

				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);

				// Get params of tag
				$params = explode('|', $tmpMatch);

				// Param: Customfield Id
				if (isset($params[1]))
				{
					$customFieldId = (int) $params[1];
				}

				// Check if customfield Id is available
				if (!$customFieldId)
				{
					$content = str_replace($match, '', $content);

					// Skip this tag
					continue;
				}

				$field = $fieldModel->getItem($customFieldId);
				$type  = ReditemHelperType::getType($field->type_id);

				// Check if field exist
				if (!$field)
				{
					$content = str_replace($match, '', $content);

					// Skip this tag
					continue;
				}

				// Get params for generate options
				if (isset($params[2]))
				{
					$optionParams = explode(';', $params[2]);

					// Number of ranges
					$rangeCount = (int) $optionParams[0];

					// MinValue & MaxValue
					if (isset($optionParams[1]))
					{
						$minMaxParam = explode(':', $optionParams[1]);

						// Get minValue if set
						if ($minMaxParam[0] != '')
						{
							$minValue = (int) $minMaxParam[0];
						}

						// Get maxValue if set
						if (isset($minMaxParam[1]))
						{
							$maxValue = (int) $minMaxParam[1];
						}
					}

					// Text of options
					$rangeText = (isset($optionParams[2])) ? explode(',', $optionParams[2]) : array();
				}

				// If Min or Max value has not been set
				if (($minValue === false) || ($maxValue === false))
				{
					$tableName = '#__reditem_types_' . $type->table_name;

					if ($minValue == false)
					{
						// Get Min value from custom field datas
						$query = $db->getQuery(true)
							->select('MIN(CAST(' . $db->qn($field->fieldcode) . ' AS UNSIGNED))')
							->from($db->qn($tableName))
							->where($db->qn($field->fieldcode) . ' <> ' . $db->quote(''));
						$db->setQuery($query, 0, 1);
						$minValue = $db->loadResult();
					}

					if ($maxValue == false)
					{
						// Get Max value from custom field datas
						$query = $db->getQuery(true)
							->select('MAX(CAST(' . $db->qn($field->fieldcode) . ' AS UNSIGNED))')
							->from($db->qn($tableName))
							->where($db->qn($field->fieldcode) . ' <> ' . $db->quote(''));
						$db->setQuery($query, 0, 1);
						$maxValue = $db->loadResult();
					}
				}

				$minValue = (int) $minValue;
				$maxValue = (int) $maxValue;

				// Get default select option
				if (isset($params[3]))
				{
					$defaultSelect = $params[3];
				}

				// Generate the range list
				$range = ceil(($maxValue - $minValue) / $rangeCount);
				$optionProcess = true;
				$optionProcessIndex = 0;

				while ($optionProcess)
				{
					$currentRangeMin = ($optionProcessIndex * $range) + $minValue;
					$currentRangeMax = $currentRangeMin + $range;

					// Check if current max number is higher maxValue
					if ($currentRangeMax >= $maxValue)
					{
						$currentRangeMax = $maxValue;

						// Set condition for exit while loop
						$optionProcess = false;
					}

					if ($currentRangeMin != $minValue)
					{
						$currentRangeMin++;
					}

					$optionValue = $currentRangeMin . '-' . $currentRangeMax;

					// Option text
					$optionText = $currentRangeMin . ' - ' . $currentRangeMax;

					if (isset($rangeText[$optionProcessIndex]))
					{
						$optionText = $rangeText[$optionProcessIndex];
					}

					// Default select process
					if ($defaultSelect == $optionText)
					{
						$value = $optionValue;
					}

					$options[] = array('text' => $optionText, 'value' => $optionValue);
					$optionProcessIndex++;
				}

				// Check if top option of select has been set
				$topOption = JText::_('JALL') . ' ' . $field->name;

				if (isset($rangeText[$rangeCount]))
				{
					$topOption = $rangeText[$rangeCount];
				}

				$layoutData = array(
					'field'      => $field,
					'options'    => $options,
					'value'      => $value,
					'topOption'  => $topOption,
					'jsCallback' => $javascriptCallback
				);
				$layoutOption = array('component' => 'com_reditem');
				$html = ReditemHelperLayout::render($type, 'filters.filter_ranges', $layoutData, $layoutOption);
				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}

	/**
	 * Method for replace {filter_distance} tag
	 *
	 * @param   string  &$content            Template content
	 * @param   object  $category            Category object
	 * @param   string  $javascriptCallback  JavaScript call back function
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterDistance(&$content, $category, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if template content or category object not available
		if (empty($content) || empty($category) || (strpos($content, '{filter_distance}') === false))
		{
			return false;
		}

		$layoutData = array(
			'category'           => $category,
			'javascriptCallback' => $javascriptCallback
		);

		$html = ReditemHelperLayout::render(null, 'filters.filter_distance', $layoutData, array('component' => 'com_reditem'));
		$content = str_replace('{filter_distance}', $html, $content);

		return true;
	}

	/**
	 * Replace {items_sort_tool} tag on content of template
	 *
	 * @param   string  &$content            Content template
	 * @param   object  $category            Array data of current category
	 * @param   string  $javascriptCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceSortTool(&$content, $category, $javascriptCallback = 'reditemFilterAjax')
	{
		// Check if template content is empty or category object is not available or {items_sort_tool} has exist in template
		if (empty($content) || empty($category) || (strpos($content, '{items_sort_tool}') === false))
		{
			return false;
		}

		$input              = JFactory::getApplication()->input;
		$itemsSort          = strtolower($input->getString('items_sort', 'title'));
		$itemsDest          = strtolower($input->getString('items_dest', 'asc'));
		$itemOrderAvailable = array('title', 'ordering', 'created_time', 'publish_up', 'publish_down');

		// If items sort column name not match in available list, set to default "title"
		if (!in_array($itemsSort, $itemOrderAvailable))
		{
			$itemsSort = 'title';
		}

		// If items sort direction not match in available list, set to default "asc"
		if (($itemsDest != 'asc') || ($itemsDest != 'desc'))
		{
			$itemsDest = 'asc';
		}

		// Create list of options
		$itemsSortList = array(
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_BY_TITLE'), 'value' => 'title'),
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_BY_ORDERING'), 'value' => 'ordering'),
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_BY_CREATED_TIME'), 'value' => 'created_time'),
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_BY_PUBLISH_UP'), 'value' => 'publish_up'),
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_BY_PUBLISH_DOWN'), 'value' => 'publish_down')
		);

		$itemsDestList = array(
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_DIRECTION_ASCENDING'), 'value' => 'asc'),
			array('text' => JText::_('COM_REDITEM_ITEMS_SORT_TOOL_SORT_DIRECTION_DESCENDING'), 'value' => 'desc')
		);

		$layoutData = array(
			'category'      => $category,
			'jsCallback'    => $javascriptCallback,
			'itemsSort'     => $itemsSort,
			'itemsDest'     => $itemsDest,
			'itemsSortList' => $itemsSortList,
			'itemsDestList' => $itemsDestList
		);

		$html    = ReditemHelperLayout::render(null, 'items_sort', $layoutData, array('component' => 'com_reditem'));
		$content = str_replace('{items_sort_tool}', $html, $content);

		return true;
	}

	/**
	 * Replace {filter_alphabet} filter tag on content of template
	 *
	 * @param   string  &$content    Content template
	 * @param   object  $category    Array data of current category
	 * @param   string  $jsCallback  Javascript function callback for filter.
	 *
	 * @return  boolean                      True on success. False otherwise.
	 */
	public static function replaceFilterAlphabet(&$content, $category, $jsCallback = 'reditemFilterAjax')
	{
		// Check if category object is available
		if (empty($content) || !$category)
		{
			return false;
		}

		/*
		 * { filter_alphabet | <displayChars> | <displayNumber> }
		 *
		 * <displayChars>   int     Show all characters option.     [0]: Show all characters. (Default)
                                                                    [1] Just show available characters.
                                                                    [2] Show all characters, but disable link of not available chars.
		 * <displayNumber>  int     Display number char option.     [0] Do not display number char (Default).
		                                                            [1] Display number char.
		                                                            [2] Just display number if available.
		 *
		 */
		if (preg_match_all('/{filter_alphabet[^}]*}/i', $content, $matches) > 0)
		{
			$matches      = $matches[0];
			$filterValue  = JFactory::getApplication()->input->getRaw('filter_alphabet', null);
			$normalRanges = range('A', 'Z');

			// Add DK characters
			if (JFactory::getLanguage()->getTag() == 'da-DK')
			{
				$normalRanges = array_merge($normalRanges, array('Æ', 'Ø', 'Å'));
			}

			// Prepare first available characters of items.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('UPPER(LEFT(' . $db->qn('i.title') . ', 1)) AS ' . $db->qn('alphabet'))
				->from($db->qn('#__reditem_items', 'i'))
				->join('LEFT', $db->qn('#__reditem_item_category_xref', 'c') . ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('c.item_id'))
				->where($db->qn('c.category_id') . ' = ' . (int) $category->id)
				->where($db->qn('i.published') . ' = 1')
				->where($db->qn('i.blocked') . ' = 0')
				->group($db->qn('alphabet'));
			$db->setQuery($query);
			$availableCharacters = $db->loadColumn();

			foreach ($matches as $match)
			{
				// Default config and filter range
				$filterConfig = array('showAvailableChars' => 0, 'showNumber' => 0);
				$ranges       = array();

				// Get config from template
				$tmpMatch = str_replace('{', '', $match);
				$tmpMatch = str_replace('}', '', $tmpMatch);
				$filterParams = explode('|', $tmpMatch);

				// If showAll has been set
				if (isset($filterParams[1]))
				{
					$filterConfig['showAvailableChars'] = (int) $filterParams[1];
				}

				switch ($filterConfig['showAvailableChars'])
				{
					// Just show available alphabet.
					case 1:
						foreach (array_values(array_intersect($normalRanges, $availableCharacters)) as $char)
						{
							$availableChar          = new stdClass;
							$availableChar->char    = $char;
							$availableChar->hasItem = true;

							$ranges[] = $availableChar;
						}
						break;

					// Show all chars (disable link for not available).
					case 2:
						foreach ($normalRanges as $normalChar)
						{
							$availableChar          = new stdClass;
							$availableChar->char    = $normalChar;
							$availableChar->hasItem = false;

							if (in_array($normalChar, $availableCharacters))
							{
								$availableChar->hasItem = true;
							}

							$ranges[] = $availableChar;
						}
						break;

					// Show all characters
					default:
						foreach ($normalRanges as $normalChar)
						{
							$availableChar          = new stdClass;
							$availableChar->char    = $normalChar;
							$availableChar->hasItem = true;

							$ranges[] = $availableChar;
						}
						break;
				}

				// If showNumber has been set
				if (isset($filterParams[2]))
				{
					$filterConfig['showNumber'] = (int) $filterParams[2];
				}

				$intersect = array_intersect(range(0, 9), $availableCharacters);

				// If include number or show available number
				if (($filterConfig['showNumber'] == 1)
					|| ($filterConfig['showNumber'] == 2 && !empty($intersect)))
				{
					$numberChar = new stdClass;
					$numberChar->char = '#';
					$numberChar->hasItem = true;
					array_unshift($ranges, $numberChar);
				}
				elseif ($filterConfig['showNumber'] == 2 && empty($intersect))
				{
					$numberChar = new stdClass;
					$numberChar->char = '#';
					$numberChar->hasItem = false;
					array_unshift($ranges, $numberChar);
				}

				// Prepare data for layout
				$layoutData = array(
					'config'     => $filterConfig,
					'jsCallback' => $jsCallback,
					'ranges'     => $ranges,
					'category'   => $category,
					'value'      => $filterValue
				);

				$html    = ReditemHelperLayout::render(null, 'filters.filter_alphabet', $layoutData, array('component' => 'com_reditem'));
				$content = str_replace($match, $html, $content);
			}

			return true;
		}

		return false;
	}
}
