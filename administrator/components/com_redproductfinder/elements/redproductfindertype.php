<?php
 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a Productfinder type
 *
 * @package		Joomla
 * @subpackage	Banners
 * @since		1.5
 */
class JFormFieldredproductfindertype extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$type = 'redproductfindertype';

	protected function getInput()
	{
		$db = &JFactory::getDBO();
		$name = $this->name;
		$control_name = $this->name;
		// This might get a conflict with the dynamic translation - TODO: search for better solution
		$query = 'SELECT id, type_name' .
				' FROM #__redproductfinder_types WHERE published=1' .
				' ORDER BY type_name';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Type').' -', 'id', 'type_name'));
		return  JHTML::_('select.genericlist',  $options, $name.'[]' , 'multiple="multiple" class="inputbox"', 'id', 'type_name', $this->value, $this->id );
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'type_name', $value, $control_name.$name );
	}
}
