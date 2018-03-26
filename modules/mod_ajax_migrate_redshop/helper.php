<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  mod_ajax_alfix
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class ModAjaxMigrateRedshopHelper
{
	public static function migrateCategoryAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('article', 'a'))
			->leftJoin($db->qn('category', 'c') . ' ON a.article_pk = c.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->leftJoin($db->qn('media', 'm') . ' ON a.media = m.media_pk')
			->where($db->qn('c.type') . ' = ' . $db->q('CATEGORY'))
			->where($db->qn('al.language') . ' = ' . $db->q('2'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('category_id', 'category_name', 'category_short_description', 'category_description', 'category_template', 'category_more_template', 'products_per_page', 'category_thumb_image', 'category_full_image', 'metakey', 'metadesc', 'published', 'category_pdate');
			$values = array($db->q($value['article_pk']), $db->q($value['subject']), $db->q($value['description']), $db->q($value['content']), 8, 8, 27, $db->q($value['filename']), $db->q($value['filename']), $db->q($value['seo_keywords']), $db->q($value['seo_description']), $db->q($value['visible']), $db->q($value['date_inserted']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_category'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateCategory&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateCategoryXrefAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('article', 'a'))
			->leftJoin($db->qn('category', 'c') . ' ON a.article_pk = c.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->leftJoin($db->qn('media', 'm') . ' ON a.media = m.media_pk')
			->where($db->qn('c.type') . ' = ' . $db->q('CATEGORY'))
			->where($db->qn('al.language') . ' = ' . $db->q('2'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('category_parent_id', 'category_child_id');
			$values = array($db->q($value['parent']), $db->q($value['article_pk']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_category_xref'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateCategoryXref&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateProductCategoryXrefAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('category_products'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('category_id', 'product_id');
			$values = array($db->q($value['category']), $db->q($value['product']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_product_category_xref'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateProductCategoryXref&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateManufacturerAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('brand', 'b'))
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = b.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->leftJoin($db->qn('media', 'm') . ' ON b.media_icon = m.media_pk')
			->where($db->qn('al.language') . ' = ' . $db->q('2'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$manufacturer_desc = $db->q('<p><img src="images/Nha_san_xuat/' . $value['filename'] . '" /></p>' . $value['content']);
			$columns = array('manufacturer_id', 'manufacturer_name', 'manufacturer_desc', 'template_id', 'metakey', 'metadesc', 'pagetitle', 'published', 'manufacturer_url');
			$values = array($db->q($value['article_pk']), $db->q($value['subject']), $manufacturer_desc, 14, $db->q($value['seo_keywords']), $db->q($value['seo_description']), $db->q($value['seo_title']), $db->q($value['visible']), $db->q($value['filename']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_manufacturer'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateManufacturer&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateManufacturerImageAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('brand', 'b'))
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = b.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->leftJoin($db->qn('media', 'm') . ' ON b.media_icon = m.media_pk')
			->where($db->qn('al.language') . ' = ' . $db->q('2'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('media_name', 'media_section', 'section_id', 'media_type', 'media_mimetype', 'published');
			$values = array($db->q($value['filename']), $db->q('manufacturer'), $db->q($value['article_pk']), $db->q('images'), $db->q('image/jpeg'), $db->q(1));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_media'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateManufacturerImage&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateProductAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('product', 'p'))
			->leftJoin($db->qn('pricing', 'pr') . ' ON p.pricing = pr.pricing_pk')
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = p.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->leftJoin($db->qn('media', 'm') . ' ON a.media = m.media_pk')
			->where($db->qn('al.language') . ' = ' . $db->q('2'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('product_id', 'manufacturer_id', 'product_template', 'product_name', 'product_price', 'product_number', 'product_type', 'product_s_desc', 'product_desc', 'product_volume', 'published', 'product_thumb_image', 'product_full_image', 'publish_date', 'update_date', 'metakey', 'visited', 'metadesc', 'pagetitle', 'weight', 'product_height', 'product_width');
			$values = array($db->q($value['article_pk']), $db->q($value['brand']), 9, $db->q($value['subject']), $db->q($value['price']), $db->q($value['code']), $db->q('product'), $db->q($value['description']), $db->q($value['content']), $db->q($value['volume']), $db->q($value['visible']), $db->q($value['filename']), $db->q($value['filename']), $db->q($value['date_inserted']), $db->q($value['date_last_updated']), $db->q($value['seo_keywords']), $db->q($value['view_time']), $db->q($value['seo_description']), $db->q($value['seo_title']), $db->q($value['weight']), $db->q($value['height']), $db->q($value['width']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_product'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateProduct&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateProductImageAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('product_media', 'pm'))
			->leftJoin($db->qn('media', 'm') . ' ON pm.media = m.media_pk')
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('media_name', 'media_section', 'section_id', 'media_type', 'media_mimetype', 'published');
			$values = array($db->q($value['filename']), $db->q('product'), $db->q($value['product']), $db->q('images'), $db->q($value['media_type']), $db->q(1));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_media'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateManufacturerImage&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateProductStockAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('product'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('product_id', 'stockroom_id', 'quantity');
			$stock = $value['stock'] + $value['stock_security'];
			$values = array($db->q($value['article_pk']), 1, $db->q($stock));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_product_stockroom_xref'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateProductStock&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateUserAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('customer', 'c'))
			->leftJoin($db->qn('organization', 'o') . ' ON c.organization_pk = o.organization_pk')
			// ->from($db->qn('account', 'a'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('id', 'name', 'username', 'email', 'password', 'registerDate', 'lastvisitDate','activation', 'params');
			$values = array($db->q($value['organization_pk']), $db->q($value['name']), $db->q($value['email']), $db->q($value['email']), $db->q($value['password']), $db->q($value['date_inserted']), $db->q($value['date_last_updated']), $db->q(''), $db->q('{}'));
			// $values = array($db->q($value['account_pk']), $db->q($value['fullname']), $db->q($value['username']), $db->q($value['email']), $db->q($value['password']), $db->q($value['date_inserted']), $db->q($value['date_last_login']), $db->q(''), $db->q('{}'));

			$query = $db->getQuery(true)
				->insert($db->qn('#__users'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateUser&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateUserGroupAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('customer', 'c'))
			// ->from($db->qn('account', 'a'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('user_id', 'group_id');
			$values = array($db->q($value['organization_pk']), $db->q(2));
			// $values = array($db->q($value['account_pk']), $db->q(8));

			$query = $db->getQuery(true)
				->insert($db->qn('#__user_usergroup_map'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateUserGroup&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateUserInfoAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('organization', 'o'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('users_info_id', 'user_id', 'user_email', 'address_type', 'firstname', 'shopper_group_id', 'country_code', 'address', 'city', 'state_code', 'zipcode', 'phone');
			$values = array($db->q($value['organization_pk']), $db->q($value['organization_pk']), $db->q($value['email']), $db->q('BT'), $db->q($value['name']), $db->q('1'), $db->q('VNM'), $db->q($value['address']), $db->q(''), $db->q(''), $db->q(''), $db->q($value['tel']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_users_info'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateUserInfo&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function updateUserInfoAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('c.organization_pk, ll.name AS city')
			// ->from($db->qn('customer', 'c'))
			->from($db->qn('customer_delivery', 'c'))
			// ->from($db->qn('customer_billing', 'c'))
			->leftJoin($db->qn('organization', 'o') . ' ON c.organization_pk = o.organization_pk')
			->leftJoin($db->qn('location', 'l') . ' ON l.location_pk = c.location')
			->leftJoin($db->qn('location_language', 'll') . ' ON l.location_pk = ll.location')
			->where($db->qn('ll.language') . ' = 2')
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$fields = array(
				$db->qn('address_type') . ' = ' . $db->q('ST'),
				$db->qn('city') . ' = ' . $db->q($value['city'])
			);

			$conditions = array(
				$db->qn('user_id') . ' = ' . $db->q($value['organization_pk'])
			);

			$query = $db->getQuery(true)
				->update($db->qn('#__redshop_users_info'))
				->set($fields)
				->where($conditions);

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=updateUserInfo&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateOrderAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('oh.*, o.description AS customer_note')
			->from($db->qn('order_header', 'oh'))
			->leftJoin($db->qn('organization', 'o') . ' ON oh.delivery = o.organization_pk');

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$query = $db->getQuery(true)
				->select('SUM(price) AS total, SUM(vat) AS vat, SUM(discount) AS discount')
				->from($db->qn('order_item'))
				->where($db->qn('order_header') . ' = ' . $db->q($value['order_header_pk']))
				->group($db->qn('order_header'));
			$item = $db->setQuery($query)->loadAssoc();
			$value['total'] = $item['total'];
			$value['vat'] = $item['vat'];
			$value['discount'] = $item['discount'];

			if ($value['status'] == 'PROCESSING')
			{
				$value['status_code'] = 'P';
				$value['payment_status'] = 'Unpaid';
			}
			elseif ($value['status'] == 'SHIPPING')
			{
				$value['status_code'] = 'S';
				$value['payment_status'] = 'Unpaid';
			}
			elseif ($value['status'] == 'CLOSE')
			{
				$value['status_code'] = 'X';
				$value['payment_status'] = 'Unpaid';
			}

			$value['cdate'] = strtotime($value['date_inserted']);
			$value['mdate'] = strtotime($value['date_last_updated']);

			$columns = array('order_id', 'user_id', 'order_number', 'user_info_id', 'order_total', 'order_subtotal', 'order_shipping', 'order_tax', 'order_discount', 'order_status', 'order_payment_status', 'cdate', 'mdate', 'customer_note', 'track_no');
			$values = array($db->q($value['order_header_pk']), $db->q($value['customer']), $db->q($value['order_header_pk']), $db->q($value['customer']), $db->q($value['total']), $db->q($value['total']), $db->q($value['transport_cost']), $db->q($value['vat']), $db->q($value['discount']), $db->q($value['status_code']), $db->q($value['payment_status']), $db->q($value['cdate']), $db->q($value['mdate']), $db->q($value['customer_note']), $db->q(''));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_orders'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateOrder&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateOrderUserInfoAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('oh.*, o.*, ll.name as city')
			->from($db->qn('order_header', 'oh'))
			->leftJoin($db->qn('customer_delivery', 'cd') . ' ON cd.organization_pk = oh.delivery')
			->leftJoin($db->qn('location_language', 'll') . ' ON cd.location = ll.location')
			->leftJoin($db->qn('organization', 'o') . ' ON cd.organization_pk = o.organization_pk')

			// ->leftJoin($db->qn('customer_billing', 'cb') . ' ON cb.organization_pk = oh.billing')
			// ->leftJoin($db->qn('location_language', 'll') . ' ON cb.location = ll.location')
			// ->leftJoin($db->qn('organization', 'o') . ' ON cb.organization_pk = o.organization_pk')

			->where($db->qn('language') . ' = 2')
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('users_info_id', 'order_id', 'user_id', 'firstname', 'address_type', 'shopper_group_id', 'address', 'city', 'country_code', 'phone', 'user_email');

			// $values = array($db->q($value['billing']), $db->q($value['order_header_pk']), $db->q($value['customer']), $db->q($value['name']), $db->q('BT'), $db->q(1), $db->q($value['address']), $db->q($value['city']), $db->q('VNM'), $db->q($value['tel']), $db->q($value['email']));

			$values = array($db->q($value['delivery']), $db->q($value['order_header_pk']), $db->q($value['customer']), $db->q($value['name']), $db->q('ST'), $db->q(1), $db->q($value['address']), $db->q($value['city']), $db->q('VNM'), $db->q($value['tel']), $db->q($value['email']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_order_users_info'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateOrderUserInfo&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateOrderItemAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('oi.*, al.subject, p.code, o.*')
			->from($db->qn('order_item', 'oi'))
			->leftJoin($db->qn('order_header', 'o') . ' ON oi.order_header = o.order_header_pk')
			->leftJoin($db->qn('product', 'p') . ' ON p.article_pk = oi.product')
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = p.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->where($db->qn('al.language') . ' = 2')
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('order_item_id', 'order_id', 'user_info_id', 'product_id', 'order_item_sku', 'order_item_name', 'product_quantity', 'product_item_price', 'product_item_price_excl_vat', 'product_final_price', 'order_item_currency', 'order_status', 'cdate', 'mdate', 'product_item_old_price');

			if ($value['status'] == 'PROCESSING')
			{
				$value['status_code'] = 'P';
			}
			elseif ($value['status'] == 'SHIPPING')
			{
				$value['status_code'] = 'S';
			}
			elseif ($value['status'] == 'CLOSE')
			{
				$value['status_code'] = 'X';
			}

			$value['cdate'] = strtotime($value['date_inserted']);
			$value['mdate'] = strtotime($value['date_last_updated']);

			$values = array($db->q($value['order_item_pk']), $db->q($value['order_header']), $db->q($value['customer']), $db->q($value['product']), $db->q($value['code']), $db->q($value['subject']), $db->q($value['quantity']), $db->q($value['price']), $db->q($value['price']), $db->q($value['price']), $db->q('VNM'), $db->q($value['status_code']), $db->q($value['cdate']), $db->q($value['mdate']), $db->q($value['price']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_order_item'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateOrderItem&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateProductTranslateAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('product', 'p'))
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = p.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->where($db->qn('al.language') . ' = ' . $db->q('1'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('rctranslations_language', 'rctranslations_originals', 'rctranslations_modified', 'rctranslations_state', 'product_id', 'product_name', 'product_s_desc', 'product_desc', 'metakey', 'metadesc', 'pagetitle');
			$values = array($db->q('en-GB'), $db->q(''), $db->q(date('Y-m-d H:i:s')), $db->q('1'), $db->q($value['article_pk']), $db->q($value['subject']), $db->q($value['description']), $db->q($value['content']), $db->q($value['seo_keywords']), $db->q($value['seo_description']), $db->q($value['seo_title']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_product_rctranslations'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateProductTranslate&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateCategoryTranslateAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('article', 'a'))
			->leftJoin($db->qn('category', 'c') . ' ON a.article_pk = c.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->where($db->qn('c.type') . ' = ' . $db->q('CATEGORY'))
			->where($db->qn('al.language') . ' = ' . $db->q('1'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('rctranslations_language', 'rctranslations_originals', 'rctranslations_modified', 'rctranslations_state', 'category_id', 'category_name', 'category_short_description', 'category_description', 'metakey', 'metadesc', 'pagetitle');
			$values = array($db->q('en-GB'), $db->q(''), $db->q(date('Y-m-d H:i:s')), $db->q('1'), $db->q($value['article_pk']), $db->q($value['subject']), $db->q($value['description']), $db->q($value['content']), $db->q($value['seo_keywords']), $db->q($value['seo_description']), $db->q($value['seo_title']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_category_rctranslations'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateCategoryTranslate&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateManufacturerTranslateAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('brand', 'b'))
			->leftJoin($db->qn('article', 'a') . ' ON a.article_pk = b.article_pk')
			->leftJoin($db->qn('article_language', 'al') . ' ON a.article_pk = al.article')
			->where($db->qn('al.language') . ' = ' . $db->q('1'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('rctranslations_language', 'rctranslations_originals', 'rctranslations_modified', 'rctranslations_state', 'manufacturer_id', 'manufacturer_name', 'manufacturer_desc', 'metakey', 'metadesc', 'pagetitle');
			$values = array($db->q('en-GB'), $db->q(''), $db->q(date('Y-m-d H:i:s')), $db->q('1'), $db->q($value['article_pk']), $db->q($value['subject']), $db->q($value['content']), $db->q($value['seo_keywords']), $db->q($value['seo_description']), $db->q($value['seo_title']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_manufacturer_rctranslations'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateManufacturerTranslate&limit=' . $limit . '&start=' . $start);
		}
	}

	public static function migrateRootCatgoryAjax()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$limit = $input->get('limit');
		$start = $input->get('start');
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__redshop_product', 'p'))
			->setLimit($limit, $start);

		$data = $db->setQuery($query)->loadAssocList();
		$count = count($data);

		foreach ($data as $key => $value)
		{
			$columns = array('category_id', 'product_id');
			$values = array($db->q('15526'), $db->q((int) $value['product_id']));

			$query = $db->getQuery(true)
				->insert($db->qn('#__redshop_product_category_xref'))
				->columns($db->qn($columns))
				->values(implode(',', $values));

			$db->setQuery($query)->execute();
			$start++;
		}

		if ($start > $count)
		{
			return 'Successfull';
		}
		else
		{
			return $app->redirect('index.php?option=com_ajax&module=ajax_migrate_redshop&format=debug&method=migrateRootCatgory&limit=' . $limit . '&start=' . $start);
		}
	}
}
