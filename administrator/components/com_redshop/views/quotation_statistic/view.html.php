<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopViewQuotation_statistic extends RedshopViewAdmin
{
	/**
	 * The request url.
	 *
	 * @var  string
	 */
	public $request_url;

	public $state;

	public function display($tpl = null)
	{
		$quotationHelper = quotationHelper::getInstance();

		$uri      = JFactory::getURI();
		$document = JFactory::getDocument();
		$model = $this->getModel();
		$amountStatistic = $model->getStatisticAmount();
		$saleStatistic = $model->getStatisticSale();

		$document->setTitle(JText::_('COM_REDSHOP_QUOTATION_STATISTIC'));

		JToolBarHelper::title(JText::_('COM_REDSHOP_QUOTATION_STATISTIC'), 'redshop_quotation48');

		$this->state   = $this->get('State');
		$filter_status = $this->state->get('filter_status', 0);
		$filter_sale   = $this->state->get('filter_sale', 0);
		$filteroption   = $this->state->get('filteroption', 0);

		$lists['order']     = $this->state->get('list.ordering', 'q.quotation_cdate');
		$lists['order_Dir'] = $this->state->get('list.direction', 'desc');

		$quotation  = $model->getQuotations();
		$pagination = $this->get('Pagination');

		$optionsection = $quotationHelper->getQuotationStatusList();
		$lists['filter_status'] = JHTML::_('select.genericlist', $optionsection, 'filter_status',
			'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_status
		);

		$saleList = $quotationHelper->getQuotationSale();
		$lists['filter_sale'] = JHTML::_('select.genericlist', $saleList, 'filter_sale',
			'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_sale
		);

		$option[] = JHTML::_('select.option', '0', JText::_('COM_REDSHOP_Select'));
		$option[] = JHTML::_('select.option', '1', JText::_('COM_REDSHOP_DAILY'));
		$option[] = JHTML::_('select.option', '2', JText::_('COM_REDSHOP_WEEKLY'));
		$option[] = JHTML::_('select.option', '3', JText::_('COM_REDSHOP_MONTHLY'));
		$option[] = JHTML::_('select.option', '4', JText::_('COM_REDSHOP_YEARLY'));
		$lists['filteroption'] = JHTML::_('select.genericlist', $option, 'filteroption',
			'class="inputbox" size="1" onchange="document.adminForm.submit();" ', 'value', 'text', $filteroption
		);

		$this->lists = $lists;
		$this->quotation = $quotation;
		$this->pagination = $pagination;
		$this->request_url = $uri->toString();
		$this->amountStatistic = $amountStatistic;
		$this->saleStatistic = $saleStatistic;
		$this->filteroption = $filteroption;

		parent::display($tpl);
	}
}
