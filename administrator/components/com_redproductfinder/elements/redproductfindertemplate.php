<?php
 
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a Productfinder Form
 *
 * @package		Joomla
 * @subpackage	Banners
 * @since		1.5
 */
class JFormFieldredproductfindertemplate extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$type = 'redproductfindertemplate';
	
	/**
	 * Get template of redproducfinder from redshop
	 * 
	 * @return string
	 */
	protected function getInput()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$name = $this->name;
		$control_name = $this->name;
		
		// This might get a conflict with the dynamic translation - TODO: search for better solution
		$query->select("template_id, template_name");
		$query->from($db->qn("#__redshop_template"));
		$query->where("published=1");
		$query->where("template_section=" . $db->q("redproductfinder"));
		$query->order("template_id");
		
		$db->setQuery($query);
		
		$options = $db->loadObjectList();
		
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Template').' -', 'template_id', 'template_name'));
		
		return  JHTML::_('select.genericlist',  $options, $name , 'class="inputbox"', 'template_id', 'template_name', $this->value, $this->template_id );
	}
}
