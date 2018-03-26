<?php
/**
 * @package    RedPRODUCTFINDER.Module
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/* No direct access */
defined('_JEXEC') or die;
?>
<div class="<?php echo $module_class_sfx; ?>">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder&view=findproducts&Itemid=$itemId"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
			<?php if ($searchBy == 0) : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($lists as $k => $type) :?>
						<div id='typename-<?php echo $type["typeid"];?>' class='<?php echo $type["class_name"] ?>'>
							<h2><?php echo $type["typename"];?></h2>
							<?php if($type['typeselect'] == 'checkbox') : ?>
								<ul class='taglist' style="list-style: none">
									<?php foreach ($type["tags"] as $k_t => $tag) :?>
										<li>
											<label>
											<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'>
											<input <?php
											if (!empty($values)):
												foreach ($values as $key => $value) :
													if (!empty($value['typeid']) && $value['typeid'] == $type['typeid']) :
														if (isset($value['tags'])) :
															foreach ($value['tags'] as $keyTag) :
																if ($keyTag == $tag["tagid"])
																	echo 'checked="checked"';
																else
																	echo '';
															endforeach;
														endif;
													endif;
												endforeach;
											endif; ?>
											 type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
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
							<?php elseif ($type['typeselect'] == 'radio') : ?>
								<ul class='taglist' style="list-style: none">
									<?php foreach ($type["tags"] as $k_t => $tag) :?>
										<li>
											<label>
											<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'>
											<input <?php
											if (isset($value)):
												foreach ($values as $key => $value) :
													if (!empty($value['typeid']) && $value['typeid'] == $type['typeid']) :
														if (isset($value['tags'])) :
															foreach ($value['tags'] as $keyTag) :
																if ($keyTag == $tag["tagid"])
																	echo 'checked="checked"';
																else
																	echo '';
															endforeach;
														endif;
													endif;
												endforeach;
											endif; ?>
											 type="radio" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
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
							<?php elseif ($type['typeselect'] == 'generic') : ?>
								<select class="span12" name="redform[<?php echo $type["typeid"]?>][tags][]">
									<option value=""><?php echo JText::_("COM_REDSHOP_SELECT"); ?> <?php echo $type['typename']; ?></option>
									<?php foreach ($type["tags"] as $k_t => $tag) :?>
										<option <?php
										if (isset($value)):
											foreach ($values as $key => $value) :
												if (!empty($value['typeid']) && $value['typeid'] == $type['typeid']) :
													if (isset($value['tags'])) :
														foreach ($value['tags'] as $keyTag) :
															if ($keyTag == $tag["tagid"])
																echo 'selected';
															else
																echo '';
														endforeach;
													endif;
												endif;
											endforeach;
										endif;?>
										value="<?php echo $tag["tagid"]; ?>"><?php
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
								<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_FROM"); ?></label>
								<?php
									$dateFrom = "";
									$dateTo = "";
									if (isset($value)):
										foreach ($values as $key => $value) :
											if (isset($value['from']) && isset($value['to'])) :
												$dateFrom = $value['from'];
												$dateTo = $value['to'];
											endif;
										endforeach;
									endif;

									echo JHtml::_(
										'calendar',
										$dateFrom,
										'redform[' . $type["typeid"] . '][from]',
										'from',
										$calendarFormat,
										array('class' => 'input-small', 'size' => '15',  'maxlength' => '19')
									);
								?><br>
								<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_TO"); ?></label>
								<?php
									echo JHtml::_(
										'calendar',
										$dateTo,
										'redform[' . $type["typeid"] . '][to]',
										'to',
										$calendarFormat,
										array('class' => 'input-small', 'size' => '15',  'maxlength' => '19')
									);
								?>
							<?php endif; ?>
						</div>
						<input type="hidden" name="redform[<?php echo $type["typeid"]?>][typeid]" value="<?php echo $type["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php else : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php if ($searchBox == 1): ?>
					<div id="keyword">
						<label><strong><?php echo $countProduct . ' items for ' .  $keyword ?></strong></label>
					</div>
					<?php endif; ?>
					<?php if ($showAttribute == 1): ?>
					<?php foreach($attributes as $k_a => $attribute) :?>
							<div id='typename-<?php echo $attribute->attribute_id;?>'>
								<label><?php echo $attribute->attribute_name;?></label>
								<ul class='taglist'>
									<?php foreach($attributeProperties as $k_p => $property) :?>
										<?php
										if ($property->attribute_name == $attribute->attribute_name) : ?>
											<li>
												<label>
												<span class='taginput' data-aliases='<?php echo $attribute->attribute_name;?>'>
												<input
												<?php if (isset($pk['attribute'])) : ?>
													<?php foreach ($attributeCheck as $att) : unset($att["subproperty"]); ?>
														<?php foreach ($att as $pro) : ?>
															<?php if ($pro == $property->property_name) : ?>
																<?php echo 'checked' ?>
															<?php endif; ?>
														<?php endforeach ?>
													<?php endforeach ?>
												<?php endif; ?>
												<?php if (isset($subName)) : ?>
													<?php foreach ($subName as $key => $sub) : ?>
														<?php if ($key == $property->property_name) : ?>
															<?php echo 'checked' ?>
														<?php endif; ?>
													<?php endforeach ?>
												<?php endif; ?>
												type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][]" value="<?php echo $property->property_name; ?>">
												</span>
												<span class='tagname'><?php if (!empty($property->property_image)): echo '<img src="' . JUri::root() . 'components/com_redshop/assets/images/product_attributes/' . $property->property_image . '">'; else: echo $property->property_name; endif;?></span>
												</label>
												<ul class='taglist'>
												<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
													<?php
														if ($subProperty->property_name == $property->property_name) :
															$newArr[$subProperty->property_name][] = $subProperty->subattribute_color_name;
													?>
													<?php endif; ?>
												<?php endforeach;?>
												<?php  if (isset($newArr)) :
												foreach($newArr as $key => $value) :?>
													<?php if ($key == $property->property_name) : ?>
													<?php foreach(array_unique($value) as $key => $valueSub) :?>
													<li>
														<label>
														<span class='taginput' data-aliases='<?php echo $property->property_name;?>'>
														<input
														<?php if (isset($subName)) : ?>
															<?php foreach ($subName as $key => $sub) : ?>
																<?php foreach ($sub as $s) : ?>
																	<?php if ($property->property_name == $key ) : ?>
																		<?php if ($s == $valueSub) : ?>
																			<?php echo 'checked' ?>
																		<?php endif; ?>
																	<?php endif; ?>
																<?php endforeach ?>
															<?php endforeach ?>
														<?php endif; ?>
														type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][subproperty][<?php echo $property->property_name; ?>][]" value="<?php echo $valueSub; ?>">
														</span>
														<span class='tagname'><?php echo $valueSub; ?></span>
														</label>
													</li>
													<?php endforeach; ?>
													<?php endif; ?>
												<?php endforeach;
												endif;?>
												</ul>
											</li>
										<?php endif; ?>
									<?php endforeach;?>
								</ul>
							</div>
						<?php endforeach;?>
						<?php endif; ?>
					</div>
			<?php endif; ?>
			<?php if ($searchManu == 1): ?>
				<div id='manu'>
					<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_MANUFACTURER"); ?></label>
					<ul class='taglist'>
						<?php foreach ($manufacturer as $m => $manu) : ?>
							<li style="list-style: none">
								<label>
									<span class='taginput' data-aliases='manu-<?php echo $manu->manufacturer_id;?>'>
									<input
									<?php if (isset($manuCheck)) : ?>
											<?php foreach ($manuCheck as $mc => $check) : ?>
												<?php if ($check == $manu->manufacturer_id) : ?>
													<?php echo 'checked' ?>
												<?php endif; ?>
											<?php endforeach ?>
										<?php endif; ?>
									type="checkbox" name="redform[manufacturer][]" value="<?php echo $manu->manufacturer_id ?>">
									</span>
									<span class='tagname'><?php echo $manu->manufacturer_name; ?></span>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>
				<?php if ($isAvailable == 1): ?>
				<div id='available'>
					<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_AVAILABLE"); ?></label>
					<label>
						<span class='taginput' data-aliases='available'>
							<input
							<?php if (isset($available)) : ?>
						  		<?php echo "checked"; ?>
						  	<?php endif; ?>
							type="checkbox" value="1" name="redform[available]">
						</span>
					</label>
				</div>
				<?php endif; ?>
				<?php if ($orderBy == 1): ?>
				<div id='order_by'>
					<?php echo $lists_order['order_select']; ?>
				</div>
				<?php endif; ?>
				<?php if ($searchCat == 1): ?>
				<div id="categories">
					<select name="redform[category]">
					<?php foreach ($categories as $key => $cat) :?>
						<option <?php if (isset($category) && $category == $cat->category_id): echo 'selected'; endif; ?> value="<?php echo $cat->category_id ?>"><?php echo $cat->category_name ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ($showMaxMin == 1) : ?>
		<div class="row-fluid">
			<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MIN"); ?></label><span><input type="text" pattern="^\d*(\.\d{2}$)?" class="span12" name="redform[filterprice][min]" value="<?php echo $rangeMin; ?>" required/></span><br>
			<label><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MAX"); ?></label><span><input type="text" pattern="^\d*(\.\d{2}$)?" class="span12" name="redform[filterprice][max]" value="<?php echo $rangeMax; ?>" required/></span>
		</div>
		<?php endif; ?>
	</div>
	<input type="submit" name="submit" value="<?php echo JText::_("MOD_REDPRODUCTFORM_FORM_FORMS_SUBMIT_FORM"); ?>" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $templateId;?>" />
	<input type="hidden" name="redform[cid]" value="<?php if ($catId) echo $catId; elseif ($count > 0) echo $catFormId;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php if ($manufacturer_id) echo $manufacturer_id; elseif ($count > 0) echo $manufacturerId;?>" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="limit" value="5" />
	<input type="hidden" name="redform[keyword]" value="<?php echo $keyword;?>" />
	<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>" >
</form>
</div>
