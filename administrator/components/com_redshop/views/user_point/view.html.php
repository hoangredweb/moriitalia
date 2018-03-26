<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;


class RedshopViewUser_point extends RedshopViewAdmin
{
	public $state;

	public function display($tpl = null)
	{
		$uri      = JFactory::getURI();
		$document = JFactory::getDocument();

		$document->setTitle(JText::_('COM_REDSHOP_USER'));

		$userhelper = new rsUserhelper;

		$this->state = $this->get('State');
		$spgrp_filter              = $this->state->get('spgrp_filter');

		$this->setLayout('default');
		JToolBarHelper::title(JText::_('COM_REDSHOP_USER_POINT_MANAGEMENT'), 'users redshop_user48');
		RedshopToolbarHelper::link('index.php?option=com_redshop&view=user_point_config', 'save', JText::_('COM_REDSHOP_USER_POINT_CONFIG'));

		$lists ['order']     = $this->state->get('list.ordering', 'users_info_id');
		$lists ['order_Dir'] = $this->state->get('list.direction');

		$user                = $this->get('Data');
		$pagination          = $this->get('Pagination');

		$shopper_groups      = $userhelper->getShopperGroupList();

		$temps               = array();
		$temps[0]            = new stdClass;
		$temps[0]->value     = 0;
		$temps[0]->text      = JText::_('COM_REDSHOP_SELECT');
		$shopper_groups      = array_merge($temps, $shopper_groups);

		$lists['shopper_group'] = JHTML::_('select.genericlist', $shopper_groups, 'spgrp_filter',
			'class="inputbox" size="1" onchange="document.adminForm.submit()"', 'value', 'text', $spgrp_filter
		);

		$this->lists       = $lists;
		$this->user        = $user;
		$this->pagination  = $pagination;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}
}
