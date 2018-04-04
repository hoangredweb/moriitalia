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
JHTMLBehavior::modal();

$alphas = range('A', 'Z');
array_unshift($alphas, 'ALL');
$result = array();
$Itemid = JRequest::getInt('Itemid');


foreach ($alphas as $i => $alpha)
{
	foreach ($this->detail as $key => $value)
	{
		if ($value->name[0] == $alpha || $value->name[0] == strtolower($alpha))
		{
			$result[$alpha][] = $value;
		}
		elseif ($alpha == 'ALL')
		{
			$result[$alpha][$value->name[0]][$key] = new StdClass;
			$result[$alpha][$value->name[0]][$key]->id = $value->id;
			$result[$alpha][$value->name[0]][$key]->name = $value->name;
			$result[$alpha][$value->name[0]][$key]->manufacturer_url = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $value->id . '&Itemid=' . $Itemid);
		}
		else
		{
			$result[$alpha][] = '';
		}
	}
}

		
?>
<ul class="nav nav-tabs" role="tablist" id="nav-manufacturer">
	<?php foreach ($alphas as $i => $alpha) : ?>
		<li id="li-<?php echo $alpha; ?>" role="presentation" class="<?php echo $i == 'ALL' ? 'active' : ''; ?>"><a href="#<?php echo $alpha; ?>" aria-controls="<?php echo $alpha; ?>" role="tab" data-toggle="tab"><?php echo $alpha ?></a></li>
	<?php endforeach; ?>
</ul>
<div class="brand-input">
	<input type="text" name="keyword-manufacturer" id="keyword-manufacturer" placeholder="<?php echo JText::_('TYPE_A_KEYWORD')?>" />
	<i class="icon-search"></i>
</div>
 <div class="tab-content">
 	<?php foreach ($result as $key => $value) : ?>
    	<div role="tabpanel" class="tab-pane <?php echo $key == 'ALL' ? 'active' : ''; ?>" id="<?php echo $key; ?>">
<?php
JLoader::load('RedshopHelperProduct');
$producthelper = new producthelper;
$redTemplate = Redtemplate::getInstance();
$extraField = new extraField;
$config = Redconfiguration::getInstance();
$url = JURI::base();
$print = JRequest::getInt('print');
$redhelper = new redhelper;

// Page Title Start
$pagetitle = JText::_('COM_REDSHOP_MANUFACTURER');

if ($this->pageheadingtag != '')
{
	$pagetitle = $this->pageheadingtag;
}?>
	<h1 class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php
		if ($this->params->get('show_page_heading', 1))
		{
			if ($this->params->get('page_title') != $pagetitle)
			{
				echo $this->escape($this->params->get('page_title'));
			}
			else
			{
				echo $pagetitle;
			}
		}?>
	</h1>
<?php
// Page title end
$manufacturers_template = $redTemplate->getTemplate("manufacturer");

if (count($manufacturers_template) > 0 && $manufacturers_template[0]->template_desc != "")
{
	$template_desc = $manufacturers_template[0]->template_desc;
}
else
{
	$template_desc = "<div class=\"category_print\">{print}</div>\r\n<div style=\"clear: both;\"></div>\r\n<div id=\"category_header\">\r\n<div class=\"category_order_by\">{order_by} </div>\r\n</div>\r\n<div class=\"manufacturer_box_wrapper\">{manufacturer_loop_start}\r\n<div class=\"manufacturer_box_outside\">\r\n<div class=\"manufacturer_box_inside\">\r\n<div class=\"manufacturer_image\">{manufacturer_image}</div>\r\n<div class=\"manufacturer_title\">\r\n<h3>{manufacturer_name}</h3>\r\n</div>\r\n<div class=\"manufacturer_desc\">{manufacturer_description}</div>\r\n<div class=\"manufacturer_link\"><a href=\"{manufacturer_link}\">{manufacturer_link_lbl}</a></div>\r\n<div class=\"manufacturer_product_link\"><a href=\"{manufacturer_allproductslink}\">{manufacturer_allproductslink_lbl}</a></div>\r\n</div>\r\n</div>\r\n{manufacturer_loop_end}<div class=\"category_product_bottom\" style=\"clear: both;\"></div></div>\r\n<div class=\"pagination\">{pagination}</div>";
}

// Replace Product Template
if ($print)
{
	$onclick = "onclick='window.print();'";
}
else
{
	$print_url = $url . "index.php?option=com_redshop&view=manufacturers&print=1&tmpl=component&Itemid=" . $Itemid;
	$onclick   = "onclick='window.open(\"$print_url\",\"mywindow\",\"scrollbars=1\",\"location=1\")'";
}

$print_tag = "<a " . $onclick . " title='" . JText::_('COM_REDSHOP_PRINT_LBL') . "'>";
$print_tag .= "<img src='" . JSYSTEM_IMAGES_PATH . "printButton.png' alt='" . JText::_('COM_REDSHOP_PRINT_LBL') . "' title='" . JText::_('COM_REDSHOP_PRINT_LBL') . "' />";
$print_tag .= "</a>";

$template_start  = $template_desc;
$template_middle = "";
$template_end    = "";
$template_desc = str_replace("{alphabet}", $key, $template_desc);

if (strstr($template_desc, '{manufacturer_loop_start}') && strstr($template_desc, '{manufacturer_loop_end}'))
{
	$template_sdata  = explode('{manufacturer_loop_start}', $template_desc);
	$template_start  = $template_sdata[0];
	$template_edata  = explode('{manufacturer_loop_end}', $template_sdata[1]);
	$template_end    = $template_edata[1];
	$template_middle = $template_edata[0];
}

$extraFieldName     = $extraField->getSectionFieldNameArray(10, 1, 1);
$replace_middledata = '';

if ($this->detail && $template_middle != "")
{
	// Limit the number of manufacturers shown
	$maxCount = $this->params->get('maxmanufacturer');

	if (count($this->detail) < $maxCount)
	{
		$maxCount = count($this->detail);
	}

	$value = array_filter($value);
	$count = count($value);

	if (!empty($value))
	{
		foreach ($value as $a => $row)
		{
			if (!empty($row))
			{
				if (is_array($row))
				{
					$replace_middledata .= '<div class="row-manufacture col-sm-3 col-xs-12">';
					$replace_middledata .= '<h2>' . $a . '</h2>';
					$replace_middledata .= '<ul>';

					foreach ($row as $k => $val)
					{
						$manproducts       = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $val->id . '&Itemid=' . $Itemid);
						$manufacturer_name = "<a href='" . $manproducts . "'><b>" . $val->name . "</b></a>";

						$manu_name  = $config->maxchar($manufacturer_name, MANUFACTURER_TITLE_MAX_CHARS, MANUFACTURER_TITLE_END_SUFFIX);
						$replace_middledata .= '<li>' . $manu_name . '</li>';
					}

					$replace_middledata .= '</ul>';
					$replace_middledata .= '</div>';
				}
				else
				{
					$mimg_tag = '{manufacturer_image}';
					$mh_thumb = MANUFACTURER_THUMB_HEIGHT;
					$mw_thumb = MANUFACTURER_THUMB_WIDTH;

					$link = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=detail&mid=' . $row->id . '&Itemid=' . $Itemid);

					$manproducts       = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $row->id . '&Itemid=' . $Itemid);
					$manufacturer_name = "<a href='" . $manproducts . "'><b>" . $row->name . "</b></a>";

					$middledata = $template_middle;
					$manu_name  = $config->maxchar($manufacturer_name, MANUFACTURER_TITLE_MAX_CHARS, MANUFACTURER_TITLE_END_SUFFIX);
					$middledata = str_replace("{manufacturer_name}", $manu_name, $middledata);

					// Extra field display
					$middledata = $producthelper->getExtraSectionTag($extraFieldName, $row->id, "10", $middledata);

					if (strstr($middledata, $mimg_tag))
					{
						$thum_image  = "";
						$media_image = $producthelper->getAdditionMediaImage($row->id, "manufacturer");

						for ($m = 0, $mn = count($media_image); $m < $mn; $m++)
						{
							if ($media_image[$m]->media_name && file_exists(REDSHOP_FRONT_IMAGES_RELPATH . "manufacturer/" . $media_image[$m]->media_name))
							{
								$altText = $producthelper->getAltText('manufacturer', $row->id);

								if (!$altText)
								{
									$altText = $media_image[$m]->media_name;
								}

								if (WATERMARK_MANUFACTURER_IMAGE || WATERMARK_MANUFACTURER_THUMB_IMAGE)
								{
									$manufacturer_img = $redhelper->watermark('manufacturer', $media_image[$m]->media_name, $mw_thumb, $mh_thumb, WATERMARK_MANUFACTURER_IMAGE);
								}
								else
								{
									$manufacturer_img = RedShopHelperImages::getImagePath(
															$media_image[$m]->media_name,
															'',
															'thumb',
															'manufacturer',
															$mw_thumb,
															$mh_thumb,
															USE_IMAGE_SIZE_SWAPPING
														);
								}

								if (PRODUCT_IS_LIGHTBOX == 1)
								{
									$thum_image = "<a title='" . $altText . "' class=\"modal\" href='" . REDSHOP_FRONT_IMAGES_ABSPATH . "manufacturer/" . $media_image[$m]->media_name . "'   rel=\"{handler: 'image', size: {}}\">
									<img alt='" . $altText . "' title='" . $altText . "' src='" . $manufacturer_img . "'></a>";
								}
								else
								{
									$thum_image = "<a title='" . $altText . "' href='" . $manproducts . "'>
									<img alt='" . $altText . "' title='" . $altText . "' src='" . $manufacturer_img . "'></a>";
								}
							}
						}

						$middledata = str_replace($mimg_tag, $thum_image, $middledata);
					}

					$middledata = str_replace("{manufacturer_description}", $row->desc, $middledata);
					$middledata = str_replace("{manufacturer_link}", $link, $middledata);
					$middledata = str_replace("{manufacturer_allproductslink}", $manproducts, $middledata);
					$middledata = str_replace("{manufacturer_allproductslink_lbl}", JText::_('COM_REDSHOP_MANUFACTURER_ALLPRODUCTSLINK_LBL'), $middledata);
					$middledata = str_replace("{manufacturer_link_lbl}", JText::_('COM_REDSHOP_MANUFACTURER_LINK_LBL'), $middledata);
					$replace_middledata .= $middledata;
				}
			}
		}
	}
	else
	{
		$replace_middledata = JText::sprintf('COM_REDSHOP_MANUFACTURER_NOT_HAVE_MANUFACTURER', $key);
	}
}

$template_desc = $template_start . $replace_middledata . $template_end;

$template_desc = str_replace("{print}", $print_tag, $template_desc);

if (strstr($template_desc, '{order_by}'))
{
	$orderby_form  = "<form name='orderby_form' action='' method='post'>" . JText::_('COM_REDSHOP_SELECT_ORDER_BY') . $this->lists['order_select'] . "</form>";
	$template_desc = str_replace("{order_by}", $orderby_form, $template_desc);
}

if (strstr($template_desc, '{pagination}'))
{
	if ($print)
	{
		$template_desc = str_replace("{pagination}", "", $template_desc);
	}
	else
	{
		$template_desc = str_replace("{pagination}", $this->pagination->getPagesLinks(), $template_desc);
	}
}

$template_desc = $redTemplate->parseredSHOPplugin($template_desc);
echo eval("?>" . $template_desc . "<?php ");
?>
		</div>
	<?php endforeach; ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery('input[name="keyword-manufacturer"]').on('keyup', function(){
			jQuery('ul#nav-manufacturer li').removeClass('active');
			jQuery('div.tab-content div.tab-pane').removeClass('active');
			jQuery('ul#nav-manufacturer li#li-ALL').addClass('active');
			jQuery('div.tab-content div#ALL').addClass('active');
			var json    = '<?php echo json_encode($result['ALL']); ?>';
			var arr     = jQuery.parseJSON(json);
			var keyword = jQuery(this).val();
			var new_arr = {};
			jQuery.each(arr, function(i, value){
				new_arr[i] = [];
				jQuery.each(value, function(k, val){
					if (val.name.toLowerCase().indexOf(keyword.toLowerCase()) > -1){
						var object = {};
						object[i] = val;
						new_arr[i].push(object);
					}
				});
			});

			var html = '';
			
			jQuery.each(new_arr, function(key, value){
				var check = Object.keys(value).length;
				if (check > 0){
					html += '<div class="row-manufacture col-sm-3 col-xs-12">'+'<h2>'+key+'</h2>'+'<ul>';
					jQuery.each(value, function(k, val){
						jQuery.each(val, function(i, data){
							html += '<li><a href="'+data.manufacturer_url+'"><b>' + data.name + '</b></a></li>';
						});		
					});
					html += '</ul></div>';	
				}
			});
			jQuery('.tab-content #ALL .manufacturer_box_wrapper').html('');
			jQuery('.tab-content #ALL .manufacturer_box_wrapper').append(html);
			jQuery('.manufacturer_box_wrapper .row-manufacture').responsiveEqualHeightGrid();
		});
    });
</script>
