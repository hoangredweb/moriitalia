<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class leftmenu
{
	protected static $view = NULL;

	protected static $layout = NULL;

	public static function render($disableMenu = false)
	{
		self::$view = JFactory::getApplication()->input->getCmd('view');
		self::$layout = JFactory::getApplication()->input->getCmd('layout', '');

		$menu = RedshopAdminMenu::getInstance();

		$menu->disableMenu = $disableMenu;

		$active = self::getActive();

		self::setProductGroup();
		self::setOrderGroup();
		self::setDiscountGroup();
		self::setCommunicationGroup();
		self::setShippingGroup();
		self::setUserGroup();
		self::setVatGroup();
		self::setIEGroup();
		self::setCustomisationGroup();
		self::setCustomerInputGroup();
		self::setAccountGroup();
		self::setStatisticsGroup();
		self::setConfigGroup();

		if ($disableMenu)
		{
			return $menu->items;
		}
		else
		{
			return JLayoutHelper::render('component.full.sidebar.menu', array('items' => $menu->items, 'active' => $active));
		}
	}

	protected static function getActive()
	{
		$activeGroup = '';
		$activeItem = '';

		switch (self::$view)
		{
			case "product":
			case "product_detail":
			case "prices":
			case "mass_discount_detail":
			case "mass_discount":
				$activeItem = "product";
				$activeGroup = "PRODUCT_MANAGEMENT";
				break;

			case "category":
			case "category_detail":
				$activeItem = "category";
				$activeGroup = "PRODUCT_MANAGEMENT";
				break;

			case "manufacturer":
			case "manufacturer_detail":
				$activeItem = "manufacturer";
				$activeGroup = "PRODUCT_MANAGEMENT";
				break;

			case "media":
			case 'media_detail':
				$activeItem = "media";
				$activeGroup = "PRODUCT_MANAGEMENT";
				break;

			case "order":
			case "order_detail":
			case "addorder_detail":
			case "orderstatus":
			case "orderstatus_detail":
			case "opsearch":
			case "barcode":
				$activeItem = "order";
				$activeGroup = "ORDER";
				break;

			case "quotation":
			case "addquotation_detail":
				$activeItem = "quotation";
				$activeGroup = "ORDER";
				break;

			case "stockroom":
			case "stockroom_listing":
			case "stockimage":
				$activeItem = "stockroom";
				$activeGroup = "ORDER";
				break;

			case "supplier":
			case "supplier_detail":
				$activeItem = "supplier";
				$activeGroup = "ORDER";
				break;

			case "discount":
			case "discount_detail":
				$activeItem = "discount";
				$activeGroup = "DISCOUNT";
				break;

			case "giftcards":
			case "giftcard":
				$activeItem = "giftcards";
				$activeGroup = "DISCOUNT";
				break;

			case "voucher":
			case "voucher_detail":
				$activeItem = "voucher";
				$activeGroup = "DISCOUNT";
				break;

			case "coupon":
			case "coupon_detail":
				$activeItem = "coupon";
				$activeGroup = "DISCOUNT";
				break;

			case "mail":
			case "mail_detail":
				$activeItem = "mail";
				$activeGroup = "COMMUNICATION";
				break;

			case "newsletter":
			case "newsletter_detail":
			case "newslettersubscr":
			case 'newslettersubscr_detail':
				$activeItem = "newsletter";
				$activeGroup = "COMMUNICATION";
				break;

			case "shipping":
			case "shipping_detail":
			case "shipping_rate":
				$activeItem = "shipping_method";
				$activeGroup = "SHIPPING";
				break;

			case "shipping_box":
			case "shipping_box_detail":
				$activeItem = "shipping_box";
				$activeGroup = "SHIPPING";
				break;

			case "wrapper":
			case "wrapper_detail":
				$activeItem = "wrapper";
				$activeGroup = "SHIPPING";
				break;

			case "user":
			case 'user_detail':
			case "shopper_group":
			case "shopper_group_detail":
				$activeItem = "user";
				$activeGroup = "USER";
				break;

			case "accessmanager":
			case 'accessmanager_detail':
				$activeItem = "accessmanager";
				$activeGroup = "USER";
				break;

			case "tax_group":
			case "tax_group_detail":
			case "tax":
				$activeItem = "tax";
				$activeGroup = "VAT_AND_CURRENCY";
				break;

			case "currency":
			case "currency_detail":
				$activeItem = "currency";
				$activeGroup = "VAT_AND_CURRENCY";
				break;

			case "country":
			case "country_detail":
				$activeItem = "country";
				$activeGroup = "VAT_AND_CURRENCY";
				break;

			case "state":
			case "state_detail":
				$activeItem = "state";
				$activeGroup = "VAT_AND_CURRENCY";
				break;

			case "zipcode":
			case "zipcode_detail":
				$activeItem = "zipcode";
				$activeGroup = "VAT_AND_CURRENCY";
				break;

			case "importexport":
			case "import":
			case "export":
			case "vmimport":
				$activeItem = "importexport";
				$activeGroup = "IMPORT_EXPORT";
				break;

			case "xmlimport":
			case "xmlexport":
				$activeItem = "xmlimportexport";
				$activeGroup = "IMPORT_EXPORT";
				break;

			case "fields":
			case "fields_detail":
			case "addressfields_listing":
				$activeItem = "fields";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "template":
			case "template_detail":
				$activeItem = "template";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "textlibrary":
			case "textlibrary_detail":
				$activeItem = "textlibrary";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "catalog":
			case "catalog_request":
				$activeItem = "catalog";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "sample":
			case "sample_request":
				$activeItem = "sample";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "producttags":
			case "producttags_detail":
				$activeItem = "producttags";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "attribute_set":
			case "attribute_set_detail":
				$activeItem = "attribute_set";
				$activeGroup = "CUSTOMIZATION";
				break;

			case "question":
			case "question_detail":
				$activeItem = "question";
				$activeGroup = "CUSTOMER_INPUT";
				break;

			case "rating":
			case "rating_detail":
				$activeItem = "rating";
				$activeGroup = "CUSTOMER_INPUT";
				break;

			case "accountgroup":
			case "accountgroup_detail":
				$activeItem = "accountgroup";
				$activeGroup = "ACCOUNTING";
				break;

			case "statistic":
				$activeItem = "statistic";
				$activeGroup = "STATISTIC";
				break;

			case "configuration":
			case 'update':
				$activeItem = "configuration";
				$activeGroup = "CONFIG";
				break;

			default:
				$activeItem = '';
				$activeGroup = '';
				break;
		}

		return array($activeGroup, $activeItem);
	}

	protected static function setProductGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setProduct();
		self::setCategory();
		self::setManufacturer();
		self::setMedia();

		$menu->group('PRODUCT_MANAGEMENT');
	}

	protected static function setOrderGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setOrder();
		self::setQuotation();
		self::setStockroom();
		self::setSupplier();

		$menu->group('ORDER');
	}

	protected static function setDiscountGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setDiscount();
		self::setGiftCard();
		self::setVoucher();
		self::setCoupon();

		$menu->group('DISCOUNT');
	}

	protected static function setCommunicationGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setMail();
		self::setNewsLetter();

		$menu->group('COMMUNICATION');
	}

	protected static function setShippingGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setShipping();
		self::setShippingBox();
		self::setWrapper();

		$menu->group('SHIPPING');
	}

	protected static function setUserGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		$menu->section('user')
			->title('COM_REDSHOP_USER')
			->addItem(
				'index.php?option=com_redshop&view=user',
				'COM_REDSHOP_USER_LISTING',
				(self::$view == 'user') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=user_detail',
				'COM_REDSHOP_ADD_USER',
				(self::$view == 'user_detail') ? true : false
			)
			->addItem(
				'javascript:userSync();',
				'COM_REDSHOP_USER_SYNC'
			)
			->addItem(
				'index.php?option=com_redshop&view=shopper_group',
				'COM_REDSHOP_SHOPPER_GROUP_LISTING',
				(self::$view == 'shopper_group') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=shopper_group_detail',
				'COM_REDSHOP_ADD_SHOPPER_GROUP',
				(self::$view == 'shopper_group_detail') ? true : false
			);

		JFactory::getDocument()->addScriptDeclaration('
			function userSync() {
				if (confirm("' . JText::_('COM_REDSHOP_DO_YOU_WANT_TO_SYNC') . '") == true)
					window.location = "index.php?option=com_redshop&view=user&sync=1";
			}
		');

		$menu->group('USER');
	}

	protected static function setVatGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setTax();
		self::setCurrency();
		self::setCountry();
		self::setState();
		self::setZipcode();

		$menu->group('VAT_AND_CURRENCY');
	}

	protected static function setIEGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		$menu->section('import')
			->title('COM_REDSHOP_IMPORT_EXPORT')
			->addItem(
				'index.php?option=com_redshop&view=import',
				'COM_REDSHOP_DATA_IMPORT',
				(self::$view == 'import') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=export',
				'COM_REDSHOP_DATA_EXPORT',
				(self::$view == 'export') ? true : false
			)
			->addItem(
				'javascript:vmImport();',
				'COM_REDSHOP_IMPORT_FROM_VM'
			);

		$menu->section('xmlimport')
			->title('COM_REDSHOP_XML_IMPORT_EXPORT')
			->addItem(
				'index.php?option=com_redshop&view=xmlimport',
				'COM_REDSHOP_XML_IMPORT',
				(self::$view == 'xmlimport') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=xmlexport',
				'COM_REDSHOP_XML_EXPORT',
				(self::$view == 'xmlexport') ? true : false
			);

		JFactory::getDocument()->addScriptDeclaration('
			function vmImport() {
				if (confirm("' . JText::_('COM_REDSHOP_DO_YOU_WANT_TO_IMPORT_VM') . '") == true)
					window.location = "index.php?option=com_redshop&view=import&vm=1";
			}
		');

		$menu->group('IMPORT_EXPORT');
	}

	protected static function setCustomisationGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		self::setCustomFields();
		self::setTemplate();
		self::setTextLibrary();
		self::setCatelogue();
		self::setProductSample();
		self::setCustomerTags();
		self::setAttributeBank();

		$menu->group('CUSTOMIZATION');
	}

	protected static function setCustomerInputGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		$menu->section('question')
			->title('COM_REDSHOP_QUESTION')
			->addItem(
				'index.php?option=com_redshop&view=question',
				'COM_REDSHOP_QUESTION_LISTING',
				(self::$view == 'question') ? true : false
			);

		$menu->section('rating')
			->title('COM_REDSHOP_REVIEW')
			->addItem(
				'index.php?option=com_redshop&view=rating',
				'COM_REDSHOP_RATING_REVIEW',
				(self::$view == 'rating') ? true : false
			);

		$menu->group('CUSTOMER_INPUT');
	}

	protected static function setAccountGroup()
	{
		if (ECONOMIC_INTEGRATION && JPluginHelper::isEnabled('economic'))
		{
			$menu = RedshopAdminMenu::getInstance()->init();

			$menu->section('accountgroup')
				->title('COM_REDSHOP_ECONOMIC_ACCOUNT_GROUP')
				->addItem(
					'index.php?option=com_redshop&view=accountgroup',
					'COM_REDSHOP_ACCOUNTGROUP_LISTING',
					(self::$view == 'accountgroup') ? true : false
				)
				->addItem(
					'index.php?option=com_redshop&view=accountgroup_detail',
					'COM_REDSHOP_ADD_ACCOUNTGROUP',
					(self::$view == 'accountgroup_detail') ? true : false
				);

			$menu->group('ACCOUNTING');
		}
	}

	protected static function setStatisticsGroup()
	{
		$menu = RedshopAdminMenu::getInstance()->init();

		$menu->section('statistic')
			->title('COM_REDSHOP_STATISTIC')
			->addItem(
				'index.php?option=com_redshop&view=statistic',
				'COM_REDSHOP_TOTAL_VISITORS',
				(self::$view == 'statistic' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=pageview',
				'COM_REDSHOP_TOTAL_PAGEVIEWERS',
				(self::$view == 'statistic' && self::$layout == 'pageview') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=turnover',
				'COM_REDSHOP_TOTAL_TURNOVER',
				(self::$view == 'statistic' && self::$layout == 'turnover') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=avrgorder',
				'COM_REDSHOP_AVG_ORDER_AMOUNT_CUSTOMER',
				(self::$view == 'statistic' && self::$layout == 'avrgorder') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=amountorder',
				'COM_REDSHOP_TOP_CUSTOMER_AMOUNT_OF_ORDER',
				(self::$view == 'statistic' && self::$layout == 'amountorder') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=amountprice',
				'COM_REDSHOP_TOP_CUSTOMER_AMOUNT_OF_PRICE_PER_ORDER',
				(self::$view == 'statistic' && self::$layout == 'amountprice') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=amountspent',
				'COM_REDSHOP_TOP_CUSTOMER_AMOUNT_SPENT_IN_TOTAL',
				(self::$view == 'statistic' && self::$layout == 'amountspent') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=bestsell',
				'COM_REDSHOP_BEST_SELLERS',
				(self::$view == 'statistic' && self::$layout == 'bestsell') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=popularsell',
				'COM_REDSHOP_MOST_VISITED_PRODUCTS',
				(self::$view == 'statistic' && self::$layout == 'popularsell') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=newprod',
				'COM_REDSHOP_NEWEST_PRODUCTS',
				(self::$view == 'statistic' && self::$layout == 'newprod') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=statistic&layout=neworder',
				'COM_REDSHOP_NEWEST_ORDERS',
				(self::$view == 'statistic' && self::$layout == 'neworder') ? true : false
			);

		$menu->group('STATISTIC');
	}

	protected static function setConfigGroup()
	{
		$user = JFactory::getUser();
		$menu = RedshopAdminMenu::getInstance()->init();

		$menu->section('configuration')
			->title('COM_REDSHOP_CONFIG')
			->addItem(
				'index.php?option=com_redshop&view=configuration',
				'COM_REDSHOP_RESHOP_CONFIGURATION',
				(self::$view == 'configuration' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&wizard=1',
				'COM_REDSHOP_START_CONFIGURATION_WIZARD'
			)
			->addItem(
				'index.php?option=com_redshop&view=configuration&layout=resettemplate',
				'COM_REDSHOP_RESET_TEMPLATE_LBL',
				(self::$view == 'configuration' && self::$layout == 'resettemplate') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=update',
				'COM_REDSHOP_UPDATE_TITLE',
				(self::$view == 'update') ? true : false
			);

		$menu->section('accessmanager')
			->title('COM_REDSHOP_ACCESS_MANAGER')
			->addItem(
				'index.php?option=com_redshop&view=accessmanager',
				'COM_REDSHOP_ACCESS_MANAGER',
				(self::$view == 'accessmanager') ? true : false
			);

			$menu->section('redshopbackendaccess')
				->title('COM_REDSHOP_BACKEND_ACCESS_CONFIG')
				->addItem(
					'index.php?option=com_config&view=component&component=com_redshop',
					'COM_REDSHOP_BACKEND_ACCESS_CONFIG',
					false
				);

		$menu->group('CONFIG');
	}

	protected static function setProduct()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('product')
			->title('COM_REDSHOP_PRODUCTS')
			->addItem(
				'index.php?option=com_redshop&view=product',
				'COM_REDSHOP_PRODUCT_LISTING',
				(self::$view == 'product' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=product&layout=listing',
				'COM_REDSHOP_PRODUCT_PRICE_VIEW',
				(self::$view == 'product' && self::$layout == 'listing') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=product_detail',
				'COM_REDSHOP_ADD_PRODUCT',
				(self::$view == 'product_detail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=mass_discount_detail',
				'COM_REDSHOP_ADD_MASS_DISCOUNT',
				(self::$view == 'mass_discount_detail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=mass_discount',
				'COM_REDSHOP_MASS_DISCOUNT',
				(self::$view == 'mass_discount') ? true : false
			);

		if (ECONOMIC_INTEGRATION == 1 && JPluginHelper::isEnabled('economic'))
		{
			$menu->section('product')
				->addItem(
					'index.php?option=com_redshop&view=product&layout=importproduct',
					'COM_REDSHOP_IMPORT_PRODUCTS_TO_ECONOMIC',
					(self::$view == 'product' && self::$layout == 'importproduct') ? true : false
				);

			if (ATTRIBUTE_AS_PRODUCT_IN_ECONOMIC == 1)
			{
				$menu->section('product')
				->addItem(
					'index.php?option=com_redshop&view=product&layout=importattribute',
					'COM_REDSHOP_IMPORT_ATTRIBUTES_TO_ECONOMIC',
					(self::$view == 'product' && self::$layout == 'importattribute') ? true : false
				);
			}
		}
	}

	protected static function setCategory()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('category')
			->title('COM_REDSHOP_CATEGORY')
			->addItem(
				'index.php?option=com_redshop&view=category',
				'COM_REDSHOP_CATEGORY_LISTING',
				(self::$view == 'category') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=category_detail',
				'COM_REDSHOP_ADD_CATEGORY',
				(self::$view == 'category_detail') ? true : false
			);
	}

	protected static function setManufacturer()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('manufacturer')
			->title('COM_REDSHOP_MANUFACTURER')
			->addItem(
				'index.php?option=com_redshop&view=manufacturer',
				'COM_REDSHOP_MANUFACTURER_LISTING',
				(self::$view == 'manufacturer') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=manufacturer_detail',
				'COM_REDSHOP_ADD_MANUFACTURER',
				(self::$view == 'manufacturer_detail') ? true : false
			);
	}

	protected static function setMedia()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('media')
			->title('COM_REDSHOP_MEDIA')
			->addItem(
				'index.php?option=com_redshop&view=media',
				'COM_REDSHOP_MEDIA_LISTING',
				(self::$view == 'media') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=media_detail',
				'COM_REDSHOP_BULK_UPLOAD',
				(self::$view == 'media_detail') ? true : false
			);
	}

	protected static function setOrder()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('order')
			->title('COM_REDSHOP_ORDER')
			->addItem(
				'index.php?option=com_redshop&view=order',
				'COM_REDSHOP_ORDER_LISTING',
				(self::$view == 'order' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=addorder_detail',
				'COM_REDSHOP_ADD_ORDER',
				(self::$view == 'addorder_detail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=order&layout=labellisting',
				'COM_REDSHOP_DOWNLOAD_LABEL',
				(self::$view == 'order' && self::$layout == 'labellisting') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=orderstatus',
				'COM_REDSHOP_ORDERSTATUS_LISTING',
				(self::$view == 'orderstatus') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=opsearch',
				'COM_REDSHOP_PRODUCT_ORDER_SEARCH',
				(self::$view == 'opsearch') ? true : false
			);
	}

	protected static function setQuotation()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('quotation')
			->title('COM_REDSHOP_QUOTATION')
			->addItem(
				'index.php?option=com_redshop&view=quotation',
				'COM_REDSHOP_QUOTATION_LISTING',
				(self::$view == 'quotation') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=addquotation_detail',
				'COM_REDSHOP_ADD_QUOTATION',
				(self::$view == 'addquotation_detail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=quotation_statistic',
				'COM_REDSHOP_QUOTATION_STATISTIC',
				(self::$view == 'quotation_statistic') ? true : false
			);
	}

	protected static function setStockroom()
	{
		if (USE_STOCKROOM == 0)
		{
			return;
		}

		$menu = RedshopAdminMenu::getInstance();

		$menu->section('stockroom')
			->title('COM_REDSHOP_STOCKROOM')
			->addItem(
				'index.php?option=com_redshop&view=stockroom',
				'COM_REDSHOP_STOCKROOM_LISTING',
				(self::$view == 'stockroom') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=stockroom_detail',
				'COM_REDSHOP_ADD_STOCKROOM',
				(self::$view == 'stockroom_detail' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=stockroom_listing',
				'COM_REDSHOP_STOCKROOM_AMOUNT_LISTING',
				(self::$view == 'stockroom_listing') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=stockimage',
				'COM_REDSHOP_STOCKIMAGE_LISTING',
				(self::$view == 'stockimage') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=stockimage_detail',
				'COM_REDSHOP_ADD_STOCKIMAGE',
				(self::$view == 'stockimage_detail') ? true : false
			);

		if (ECONOMIC_INTEGRATION && JPluginHelper::isEnabled('economic'))
		{
			$menu->addItem(
				'index.php?option=com_redshop&view=stockroom_detail&layout=importstock',
				'COM_REDSHOP_IMPORT_STOCK_FROM_ECONOMIC',
				(self::$view == 'stockroom_detail' && self::$layout == 'importstock') ? true : false
			);
		}
	}

	protected static function setSupplier()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('supplier')
			->title('COM_REDSHOP_SUPPLIER')
			->addItem(
				'index.php?option=com_redshop&view=supplier',
				'COM_REDSHOP_SUPPLIER_LISTING',
				(self::$view == 'supplier') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=supplier_detail',
				'COM_REDSHOP_ADD_SUPPLIER',
				(self::$view == 'supplier_detail') ? true : false
			);
	}

	protected static function setDiscount()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('discount')
			->title('COM_REDSHOP_DISCOUNT')
			->addItem(
				'index.php?option=com_redshop&view=discount',
				'COM_REDSHOP_DISCOUNT_LISTING',
				(self::$view == 'discount' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=discount_detail',
				'COM_REDSHOP_ADD_DISCOUNT',
				(self::$view == 'discount_detail' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=discount&layout=product',
				'COM_REDSHOP_DISCOUNT_PRODUCT_LISTING',
				(self::$view == 'discount' && self::$layout == 'product') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=discount_detail&layout=product',
				'COM_REDSHOP_ADD_DISCOUNT_PRODUCT',
				(self::$view == 'discount_detail' && self::$layout == 'product') ? true : false
			);
	}

	protected static function setGiftCard()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('giftcards')
			->title('COM_REDSHOP_GIFTCARD')
			->addItem(
				'index.php?option=com_redshop&view=giftcards',
				'COM_REDSHOP_GIFTCARD_LISTING',
				(self::$view == 'giftcards') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=giftcard&task=giftcard.edit',
				'COM_REDSHOP_ADD_GIFTCARD',
				(self::$view == 'giftcard') ? true : false
			);
	}

	protected static function setVoucher()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('voucher')
			->title('COM_REDSHOP_VOUCHER')
			->addItem(
				'index.php?option=com_redshop&view=voucher',
				'COM_REDSHOP_VOUCHER_LISTING',
				(self::$view == 'voucher') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=voucher_detail',
				'COM_REDSHOP_ADD_VOUCHER',
				(self::$view == 'voucher_detail') ? true : false
			);
	}

	protected static function setCoupon()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('coupon')
			->title('COM_REDSHOP_COUPON')
			->addItem(
				'index.php?option=com_redshop&view=coupon',
				'COM_REDSHOP_COUPON_LISTING',
				(self::$view == 'coupon') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=coupon_detail',
				'COM_REDSHOP_ADD_COUPON',
				(self::$view == 'coupon_detail') ? true : false
			);
	}

	protected static function setMail()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('mail')
			->title('COM_REDSHOP_MAIL_CENTER')
			->addItem(
				'index.php?option=com_redshop&view=mail',
				'COM_REDSHOP_MAIL_CENTER_LISTING',
				(self::$view == 'mail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=mail_detail',
				'COM_REDSHOP_ADD_MAIL_CENTER',
				(self::$view == 'mail_detail') ? true : false
			);
	}

	protected static function setNewsLetter()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('newsletter')
			->title('COM_REDSHOP_NEWSLETTER')
			->addItem(
				'index.php?option=com_redshop&view=newsletter',
				'COM_REDSHOP_NEWSLETTER_LISTING',
				(self::$view == 'newsletter') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=newsletter_detail',
				'COM_REDSHOP_ADD_NEWSLETTER',
				(self::$view == 'newsletter_detail' && self::$layout == '') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=newslettersubscr',
				'COM_REDSHOP_NEWSLETTER_SUBSCR_LISTING',
				(self::$view == 'newslettersubscr') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=newslettersubscr_detail',
				'COM_REDSHOP_ADD_NEWSLETTER_SUBSCR',
				(self::$view == 'newslettersubscr_detail') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=newsletter_detail&layout=statistics',
				'COM_REDSHOP_NEWSLETTER_STATISTICS',
				(self::$view == 'newsletter_detail' && self::$layout == 'statistics') ? true : false
			);
	}

	protected static function setShipping()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('shipping')
			->title('COM_REDSHOP_SHIPPING_METHOD')
			->addItem(
				'index.php?option=com_redshop&view=shipping',
				'COM_REDSHOP_SHIPPING_METHOD_LISTING',
				(self::$view == 'shipping') ? true : false
			)
			->addItem(
				'index.php?option=com_installer',
				'COM_REDSHOP_ADD_SHIPPING_METHOD'
			);

		if (ECONOMIC_INTEGRATION == 1 && JPluginHelper::isEnabled('economic'))
		{
			$menu->section('shipping')
				->addItem(
					'index.php?option=com_redshop&view=shipping&task=importeconomic',
					'COM_REDSHOP_IMPORT_RATES_TO_ECONOMIC'
				);
		}
	}

	protected static function setShippingBox()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('shipping_box')
			->title('COM_REDSHOP_SHIPPING_BOX')
			->addItem(
				'index.php?option=com_redshop&view=shipping_box',
				'COM_REDSHOP_SHIPPING_BOXES',
				(self::$view == 'shipping_box') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=shipping_box_detail',
				'COM_REDSHOP_ADD_SHIPPING_BOXES',
				(self::$view == 'shipping_box_detail') ? true : false
			);
	}

	protected static function setWrapper()
	{
		$menu = RedshopAdminMenu::getInstance();

		$menu->section('wrapper')
			->title('COM_REDSHOP_WRAPPER')
			->addItem(
				'index.php?option=com_redshop&view=wrapper',
				'COM_REDSHOP_WRAPPER_LISTING',
				(self::$view == 'wrapper') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=wrapper_detail',
				'COM_REDSHOP_ADD_WRAPPER',
				(self::$view == 'wrapper_detail') ? true : false
			);
	}

	protected static function setTax()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('tax_group')
			->title('COM_REDSHOP_TAX_GROUP')
			->addItem(
				'index.php?option=com_redshop&view=tax_group',
				'COM_REDSHOP_TAX_GROUP_LISTING',
				(self::$view == 'tax_group') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=tax_group_detail',
				'COM_REDSHOP_TAX_GROUP_DETAIL',
				(self::$view == 'tax_group_detail') ? true : false
			);
	}

	protected static function setCurrency()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('currency')
			->title('COM_REDSHOP_CURRENCY')
			->addItem(
				'index.php?option=com_redshop&view=currency',
				'COM_REDSHOP_CURRENCY_LISTING',
				(self::$view == 'currency') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=currency_detail',
				'COM_REDSHOP_ADD_CURRENCY',
				(self::$view == 'currency_detail') ? true : false
			);
	}

	protected static function setCountry()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('country')
			->title('COM_REDSHOP_COUNTRY')
			->addItem(
				'index.php?option=com_redshop&view=country',
				'COM_REDSHOP_COUNTRY_LISTING',
				(self::$view == 'country') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=country_detail',
				'COM_REDSHOP_ADD_COUNTRY',
				(self::$view == 'country_detail') ? true : false
			);
	}

	protected static function setState()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('state')
			->title('COM_REDSHOP_STATE')
			->addItem(
				'index.php?option=com_redshop&view=state',
				'COM_REDSHOP_STATE_LISTING',
				(self::$view == 'state') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=state_detail',
				'COM_REDSHOP_ADD_STATE',
				(self::$view == 'state_detail') ? true : false
			);
	}

	protected static function setZipcode()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('zipcode')
			->title('COM_REDSHOP_ZIPCODE')
			->addItem(
				'index.php?option=com_redshop&view=zipcode',
				'COM_REDSHOP_ZIPCODE_LISTING',
				(self::$view == 'zipcode') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=zipcode_detail',
				'COM_REDSHOP_ADD_ZIPCODE',
				(self::$view == 'zipcode_detail') ? true : false
			);
	}

	protected static function setCustomFields()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('fields')
			->title('COM_REDSHOP_FIELDS')
			->addItem(
				'index.php?option=com_redshop&view=fields',
				'COM_REDSHOP_FIELDS_LISTING',
				(self::$view == 'fields') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=fields_detail',
				'COM_REDSHOP_ADD_FIELD',
				(self::$view == 'fields_detail') ? true : false
			);
	}

	protected static function setTemplate()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('template')
			->title('COM_REDSHOP_TEMPLATE')
			->addItem(
				'index.php?option=com_redshop&view=template',
				'COM_REDSHOP_TEMPLATE_LISTING',
				(self::$view == 'template') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=template_detail',
				'COM_REDSHOP_ADD_TEMPLATE',
				(self::$view == 'template_detail') ? true : false
			);
	}

	protected static function setTextLibrary()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('textlibrary')
			->title('COM_REDSHOP_TEXT_LIBRARY')
			->addItem(
				'index.php?option=com_redshop&view=textlibrary',
				'COM_REDSHOP_TEXT_LIBRARY_LISTING',
				(self::$view == 'textlibrary') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=textlibrary_detail',
				'COM_REDSHOP_ADD_TEXT_LIBRARY_TAG',
				(self::$view == 'textlibrary_detail') ? true : false
			);
	}

	protected static function setCatelogue()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('catalog')
			->title('COM_REDSHOP_CATALOG_MANAGEMENT')
			->addItem(
				'index.php?option=com_redshop&view=catalog',
				'COM_REDSHOP_CATALOG',
				(self::$view == 'catalog') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=catalog_request',
				'COM_REDSHOP_CATALOG_REQUEST',
				(self::$view == 'catalog_request') ? true : false
			);
	}

	protected static function setProductSample()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('sample')
			->title('COM_REDSHOP_COLOUR_SAMPLE_MANAGEMENT')
			->addItem(
				'index.php?option=com_redshop&view=sample',
				'COM_REDSHOP_CATALOG_PRODUCT_SAMPLE',
				(self::$view == 'sample') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=sample_request',
				'COM_REDSHOP_SAMPLE_REQUEST',
				(self::$view == 'sample_request') ? true : false
			);
	}

	protected static function setCustomerTags()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('producttags')
			->title('COM_REDSHOP_TAGS')
			->addItem(
					'index.php?option=com_redshop&view=producttags',
					'COM_REDSHOP_TAGS_LISTING',
					(self::$view == 'producttags') ? true : false
				);
	}

	protected static function setAttributeBank()
	{
		$menu = RedshopAdminMenu::getInstance();
		$menu->section('attribute_set')
			->title('COM_REDSHOP_ATTRIBUTE_BANK')
			->addItem(
				'index.php?option=com_redshop&view=attribute_set',
				'COM_REDSHOP_ATTRIBUTE_SET_LISTING',
				(self::$view == 'attribute_set') ? true : false
			)
			->addItem(
				'index.php?option=com_redshop&view=attribute_set_detail',
				'COM_REDSHOP_ADD_ATTRIBUTE_SET',
				(self::$view == 'attribute_set_detail') ? true : false
			);
	}
}
