<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Welcome Controller
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedsliderControllerWelcome extends JControllerLegacy
{
	/**
	 * Redirect to the panel.
	 *
	 * @return  void
	 */
	public function toPanel()
	{
		$this->setRedirect('index.php?option=com_redslider');
	}
}
