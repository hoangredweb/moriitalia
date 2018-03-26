<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * redSHOP template manager
 *
 * @package  RedSHOP
 * @since    2.5
 */
class Redtemplate extends RedtemplateDefault
{
	/**
	 * Collect Mail Template Section Select Option Value
	 *
	 * @param   string  $sectionValue  Selected Section Name
	 *
	 * @return  array                 Mail Template Select list options
	 *
	 * @deprecated  2.0.0.3  Use RedshopHelperTemplate::getMailSections($sectionValue) instead
	 */
	public function getMailSections($sectionValue = "")
	{
		$options = array(
			'order'                             => JText::_('COM_REDSHOP_ORDER_MAIL'),
			'catalogue_order'                   => JText::_('COM_REDSHOP_CATALOGUE_ORDER_MAIL'),
			'order_special_discount'            => JText::_('COM_REDSHOP_ORDER_SPECIAL_DISCOUNT_MAIL'),
			'order_status'                      => JText::_('COM_REDSHOP_ORDER_STATUS_CHANGE'),
			'register'                          => JText::_('COM_REDSHOP_REGISTRATION_MAIL'),
			'product'                           => JText::_('COM_REDSHOP_PRODUCT_INFORMATION'),
			'tax_exempt_approval_mail'          => JText::_('COM_REDSHOP_TAX_EXEMPT_APPROVAL_MAIL'),
			'tax_exempt_disapproval_mail'       => JText::_('COM_REDSHOP_TAX_EXEMPT_DISAPPROVAL_MAIL'),
			'tax_exempt_waiting_approval_mail'  => JText::_('COM_REDSHOP_TAX_EXEMPT_WAITING_APPROVAL_MAIL'),
			'catalog'                           => JText::_('COM_REDSHOP_CATALOG_SEND_MAIL'),
			'catalog_first_reminder'            => JText::_('COM_REDSHOP_CATALOG_FIRST_REMINDER'),
			'catalog_second_reminder'           => JText::_('COM_REDSHOP_CATALOG_SECOND_REMINDER'),
			'catalog_coupon_reminder'           => JText::_('COM_REDSHOP_CATALOG_COUPON_REMINDER'),
			'colour_sample_first_reminder'      => JText::_('COM_REDSHOP_CATALOG_SAMPLE_FIRST_REMINDER'),
			'colour_sample_second_reminder'     => JText::_('COM_REDSHOP_CATALOG_SAMPLE_SECOND_REMINDER'),
			'colour_sample_third_reminder'      => JText::_('COM_REDSHOP_CATALOG_SAMPLE_THIRD_REMINDER'),
			'colour_sample_coupon_reminder'     => JText::_('COM_REDSHOP_CATALOG_SAMPLE_COUPON_REMINDER'),
			'first_mail_after_order_purchased'  => JText::_('COM_REDSHOP_FIRST_MAIL_AFTER_ORDER_PURCHASED'),
			'second_mail_after_order_purchased' => JText::_('COM_REDSHOP_SECOND_MAIL_AFTER_ORDER_PURCHASED'),
			'third_mail_after_order_purchased'  => JText::_('COM_REDSHOP_THIRD_MAIL_AFTER_ORDER_PURCHASED'),
			'economic_inoice'                   => JText::_('COM_REDSHOP_ECONOMIC_INVOICE'),
			'newsletter_confirmation'           => JText::_('COM_REDSHOP_NEWSLETTER_CONFIRMTION'),
			'newsletter_cancellation'           => JText::_('COM_REDSHOP_NEWSLETTER_CANCELLATION'),
			'mywishlist_mail'                   => JText::_('COM_REDSHOP_WISHLIST_MAIL'),
			'ask_question_mail'                 => JText::_('COM_REDSHOP_ASK_QUESTION_MAIL'),
			'downloadable_product_mail'         => JText::_('COM_REDSHOP_DOWNLOADABLE_PRODUCT_MAIL'),
			'giftcard_mail'                     => JText::_('COM_REDSHOP_GIFTCARD_MAIL'),
			'invoice_mail'                      => JText::_('COM_REDSHOP_INVOICE_MAIL'),
			'quotation_mail'                    => JText::_('COM_REDSHOP_QUOTATION_MAIL'),
			'quotation_user_register'           => JText::_('COM_REDSHOP_QUOTATION_USER_REGISTER_MAIL'),
			'request_tax_exempt_mail'           => JText::_('COM_REDSHOP_REQUEST_TAX_EXEMPT_MAIL'),
			'subscription_renewal_mail'         => JText::_('COM_REDSHOP_SUBSCRIPTION_RENEWAL_MAIL'),
			'review_mail'                       => JText::_('COM_REDSHOP_REVIEW_MAIL'),
			'notify_stock_mail'                 => JText::_('COM_REDSHOP_NOTIFY_STOCK'),
			'invoicefile_mail'                  => JText::_('COM_REDSHOP_INVOICE_FILE_MAIL'),
			'notify_coupon'                     => JText::_('COM_REDSHOP_NOTIFY_COUPON'),
			'introduce_friend'                  => JText::_('COM_REDSHOP_INTRODUCE_FRIEND')
		);

		return self::prepareSectionOptions($options, $sectionValue);
	}
}