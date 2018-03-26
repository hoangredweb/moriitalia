<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');


$config = Redconfiguration::getInstance();
$producthelper = producthelper::getInstance();
$redhelper = redhelper::getInstance();

$url = JURI::base();
$Itemid = JRequest::getInt('Itemid');
$weddingList = $this->weddingList;
$product_id = JRequest::getInt('product_id');
$user = JFactory::getUser();

$pagetitle = JText::_('COM_REDSHOP_MY_WISHLIST');

$redTemplate = Redtemplate::getInstance();
$extraField = extraField::getInstance();
$template = $redTemplate->getTemplate("wishlist_template");
$wishlist_data1 = $template[0]->template_desc;
$returnArr = $producthelper->getProductUserfieldFromTemplate($wishlist_data1);
$template_userfield = $returnArr[0];
$userfieldArr = $returnArr[1];

	if ($this->params->get('show_page_heading', 1))
	{
		?>
		<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"><?php echo $pagetitle; ?></h1>
		<div>&nbsp;</div>
	<?php
	}
	echo "<div class='mod_redshop_wishlist'>";
		if (count($weddingList) <= 0)
		{
			echo "<div>" . JText::_('COM_REDSHOP_NO_PRODUCTS_IN_WISHLIST') . "</div>";
		}
		else
		{
			echo "<div>" . display_products($weddingList) . "</div>";
		}
	echo "</div>";

