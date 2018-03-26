<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;


class RedshopViewUser_point_config extends RedshopViewAdmin
{
	public function display($tpl = null)
	{
		$db = JFactory::getDbo();

		$uri = JFactory::getURI();

		$this->setLayout('default');

		$lists = array();

		$details = $this->get('data');


		JToolBarHelper::title(JText::_('COM_REDSHOP_USER_POINT_CONFIG'), 'tag redshop_vat48');

		JToolBarHelper::save();

		if (!empty($isNew))
		{
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::cancel('cancel', JText::_('JTOOLBAR_CLOSE'));
		}

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__redshop_shopper_group'));

		$groups = $db->setQuery($query)->loadObjectList();

		foreach ($groups as $key => $group)
		{
			foreach ($details as $i => $detail)
			{
				if ($group->shopper_group_id == $detail->shoppergroup_id)
				{
					$groups[$key]->point = $detail->point;
				}
			}
		}

		$lists['groups'] = $groups;

		$this->lists = $lists;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}
}
