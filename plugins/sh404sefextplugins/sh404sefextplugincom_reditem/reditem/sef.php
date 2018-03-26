<?php
/**
 * sh404SEF support for com_reditem component.
 *
 * @package     RedITEM
 * @subpackage  Plugin.Reditem
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 *
 * @since       2.1
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('redshop.library');

/* ------------------  standard plugin initialize function - don't change --------------------------- */
global $sh_LANG;

$sefConfig      = &shRouter::shGetConfig();
$shLangName     = '';
$shLangIso      = '';
$title          = array();
$shItemidString = '';
$dosef          = shInitializePlugin($lang, $shLangName, $shLangIso, $option);

if ($dosef == false)
{
	return;
}
/* ------------------  standard plugin initialize function - don't change --------------------------- */

shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

if (!empty($Itemid))
{
	shRemoveFromGETVarsList('Itemid');
}

if (!empty($limit))
{
	shRemoveFromGETVarsList('limit');
}

if (isset($limitstart))
{
	shRemoveFromGETVarsList('limitstart');
}

$view        = isset($view) ? @$view : null;
$id          = isset($id) ? @$id : null;
$sampleTitle = '';

switch ($view)
{
	case 'categorydetail':
	case 'categorydetailgmap':
		if (!empty($id))
		{
			$query = $database->getQuery(true);
			$query->select(
				array(
					$database->qn('c2.title') . ' AS ' . $database->qn('title')
				)
			)
				->from($database->qn('#__reditem_categories') . ' AS ' . $database->qn('c1'))
				->leftJoin(
					$database->qn('#__reditem_categories') . ' AS ' . $database->qn('c2') . ' ON (' .
					$database->qn('c1.lft') . ' >= ' . $database->qn('c2.lft') . ' AND ' .
					$database->qn('c1.rgt') . ' <= ' . $database->qn('c2.rgt') . ')'
				)
				->where($database->qn('c2.level') . ' > 0')
				->where($database->qn('c1.id') . ' = ' . (int) $id)
				->order($database->qn('c2.lft'));
			$database->setQuery($query);
			$categories = $database->loadObjectList();

			if (!empty($categories))
			{
				foreach ($categories as $c)
				{
					if (class_exists('RedshopHelperUtility'))
					{
						$title[] = RedshopHelperUtility::convertToNonSymbol($c->title);
					}
					else
					{
						$title[] = JFilterOutput::stringURLSafe($c->title);
					}
				}

				shRemoveFromGETVarsList('id');
				shMustCreatePageId('set', true);
			}
		}

		shRemoveFromGETVarsList('task');
		shRemoveFromGETVarsList('view');
		shRemoveFromGETVarsList('templateId');

		break;
	case 'archiveditems':
		$title[] = JFilterOutput::stringURLSafe('archiveditems-list');
		shRemoveFromGETVarsList('view');

		break;
	case 'item':
		$layout = isset($layout) ? @$layout : null;

		if (!empty($id))
		{
			$query = $database->getQuery(true);
			$query->select(
				array(
					$database->qn('i.title') . ' AS ' . $database->qn('title'),
					$database->qn('i.id') . ' AS ' . $database->qn('id'),
					$database->qn('icx.category_id') . ' AS ' . $database->qn('catId')
				)
			)
				->from($database->qn('#__reditem_items') . ' AS ' . $database->qn('i'))
				->leftJoin(
					$database->qn('#__reditem_item_category_xref') . ' AS ' . $database->qn('icx') . ' ON ' .
					$database->qn('i.id') . ' = ' . $database->qn('icx.item_id')
				)
				->where($database->qn('id') . ' = ' . (int) $id);
			$database->setQuery($query);
			$item = $database->loadObject();
		}

		if (!empty($item))
		{
			if (is_numeric($item->catId) && ((int) $item->catId) > 0)
			{
				$query = $database->getQuery(true);
				$query->select(
					array(
						$database->qn('c2.title') . ' AS ' . $database->qn('title')
					)
				)
					->from($database->qn('#__reditem_categories') . ' AS ' . $database->qn('c1'))
					->leftJoin(
						$database->qn('#__reditem_categories') . ' AS ' . $database->qn('c2') . ' ON (' .
						$database->qn('c1.lft') . ' >= ' . $database->qn('c2.lft') . ' AND ' .
						$database->qn('c1.rgt') . ' <= ' . $database->qn('c2.rgt') . ')'
					)
					->where($database->qn('c2.level') . ' > 0')
					->where($database->qn('c1.id') . ' = ' . (int) $item->catId)
					->order($database->qn('c2.lft'));
				$database->setQuery($query);
				$categories = $database->loadObjectList();

				if (!empty($categories))
				{
					foreach ($categories as $c)
					{
						if (class_exists('RedshopHelperUtility'))
						{
							$title[] = RedshopHelperUtility::convertToNonSymbol($c->title);
						}
						else
						{
							$title[] = JFilterOutput::stringURLSafe($c->title);
						}
					}
				}
			}

			if ($layout == 'customfields')
			{
				$title[] = JFilterOutput::stringURLSafe($item->title . '-customfields');
				shMustCreatePageId('set', true);
			}
			else
			{
				$title[] = JFilterOutput::stringURLSafe($item->title);
				shMustCreatePageId('set', true);
			}

			shRemoveFromGETVarsList('id');
			shRemoveFromGETVarsList('task');
			shRemoveFromGETVarsList('view');
			shRemoveFromGETVarsList('templateId');
		}
		else
		{
			$dosef = false;
		}

		break;
	case 'itemdetail':
		if (!empty($id))
		{
			$query = $database->getQuery(true);
			$query->select(
				array(
					$database->qn('i.title') . ' AS ' . $database->qn('title'),
					$database->qn('i.id') . ' AS ' . $database->qn('id'),
					$database->qn('icx.category_id') . ' AS ' . $database->qn('catId')
				)
			)
				->from($database->qn('#__reditem_items') . ' AS ' . $database->qn('i'))
				->leftJoin(
					$database->qn('#__reditem_item_category_xref') . ' AS ' . $database->qn('icx') . ' ON ' .
					$database->qn('i.id') . ' = ' . $database->qn('icx.item_id')
				)
				->where($database->qn('id') . ' = ' . (int) $id);
			$database->setQuery($query);
			$item = $database->loadObject();

			if (!empty($item))
			{
				if (is_numeric($item->catId) && ((int) $item->catId) > 0)
				{
					$query = $database->getQuery(true);
					$query->select(
						array(
							$database->qn('c2.title') . ' AS ' . $database->qn('title')
						)
					)
						->from($database->qn('#__reditem_categories') . ' AS ' . $database->qn('c1'))
						->leftJoin(
							$database->qn('#__reditem_categories') . ' AS ' . $database->qn('c2') . ' ON (' .
							$database->qn('c1.lft') . ' >= ' . $database->qn('c2.lft') . ' AND ' .
							$database->qn('c1.rgt') . ' <= ' . $database->qn('c2.rgt') . ')'
						)
						->where($database->qn('c2.level') . ' > 0')
						->where($database->qn('c1.id') . ' = ' . (int) $item->catId)
						->order($database->qn('c2.lft'));
					$database->setQuery($query);
					$categories = $database->loadObjectList();

					if (!empty($categories))
					{
						foreach ($categories as $c)
						{
							if (class_exists('RedshopHelperUtility'))
							{
								$title[] = RedshopHelperUtility::convertToNonSymbol($c->title);
							}
							else
							{
								$title[] = JFilterOutput::stringURLSafe($c->title);
							}
						}
					}
				}

				if (class_exists('RedshopHelperUtility'))
				{
					$title[] = RedshopHelperUtility::convertToNonSymbol($item->title);
				}
				else
				{
					$title[] = JFilterOutput::stringURLSafe($item->title);
				}
				shRemoveFromGETVarsList('id');
				shMustCreatePageId('set', true);
			}
		}

		shRemoveFromGETVarsList('task');
		shRemoveFromGETVarsList('view');
		shRemoveFromGETVarsList('templateId');

		break;
	case 'itemedit':
		if (!empty($id))
		{
			$query = $database->getQuery(true);
			$query->select(
				array(
					$database->qn('i.title') . ' AS ' . $database->qn('title'),
					$database->qn('i.id') . ' AS ' . $database->qn('id'),
					$database->qn('icx.category_id') . ' AS ' . $database->qn('catId')
				)
			)
				->from($database->qn('#__reditem_items') . ' AS ' . $database->qn('i'))
				->leftJoin(
					$database->qn('#__reditem_item_category_xref') . ' AS ' . $database->qn('icx') . ' ON ' .
					$database->qn('i.id') . ' = ' . $database->qn('icx.item_id')
				)
				->where($database->qn('id') . ' = ' . (int) $id);
			$database->setQuery($query);
			$item = $database->loadObject();

			if (!empty($sampleTitle))
			{
				if (is_numeric($item->catId) && ((int) $item->catId) > 0)
				{
					$query = $database->getQuery(true);
					$query->select(
						array(
							$database->qn('c2.title') . ' AS ' . $database->qn('title')
						)
					)
						->from($database->qn('#__reditem_categories') . ' AS ' . $database->qn('c1'))
						->leftJoin(
							$database->qn('#__reditem_categories') . ' AS ' . $database->qn('c2') . ' ON (' .
							$database->qn('c1.lft') . ' >= ' . $database->qn('c2.lft') . ' AND ' .
							$database->qn('c1.rgt') . ' <= ' . $database->qn('c2.rgt') . ')'
						)
						->where($database->qn('c2.level') . ' > 0')
						->where($database->qn('c1.id') . ' = ' . (int) $item->catId)
						->order($database->qn('c2.lft'));
					$database->setQuery($query);
					$categories = $database->loadObjectList();

					if (!empty($categories))
					{
						foreach ($categories as $c)
						{
							if (class_exists('RedshopHelperUtility'))
							{
								$title[] = RedshopHelperUtility::convertToNonSymbol($c->title);
							}
							else
							{
								$title[] = JFilterOutput::stringURLSafe($c->title);
							}
						}
					}
				}

				if (class_exists('RedshopHelperUtility'))
					{
						$title[] = RedshopHelperUtility::convertToNonSymbol($item->title . '-edit');
					}
					else
					{
						$title[] = JFilterOutput::stringURLSafe($item->title . '-edit');
					}
				shRemoveFromGETVarsList('id');
				shMustCreatePageId('set', true);
			}
		}

		shRemoveFromGETVarsList('task');
		shRemoveFromGETVarsList('view');
		shRemoveFromGETVarsList('templateId');

		break;
	case 'items':
		$title[] = JFilterOutput::stringURLSafe('items-list');
		shRemoveFromGETVarsList('view');

		break;
	case 'search':
	case 'searchendgine':
		$title[] = JFilterOutput::stringURLSafe('reditem-search');
		shRemoveFromGETVarsList('view');

		break;
	default:
		$dosef = false;

		break;
}

if ($dosef)
{
	$string = shFinalizePlugin(
		$string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : null),
		(isset($limitstart) ? @$limitstart : null),
		(isset($shLangName) ? @$shLangName : null)
	);
}
