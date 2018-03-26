<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_BASE') or die;

/**
 * Assets helper
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Assets
 * @since       2.2.0
 *
 */
class ReditemHelperAssets
{
	/**
	 * Get redITEM root asset.
	 *
	 * @param   boolean  $rebuild  Rebuild asset if root is missing.
	 *
	 * @return  int  Root asset id.
	 */
	public static function getRoot($rebuild = true)
	{
		$root = self::getByName('com_reditem');

		if ($root)
		{
			$root = (int) $root->id;
		}
		// If root is missing and $rebuild is true, rebuild it!
		elseif ($rebuild)
		{
			$assets = JTable::getInstance('Asset', 'JTable');

			if ($assets)
			{
				$assets->save(
					array (
						'parent_id' => 1,
						'level'     => 1,
						'name'      => 'com_reditem',
						'rules'     => '{' .
							'"core.admin":[],"core.manage":[],"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.searchengine":[],' .
							'"category.view":[],"category.edit":[],"category.edit.own":[],"category.create":[],"category.delete":[],' .
							'"item.view":[],"item.create":[],"item.delete":[],"item.edit":[],"item.edit.own":[]' .
							'}'
					)
				);
				$root = (int) $assets->id;
			}
		}

		// If we are still missing our asset, return root one
		return (!empty($root)) ? $root : 1;
	}

	/**
	 * Function for rebuilding current types assets.
	 *
	 * @param   array  $ids  Types to rebuild.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @throws  RuntimeException
	 */
	public static function rebuildTypes($ids = array())
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$root  = self::getRoot();

		// Get current assets
		$currentAssets = self::getByName('com_reditem.type.%', true);

		// Get all types
		$query->clear()
			->select('*')
			->from($db->qn('#__reditem_types'));

		if (!empty($ids))
		{
			$query->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');
		}

		$allTypes = $db->setQuery($query)->loadObjectList('id');

		// Start a transaction so we won't lose data on failure
		$db->transactionStart();

		// Start rebuild process
		try
		{
			// Clear current type assets
			$currentIds = array_keys($currentAssets);

			if (!empty($currentIds))
			{
				self::delete(null, $currentIds);
			}

			foreach ($allTypes as $id => $type)
			{
				$rules    = self::setObjectRules($type, $currentAssets, '{}');
				$name     = 'com_reditem.type.' . $id;
				$newAsset = self::saveNewAsset($root, $name, $rules);

				if (!empty($newAsset) && isset($newAsset->id))
				{
					// Update type with the new asset id.
					$query->clear()
						->update($db->qn('#__reditem_types'))
						->set($db->qn('asset_id') . ' = ' . (int) $newAsset->id)
						->where($db->qn('id') . ' = ' . (int) $id);
					$db->setQuery($query)->execute();
				}
				else
				{
					throw new Exception('Can\'t find new asset in the table!', 500);
				}
			}
		}
		// Error on recreating asset, rollback everything!
		catch (Exception $e)
		{
			$db->transactionRollback();

			// Inform the client with the error message
			throw new RuntimeException($e->getMessage());
		}

		// All went ok, lets commit our changes
		$db->transactionCommit();

		return true;
	}

	/**
	 * Function for rebuilding current items assets.
	 *
	 * @param   array  $ids  Items to rebuild.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @throws  RuntimeException
	 */
	public static function rebuildItems($ids = array())
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$root  = self::getRoot();

		// Get current assets
		$currentAssets = self::getByName('com_reditem.item.%', true);

		// Get all types
		$query->clear()
			->select(
				array (
					$db->qn('i.id', 'id'),
					$db->qn('i.asset_id', 'asset_id'),
					$db->qn('t.asset_id', 'parent')
				)
			)
			->from($db->qn('#__reditem_items', 'i'))
			->innerJoin($db->qn('#__reditem_types', 't') . ' ON ' . $db->qn('i.type_id') . ' = ' . $db->qn('t.id'));

		if (!empty($ids))
		{
			$query->where($db->qn('i.id') . ' IN (' . implode(',', $ids) . ')');
		}

		$allItems = $db->setQuery($query)->loadObjectList('id');

		// Start a transaction so we won't lose data on failure
		$db->transactionStart();

		// Start rebuild process
		try
		{
			// Clear current type assets
			$currentIds = array_keys($currentAssets);

			if (!empty($currentIds))
			{
				self::delete(null, $currentIds);
			}

			foreach ($allItems as $id => $item)
			{
				$rules = self::setObjectRules($item, $currentAssets, '{}');
				$name  = 'com_reditem.item.' . $id;

				if ($item->parent)
				{
					$newAsset = self::saveNewAsset($item->parent, $name, $rules);
				}
				else
				{
					$newAsset = self::saveNewAsset($root, $name, $rules);
				}

				if (!empty($newAsset) && isset($newAsset->id))
				{
					// Update item with the new asset id.
					$query->clear()
						->update($db->qn('#__reditem_items'))
						->set($db->qn('asset_id') . ' = ' . (int) $newAsset->id)
						->where($db->qn('id') . ' = ' . (int) $id);
					$db->setQuery($query)->execute();
				}
				else
				{
					throw new Exception('Can\'t find new asset in the table!', 500);
				}
			}
		}
		// Error on recreating asset, rollback everything!
		catch (Exception $e)
		{
			$db->transactionRollback();

			// Inform the client with the error message
			throw new RuntimeException($e->getMessage());
		}

		// All went ok, lets commit our changes
		$db->transactionCommit();

		return true;
	}

	/**
	 * Function for rebuilding current categories assets.
	 *
	 * @param   array  $ids  Categories to rebuild.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @throws  RuntimeException
	 */
	public static function rebuildCategories($ids = array())
	{
		$db        = JFactory::getDbo();
		$query     = $db->getQuery(true);
		$root      = self::getRoot();
		$catAssets = array();

		// Get current assets
		$currentAssets = self::getByName('com_reditem.category.%', true);

		// Get all categories in tree ordering
		$query->clear()
			->select('*')
			->from($db->qn('#__reditem_categories'))
			->order($db->qn('lft'))
			->where($db->qn('level') . ' > 0');

		if (!empty($ids))
		{
			$query->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');
		}

		$allCategories = $db->setQuery($query)->loadObjectList('id');

		// Start a transaction so we won't lose data on failure
		$db->transactionStart();

		// Start rebuild process
		try
		{
			// Clear current category assets
			$currentIds = array_keys($currentAssets);

			if (!empty($currentIds))
			{
				self::delete(null, $currentIds);
			}

			foreach ($allCategories as $id => $category)
			{
				$rules = self::setObjectRules($category, $currentAssets, '{}');
				$name  = 'com_reditem.category.' . $id;

				if ($category->level == 1)
				{
					$newAsset = self::saveNewAsset($root, $name, $rules);
				}
				else
				{
					$newAsset = self::saveNewAsset($catAssets[$category->parent_id], $name, $rules);
				}

				if (!empty($newAsset) && isset($newAsset->id))
				{
					// Update category with the new asset id.
					$query->clear()
						->update($db->qn('#__reditem_categories'))
						->set($db->qn('asset_id') . ' = ' . (int) $newAsset->id)
						->where($db->qn('id') . ' = ' . (int) $id);
					$db->setQuery($query)->execute();
					$catAssets[$id] = (int) $newAsset->id;
				}
				else
				{
					throw new Exception('Can\'t find new asset in the table!', 500);
				}
			}
		}
		// Error on recreating asset, rollback everything!
		catch (Exception $e)
		{
			$db->transactionRollback();

			// Inform the client with the error message
			throw new RuntimeException($e->getMessage());
		}

		// All went ok, lets commit our changes
		$db->transactionCommit();

		return true;
	}

	/**
	 * Get asset by name.
	 *
	 * @param   string   $name  Asset name.
	 * @param   boolean  $all   Get all assets for given name or just first one.
	 *
	 * @return  object|array  Asset object/objects for given name.
	 */
	public static function getByName($name, $all = false)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($all)
		{
			$query->select('*')
				->from($db->qn('#__assets'))
				->where($db->qn('name') . ' LIKE (' . $db->q($name) . ')');
			$db->setQuery($query);

			return $db->loadObjectList('id');
		}

		$query->select('*')
			->from($db->qn('#__assets'))
			->where($db->qn('name') . ' = ' . $db->q($name));
		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}

	/**
	 * Save new asset to assets table.
	 *
	 * @param   int     $parent  Parent id.
	 * @param   string  $name    Asset name.
	 * @param   string  $rules   Asset rules.
	 *
	 * @return  object  Newly saved asset.
	 *
	 * @throws  RuntimeException
	 */
	public static function saveNewAsset($parent, $name, $rules)
	{
		$assets = JTable::getInstance('Asset', 'JTable');
		$assets->setLocation($parent, 'last-child');

		if (!$assets->save(
			array (
				'parent_id' => $parent,
				'name'      => $name,
				'title'     => $name,
				'rules'     => $rules
			)
		))
		{
			throw new RuntimeException('Error saving asset!', 500);
		}

		unset($assets);

		return self::getByName($name);
	}

	/**
	 * Set object rules for matching asset.
	 * If asset does not exists, rules will be set to default rules provided.
	 *
	 * @param   object  $object   Array of objects.
	 * @param   array   $assets   Array of assets.
	 * @param   string  $default  Default rules.
	 *
	 * @return  array Sent objects with rules set.
	 */
	public static function setObjectRules($object, $assets, $default = '{}')
	{
		$rules = $default;

		// No need to check for the rules if assets array is empty
		if (!empty($assets))
		{
			// Check if we have asset id set
			if (!empty($object->asset_id) && isset($assets[$object->asset_id]))
			{
				$rules = $assets[$object->asset_id]->rules;
			}
			// Nope, try to find asset in current assets
			else
			{
				foreach ($assets as $aId => $asset)
				{
					$parts  = explode('.', $asset->name);
					$typeId = $parts[2];

					if ($typeId == $object->id)
					{
						$rules = $asset->rules;
						break;
					}
				}
			}
		}

		return $rules;
	}

	/**
	 * Function for deleting assets by given query (name or id).
	 *
	 * @param   string  $name  Name query for assets delete.
	 * @param   array   $ids   Ids query for assets delete.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function delete($name = '', $ids = array())
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (!empty($name))
		{
			$query->delete($db->qn('#__assets'))
				->where($db->qn('name') . ' LIKE (' . $db->q($name) . ')');
		}
		elseif (!empty($ids))
		{
			// If we sent some id particular, put it in array
			if (!is_array($ids))
			{
				$ids = array((int) $ids);
			}

			$query->delete($db->qn('#__assets'))
				->where($db->qn('id') . ' IN (' . implode(',', $ids) . ')');
		}
		// Nothing set there for deletion, just return true.
		else
		{
			return true;
		}

		$db->setQuery($query);

		// Try delete
		if ($db->execute())
		{
			return true;
		}

		return false;
	}
}
