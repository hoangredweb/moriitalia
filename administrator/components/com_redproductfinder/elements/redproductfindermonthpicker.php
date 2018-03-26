<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a Productfinder monthpicker
 *
 * @package		Joomla
 * @subpackage	Banners
 * @since		1.5
 */
class JFormFieldredproductfindermonthpicker extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$type = 'redproductfindermonthpicker';

	protected function getInput()
	{
			
		$temps = array();
		$shop_monthpicker = array("01","02","03","04","05","06","07","08","09","10","11","12");
		for($i=0;$i<count($shop_monthpicker);$i++)
		{
		
			$month_name = strtoupper(date( 'F', mktime(0, 0, 0, $shop_monthpicker[$i]) ));
			$temps[$i]->value=$shop_monthpicker[$i];
			$temps[$i]->text=JText::_($month_name);

		}

$ctrl  = $this->name ;        
   		// Construct the various argument calls that are supported.
		
		
		
return JHTML::_('select.genericlist', $temps, $ctrl, 'multiple="multiple" size="5"', 'value', 'text', $this->value, $this->id);
		//return JHTML::_('select.genericlist', $temps, ''.$this->name.'[]','multiple="multiple" size="5"', 'value', 'text', $this->value, $this->id );
	}
}
