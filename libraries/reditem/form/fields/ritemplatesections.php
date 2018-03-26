<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

require_once JPATH_ROOT . '/administrator/components/com_reditem/helpers/helper.php';

/**
 * RedITEM type select list
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RITemplateSections
 *
 * @since       2.0
 */
class JFormFieldRITemplateSections extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RITemplateSections';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  Options to populate the select field
	 */
	public function getOptions()
	{
		$options = array();
		$currentOptions = parent::getOptions();

		$sections = array(
			'view_archiveditems'      => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ARCHIVEDITEMS'),
			'view_itemdetail'         => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ITEMDETAIL'),
			'view_itemedit'           => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_ITEMEDIT'),
			'view_categorydetail'     => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_CATEGORYDETAIL'),
			'view_categorydetailgmap' => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_CATEGORYDETAIL_GMAP'),
			'view_search'             => JText::_('COM_REDITEM_TEMPLATE_TYPE_VIEW_SEARCH'),
			'module_items'            => JText::_('COM_REDITEM_TEMPLATE_TYPE_ITEMS_MODULE'),
			'module_relateditems'     => JText::_('COM_REDITEM_TEMPLATE_TYPE_RELATED_ITEMS_MODULE'),
			'module_search'           => JText::_('COM_REDITEM_TEMPLATE_TYPE_SEARCH_MODULE'),
		);

		// Sort these sections
		asort($sections);

		foreach ($sections as $section => $description)
		{
			$options[] = JHTML::_('select.option', $section, $description);
		}

		if (!empty($currentOptions))
		{
			$options = array_merge($currentOptions, $options);
		}

		return $options;
	}
}
