<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;



/**
 * Class wishlistModelwishlist
 *
 * @package     RedSHOP.Frontend
 * @subpackage  Model
 * @since       1.0
 */
class RedshopModelWishlist extends RedshopModelWishlistDefault
{
	/**
	 * Method for save wishlist.
	 *
	 * @param   array  $data  List of data
	 *
	 * @return  boolean       True if success. False otherwise.
	 */
	public function savewishlist($data)
	{
		if (empty($data))
		{
			$input = JFactory::getApplication()->input;

			$wishlistIds     = $input->get('wishlist_id', array(), 'Array');
			$productId       = $input->getInt('product_id', 0);
			$attributeIds    = $input->getString('attribute_id', 0);
			$propertyIds     = $input->getString('property_id', 0);
			$subAttributeIds = $input->getString('subattribute_id', 0);
		}
		else
		{
			$wishlistIds     = isset($data['wishlist_id']) ? $data['wishlist_id'] : array();
			$productId       = isset($data['product_id']) ? $data['product_id'] : 0;
			$attributeIds    = isset($data['attribute_id']) ? $data['attribute_id'] : '';
			$propertyIds     = isset($data['property_id']) ? $data['property_id'] : '';
			$subAttributeIds = isset($data['subattribute_id']) ? $data['subattribute_id'] : '';
		}

		if (empty($wishlistIds))
		{
			return false;
		}

		$attributeIds    = explode('##', $attributeIds);
		$propertyIds     = explode('##', $propertyIds);
		$subAttributeIds = explode('##', $subAttributeIds);

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redshop/tables');

		foreach ($wishlistIds as $wishlistId)
		{
			/** @var RedshopTableWishlist_Product $table */
			$wishlistProductTable = JTable::getInstance('Wishlist_Product', 'RedshopTable');

			$tmpData = array(
				'wishlist_id' => $wishlistId,
				'product_id' => $productId
			);

			// If wishlist product has already exist. Skip it.
			if ($wishlistProductTable->load($tmpData))
			{
				continue;
			}

			$tmpData['cdate'] = time();

			if (!$wishlistProductTable->save($tmpData))
			{
				throw new Exception($wishlistProductTable->getError());
			}

			// If there are not attribute with product.
			if (empty($attributeIds[0]))
			{
				return true;
			}

			foreach ($attributeIds as $index => $attributeId)
			{
				/** @var RedshopTableWishlist_Product_Item $table */
				$wishlistProductItemTable = JTable::getInstance('Wishlist_Product_Item', 'RedshopTable');

				$tmpData = array(
					'ref_id'       => (int) $wishlistProductTable->get('wishlist_product_id'),
					'attribute_id' => $attributeId
				);

				if (!empty($propertyIds[$index]))
				{
					$tmpData['property_id'] = (int) $propertyIds[$index];
				}

				if (!empty($subAttributeIds[$index]))
				{
					$tmpData['subattribute_id'] = (int) $subAttributeIds[$index];
				}

				// If wishlist product item has already exist. Skip it.
				if ($wishlistProductItemTable->load($tmpData))
				{
					continue;
				}

				if (!$wishlistProductItemTable->save($tmpData))
				{
					throw new Exception($wishlistProductItemTable->getError());
				}
			}
		}

		return true;
	}
}