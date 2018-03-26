<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;



class RedshopViewAccount_billto extends RedshopViewAccount_billtoDefault
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 */
	public function display($tpl = null)
	{
		$app         = JFactory::getApplication();
		$extra_field = extraField::getInstance();

		$params = $app->getParams('com_redshop');

		$billingaddresses = $GLOBALS['billingaddresses'];

		if (count($billingaddresses) <= 0)
		{
			$model            = $this->getModel('account_billto');
			$billingaddresses = $model->_initData();
		}

		$user    = JFactory::getUser();
		$uri     = JFactory::getURI();
		$session = JFactory::getSession();
		$auth    = $session->get('auth');

		if (!is_array($auth))
		{
			$auth['users_info_id'] = 0;
			$session->set('auth', $auth);
			$auth = $session->get('auth');
		}

		JHtml::_('redshopjquery.framework');
		JHtml::script('com_redshop/jquery.validate.js', false, true);
		JHtml::script('com_redshop/common.js', false, true);
		JHtml::script('com_redshop/registration.js', false, true);
		JHtml::stylesheet('com_redshop/validation.css', array(), true);

		// Preform security checks
		if ($user->id == 0 && $auth['users_info_id'] == 0)
		{
			$Itemid    = JRequest::getInt('Itemid');
			$redhelper = new redhelper;
			$menus     = $redhelper->getRedshopMenuItems();
			foreach ($menus as $oneMenuItem)
			{
				if ($redhelper->checkMenuQuery($oneMenuItem, array('option' => 'com_redshop', 'view' => 'account')))
				{
					$Itemid_login = $oneMenuItem->id;
					if ( $Itemid == 0 ) {
						$Itemid = $Itemid_login;
					}
					break;
				}
			}
			$url = 'index.php?option=com_redshop&view=login&Itemid=' . $Itemid;
			$app->redirect( jRoute::_($url) );
			exit;
		}

		$lists['requesting_tax_exempt'] = JHTML::_('select.booleanlist', 'requesting_tax_exempt', 'class="inputbox"', @$billingaddresses->requesting_tax_exempt);

		if ($billingaddresses->is_company)
		{
			$lists['extra_field_company'] = $extra_field->list_all_field(8, $billingaddresses->users_info_id);
		}
		else
		{
			$lists['extra_field_user'] = $extra_field->list_all_field(7, $billingaddresses->users_info_id);
		}

		$this->request_url = $uri->toString();

		$this->lists            = $lists;
		$this->billingaddresses = $billingaddresses;
		JFilterOutput::cleanText($this->request_url);
		$this->params           = $params;

		parent::display($tpl);
	}
}
