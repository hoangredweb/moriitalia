<?php
/**
 * @package    RedPRODUCTFINDER
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('helper', JPATH_SITE . '/modules/mod_redproductforms');

$data = RedproductfinderForms::filterForm($this->item);
$model = $this->getModel('forms');
$attributes = $model->getAttribute();
$attributeProperties = $model->getAttributeProperty();
$attributeSubProperties = $model->getAttributeSubProperty();
$param = JComponentHelper::getParams('com_redproductfinder');
$searchBy = $param->get('search_by');
$searchBox = $param->get('search_box');
$showMaxMin = $param->get('show_max_min');
$cid = 0;
$manufacturer_id = 0;
$template_id = $param->get('prod_template');
$formid = $param->get('form');
$filterPriceMin = $param->get("filter_price_min_value", 0);
$filterPriceMax = $param->get("filter_price_max_value", 100);
$calendarFormat = '%d-%m-%Y';
?>
<div class="">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder&view=findproducts"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<?php if ($searchBox == 1) : ?>
		<div class="row-fluid">
			<input type="text" name="redform[keyword]" value="">
		</div>
		<?php endif; ?>
		<div class="row-fluid">
			<div class="span9">
			<?php if ($searchBy == 0) : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($data as $key => $value) :?>
						<div id='typename-<?php echo $value["typeid"];?>' class='<?php echo $type["class_name"] ?>'>
							<h2><?php echo $value["typename"];?></h2>
							<?php if ($value['typeselect'] == 'checkbox') : ?>
								<ul class='taglist' style="list-style: none">
									<?php foreach ($value["tags"] as $k_t => $tag) :?>
										<li>
											<label>
											<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'><input type="checkbox" name="redform[<?php echo $value["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
											<span class='tagname'><?php 
												if (strstr($tag["tagname"], '{more}')) :
													$tag["tagname"] = str_replace("{more}", "&gt;", $tag["tagname"]);
												endif;
												if (strstr($tag["tagname"], '{less}')) :
													$tag["tagname"] = str_replace("{less}", "&lt;", $tag["tagname"]);
												endif;
												 	echo $tag["tagname"]; ?></span>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php elseif ($value['typeselect'] == 'radio') : ?>
								<ul class='taglist' style="list-style: none">
									<?php foreach ($value["tags"] as $k_t => $tag) :?>
										<li>
											<label>
											<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'><input type="radio" name="redform[<?php echo $value["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
											<span class='tagname'><?php 
												if (strstr($tag["tagname"], '{more}')) :
													$tag["tagname"] = str_replace("{more}", "&gt;", $tag["tagname"]);
												endif;
												if (strstr($tag["tagname"], '{less}')) :
													$tag["tagname"] = str_replace("{less}", "&lt;", $tag["tagname"]);
												endif;
												 	echo $tag["tagname"]; ?></span>
											</label>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php elseif ($value['typeselect'] == 'generic') : ?>
								<select class="" name="redform[<?php echo $value["typeid"]?>][tags][]">
									<option name="" value=""><?php echo JText::_("COM_REDSHOP_SELECT"); ?> <?php echo $value['typename']; ?></option>
									<?php foreach ($value["tags"] as $k_t => $tag) :?>
										<option name="" value="<?php echo $tag["tagid"]; ?>"><?php 
												if (strstr($tag["tagname"], '{more}')) :
													$tag["tagname"] = str_replace("{more}", "&gt;", $tag["tagname"]);
												endif;
												if (strstr($tag["tagname"], '{less}')) :
													$tag["tagname"] = str_replace("{less}", "&lt;", $tag["tagname"]);
												endif;
												 	echo $tag["tagname"]; ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_FROM"); ?></span>
								<span>
								<?php
									$sdate = "";
									if ($sdate != "") :
										$sdate = date("d-m-Y", $sdate);
									endif;

									echo JHtml::_(
										'calendar',
										$sdate,
										'redform[' . $value["typeid"] . '][from]',
										'from',
										$calendarFormat,
										array('class' => 'input-small', 'size' => '15',  'maxlength' => '19')
									);
								?>
								</span>
								<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_TO"); ?></span>
								<span>
								<?php
									$sdate = "";
									if ($sdate != "") :
										$sdate = date("d-m-Y", $sdate);
									endif;

									echo JHtml::_(
										'calendar',
										$sdate,
										'redform[' . $value["typeid"] . '][to]',
										'to',
										$calendarFormat,
										array('class' => 'input-small', 'size' => '15',  'maxlength' => '19')
									);
								?>
								</span>
							<?php endif; ?>
						</div>
						<input type="hidden" name="redform[<?php echo $value["typeid"]?>][typeid]" value="<?php echo $value["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php else : ?>
				<div class="row-fluid form-horizontal-desktop">
			<?php 
			?>
					<?php foreach($attributes as $k_a => $attribute) :?>
						<div id='typename-<?php echo $attribute->attribute_id;?>'>
							<h2><?php echo $attribute->attribute_name;?></h2>
							<ul class='taglist' style="list-style: none">
								<?php foreach($attributeProperties as $k_p => $property) :?>
									<?php
									if ($property->attribute_name == $attribute->attribute_name) : ?>
										<li>
											<label>
											<span class='taginput'><input type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][]" value="<?php echo $property->property_name; ?>"></span>
											<span class='tagname'><?php echo $property->property_name; ?></span>
											</label>
											<ul class='taglist' style="list-style: none">
											<?php if (!empty($attributeSubProperties)) : ?>
												<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
													<?php
														if ($subProperty->property_name == $property->property_name) :
															$newArr[$subProperty->property_name][] = $subProperty->subattribute_color_name;
														endif; ?>
												<?php endforeach; ?>
												<?php foreach($newArr as $key => $value) :?>
													<?php if ($key == $property->property_name) : ?>
													<?php foreach(array_unique($value) as $key => $valueSub) :?>
													<li>
														<label>
														<span class='taginput'>
														<input type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][subproperty][<?php echo $property->property_name;?>][]" value="<?php echo $valueSub; ?>"></span>
														<span class='tagname'><?php echo $valueSub; ?></span>
														</label>
													</li>
													<?php endforeach; ?>
													<?php endif; ?>
												<?php endforeach; ?>
											<?php endif; ?>
											</ul>
										</li>
									<?php endif; ?>
								<?php endforeach;?>
							</ul>
						</div>
					<?php endforeach;?>
				</div>
			<?php endif; ?>
			</div>
		</div>
		<?php if ($showMaxMin == 1) : ?>
		<div  class="row-fluid">
			<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_DEFAULT_MIN"); ?></span><span><input type="number" min="0" name="redform[filterprice][min]" value="<?php echo $filterPriceMin ?>" /></span>
			<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_DEFAULT_MAX")?></span><span><input type="number" min="0" name="redform[filterprice][max]" value="<?php echo $filterPriceMax ?>" /></span>
		</div>
		<?php endif;?>
	</div>

	<input type="submit" name="submit" value="<?php echo JText::_("COM_REDPRODUCTFINDER_FORM_FORMS_SUBMIT_FORM"); ?>" />
	<input type="hidden" name="view" value="findproducts" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $template_id;?>" />
	<input type="hidden" name="redform[cid]" value="<?php echo $cid;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php echo $manufacturer_id;?>" />
	<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
