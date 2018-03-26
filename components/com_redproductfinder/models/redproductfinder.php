<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER model
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
jimport( 'joomla.application.module.helper' );
jimport( 'joomla.html.parameter' );

class RedproductfinderModelRedproductfinder extends JModelForm
{
	private $_results = array();

	public function __construct()
	{

		$config = JComponentHelper::getParams('com_redproductfinder');

		$db = JFactory::getDBO();
		$module = JModuleHelper::getModule( 'redproductfinder' );

		if($module!='')
		{
			$params = new JParameter($module->params);
			$form=$params->get('form');

			$query = "SELECT f.dependency,f.id FROM  #__redproductfinder_forms f
	         where id='".$form."'
	          ORDER BY id";
			$db->setQuery($query);
			$frmdependancy = $db->loadObject();

			$check_dependency =$frmdependancy ->dependency;
		}
		else
		{
			$form=$config->get('form');

			$query = "SELECT f.dependency,f.id FROM  #__redproductfinder_forms f
	         where id='".$form."'
	          ORDER BY id";

			$db->setQuery($query);
			$frmdependancy = $db->loadObject();

			$check_dependency =$frmdependancy ->dependency;
		}

		parent::__construct();
	}

	public function getFindProducts()
	{
		global $mainframe,$context;
		$config = JComponentHelper::getParams('com_redproductfinder');
		$show_main_product=$config->get('show_main_product');
		$search_child_product=$config->get('search_child_product');
		$db = JFactory::getDBO();
		$post = JRequest::get('request');
		$from_startdate=JRequest::getVar('from_startdate');
		$to_enddate=JRequest::getVar('to_enddate');
		$month=JRequest::getVar('month');
		$mainmonth='';

		if($month!='')
		{
			$splitmonth=explode(",",$month);



			for($s=0;$s<count($splitmonth);$s++)
			{
				if($splitmonth[$s]!='')
				{
					$mnth=explode("-",$splitmonth[$s]);
					$mainmonth.=$mnth[0];
					$mainyear.=$mnth[2];


				}
			}
		}


		$fullyear = date("Y");
		//echo "<pre />";
		//print_r($post);
		//exit;
		$config = JComponentHelper::getParams('com_redproductfinder');

		$query = "SELECT id FROM #__redproductfinder_filters WHERE published=1";
		$this->_db->setQuery($query);
		$rs_filters = $this->_db->loadResultArray();
		if(count($rs_filters)>0)
			{$this->_is_filter_enable = true;}
		$tag = '';
		for($f=0;$f<count($rs_filters);$f++)
		{

			$tmp_tag = JRequest::getCmd('tag'.$rs_filters[$f]);
			if(is_array($tmp_tag))
			{
				$tag[] = implode("','",$tmp_tag);
			}
			else if($tmp_tag!="" && $tmp_tag!="0")
			{
				$tag[] = $tmp_tag;
			}
		}

		$finder_condition = "";

		$finder_filter_option = $config->get('redshop_filter_option');

		if($tag)
		{
			if(is_array($tag))
			{
				if(count($tag)>1 || $tag[0]!=0)
				{
					//$tag = implode("','",$tag);
					$finder_query = "SELECT product_id FROM #__redproductfinder_associations AS a,#__redproductfinder_association_tag AS at ";
					$finder_where = "";
					for($t=1;$t<count($tag);$t++)
					{
						$finder_query .= "LEFT JOIN #__redproductfinder_association_tag AS at".$t." ON at".$t.".association_id=at.association_id";
						$finder_where[] = " at".$t.".tag_id = '".$tag[$t]."'";
					}
					$finder_query .= " WHERE a.id=at.association_id AND at.tag_id = '".$tag[0]."'";
					if(is_array($finder_where))
						$finder_where = " AND ".implode(" AND ",$finder_where);
					$finder_query .= $finder_where;
					$this->_db->setQuery($finder_query);

					$finder_products = "";
					$rs = $this->_db->loadResultArray();
					if(!empty($rs))
					 	$finder_products = implode("','",$rs);

					$finder_condition = " AND p.product_id IN('".$finder_products."')";
					$this->_is_filter_enable = true;
				}
				if(count($tag)==1 && $tag[0]==0)
					$finder_condition = "";
			}
		}
		$filter_order= "";
		$finder_products='';
		if($finder_filter_option)
		{
			if($finder_products!="")
			{
			//echo $order_by;exit;
				$query = "SELECT p.product_id FROM ". $this->_table_prefix."product AS p "
				.", ".$this->_table_prefix."product_category_xref AS pc "
				." WHERE p.product_id = pc.product_id AND p.product_id IN('".$finder_products."') ORDER BY ".$order_by ;
				$this->_db->setQuery($query);
				$finder_products = $this->_db->loadResultArray();
				$finder_products = array_reverse($finder_products);
				$finder_products = implode("','",$finder_products);
				$filter_order =  " FIELD( p.product_id,'".$finder_products."') DESC, ";
			}
			$finder_condition = "";
		}

		$q = "SELECT COUNT(id) AS total FROM #__redproductfinder_types";
		$db->setQuery($q);
		$typecount = $db->loadResult();

		$q = "SELECT id  AS total FROM #__redproductfinder_types";
		$db->setQuery($q);
		$type_result = $db->loadResultArray();

		$q = "SELECT extrafield  FROM #__redproductfinder_types where type_select='Productfinder datepicker'";
		$db->setQuery($q);
		$finaltypetype_result = $db->loadObject();

		$query = "SELECT * FROM #__redshop_product where product_parent_id!=0";
		$db->setQuery($query);
		$childproducts = $db->loadObjectList();
		$fianlparentid='';
		for($k=0;$k<count($childproducts);$k++)
		{
			$fianlparentid.=$childproducts[$k]->product_id.",";
		}

		$condition = "";

		for ($i = 0; $i < $typecount; $i++)
		{
			if ($i > 0 && $i != $typecount)
			$condition .= "|| ";

			$condition .= "JRequest::getVar('type".$type_result[$i]."') != 0 ";
		}
		$myq='';
		// If any product associations exists (queries exists).
		//if (JRequest::getVar('type0') != 0 || JRequest::getVar('type1') != 0 || JRequest::getVar('type2') != 0)
		if ($condition)
		{
				// Select products with associations
				if(($from_startdate!='' && $to_enddate!='') || $month!='')
				{
					$myq=",t.*";
				}
				$q  = "SELECT ta.*, p.* ".$myq."
					  FROM #__redproductfinder_association_tag AS ta
					  LEFT JOIN #__redproductfinder_associations AS a ON a.id = ta.association_id
					  LEFT JOIN #__redshop_product AS p ON p.product_id = a.product_id ";



				if(($from_startdate!='' && $to_enddate!='') || $month!='')
				{


					if($search_child_product==1 && $show_main_product==1)
					{
						$q .=" AND p.product_id IN (SELECT product_parent_id FROM jos_redshop_product WHERE product_id IN (SELECT f.itemid FROM jos_redshop_fields_data AS f WHERE  f.fieldid='".$finaltypetype_result->extrafield."' AND f.itemid IN (".substr_replace($fianlparentid,"",-1).") AND ";

							$cnt=1;
							for($w=0;$w<count($splitmonth);$w++)
							{
								if($splitmonth[$w]!='')
								{

										$t=explode("-",$splitmonth[$w]);


										$fmonth=$t[0];
										$fyear=$t[2];

										//$fmonth=date('m',strtotime($t[0]));
										$q .=" FIND_IN_SET( '".$fmonth."', SUBSTRING(data_txt,4,2)) AND FIND_IN_SET( '".$fyear."', SUBSTRING(data_txt,7,4)) ";

										if(count($splitmonth)>$cnt && count($splitmonth)>1)
										{
											$q .=" OR ";
											$cnt++;
										}

								}
							}
							if(($from_startdate!='' && $to_enddate!='') && ($search_child_product==1 && $show_main_product==1))
							{
							$q .=" 	f.data_txt between '".trim($from_startdate)."' AND '".trim($to_enddate)."' AND  f.fieldid='".$finaltypetype_result->extrafield."'  ";
							}
						$q .=" ))";
						$q .=" LEFT JOIN #__redproductfinder_types  AS t ON t.id=ta.type_id ";
					}elseif(($search_child_product==0 && $show_main_product==1) || ($search_child_product==1 && $show_main_product==0) || ($search_child_product==0 && $show_main_product==0)){
						$q .=" LEFT JOIN #__redshop_fields_data AS f ON f.itemid=a.product_id LEFT JOIN #__redproductfinder_types  AS t ON t.id=ta.type_id ";
					}

				}
				if($from_startdate!='' && $to_enddate!='')
				{
				//$q .=" LEFT JOIN #__redshop_fields_data AS f ON f.itemid=a.product_id ";
				}
				$check_dependency = $config->get('check_dependency');
				$consider_all_tags = $config->get('consider_all_tags');
				$search_relation = $config->get('search_relation', 'AND');

				$dep_cond = array();

				for ($i = 0; $i < $typecount; $i++)
				{
					if (JRequest::getVar('type'.$type_result[$i]) != 0 || is_array(JRequest::getVar('type'.$type_result[$i])))
					{
						if($i!=0)
							$q .= " LEFT JOIN #__redproductfinder_association_tag AS t".$i." ON t".$i.".association_id=ta.association_id ";
					}
				}




				$wherevar = (JRequest::getVar('searchkey') != "" || JRequest::getVar('productprice') != "" || $condition) ? "WHERE " : "";
				if(($from_startdate!='' && $to_enddate!='') &&  ($search_child_product==0 && $show_main_product==1))
				{
					$wherevar .=" f.data_txt between '".trim($from_startdate)."' AND '".trim($to_enddate)."' AND  f.fieldid='".$finaltypetype_result->extrafield."' AND ";

				}elseif($month!='' )
				{
						$cnt=1;
						for($w=0;$w<count($splitmonth);$w++)
						{
							if($splitmonth[$w]!='')
							{
                                 if(($search_child_product==0 && $show_main_product==1) || ($search_child_product==1 && $show_main_product==0)  || ($search_child_product==0 && $show_main_product==0)){
									$t=explode("-",$splitmonth[$w]);

										$fmonth=$t[0];
										$fyear=$t[2];
									//$fmonth=date('m',strtotime($t[0]));
									$wherevar .=" FIND_IN_SET( '".$fmonth."', SUBSTRING(data_txt,4,2)) AND FIND_IN_SET( '".$fyear."', SUBSTRING(data_txt,7,4)) ";
									if(count($splitmonth)>$cnt && count($splitmonth)>1)
									{
										$wherevar .=" OR ";
										$cnt++;
									}
								}
							}
						}
						if(($search_child_product==0 && $show_main_product==1)  || ($search_child_product==1 && $show_main_product==0)  || ($search_child_product==0 && $show_main_product==0)){
						$wherevar .=" AND  f.fieldid='".$finaltypetype_result->extrafield."' AND ";
						}

				}

						if($search_child_product==1 && $show_main_product==0){
							$wherevar .=" p.product_parent_id!=0 AND ";
						}
						if($search_child_product==0 && $show_main_product==1){
							$wherevar .=" p.product_parent_id=0 AND ";
						}

				$q .= $wherevar;

				$q .= "a.published = '1' AND  p.product_id IS NOT NULL AND ";

				if (JRequest::getVar('searchkey'))
				$searchkey = "";
				$searchkey = JRequest::getVar('searchkey');
				if(trim($searchkey)!="")
				{
					$q .= "p.product_name LIKE '%".$searchkey."%' ";
					$q .= ($search_relation=='or') ? " OR ": " AND ";
					//$q .= ($search_relation=='and') ? " OR ": " AND ";
				}
				/*if (JRequest::getVar('productprice'))
					$productprice = "";*/
				$productprice = JRequest::getVar('productprice');
				if(trim($productprice)!="")
				{
					$q .= "p.product_price LIKE '%".$productprice."%' ";
					$q .= ($search_relation=='or') ? " OR ": " AND ";
				}

				$q .= " (";

				$or = false; //To determine if or should be added

				$condition = "";

				if($check_dependency)
				{
					if($consider_all_tags)
					{
						$dep_cond = array();
						for ($i = 0; $i < $typecount; $i++)
						{
							if(is_array(JRequest::getVar('type'.$type_result[$i])))
							{
								$chk_q = "";
								//Search for checkboxes
								foreach(JRequest::getVar('type'.$type_result[$i]) as $j => $value)
								{
									if ($value != 0)
									{
										if ($or == false) //Don't add or the first time
										{
											$or = true;
										}
										else //Only add "or" the second time
										{
											$chk_q .= " OR ";
										}
										if($i!=0)
											$chk_q .= "t".$i.".tag_id='".$value."' ";
										else
											$chk_q .= "ta.tag_id='".$value."' ";
									}
								}
								if($chk_q!="")
									$dep_cond[] = " ( ".$chk_q." ) ";
							}
							else if (JRequest::getVar('type'.$type_result[$i]) != 0)
							{
								if($i!=0)
									$dep_cond[] = "t".$i.".tag_id='".JRequest::getVar('type'.$type_result[$i])."' ";
								else
									$dep_cond[] = "ta.tag_id='".JRequest::getVar('type'.$type_result[$i])."' ";
							}
						}

						$q .= implode(" AND ",$dep_cond);
					}
					else
					{
						$dep_cond = "";
						for ($i = 0; $i < $typecount; $i++)
						{
							if (JRequest::getVar('type'.$type_result[$i]) != 0)
							{
								$dep_cond = "ta.tag_id='".JRequest::getVar('type'.$type_result[$i])."' ";
							}
						if(is_array(JRequest::getVar('type'.$type_result[$i])))
						{
							$chk_q = "";
								//Search for checkboxes
							foreach(JRequest::getVar('type'.$type_result[$i]) as $j => $value)
							{
								if ($value != 0)
								{
									if ($or == false) //Don't add or the first time
									{
										$or = true;
									}
									else //Only add "or" the second time
									{
										$chk_q .= " OR ";
									}
/*									if($i!=0)
										$chk_q .= "t".$i.".tag_id='".$value."' ";
									else*/
										$chk_q .= "ta.tag_id='".$value."' ";
								}
							}
							if($chk_q!="")
								$dep_cond = " ( ".$chk_q." ) ";
						}
						}
						$q .= $dep_cond;
					}
				}
				else
				{
					$dep_cond = array();
					for ($i = 0; $i < $typecount; $i++)
					{
						$or = false;
						if(is_array(JRequest::getVar('type'.$type_result[$i])))
						{
							$chk_q = "";
							//Search for checkboxes
							foreach(JRequest::getVar('type'.$type_result[$i]) as $j => $value)
							{
								if ($value != 0)
								{
									if ($or == false) //Don't add or the first time
									{
										$or = true;
									}
									else //Only add "or" the second time
									{
										$chk_q .= " OR ";
									}
									if($i!=0)
										$chk_q .= "t".$i.".tag_id='".$value."' ";
									else
										$chk_q .= "ta.tag_id='".$value."' ";
								}
							}
							if($chk_q!="")
								$dep_cond[] = " ( ".$chk_q." ) ";
						}
						else if (JRequest::getVar('type'.$type_result[$i]) != 0)
						{
							if($i!=0 && count($dep_cond)>0)
								$dep_cond[] = "t".$i.".tag_id='".JRequest::getVar('type'.$type_result[$i])."' ";
							else
								$dep_cond[] = "ta.tag_id='".JRequest::getVar('type'.$type_result[$i])."' ";
						}
					}
					if(count($dep_cond)<=0)
						 $dep_cond[] = "1=1";
					if($search_relation=='or')
						$q .= implode(" OR ",$dep_cond);
					else
						$q .= implode(" AND ",$dep_cond);
				}

				$q .= ") ";


				$q .= $finder_condition;

				if (substr(trim($q), -6, 3) == "AND") //If last three characters is "AND" (nothing is filled out)
				{
					//Replace string with full search string
					$q  = "SELECT p.* FROM #__redshop_product as p ";
					$q .= "WHERE ";
					if (JRequest::getVar('searchkey'))
					$searchkey = "";
					$searchkey = JRequest::getVar('searchkey');
					$q .= "p.product_name LIKE '%".$searchkey."%' AND ";
					if (JRequest::getVar('productprice'))
					$productprice = "";
					$productprice = JRequest::getVar('productprice');
					$q .= "p.product_price LIKE '%".$productprice."%' ";
					//$q .= "AND p.published = '1' ";
				}


				if (substr(trim($q), -3, 3) == "AND")
				{
					$q .= "order by ".$filter_order." ta.quality_score";
				}

				$order_by = JRequest::getVar('order_by');
				if($order_by!="")
				{
					if($order_by == "pc.ordering ASC")
					{
						$order_by = " order by a.ordering ASC";
					}else{
						$order_by = " order by ".$order_by."";
					}
				}else
				{
					$order_by = " order by p.product_name";
					//$order_by = "";
				}
				$q .= " AND p.published = '1' group by association_id".$order_by."";

				$db->setQuery($q);
				//echo $db->getQuery();
				$products = $db->loadObjectList();

				return $products;
		}
	}

	/**
	 * Show all types that have been created
	 */
	function getTypes()
	{
		$db = JFactory::getDBO();
		// Get all the fields based on the limits
		$query = "SELECT * FROM #__redproductfinder_types
				WHERE published = 1
				ORDER BY ordering";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get the tag names
	 */
	public function typeTags($id)
	{
		$db = JFactory::getDBO();
		$q = "SELECT tag_id as id, tag_name
			 FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
			 WHERE j.tag_id = t.id ";
		$q .= "AND j.type_id = '".$id."' ";
		$q .= "AND t.published = 1
			  ORDER BY t.ordering";
		$db->setQuery($q);
		return $db->loadObjectList();
	}
	/**
	 * Get the dependent tag names only
	 */
	public function dependent_typeTags($id,$dependent_id=array(),$dependet_typeid=0)
	{
		$db = JFactory::getDBO();
		if(is_array($dependent_id))
			$dependent_id = implode("','",$dependent_id);
		if($dependent_id!="" &&  $dependent_id!="0")
		{
			$q = "SELECT dependent_tags FROM #__redproductfinder_dependent_tag WHERE type_id='".$dependet_typeid."' AND tag_id IN('".$dependent_id."')";
			$db->setQuery($q);
			$rs = $db->loadResultArray();

			$depdents = implode(",",$rs);
			$depdents = explode(",",$depdents);
			$depdents = implode("','",$depdents);
			$q = "SELECT tag_id as id, tag_name
				 FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
				 WHERE j.tag_id = t.id ";
			$q .= "AND j.type_id = '".$id."' ";
			$q .= "AND t.id IN ('".$depdents."') ";
			$q .= "AND t.published = 1
				  ORDER BY t.ordering";
		}
		else
		{
			$q = "SELECT tag_id as id, tag_name
				 FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
				 WHERE j.tag_id = t.id ";
			$q .= "AND j.type_id = '".$id."' ";
			$q .= "AND t.published = 1
				  ORDER BY t.ordering";
		}
		$db->setQuery($q);
		return $db->loadObjectList();
	}
	/**
	 * Clean up foreign characters
	 */
	public function replace_accents($str)
	{
		$str = htmlentities($str, ENT_COMPAT, "UTF-8");
		$str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|elig|slash|ring);/','$1',$str);
		$str = str_replace(' ', '', $str);
		return html_entity_decode($str);
	}
	/**
	 * Show tag name
	 */
	public function getTagname($tag_ids='') {
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$query = "SELECT id AS tag_id,tag_name FROM #__redproductfinder_tags
				WHERE id IN (".$tag_ids.")";
		$db->setQuery($query);
		return $db->loadObjectlist();
	}

/**
	 * Show tag name
	 */
	public function getType($field_name='id',$type='types',$Assoc=0,$Assocby='id') {
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$q = "SELECT $field_name FROM #__redproductfinder_$type";
		$db->setQuery($q);

		if($Assoc)
			return $db->loadAssocList($Assocby);
		else
			return $db->loadObjectList();
	}

	public function getForm($data = array(), $loadData = true){}

	/**
	 * Get finder list
	 *
	 * @param   string  $form  Form id
	 *
	 * @return array
	 */
	public function getFinderList($form = '')
	{
		$list = array();
		$db = JFactory::getDBO();
		$finderConfig = JComponentHelper::getParams('com_redproductfinder');
		$searchRelation = $finderConfig->get('search_relation', 'AND');
		$types = array();
		$app = JFactory::getApplication();

		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__redproductfinder_types'))
		->where('published = 1');

		if ($form)
		{
			$query->where('form_id = ' . (int) $form);
		}

		$db->setQuery($query);

		if ($typeResults = $db->setQuery($query)->loadObjectList())
		{
			$productsInPriceFilter = array();
			$query->clear()
			->select('p.product_id')
			->from($db->qn('#__redshop_product', 'p'))
			->where('p.published = 1')
			->where('p.expired = 0');
			$db->setQuery($query);

			if ($productIds = $db->loadColumn())
			{
				$priceMin = $app->getUserState('finder.texpricemin', null);
				$priceMax = $app->getUserState('finder.texpricemax', null);

				if ($priceMin !== null && $priceMax !== null)
				{
					$productHelper = new producthelper;

					foreach ($productIds as $productId)
					{
						$productPrices = $productHelper->getProductNetPrice($productId);

						if ($productPrices['product_price'] >= $priceMin && $productPrices['product_price'] <= $priceMax)
						{
							$productsInPriceFilter[] = $productId;
						}
					}
				}
				else
				{
					$productsInPriceFilter = $productIds;
				}
			}

			foreach ($typeResults as $typeResult)
			{
				$types[$typeResult->id] = $app->getUserState('finder.type' . $typeResult->id);
			}

			foreach ($typeResults as $typeResult)
			{
				$subQuery = $db->getQuery(true)
				->select('COUNT(DISTINCT(p.product_id))')
				->from($db->qn('#__redshop_product', 'p'))
				->leftJoin($db->qn('#__redproductfinder_associations', 'rpfa') . ' ON rpfa.product_id = p.product_id')
				->leftJoin($db->qn('#__redproductfinder_association_tag', 'rpfat') . ' ON rpfat.association_id = rpfa.id')
				->leftJoin($db->qn('#__redproductfinder_tags', 'tags') . ' ON tags.id = rpfat.tag_id')
				//->leftJoin($db->qn('#__redshop_product_attribute', 'att') . ' ON att.product_id = p.product_id')
				//->leftJoin($db->qn('#__redshop_product_attribute_property', 'pr') . ' ON pr.attribute_id = att.attribute_id')
				//->leftJoin($db->qn('#__redshop_product_subattribute_color', 'sp') . ' ON sp.subattribute_id = pr.property_id')
				->where('p.published = 1')
				->where('p.expired = 0')
				//->where('sp.subattribute_published = 1')
				->where('rpfat.tag_id = tag.id');

				if (count($productsInPriceFilter) > 0)
				{
					$subQuery->where('p.product_id IN (' . implode(',', $productsInPriceFilter) . ')');
				}

				if (count($types) > 0)
				{
					$conditions = array();
					$and = '';

					foreach ($types as $key => $type)
					{
						if ($typeResult->id == $key)
						{
							continue;
						}

						if (is_array($type))
						{
							$condition = array();

							foreach ($type as $value)
							{
								$condition[] = 't' . (int) $key . '.tag_id = ' . (int) $value;
							}

							if (count($condition) > 0)
							{
								$conditions[] = '(' . implode(' AND ', $condition) . ')';
							}
						}
						elseif ($type)
						{
							$conditions[] = 't' . (int) $key . '.tag_id = ' . (int) $type;
						}
						else
						{
							continue;
						}

						$subQuery->leftJoin(
							$db->qn('#__redproductfinder_association_tag', 't' . (int) $key)
							. ' ON t' . (int) $key . '.association_id = rpfa.id');
					}

					if (count($conditions) > 0)
					{
						switch ($searchRelation)
						{
							case 'or':
								$and .= '(' . implode(' OR ', $conditions) . ') ';
								break;
							default:
								$and .= '(' . implode(' AND ', $conditions) . ') ';
						}

						$subQuery->where($and);
					}
				}

				// Names should only be compared when the tag is a size or a color
				if ($typeResult->id == '6')
				{
					//$subQuery->where('sp.subattribute_color_name = tags.tag_name');
				}

				$query->clear()
				->select(
					array(
						'tag.id', 'tag.tag_name', 'tag.parent_id',
						'(' . $subQuery . ') AS countProduct'
					)
				)
				->from($db->qn('#__redproductfinder_tags', 'tag'))
				->leftJoin($db->qn('#__redproductfinder_tag_type', 'ty') . ' ON ty.tag_id = tag.id')
				->where('ty.type_id = ' . (int) $typeResult->id)
				->where('tag.published = 1')
				->order('tag.parent_id asc')
				->order('tag.ordering asc')
				->order('tag.tag_name asc');
				$db->setQuery($query);

				if ($tags = $db->loadObjectList('id'))
				{
					foreach ($tags as $id => $tag)
					{
						if ($tag->parent_id != 0)
						{
							if (!isset($tags[$tag->parent_id]->childs))
							{
								$tags[$tag->parent_id]->childs = array();
							}

							$tags[$tag->parent_id]->childs[] = $tag;
							unset($tags[$tag->id]);
						}
					}

					foreach ($tags as $tag)
					{
						if (!isset($list[$typeResult->id]))
						{
							$list[$typeResult->id] = new stdClass;
						}

						$list[$typeResult->id]->type_name = $typeResult->type_name;
						$list[$typeResult->id]->parents[] = $tag;
					}
				}
			}
		}

		return $list;
	}
}
?>
