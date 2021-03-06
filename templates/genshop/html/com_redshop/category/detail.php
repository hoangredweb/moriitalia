<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2017 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JHtml::_('behavior.modal');

JHtml::stylesheet(Juri::base() . 'templates/genshop/css/swiper.css', false, true);
JHtml::script(Juri::base() . 'templates/genshop/js/swiper.min.js', false, true);

$url = JURI::base();

$objhelper = redhelper::getInstance();

$Redconfiguration = Redconfiguration::getInstance();
$producthelper = productHelper::getInstance();
$extraField = extraField::getInstance();
$stockroomhelper = rsstockroomhelper::getInstance();
$redTemplate = Redtemplate::getInstance();
$texts = new text_library;

$start = $this->input->getInt('limitstart', 0);

$slide = $this->input->getInt('ajaxslide', null);
$filter_by = $this->input->getInt('manufacturer_id', $this->params->get('manufacturer_id'));
$category_template = $this->input->getInt('category_template', 0);

/** @var RedshopModelCategory $model */
$model = $this->getModel('category');
$minmax = $model->getMaxMinProductPrice();
$texpricemin = $minmax[0];
$texpricemax = $minmax[1];

$loadCategorytemplate = $this->loadCategorytemplate;
$fieldArray = $extraField->getSectionFieldList(17, 0, 0);

if (count($loadCategorytemplate) > 0 && $loadCategorytemplate[0]->template_desc != "")
{
	$template_desc = $loadCategorytemplate[0]->template_desc;
}
else
{
	$template_desc  = "<div class=\"category_print\">{print}</div>\r\n<div style=\"clear: both;\"></div>\r\n";
	$template_desc .= "<div class=\"category_main_description\">{category_main_description}</div>\r\n";
	$template_desc .= "<p>{if subcats} {category_loop_start}</p>\r\n<div id=\"categories\">\r\n";
	$template_desc .= "<div style=\"float: left; width: 200px;\">\r\n<div class=\"category_image\">{category_thumb_image}</div>\r\n";
	$template_desc .= "<div class=\"category_description\">\r\n<h2 class=\"category_title\">{category_name}</h2>\r\n";
	$template_desc .= "{category_description}</div>\r\n</div>\r\n</div>\r\n<p>{category_loop_end} {subcats end if}</p>\r\n";
	$template_desc .= "<div style=\"clear: both;\"></div>\r\n<div id=\"category_header\">\r\n<div class=\"category_order_by\">";
	$template_desc .= "{order_by}</div>\r\n</div>\r\n<div class=\"category_box_wrapper\">{product_loop_start}\r\n";
	$template_desc .= "<div class=\"category_box_outside\">\r\n<div class=\"category_box_inside\">\r\n<div class=\"category_product_image\">";
	$template_desc .= "{product_thumb_image}</div>\r\n<div class=\"category_product_title\">\r\n<h3>{product_name}</h3>\r\n</div>\r\n";
	$template_desc .= "<div class=\"category_product_price\">{product_price}</div>\r\n<div class=\"category_product_readmore\">{read_more}</div>\r\n";
	$template_desc .= "<div>{product_rating_summary}</div>\r\n<div class=\"category_product_addtocart\">{form_addtocart:add_to_cart1}";
	$template_desc .= "</div>\r\n</div>\r\n</div>\r\n{product_loop_end}\r\n<div class=\"category_product_bottom\" style=\"clear: both;\"></div>\r\n";
	$template_desc .= "</div>\r\n<div class=\"pagination\">{pagination}</div>";
}

$categoryItemId = (int) RedshopHelperRouter::getCategoryItemid($this->catid);
$mainItemid = !$categoryItemId ? $this->itemid : $categoryItemId;

// New tags replacement for category template section
$template_desc = RedshopTagsReplacer::_('category', $template_desc, array('category' => $this->maincat, 'subCategories' => $this->detail, 'manufacturerId' => $this->manufacturer_id, 'itemId' => $mainItemid));

$endlimit = $this->state->get('list.limit');

$app = JFactory::getApplication();
$router = $app->getRouter();

$document = JFactory::getDocument();

$document->addScript('plugins/system/redproductimagedetail/js/redlightbox.js');

$model = $this->getModel('category');

// Replace redproductfilder filter tag
if (strpos($template_desc, "{redproductfinderfilter:") !== false)
{
	if (file_exists(JPATH_SITE . '/components/com_redproductfinder/helpers/redproductfinder_helper.php'))
	{
		include_once JPATH_SITE . "/components/com_redproductfinder/helpers/redproductfinder_helper.php";
		$redproductfinder_helper = new redproductfinder_helper;
		$hdnFields               = array(
											'texpricemin' => '0',
											'texpricemax' => '0',
											'manufacturer_id' => $filter_by,
											'category_template' => $category_template
										);
		$hide_filter_flag        = false;

		if ($this->catid)
		{
			$prodctofcat = $producthelper->getProductCategory($this->catid);

			if (empty($prodctofcat))
			{
				$hide_filter_flag = true;
			}
		}

		$template_desc = $redproductfinder_helper->replaceProductfinder_tag($template_desc, $hdnFields, $hide_filter_flag);
	}
}

// Replace redproductfilder filter tag end here
if (!$slide)
{
	echo '<div class="category">';

	if ($this->params->get('show_page_heading', 0))
	{
		?>
		<div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
			<?php
			if ($this->maincat->pageheading != "")
			{
				echo $this->escape($this->maincat->pageheading);
			}
			else
			{
				echo $this->escape($this->pageheadingtag);
			}
			?>
		</div>
	<?php
	}

	echo "</div>";

	if ($this->print)
	{
		$onclick       = "onclick='window.print();'";
		$template_desc = str_replace("{product_price_slider}", "", $template_desc);
		$template_desc = str_replace("{pagination}", "", $template_desc);
	}
	else
	{
		$print_url  = $url . "index.php?option=com_redshop&view=category&layout=detail&cid=" . $this->catid;
		$print_url .= "&print=1&tmpl=component&Itemid=" . $this->itemid;
		$print_url .= "&limit=" . $endlimit . "&texpricemin=" . $texpricemin . "&texpricemax=" . $texpricemax . "&order_by=" . $this->order_by_select;
		$print_url .= "&manufacturer_id=" . $this->manufacturer_id . "&category_template=" . $this->category_template_id;

		$onclick = "onclick='window.open(\"$print_url\",\"mywindow\",\"scrollbars=1\",\"location=1\")'";
	}

	$print_tag = "<a " . $onclick . " title='" . JText::_('COM_REDSHOP_PRINT_LBL') . "'>";
	$print_tag .= "<img src='" . JSYSTEM_IMAGES_PATH . "printButton.png' alt='" .
					JText::_('COM_REDSHOP_PRINT_LBL') . "' title='" . JText::_('COM_REDSHOP_PRINT_LBL') . "' />";
	$print_tag .= "</a>";

	$template_desc = str_replace("{print}", $print_tag, $template_desc);
	$template_desc = str_replace("{total_product}", JText::sprintf('COM_REDSHOP_TOTAL_PRODUCT_COUNT', $model->_total), $template_desc);
	$template_desc = str_replace("{total_product_lbl}", JText::_('COM_REDSHOP_TOTAL_PRODUCT'), $template_desc);

	if (strpos($template_desc, '{returntocategory_link}') !== false || strpos($template_desc, '{returntocategory_name}') !== false || strpos($template_desc, '{returntocategory}') !== false)
	{
		$parentid              = $producthelper->getParentCategory($this->catid);
		$returncatlink         = '';
		$returntocategory      = '';
		$returntocategory_name = '';

		if ($parentid != 0)
		{
			$categorylist     = RedshopEntityCategory::getInstance($parentid)->getItem();
			$returntocategory_name = $categorylist->name;
			$returncatlink    = JRoute::_(
											"index.php?option=" . $this->option .
											"&view=category&cid=" . $parentid .
											'&manufacturer_id=' . $this->manufacturer_id .
											"&Itemid=" . $this->itemid
										);
			$returntocategory = '<a href="' . $returncatlink . '">' . Redshop::getConfig()->get('DAFULT_RETURN_TO_CATEGORY_PREFIX') . '&nbsp;' . $categorylist->name . '</a>';
		}
		else if (Redshop::getConfig()->get('DAFULT_RETURN_TO_CATEGORY_PREFIX'))
		{
			$returntocategory_name = Redshop::getConfig()->get('DAFULT_RETURN_TO_CATEGORY_PREFIX');
			$returncatlink               = JRoute::_(
												"index.php?option=" . $this->option .
												"&view=category&manufacturer_id=" . $this->manufacturer_id .
												"&Itemid=" . $this->itemid
											);
			$returntocategory            = '<a href="' . $returncatlink . '">' . Redshop::getConfig()->get('DAFULT_RETURN_TO_CATEGORY_PREFIX') . '</a>';
		}

		$template_desc = str_replace("{returntocategory_link}", $returncatlink, $template_desc);
		$template_desc = str_replace('{returntocategory_name}', $returntocategory_name, $template_desc);
		$template_desc = str_replace("{returntocategory}", $returntocategory, $template_desc);
	}

	if (strpos($template_desc, '{category_main_description}') !== false)
	{
		$main_cat_desc = $Redconfiguration->maxchar($this->maincat->description, Redshop::getConfig()->get('CATEGORY_SHORT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_SHORT_DESC_END_SUFFIX'));
		$template_desc = str_replace("{category_main_description}", $main_cat_desc, $template_desc);
	}

	if (strpos($template_desc, '{category_main_short_desc}') !== false)
	{
		$main_cat_s_desc = $Redconfiguration->maxchar(
														$this->maincat->short_description,
														Redshop::getConfig()->get('CATEGORY_SHORT_DESC_MAX_CHARS'),
														Redshop::getConfig()->get('CATEGORY_SHORT_DESC_END_SUFFIX')
													);
		$template_desc   = str_replace("{category_main_short_desc}", $main_cat_s_desc, $template_desc);
	}

	if (strpos($template_desc, '{shopname}') !== false)
	{
		$template_desc = str_replace("{shopname}", Redshop::getConfig()->get('SHOP_NAME'), $template_desc);
	}

	$main_cat_name = $Redconfiguration->maxchar($this->maincat->name, Redshop::getConfig()->get('CATEGORY_TITLE_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_TITLE_END_SUFFIX'));
	$template_desc = str_replace("{category_main_name}", $main_cat_name, $template_desc);

	if (strpos($template_desc, '{category_main_thumb_image_2}') !== false)
	{
		$ctag     = '{category_main_thumb_image_2}';
		$ch_thumb = Redshop::getConfig()->get('THUMB_HEIGHT_2');
		$cw_thumb = Redshop::getConfig()->get('THUMB_WIDTH_2');
	}
	elseif (strpos($template_desc, '{category_main_thumb_image_3}') !== false)
	{
		$ctag     = '{category_main_thumb_image_3}';
		$ch_thumb = Redshop::getConfig()->get('THUMB_HEIGHT_3');
		$cw_thumb = Redshop::getConfig()->get('THUMB_WIDTH_3');
	}
	elseif (strpos($template_desc, '{category_main_thumb_image_1}') !== false)
	{
		$ctag     = '{category_main_thumb_image_1}';
		$ch_thumb = Redshop::getConfig()->get('THUMB_HEIGHT');
		$cw_thumb = Redshop::getConfig()->get('THUMB_WIDTH');
	}
	else
	{
		$ctag     = '{category_main_thumb_image}';
		$ch_thumb = Redshop::getConfig()->get('THUMB_HEIGHT');
		$cw_thumb = Redshop::getConfig()->get('THUMB_WIDTH');
	}

	$link = JRoute::_(
						'index.php?option=' . $this->option .
						'&view=category&cid=' . $this->catid .
						'&manufacturer_id=' . $this->manufacturer_id .
						'&layout=detail&Itemid=' . $mainItemid
					);

	$cat_main_thumb = "";

	$medias = RedshopEntityCategory::getInstance($this->maincat->id)->getMedia();

	/** @var RedshopEntityMediaImage $fullImage */
	$fullImage = null;

	foreach ($medias->getAll() as $media)
	{
		/** @var RedshopEntityMedia $media */
		if ($media->get('scope') == 'full')
		{
			$fullImage = RedshopEntityMediaImage::getInstance($media->getId());

			break;
		}
	}

	if (null !== $fullImage)
	{
		$water_cat_img = $fullImage->generateThumb($cw_thumb, $ch_thumb);
		$cat_main_thumb = "<a href='" . $link . "' title='" . $main_cat_name .
			"'><img src='" . $water_cat_img['abs'] . "' alt='" . $main_cat_name . "' title='" . $main_cat_name . "'></a>";
	}

	/*if ($this->maincat->category_full_image && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . 'category/' . $this->maincat->category_full_image))
	{
		$water_cat_img  = RedshopHelperMedia::watermark('category', $this->maincat->category_full_image, $cw_thumb, $ch_thumb, Redshop::getConfig()->get('WATERMARK_CATEGORY_THUMB_IMAGE'), '0');
		$cat_main_thumb = "<a href='" . $link . "' title='" . $main_cat_name .
							"'><img src='" . $water_cat_img . "' alt='" . $main_cat_name . "' title='" . $main_cat_name . "'></a>";
	}*/

	$template_desc = str_replace($ctag, $cat_main_thumb, $template_desc);

	$extraFieldName = Redshop\Helper\ExtraFields::getSectionFieldNames(2, 1, 1);
	$template_desc  = $producthelper->getExtraSectionTag($extraFieldName, $this->catid, "2", $template_desc, 0);

	if (strpos($template_desc, "{compare_product_div}") !== false)
	{
		$compare_product_div = "";

		if (Redshop::getConfig()->get('PRODUCT_COMPARISON_TYPE') != "")
		{
			$comparediv           = Redshop\Product\Compare::generateCompareProduct();
			$compareUrl           = JRoute::_('index.php?option=com_redshop&view=product&layout=compare&Itemid=' . $this->itemid);
			$compare_product_div = '<a href="' . $compareUrl . '">' . JText::_('COM_REDSHOP_COMPARE') . '</a>';
			$compare_product_div .= "<div id='divCompareProduct'>" . $comparediv . "</div>";
		}

		$template_desc = str_replace("{compare_product_div}", $compare_product_div, $template_desc);
	}

	if (strpos($template_desc, "{category_loop_start}") !== false && strpos($template_desc, "{category_loop_end}") !== false)
	{
		$template_d1     = explode("{category_loop_start}", $template_desc);
		$template_d2     = explode("{category_loop_end}", $template_d1 [1]);
		$subcat_template = $template_d2 [0];

		if (strpos($subcat_template, '{category_thumb_image_2}') !== false)
		{
			$tag     = '{category_thumb_image_2}';
			$h_thumb = Redshop::getConfig()->get('THUMB_HEIGHT_2');
			$w_thumb = Redshop::getConfig()->get('THUMB_WIDTH_2');
		}
		elseif (strpos($subcat_template, '{category_thumb_image_3}') !== false)
		{
			$tag     = '{category_thumb_image_3}';
			$h_thumb = Redshop::getConfig()->get('THUMB_HEIGHT_3');
			$w_thumb = Redshop::getConfig()->get('THUMB_WIDTH_3');
		}
		elseif (strpos($subcat_template, '{category_thumb_image_1}') !== false)
		{
			$tag     = '{category_thumb_image_1}';
			$h_thumb = Redshop::getConfig()->get('THUMB_HEIGHT');
			$w_thumb = Redshop::getConfig()->get('THUMB_WIDTH');
		}
		else
		{
			$tag     = '{category_thumb_image}';
			$h_thumb = Redshop::getConfig()->get('THUMB_HEIGHT');
			$w_thumb = Redshop::getConfig()->get('THUMB_WIDTH');
		}

		$cat_detail = "";
		$extraFieldsForCurrentTemplate = RedshopHelperTemplate::getExtraFieldsForCurrentTemplate($extraFieldName, $subcat_template);

		for ($i = 0, $nc = count($this->detail); $i < $nc; $i++)
		{
			$row = $this->detail[$i];

			// Filter categories based on Shopper group category ACL
			$checkcid = RedshopHelperAccess::checkPortalCategoryPermission($row->id);
			$sgportal = RedshopHelperShopper_Group::getShopperGroupPortal();
			$portal   = 0;

			if (count($sgportal) > 0)
			{
				$portal = $sgportal->shopper_group_portal;
			}

			if (!$checkcid && (Redshop::getConfig()->get('PORTAL_SHOP') == 1 || $portal == 1))
			{
				continue;
			}

			$data_add = $subcat_template;

			$categoryItemId = RedshopHelperRouter::getCategoryItemid($row->id);
			$mainItemId = !$categoryItemId ? $this->itemid : $categoryItemId;

			$link = JRoute::_(
								'index.php?option=' . $this->option .
								'&view=category&cid=' . $row->id .
								'&manufacturer_id=' . $this->manufacturer_id .
								'&layout=detail&Itemid=' . $mainItemId
							);

			$middlepath  = REDSHOP_FRONT_IMAGES_RELPATH . 'category/';
			$title       = " title='" . $row->name . "' ";
			$alt         = " alt='" . $row->name . "' ";
			$product_img = REDSHOP_FRONT_IMAGES_ABSPATH . "noimage.jpg";
			$linkimage   = $product_img;
			if ($row->category_full_image && file_exists($middlepath . $row->category_full_image))
			{
				$categoryFullImage = $row->category_full_image;
				$product_img       = RedshopHelperMedia::watermark('category', $row->category_full_image, $w_thumb, $h_thumb, Redshop::getConfig()->get('WATERMARK_CATEGORY_THUMB_IMAGE'), '0');
				$linkimage         = RedshopHelperMedia::watermark(
						'category', $row->category_full_image, '', '', Redshop::getConfig()->get('WATERMARK_CATEGORY_IMAGE'), '0');
			}
			elseif (Redshop::getConfig()->get('CATEGORY_DEFAULT_IMAGE') && file_exists($middlepath . Redshop::getConfig()->get('CATEGORY_DEFAULT_IMAGE')))
			{
				$categoryFullImage = Redshop::getConfig()->get('CATEGORY_DEFAULT_IMAGE');
				$product_img       = RedshopHelperMedia::watermark('category', Redshop::getConfig()->get('CATEGORY_DEFAULT_IMAGE'), $w_thumb, $h_thumb, Redshop::getConfig()->get('WATERMARK_CATEGORY_THUMB_IMAGE'), '0');
				$linkimage         = RedshopHelperMedia::watermark('category', Redshop::getConfig()->get('CATEGORY_DEFAULT_IMAGE'), '', '', Redshop::getConfig()->get('WATERMARK_CATEGORY_IMAGE'), '0');
			}

			if (Redshop::getConfig()->get('CAT_IS_LIGHTBOX'))
			{
				$cat_thumb = "<a class='modal' href='" . REDSHOP_FRONT_IMAGES_ABSPATH . 'category/' . $categoryFullImage . "' rel=\"{handler: 'image', size: {}}\" " . $title . ">";
			}
			else
			{
				$cat_thumb = "<a href='" . $link . "' " . $title . ">";
			}

			$cat_thumb .= "<img src='" . $product_img . "' " . $alt . $title . ">";
			$cat_thumb .= "</a>";
			$data_add = str_replace($tag, $cat_thumb, $data_add);

			if (strpos($data_add, '{category_name}') !== false)
			{
				$cat_name = '<a href="' . $link . '" ' . $title . '>' . $row->name . '</a>';
				$data_add = str_replace("{category_name}", $cat_name, $data_add);
			}

			if (strpos($data_add, '{category_readmore}') !== false)
			{
				$cat_name = '<a href="' . $link . '" ' . $title . '>' . JText::_('COM_REDSHOP_READ_MORE') . '</a>';
				$data_add = str_replace("{category_readmore}", $cat_name, $data_add);
			}

			if (strpos($data_add, '{category_description}') !== false)
			{
				$cat_desc = $Redconfiguration->maxchar($row->description, Redshop::getConfig()->get('CATEGORY_SHORT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_SHORT_DESC_END_SUFFIX'));
				$data_add = str_replace("{category_description}", $cat_desc, $data_add);
			}

			if (strpos($data_add, '{category_short_desc}') !== false)
			{
				$cat_s_desc = $Redconfiguration->maxchar($row->short_description, Redshop::getConfig()->get('CATEGORY_SHORT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_SHORT_DESC_END_SUFFIX'));
				$data_add   = str_replace("{category_short_desc}", $cat_s_desc, $data_add);
			}

			if (strpos($data_add, '{category_total_product}') !== false)
			{
				$totalprd = $producthelper->getProductCategory($row->id);
				$data_add = str_replace("{category_total_product}", count($totalprd), $data_add);
				$data_add = str_replace("{category_total_product_lbl}", JText::_('COM_REDSHOP_TOTAL_PRODUCT'), $data_add);
			}

			if (strstr($data_add, '{category_link}'))
			{
				$data_add = str_replace("{category_link}", $link, $data_add);
			}

			if (strstr($data_add, '{category_image_nolink}'))
			{
				$data_add = str_replace("{category_image_nolink}", $product_img, $data_add);
			}

			if (strstr($data_add, '{category_name_nolink}'))
			{
				$data_add = str_replace("{category_name_nolink}", $row->name, $data_add);
			}

			/*
			 * category template extra field
			 * "2" argument is set for category
			 */
			if ($extraFieldsForCurrentTemplate)
			{
				$data_add = Redshop\Helper\ExtraFields::displayExtraFields(2, $row->id, $extraFieldsForCurrentTemplate, $data_add);
			}

			$cat_detail .= $data_add;
		}

		$template_desc = str_replace("{category_loop_start}", "", $template_desc);
		$template_desc = str_replace("{category_loop_end}", "", $template_desc);
		$template_desc = str_replace($subcat_template, $cat_detail, $template_desc);
	}

	if (strpos($template_desc, "{if subcats}") !== false && strpos($template_desc, "{subcats end if}") !== false)
	{
		$template_d1 = explode("{if subcats}", $template_desc);
		$template_d2 = explode("{subcats end if}", $template_d1 [1]);

		if (count($this->detail) > 0)
		{
			$template_desc = str_replace("{if subcats}", "", $template_desc);
			$template_desc = str_replace("{subcats end if}", "", $template_desc);
		}
		else
		{
			$template_desc = $template_d1 [0] . $template_d2 [1];
		}
	}

	if (strpos($template_desc, "{product_price_slider}") !== false)
	{
		$price_slider  = '<div id="pricefilter">
				<div class="left" id="leftSlider">
					<div id="range">' . JText::_('COM_REDSHOP_PRICE') . ': <span id="redcatamount"> </span></div>
					<div class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="redcatslider">
						<div style="left: 52.381%; width: 0%;" class="ui-slider-range ui-widget-header"></div>
						<a style="left: 52.381%;" class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
						<a style="left: 52.381%;" class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
					</div>
				</div>
				<div class="left" id="blankfilter"></div>
				<div class="left" id="productsWrap">
					<div style="display: none;" id="ajaxcatMessage">' . JText::_('COM_REDSHOP_LOADING') . '</div>
				</div>
			</div>';
		$template_desc = str_replace("{product_price_slider}", $price_slider, $template_desc);
		$product_tmpl  = JText::_('COM_REDSHOP_NO_PRODUCT_FOUND');
	}
}

if (strpos($template_desc, "{product_loop_start}") !== false && strpos($template_desc, "{product_loop_end}") !== false)
{
	$template_d1      = explode("{product_loop_start}", $template_desc);
	$template_d2      = explode("{product_loop_end}", $template_d1 [1]);
	$template_product = $template_d2[0];
	$template_tag     = $template_d2[0];

	if (false === JModuleHelper::isEnabled('mod_redshop_filter'))
    {
	    $attribute_template = \Redshop\Template\Helper::getAttribute($template_product);

	    $extraFieldName = Redshop\Helper\ExtraFields::getSectionFieldNames(1, 1, 1);
	    $extraFieldsForCurrentTemplate = $producthelper->getExtraFieldsForCurrentTemplate($extraFieldName, $template_product, 1);
	    $product_data   = array();
	    list($template_userfield, $userfieldArr) = $producthelper->getProductUserfieldFromTemplate($template_product);
	    $template_product                        = $producthelper->replaceVatinfo($template_product);

	    foreach ($this->product as $product)
	    {
		    // ToDo: This is wrong way to generate tmpl file. And model function to load $this->product is wrong way also. Fix it.
		    // ToDo: Echo a message when no records is returned by selection of empty category or wrong manufacturer in menu item params.

		    /*		$attributeproductStockStatus = $producthelper->getproductStockStatus(
						$product->product_id,
						0,
						0,
						0
					);*/

		    $count_no_user_field = 0;

		    $data_add = $template_product;

		    // ProductFinderDatepicker Extra Field Start

		    $data_add = $producthelper->getProductFinderDatepickerValue($template_product, $product->product_id, $fieldArray);

		    // ProductFinderDatepicker Extra Field End

		    /*
			 * Process the prepare Product plugins
			 */
		    $params  = array();
		    $results = $this->dispatcher->trigger('onPrepareProduct', array(& $data_add, & $params, $product));

		    /*		$data_add = $producthelper->replaceProductStockdata(
																		$product->product_id,
																		0,
																		0,
																		$data_add,
																		$attributeproductStockStatus
							);*/

		    if (strstr($data_add, "{product_delivery_time}"))
		    {
			    $product_delivery_time = $producthelper->getProductMinDeliveryTime($product->product_id);

			    if ($product_delivery_time != "")
			    {
				    $data_add = str_replace("{delivery_time_lbl}", JText::_('COM_REDSHOP_DELIVERY_TIME'), $data_add);
				    $data_add = str_replace("{product_delivery_time}", $product_delivery_time, $data_add);
			    }
			    else
			    {
				    $data_add = str_replace("{delivery_time_lbl}", "", $data_add);
				    $data_add = str_replace("{product_delivery_time}", "", $data_add);
			    }
		    }

		    // More documents
		    if (strpos($data_add, "{more_documents}") !== false)
		    {
			    $media_documents = RedshopHelperMedia::getAdditionMediaImage($product->product_id, "product", "document");
			    $more_doc        = '';

			    for ($m = 0, $nm = count($media_documents); $m < $nm; $m++)
			    {
				    $alttext = RedshopHelperMedia::getAlternativeText(
					    "product", $media_documents[$m]->section_id, "", $media_documents[$m]->media_id, "document"
				    );

				    if (!$alttext)
				    {
					    $alttext = $media_documents[$m]->media_name;
				    }

				    if (JFile::exists(REDSHOP_FRONT_DOCUMENT_RELPATH . 'product/' . $media_documents[$m]->media_name))
				    {
					    $downlink = JURI::root() .
						    'index.php?tmpl=component&option=' . $this->option .
						    '&view=product&pid=' . $this->data->product_id .
						    '&task=downloadDocument&fname=' . $media_documents[$m]->media_name .
						    '&Itemid=' . $this->itemid;
					    $more_doc .= "<div><a href='" . $downlink . "' title='" . $alttext . "'>";
					    $more_doc .= $alttext;
					    $more_doc .= "</a></div>";
				    }
			    }

			    $data_add = str_replace("{more_documents}", "<span id='additional_docs" . $product->product_id . "'>" . $more_doc . "</span>", $data_add);
		    }

		    // More documents end

		    // Product User Field Start
		    $hidden_userfield   = "";

		    if ($template_userfield != "")
		    {
			    $ufield = "";

			    for ($ui = 0, $nui = count($userfieldArr); $ui < $nui; $ui++)
			    {
				    $productUserFields = Redshop\Fields\SiteHelper::listAllUserFields($userfieldArr[$ui], 12, '', '', 0, $product->product_id);
				    $ufield .= $productUserFields[1];

				    if ($productUserFields[1] != "")
				    {
					    $count_no_user_field++;
				    }

				    $data_add = str_replace('{' . $userfieldArr[$ui] . '_lbl}', $productUserFields[0], $data_add);
				    $data_add = str_replace('{' . $userfieldArr[$ui] . '}', $productUserFields[1], $data_add);
			    }

			    $productUserFieldsForm = "<form method='post' action='' id='user_fields_form_" . $product->product_id .
				    "' name='user_fields_form_" . $product->product_id . "'>";

			    if ($ufield != "")
			    {
				    $data_add = str_replace("{if product_userfield}", $productUserFieldsForm, $data_add);
				    $data_add = str_replace("{product_userfield end if}", "</form>", $data_add);
			    }
			    else
			    {
				    $data_add = str_replace("{if product_userfield}", "", $data_add);
				    $data_add = str_replace("{product_userfield end if}", "", $data_add);
			    }
		    }
            elseif (Redshop::getConfig()->get('AJAX_CART_BOX'))
		    {
			    $ajax_detail_template_desc = "";
			    $ajax_detail_template      = \Redshop\Template\Helper::getAjaxDetailBox($product);

			    if (null !== $ajax_detail_template)
			    {
				    $ajax_detail_template_desc = $ajax_detail_template->template_desc;
			    }

			    $returnArr          = $producthelper->getProductUserfieldFromTemplate($ajax_detail_template_desc);
			    $template_userfield = $returnArr[0];
			    $userfieldArr       = $returnArr[1];

			    if ($template_userfield != "")
			    {
				    $ufield = "";

				    for ($ui = 0, $nui = count($userfieldArr); $ui < $nui; $ui++)
				    {
					    $productUserFields = Redshop\Fields\SiteHelper::listAllUserFields($userfieldArr[$ui], 12, '', '', 0, $product->product_id);
					    $ufield .= $productUserFields[1];

					    if ($productUserFields[1] != "")
					    {
						    $count_no_user_field++;
					    }

					    $template_userfield = str_replace('{' . $userfieldArr[$ui] . '_lbl}', $productUserFields[0], $template_userfield);
					    $template_userfield = str_replace('{' . $userfieldArr[$ui] . '}', $productUserFields[1], $template_userfield);
				    }

				    if ($ufield != "")
				    {
					    $hidden_userfield = "<div style='display:none;'><form method='post' action='' id='user_fields_form_" . $product->product_id .
						    "' name='user_fields_form_" . $product->product_id . "'>" . $template_userfield . "</form></div>";
				    }
			    }
		    }

		    $data_add = $data_add . $hidden_userfield;
		    /************** end user fields ***************************/

		    $ItemData  = $producthelper->getMenuInformation(0, 0, '', 'product&pid=' . $product->product_id);
		    $catidmain = JFactory::getApplication()->input->get("cid");

		    if (count($ItemData) > 0)
		    {
			    $pItemid = $ItemData->id;
		    }
		    else
		    {
			    $pItemid = RedshopHelperRouter::getItemId($product->product_id, $catidmain);
		    }

		    $data_add              = str_replace("{product_id_lbl}", JText::_('COM_REDSHOP_PRODUCT_ID_LBL'), $data_add);
		    $data_add              = str_replace("{product_id}", $product->product_id, $data_add);
		    $data_add              = str_replace("{product_number_lbl}", JText::_('COM_REDSHOP_PRODUCT_NUMBER_LBL'), $data_add);
		    $product_number_output = '<span id="product_number_variable' . $product->product_id . '">' . $product->product_number . '</span>';
		    $data_add              = str_replace("{product_number}", $product_number_output, $data_add);

		    $product_volume_unit = '<span class="product_unit_variable">' . Redshop::getConfig()->get('DEFAULT_VOLUME_UNIT') . "3" . '</span>';

		    $dataAddStr = $producthelper->redunitDecimal($product->product_volume) . "&nbsp;" . $product_volume_unit;
		    $data_add = str_replace("{product_size}", $dataAddStr, $data_add);

		    $product_unit = '<span class="product_unit_variable">' . Redshop::getConfig()->get('DEFAULT_VOLUME_UNIT') . '</span>';
		    $data_add     = str_replace("{product_length}", $producthelper->redunitDecimal($product->product_length) . "&nbsp;" . $product_unit, $data_add);
		    $data_add     = str_replace("{product_width}", $producthelper->redunitDecimal($product->product_width) . "&nbsp;" . $product_unit, $data_add);
		    $data_add     = str_replace("{product_height}", $producthelper->redunitDecimal($product->product_height) . "&nbsp;" . $product_unit, $data_add);

		    $specificLink = $this->dispatcher->trigger('createProductLink', array($product));

		    if (empty($specificLink))
		    {
			    $productCatId = !empty($product->categories) && is_array($product->categories) ? $product->categories[0] : $this->catid;

			    $link = JRoute::_(
				    'index.php?option=' . $this->option .
				    '&view=product&pid=' . $product->product_id .
				    '&cid=' . $productCatId .
				    '&Itemid=' . $pItemid
			    );
		    }
		    else
		    {
			    $link = $specificLink[0];
		    }

		    $pname      = $Redconfiguration->maxchar($product->product_name, Redshop::getConfig()->get('CATEGORY_PRODUCT_TITLE_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_TITLE_END_SUFFIX'));
		    $product_nm = $pname;

		    if (strpos($data_add, '{product_name_nolink}') !== false)
		    {
			    $data_add = str_replace("{product_name_nolink}", $product_nm, $data_add);
		    }

		    if (strpos($data_add, '{product_name}') !== false)
		    {
			    $pname    = "<a href='" . $link . "' title='" . $product->product_name . "'>" . $pname . "</a>";
			    $data_add = str_replace("{product_name}", $pname, $data_add);
		    }

		    if (strpos($data_add, '{category_product_link}') !== false)
		    {
			    $data_add = str_replace("{category_product_link}", $link, $data_add);
		    }

		    if (strpos($data_add, '{read_more}') !== false)
		    {
			    $rmore    = "<a href='" . $link . "' title='" . $product->product_name . "'>" . JText::_('COM_REDSHOP_READ_MORE') . "</a>";
			    $data_add = str_replace("{read_more}", $rmore, $data_add);
		    }

		    if (strpos($data_add, '{read_more_link}') !== false)
		    {
			    $data_add = str_replace("{read_more_link}", $link, $data_add);
		    }

		    /**
		     * Related Product List in Lightbox
		     * Tag Format = {related_product_lightbox:<related_product_name>[:width][:height]}
		     */
		    if (strpos($data_add, '{related_product_lightbox:') !== false)
		    {
			    $related_product = $producthelper->getRelatedProduct($product->product_id);
			    $rtlnone         = explode("{related_product_lightbox:", $data_add);
			    $rtlntwo         = explode("}", $rtlnone[1]);
			    $rtlnthree       = explode(":", $rtlntwo[0]);
			    $rtln            = $rtlnthree[0];
			    $rtlnfwidth      = (isset($rtlnthree[1])) ? $rtlnthree[1] : "900";
			    $rtlnwidthtag    = (isset($rtlnthree[1])) ? ":" . $rtlnthree[1] : "";

			    $rtlnfheight   = (isset($rtlnthree[2])) ? $rtlnthree[2] : "600";
			    $rtlnheighttag = (isset($rtlnthree[2])) ? ":" . $rtlnthree[2] : "";

			    $rtlntag = "{related_product_lightbox:$rtln$rtlnwidthtag$rtlnheighttag}";

			    if (count($related_product) > 0)
			    {
				    $linktortln = JURI::root() .
					    "index.php?option=com_redshop&view=product&pid=" . $product->product_id .
					    "&tmpl=component&template=" . $rtln . "&for=rtln";
				    $rtlna      = '<a class="redcolorproductimg" href="' . $linktortln . '"  >' . JText::_('COM_REDSHOP_RELATED_PRODUCT_LIST_IN_LIGHTBOX') . '</a>';
			    }
			    else
			    {
				    $rtlna = "";
			    }

			    $data_add = str_replace($rtlntag, $rtlna, $data_add);
		    }

		    if (strpos($data_add, '{product_s_desc}') !== false)
		    {
			    $p_s_desc = $Redconfiguration->maxchar($product->product_s_desc, Redshop::getConfig()->get('CATEGORY_PRODUCT_SHORT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_SHORT_DESC_END_SUFFIX'));
			    $data_add = str_replace("{product_s_desc}", $p_s_desc, $data_add);
		    }

		    if (strpos($data_add, '{product_desc}') !== false)
		    {
			    $p_desc   = $Redconfiguration->maxchar($product->product_desc, Redshop::getConfig()->get('CATEGORY_PRODUCT_DESC_MAX_CHARS'), Redshop::getConfig()->get('CATEGORY_PRODUCT_DESC_END_SUFFIX'));
			    $data_add = str_replace("{product_desc}", $p_desc, $data_add);
		    }

		    if (strpos($data_add, '{product_rating_summary}') !== false)
		    {
			    // Product Review/Rating Fetching reviews
			    $final_avgreview_data = Redshop\Product\Rating::getRating($product->product_id);
			    $data_add             = str_replace("{product_rating_summary}", $final_avgreview_data, $data_add);
		    }

		    if (strpos($data_add, '{manufacturer_link}') !== false)
		    {
			    $manufacturer_link_href = JRoute::_(
				    'index.php?option=com_redshop&view=manufacturers&layout=detail&mid=' . $product->manufacturer_id .
				    '&Itemid=' . $this->itemid
			    );
			    $manufacturer_link      = '<a class="btn btn-primary" href="' . $manufacturer_link_href . '" title="' . $product->manufacturer_name . '">' .
				    $product->manufacturer_name .
				    '</a>';
			    $data_add               = str_replace("{manufacturer_link}", $manufacturer_link, $data_add);

			    if (strpos($data_add, "{manufacturer_link}") !== false)
			    {
				    $data_add = str_replace("{manufacturer_name}", "", $data_add);
			    }
		    }

		    if (strpos($data_add, '{manufacturer_product_link}') !== false)
		    {
			    $manuUrl = JRoute::_(
				    'index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $product->manufacturer_id .
				    '&Itemid=' . $this->itemid
			    );
			    $manufacturerPLink = "<a class='btn btn-primary' href='" . $manuUrl . "'>" .
				    JText::_("COM_REDSHOP_VIEW_ALL_MANUFACTURER_PRODUCTS") . " " . $product->manufacturer_name .
				    "</a>";
			    $data_add          = str_replace("{manufacturer_product_link}", $manufacturerPLink, $data_add);
		    }

		    if (strpos($data_add, '{manufacturer_name}') !== false)
		    {
			    $data_add = str_replace("{manufacturer_name}", $product->manufacturer_name, $data_add);
		    }

		    if (strpos($data_add, "{product_thumb_image_3}") !== false)
		    {
			    $pimg_tag = '{product_thumb_image_3}';
			    $ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT_3');
			    $pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH_3');
		    }
            elseif (strpos($data_add, "{product_thumb_image_2}") !== false)
		    {
			    $pimg_tag = '{product_thumb_image_2}';
			    $ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT_2');
			    $pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH_2');
		    }
            elseif (strpos($data_add, "{product_thumb_image_1}") !== false)
		    {
			    $pimg_tag = '{product_thumb_image_1}';
			    $ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
			    $pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');
		    }
		    else
		    {
			    $pimg_tag = '{product_thumb_image}';
			    $ph_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
			    $pw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');
		    }

		    if (strstr($data_add, "{product_category_thumb_image}"))
		    {
			    $pcimg_tag = '{product_category_thumb_image}';
			    $pch_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_HEIGHT');
			    $pcw_thumb = Redshop::getConfig()->get('CATEGORY_PRODUCT_THUMB_WIDTH');
		    }


		    $hidden_thumb_image = "<input type='hidden' name='prd_main_imgwidth' id='prd_main_imgwidth' value='" . $pw_thumb . "'>
								<input type='hidden' name='prd_main_imgheight' id='prd_main_imgheight' value='" . $ph_thumb . "'>";
		    $thum_image         = $producthelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, 2, 1);

		    // Product image flying addwishlist time start
		    $preselectedresult = $producthelper->displayAdditionalImage(
			    $product->product_id,
			    0,
			    0,
			    0,
			    0,
			    $pw_thumb,
			    $ph_thumb,
			    'product'
		    );

		    if (count($preselectedresult) > 0)
		    {
			    $thum_image = "<div class='productImageWrap' id='productImageWrapID_" . $product->product_id . "'>" .
				    $producthelper->replaceProductImage($product, "", "", "", $pw_thumb, $ph_thumb, Redshop::getConfig()->get('PRODUCT_DETAIL_IS_LIGHTBOX'), 0, $preselectedresult) .
				    "</div>";
		    }
		    else
		    {
			    // Product image flying addwishlist time start
			    $thum_image = "<div class='productImageWrap' id='productImageWrapID_" . $product->product_id . "'>" .
				    $producthelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, Redshop::getConfig()->get('PRODUCT_DETAIL_IS_LIGHTBOX')) .
				    "</div>";
		    }

		    $thum_image_category = "<span class='' id='productImageWrapCategoryID_" . $product->product_id . "'>" .
			    $producthelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb) .
			    "</span>";
		    // Product image flying addwishlist time end

		    $data_add = str_replace($pimg_tag, $thum_image . $hidden_thumb_image, $data_add);
		    $data_add = str_replace($pcimg_tag, $thum_image_category . $hidden_thumb_image, $data_add);

		    // Front-back image tag...
		    if (strstr($data_add, "{front_img_link}") || strstr($data_add, "{back_img_link}"))
		    {
			    if ($this->_data->product_thumb_image)
			    {
				    $mainsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_thumb_image;
			    }
			    else
			    {
				    $mainsrcPath = RedshopHelperMedia::getImagePath(
					    $product->product_full_image,
					    '',
					    'thumb',
					    'product',
					    $pw_thumb,
					    $ph_thumb,
					    Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
				    );
			    }

			    if ($this->_data->product_back_thumb_image)
			    {
				    $backsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_thumb_image;
			    }
			    else
			    {
				    $backsrcPath = RedShopHelperImages::getImagePath(
					    $product->product_back_full_image,
					    '',
					    'thumb',
					    'product',
					    $pw_thumb,
					    $ph_thumb,
					    Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
				    );
			    }

			    $ahrefpath     = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_full_image;
			    $ahrefbackpath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_full_image;

			    $product_front_image_link = "<a href='#' onClick='javascript:changeproductImage(" .
				    $product->product_id . ",\"" . $mainsrcPath . "\",\"" . $ahrefpath . "\");'>" .
				    JText::_('COM_REDSHOP_FRONT_IMAGE') . "</a>";
			    $product_back_image_link  = "<a href='#' onClick='javascript:changeproductImage(" .
				    $product->product_id . ",\"" . $backsrcPath . "\",\"" . $ahrefbackpath . "\");'>" .
				    JText::_('COM_REDSHOP_BACK_IMAGE') . "</a>";

			    $data_add = str_replace("{front_img_link}", $product_front_image_link, $data_add);
			    $data_add = str_replace("{back_img_link}", $product_back_image_link, $data_add);
		    }
		    else
		    {
			    $data_add = str_replace("{front_img_link}", "", $data_add);
			    $data_add = str_replace("{back_img_link}", "", $data_add);
		    }

		    // Front-back image tag end

		    // Product preview image.
		    if (strstr($data_add, '{product_preview_img}'))
		    {
			    if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . 'product/' . $product->product_preview_image))
			    {
				    $previewsrcPath = RedShopHelperImages::getImagePath(
					    $product->product_preview_image,
					    '',
					    'thumb',
					    'product',
					    Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH'),
					    Redshop::getConfig()->get('CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT'),
					    Redshop::getConfig()->get('USE_IMAGE_SIZE_SWAPPING')
				    );
				    $previewImg     = "<img src='" . $previewsrcPath . "' class='rs_previewImg' />";
				    $data_add       = str_replace("{product_preview_img}", $previewImg, $data_add);
			    }
			    else
			    {
				    $data_add = str_replace("{product_preview_img}", "", $data_add);
			    }
		    }

		    $data_add = $producthelper->getJcommentEditor($product, $data_add);

		    /*
			 * product loop template extra field
			 * lat arg set to "1" for indetify parsing data for product tag loop in category
			 * last arg will parse {producttag:NAMEOFPRODUCTTAG} nameing tags.
			 * "1" is for section as product
			 */
		    if ($extraFieldsForCurrentTemplate && count($loadCategorytemplate) > 0)
		    {
			    $data_add = $producthelper->getExtraSectionTag($extraFieldName, $product->product_id, "1", $data_add, 1);
		    }

		    /************************************
		     *  Conditional tag
		     *  if product on discount : Yes
		     *  {if product_on_sale} This product is on sale {product_on_sale end if} // OUTPUT : This product is on sale
		     *  NO : // OUTPUT : Display blank
		     ************************************/
		    $data_add = $producthelper->getProductOnSaleComment($product, $data_add);

		    // Replace wishlistbutton
		    $data_add = $producthelper->replaceWishlistButton($product->product_id, $data_add);

		    // Replace compare product button
		    $data_add = $producthelper->replaceCompareProductsButton($product->product_id, $this->catid, $data_add);

		    $data_add = $stockroomhelper->replaceStockroomAmountDetail($data_add, $product->product_id);

		    // Checking for child products
		    if ($product->count_child_products > 0)
		    {
			    if (Redshop::getConfig()->get('PURCHASE_PARENT_WITH_CHILD') == 1)
			    {
				    $isChilds = false;

				    // Get attributes
				    $attributes_set = array();

				    if ($product->attribute_set_id > 0)
				    {
					    $attributes_set = $producthelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
				    }

				    $attributes = $producthelper->getProductAttribute($product->product_id);
				    $attributes = array_merge($attributes, $attributes_set);
			    }
			    else
			    {
				    $isChilds   = true;
				    $attributes = array();
			    }
		    }
		    else
		    {
			    $isChilds = false;

			    // Get attributes
			    $attributes_set = array();

			    if ($product->attribute_set_id > 0)
			    {
				    $attributes_set = $producthelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
			    }

			    $attributes = $producthelper->getProductAttribute($product->product_id);
			    $attributes = array_merge($attributes, $attributes_set);
		    }

		    // Product attribute  Start
		    $totalatt = count($attributes);

		    // Check product for not for sale

		    $data_add = $producthelper->getProductNotForSaleComment($product, $data_add, $attributes);

		    $data_add = $producthelper->replaceProductInStock($product->product_id, $data_add, $attributes, $attribute_template);

		    $data_add = $producthelper->replaceAttributeData($product->product_id, 0, 0, $attributes, $data_add, $attribute_template, $isChilds);

		    // Get cart tempalte
		    $data_add = $producthelper->replaceCartTemplate(
			    $product->product_id,
			    $this->catid,
			    0,
			    0,
			    $data_add,
			    $isChilds,
			    $userfieldArr,
			    $totalatt,
			    $product->total_accessories,
			    $count_no_user_field
		    );


		    // -------- More images
		    if (strstr($data_add, "{more_images_3}"))
		    {
			    $mpimg_tag = '{more_images_3}';
			    $mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT_3');
			    $mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_3');
		    }
            elseif (strstr($data_add, "{more_images_2}"))
		    {
			    $mpimg_tag = '{more_images_2}';
			    $mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT_2');
			    $mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_2');
		    }
            elseif (strstr($data_add, "{more_images_1}"))
		    {
			    $mpimg_tag = '{more_images_1}';
			    $mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT');
			    $mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE');
		    }
		    else
		    {
			    $mpimg_tag = '{more_images}';
			    $mph_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE_HEIGHT');
			    $mpw_thumb = Redshop::getConfig()->get('PRODUCT_ADDITIONAL_IMAGE');
		    }


		    $moreimage_response  = '';
		    $more_images  = '';

		    if (strstr($data_add, $mpimg_tag))
		    {


			    $moreimage_response  = $preselectedresult['response'];

			    if ($moreimage_response != "")
			    {
				    $more_images = $moreimage_response;
			    }

			    $insertStr     = "<div class='redhoverImagebox' id='additional_images" . $product->product_id . "'>" . $more_images . "</div><div class=\"clr\"></div>";
			    $data_add = str_replace($mpimg_tag, $insertStr, $data_add);
		    }

		    // -------- End More images

		    $product_data[] = $data_add;
	    }

	    if (!$slide)
	    {
		    $product_tmpl = "<div id='redcatproducts'>" . implode('', $product_data) . "</div>";
	    }
	    else
	    {
		    $product_tmpl = implode('', $product_data);
	    }

	    $product_tmpl .= "<input type='hidden' name='slider_texpricemin' id='slider_texpricemin' value='" . $texpricemin . "' />";
	    $product_tmpl .= "<input type='hidden' name='slider_texpricemax' id='slider_texpricemax' value='" . $texpricemax . "' />";

	    if (strstr($template_desc, "{show_all_products_in_category}"))
	    {
		    $template_desc = str_replace("{show_all_products_in_category}", "", $template_desc);
		    $template_desc = str_replace("{pagination}", "", $template_desc);
	    }

	    $limitBox        = '';
	    $paginationList  = '';
	    $usePerPageLimit = false;

	    $pagination = new JPagination($model->_total, $start, $endlimit);

	    if ($this->productPriceSliderEnable)
	    {
		    $pagination->setAdditionalUrlParam('texpricemin', $texpricemin);
		    $pagination->setAdditionalUrlParam('texpricemax', $texpricemax);
	    }

	    if (strpos($template_desc, "{pagination}") !== false)
	    {
		    $paginationList = $pagination->getPagesLinks();
		    $template_desc = str_replace("{pagination}", $paginationList, $template_desc);
	    }

	    if (strpos($template_desc, "perpagelimit:") !== false)
	    {
		    $usePerPageLimit = true;
		    $perpage       = explode('{perpagelimit:', $template_desc);
		    $perpage       = explode('}', $perpage[1]);
		    $template_desc = str_replace("{perpagelimit:" . intval($perpage[0]) . "}", "", $template_desc);
	    }

	    if (strpos($template_desc, "{product_display_limit}") !== false)
	    {
		    if (!$usePerPageLimit)
		    {
			    $limitBox .= "<input type='hidden' name='texpricemin' value='" . $texpricemin . "' />";
			    $limitBox .= "<input type='hidden' name='texpricemax' value='" . $texpricemax . "' />";
			    $limitBox = "<form action='' method='post'> " . $limitBox . $pagination->getLimitBox() . "</form>";
		    }

		    $template_desc = str_replace("{product_display_limit}", $limitBox, $template_desc);
	    }

	    if ($this->productPriceSliderEnable)
	    {
		    $product_tmpl .= "<div id='redcatpagination' style='display:none'>" . $paginationList . "</div>";
		    $product_tmpl .= '<div id="redPageLimit" style="display:none">' . $limitBox . "</div>";
	    }
    }
    else
    {
	    $product_tmpl = '';
	    $template_desc = str_replace("{pagination}", "", $template_desc);
    }

	// $template_desc = $template_d1[0] . '<div id="productlist">' . $product_tmpl . '</div>' . $template_d2[1];
	$template_desc = str_replace("{product_loop_start}", "", $template_desc);
	$template_desc = str_replace("{product_loop_end}", "", $template_desc);
	$template_desc = str_replace($template_tag, "<div id='productlist'>" . $product_tmpl . "</div>", $template_desc);
}

if (!$slide)
{
	if (strpos($template_desc, "{filter_by}") !== false)
	{
		$filterby_form = "<form name='filterby_form' action='' method='post' >";
		$filterby_form .= $this->lists['manufacturer'];
		$filterby_form .= "<input type='hidden' name='texpricemin' id='manuf_texpricemin' value='" . $texpricemin . "' />";
		$filterby_form .= "<input type='hidden' name='texpricemax' id='manuf_texpricemax' value='" . $texpricemax . "' />";
		$filterby_form .= "<input type='hidden' name='order_by' id='order_by' value='" . $this->order_by_select . "' />";
		$filterby_form .= '<input type="hidden" name="limitstart" value="0" />';
		$filterby_form .= "<input type='hidden' name='category_template' id='category_template' value='" . $this->category_template_id . "' />";
		$filterby_form .= "</form>";

		if ($this->lists['manufacturer'] != "")
		{
			$template_desc = str_replace("{filter_by_lbl}", JText::_('COM_REDSHOP_SELECT_FILTER_BY'), $template_desc);
		}
		else
		{
			$template_desc = str_replace("{filter_by_lbl}", "", $template_desc);
		}

		$template_desc = str_replace("{filter_by}", $filterby_form, $template_desc);
	}

	if (strpos($template_desc, "{template_selector_category}") !== false)
	{
		if ($this->lists['category_template'] != "")
		{
			$template_selecter_form = "<form name='template_selecter_form' action='' method='post' >";
			$template_selecter_form .= $this->lists['category_template'];
			$template_selecter_form .= "<input type='hidden' name='order_by' id='order_by' value='" . $this->order_by_select . "' />";
			$template_selecter_form .= "<input type='hidden' name='manufacturer_id' id='manufacturer_id' value='" . $this->manufacturer_id . "' />";
			$template_selecter_form .= "</form>";

			$template_desc = str_replace("{template_selector_category_lbl}", JText::_('COM_REDSHOP_TEMPLATE_SELECTOR_CATEGORY_LBL'), $template_desc);
			$template_desc = str_replace("{template_selector_category}", $template_selecter_form, $template_desc);
		}

		$template_desc = str_replace("{template_selector_category_lbl}", "", $template_desc);
		$template_desc = str_replace("{template_selector_category}", "", $template_desc);
	}

	if (strpos($template_desc, "{order_by}") !== false)
	{
		$orderby_form = "<form name='orderby_form' action='' method='post'>";
		$orderby_form .= $this->lists['order_by'];
		$orderby_form .= "<input type='hidden' name='texpricemin' id='texpricemin' value='" . $texpricemin . "' />";
		$orderby_form .= "<input type='hidden' name='texpricemax' id='texpricemax' value='" . $texpricemax . "' />";
		$orderby_form .= "<input type='hidden' name='manufacturer_id' id='manufacturer_id' value='" . $this->manufacturer_id . "' />";
		$orderby_form .= "<input type='hidden' name='category_template' id='category_template' value='" . $this->category_template_id . "' />";
		$orderby_form .= "</form>";

		$template_desc = str_replace("{order_by_lbl}", JText::_('COM_REDSHOP_SELECT_ORDER_BY'), $template_desc);
		$template_desc = str_replace("{order_by}", $orderby_form, $template_desc);
	}
}

$template_desc = str_replace("{with_vat}", "", $template_desc);
$template_desc = str_replace("{without_vat}", "", $template_desc);
$template_desc = str_replace("{attribute_price_with_vat}", "", $template_desc);
$template_desc = str_replace("{attribute_price_without_vat}", "", $template_desc);
$template_desc = str_replace("{redproductfinderfilter_formstart}", "", $template_desc);
$template_desc = str_replace("{product_price_slider1}", "", $template_desc);
$template_desc = str_replace("{redproductfinderfilter_formend}", "", $template_desc);
$template_desc = str_replace("{redproductfinderfilter:rp_myfilter}", "", $template_desc);

$template_desc = RedshopHelperTemplate::parseRedshopPlugin($template_desc);

$template_desc = RedshopHelperText::replaceTexts($template_desc);
echo eval("?>" . $template_desc . "<?php ");

if ($slide)
{
	JFactory::getApplication()->close();
}
?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        /*getImagename = function(link) {
            var re = new RegExp("images\/(.*?)\/thumb\/(.*?)_w([0-9]*?)_h([0-9]*?)(_.*?|)([.].*?)$");
            var m = link.match(re);
            return m;
        };

        redproductzoom = function(element) {
            var mainimg = element.find('img');
            var m = getImagename(mainimg.attr('src'));
            var newxsize = m[3];
            var newysize = m[4];
            var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

            mainimg.attr('data-zoom-image', urlfull);

            //more image
            element.parents('.redSHOP_product_box_left').find('div[id*=additional_images]').find('.additional_image').each(function() {
                $(this).attr('onmouseout', '');
                $(this).attr('onmouseover', '');

                var urlimg = $(this).find('img').attr('data-src');
                if (typeof urlimg === 'undefined' || urlimg === false) {
                    urlimg = $(this).find('img').attr('src');
                }

                var m = getImagename(urlimg);

                var urlthumb = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/thumb/' + m[2] + '_w' + newxsize + '_h' + newysize + m[5] + m[6];
                var urlfull = redSHOP.RSConfig._('SITE_URL') + 'components/com_redshop/assets/images/' + m[1] + '/' + m[2] + m[6];

                $(this).find('a').attr('data-image', urlthumb);
                $(this).find('a').attr('data-zoom-image', urlfull);

                $(this).find('a').attr('class', 'elevatezoom-gallery');
            });

            if (mainimg.data('elevateZoom')) {
                var ez = mainimg.data('elevateZoom')
                ez.currentImage = urlfull
                ez.imageSrc = urlfull
                ez.zoomImage = urlfull
                ez.closeAll()
                ez.refresh()

                $('.zoomContainer').remove()

                //Create the image swap from the gallery
                $('.' + ez.options.gallery + ' a').click(function (e) {

                    //Set a class on the currently active gallery image
                    if (ez.options.galleryActiveClass) {
                        $('#' + ez.options.gallery + ' a').
                        removeClass(ez.options.galleryActiveClass)
                        $(this).addClass(ez.options.galleryActiveClass)
                    }
                    //stop any link on the a tag from working
                    e.preventDefault()

                    //call the swap image function
                    if ($(this).data('zoom-image')) {
                        ez.zoomImagePre = $(this).data('zoom-image')
                    }
                    else {
                        ez.zoomImagePre = $(this).data('image')
                    }

                    ez.swaptheimage($(this).data('image'), ez.zoomImagePre)
                    return false
                })

            } else {
                var gl = element.parents('.redSHOP_product_box_left').find('.redhoverImagebox').attr('id');

                mainimg.elevateZoom({
                    zoomType: "inner",
                    scrollZoom : true,
                    cursor: "crosshair",
                    gallery: gl,
                    responsive:true,
                    loadingIcon: 'plugins/system/redproductzoom/js/zoomloader.gif'
                });
            }
        };

        $('.quick-view').on('click', function() {
            var quick_view_id = $(this).data('target');
            redproductzoom($(quick_view_id).find('div[id*=productImageWrapID_]'));
        });

        $('#productlist .modal').on('hidden.bs.modal', function () {
            $('.zoomContainer').remove();
        })*/

       /* $('.redhoverImagebox > div').addClass('swiper-container');
        $('.redhoverImagebox > div.swiper-container').wrapInner("<div class='swiper-wrapper'></div>");
        $('.additional_image').addClass('swiper-slide');
        $('.swiper-container').append('<div class="swiper-button-next swiper-button-black"></div><div class="swiper-button-prev swiper-button-black"></div>');

        var swiperInstances = {};
        $(".swiper-container").each(function(index, element){
            var $this = $(this);
            $this.addClass("instance-" + index);
            $this.attr("data-index", index);
            $this.find(".swiper-button-prev").addClass("btn-prev-" + index);
            $this.find(".swiper-button-next").addClass("btn-next-" + index);
            swiperInstances[index] = new Swiper(".instance-" + index, {
                slidesPerView: 3,
                spaceBetween: 10,
                nextButton: ".btn-next-" + index,
                prevButton: ".btn-prev-" + index,
                onProgress: function(e){
                    if (e.isBeginning){
                        $('.btn-prev-'+index).hide();
                        $('.btn-next-'+index).show();
                    } else if(e.isEnd){
                        $('.btn-next-'+index).hide();
                        $('.btn-prev-'+index).show();
                    } else {
                        $('.btn-next-'+index).show();
                        $('.btn-prev-'+index).show();
                    }
                }
            });
        });

        $(document).on('show.bs.modal','div[id*=quick-view]', function () {
            var index = $(this).find("div[class*=instance-]").attr('data-index');

            setTimeout(function() {
                swiperInstances[index].update();
            }, 500);
        })*/

        //$('.attribute_wrapper input[type="radio"]').removeAttr('onclick');
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function($){
       /* jQuery('input[attribute_name="Color"]').each(function(idx, el){
            var color_text = $(this).next('label').text().trim().toLowerCase();
            $(this).next('label').andSelf().wrapAll("<div class='block-radio " + color_text + "'></div>");
        });

        jQuery('input[attribute_name="Size"]').each(function(idx, el){
            $(this).next('label').andSelf().wrapAll("<div class='block-radio'></div>");
        });*/
    });
</script>

<style type="text/css">
    #main-content .modal:not(a) .product .product-left .product_image img{
        width: 310px;
        height: 400px;
    }
    .modal .modal-dialog .modal-content .modal-body .product-left .product_more_images .redhoverImagebox{
        width: 100%!important;
    }
    .modal .modal-dialog .modal-content .modal-body .product-left .product_more_images .redhoverImagebox .additional_image {
        text-align: center;
        background: #fff;
        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        padding: 0;
        margin: 0;
    }
    .modal .modal-dialog .modal-content .modal-body .product-left .product_more_images .redhoverImagebox a img {
        cursor: pointer!important;
    }
</style>