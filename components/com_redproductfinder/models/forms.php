<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Forms Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class RedproductfinderModelForms extends RModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');

		$this->setState('form.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * This method will get all item data
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('form.id');

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("f.id as formid,t.*, t.id as typeid,tg.*, tg.id as tagid")
			->from($db->qn("#__redproductfinder_forms", "f"))
			->join("INNER", $db->qn("#__redproductfinder_types", "t") . " ON t.form_id = f.id")
			->join("INNER", $db->qn("#__redproductfinder_tag_type", "tt") . " ON tt.type_id = t.id")
			->join("LEFT", $db->qn("#__redproductfinder_tags", "tg") . " ON tg.id = tt.tag_id")
			->where($db->qn("f.id") . "=" . $pk)
			->where($db->qn("t.published") . " = 1")
			->where($db->qn("tg.published") . " = 1")
			->order("t.ordering ASC, tg.ordering ASC");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute name
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttribute($pk = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pa.attribute_name, pa.attribute_id")
		->from($db->qn("#__redshop_product_attribute", "pa"))
		->where($db->qn("pa.attribute_id") . "IN (SELECT attribute_id FROM #__redshop_product_attribute_property)")
		->group($db->qn("pa.attribute_name"))
		->order($db->escape("pa.attribute_name DESC"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute name after submit
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttributeSubmit($pk = null)
	{
		JArrayHelper::toInteger($pk);
		$list = implode(',', (array) $pk);
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pa.attribute_name, pa.attribute_id")
		->from($db->qn("#__redshop_product_attribute", "pa"))
		->where($db->qn("pa.attribute_id") . "IN (SELECT attribute_id FROM #__redshop_product_attribute_property)")
		->where($db->qn('pa.product_id') . ' IN (' . $list . ')')
		->group($db->qn("pa.attribute_name"))
		->order($db->escape("pa.attribute_name DESC"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute property name
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttributeProperty($pk = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pp.property_name, pp.attribute_id, pp.property_id, pa.attribute_name, pp.property_image")
		->from($db->qn("#__redshop_product_attribute_property", "pp"))
		->join("LEFT", $db->qn("#__redshop_product_attribute", "pa") . " ON pp.attribute_id = pa.attribute_id")
		->group($db->qn("pp.property_name"))
		->order($db->escape("pp.property_name DESC"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute property name after submit
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttributePropertySubmit($pk = null)
	{
		JArrayHelper::toInteger($pk);
		$list = implode(',', (array) $pk);
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pp.property_name, pp.attribute_id, pp.property_id, pa.attribute_name, pp.property_image")
		->from($db->qn("#__redshop_product_attribute_property", "pp"))
		->join("LEFT", $db->qn("#__redshop_product_attribute", "pa") . " ON pp.attribute_id = pa.attribute_id")
		->where($db->qn('pa.product_id') . ' IN (' . $list . ')')
		->group($db->qn("pp.property_name"))
		->order($db->escape("pp.property_name DESC"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute sub property name
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttributeSubProperty($pk = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("ps.subattribute_color_name, ps.subattribute_id, pp.property_name")
		->from($db->qn("#__redshop_product_subattribute_color", "ps"))
		->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp") . " ON ps.subattribute_id = pp.property_id");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all manufacturer after submit
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getManufacturerSubmit($pk = null)
	{
		$list = implode(',', (array) $pk);
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("m.manufacturer_id, m.manufacturer_name")
		->from($db->qn("#__redshop_manufacturer", "m"))
		->leftjoin($db->qn('#__redshop_product', 'p') . ' ON ' . $db->qn('p.manufacturer_id') . ' = ' . $db->qn('m.manufacturer_id'))
		->where($db->qn('m.published') . ' = 1')
		->where($db->qn('p.product_id') . ' IN (' . $list . ')')
		->order($db->escape("m.ordering ASC"))
		->group($db->qn('m.manufacturer_id'));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * Retrieve a list of article
	 *
	 * @param   $catId  category id
	 *
	 * @return  mixed
	 */
	public function getManufacturer($catId = null)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('m.media_name, ma.manufacturer_name, ma.manufacturer_id')
			->from($db->qn('#__redshop_manufacturer', 'ma'))
			->leftJoin($db->qn('#__redshop_media', 'm') . ' ON m.section_id = ma.manufacturer_id')
			->where('m.media_section = ' . $db->q('manufacturer'))
			->where('m.published = 1')
			->where('ma.published = 1');

		if (!empty($catId))
		{
			$manuList = $this->getProductIds($catId);

			if (!empty($manuList))
			{
				$query->where($db->qn('ma.manufacturer_id') . ' IN (' . implode(',', $manuList) . ')');
			}
		}

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Get product id list
	 *
	 * @param   $catId  category id
	 *
	 * @return  mixed
	 */
	private function getProductIds($catId)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('product_id'))
			->from($db->qn('#__redshop_product_category_xref'))
			->where($db->qn('category_id') . ' = ' . $db->q((int) $catId));

		$productIds = $db->setQuery($query)->loadColumn();

		if (!empty($productIds))
		{
			$query = $db->getQuery(true)
				->select($db->qn('manufacturer_id'))
				->from($db->qn('#__redshop_product'))
				->where($db->qn('product_id') . ' IN (' . implode(',', $productIds) . ')');

			return $db->setQuery($query)->loadColumn();
		}

		return array();
	}

	/**
	 * Retrieve a list of article
	 *
	 * @param   $catId  category id
	 *
	 * @return  mixed
	 */
	public function getManufacturerOnSale($manuList = NULL)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('m.media_name, ma.manufacturer_name, ma.manufacturer_id')
			->from($db->qn('#__redshop_manufacturer', 'ma'))
			->leftJoin($db->qn('#__redshop_media', 'm') . ' ON m.section_id = ma.manufacturer_id')
			->where('m.media_section = ' . $db->q('manufacturer'))
			->where('m.published = 1')
			->where('ma.published = 1');

		if (!empty($manuList))
		{
			$query->where($db->qn('ma.manufacturer_id') . ' IN (' . implode(',', $manuList) . ')');
		}

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Retrieve a list of article
	 *
	 * @param   $catId  category id
	 *
	 * @return  mixed
	 */
	public function getManufacturerById($manufacturerId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('m.media_name, ma.manufacturer_name, ma.manufacturer_id')
			->from($db->qn('#__redshop_manufacturer', 'ma'))
			->leftJoin($db->qn('#__redshop_media', 'm') . ' ON m.section_id = ma.manufacturer_id')
			->where('m.media_section = ' . $db->q('manufacturer'))
			->where('m.published = 1')
			->where($db->qn('ma.manufacturer_id') . ' = ' . $db->q((int) $manufacturerId))
			->where('ma.published = 1');

		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Retrieve a list of article
	 *
	 * @param   $catId  category id
	 *
	 * @return  mixed
	 */
	public function getProductByManu($manufacturerId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('product_id'))
			->from($db->qn('#__redshop_product'))
			->where($db->qn('manufacturer_id') . ' = ' . $db->q((int) $manufacturerId));

		return $db->setQuery($query)->loadColumn();
	}
}
