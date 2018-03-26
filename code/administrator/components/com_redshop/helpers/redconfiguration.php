<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

class Redconfiguration extends RedconfigurationDefault
{
	/**
	 * define default path
	 *
	 */
	public function __construct()
	{
		$basepath             = JPATH_SITE . '/administrator/components/com_redshop/helpers/';
		$this->configPath     = $basepath . 'redshop.cfg.php';
		$this->configDistPath = $basepath . 'wizard/redshop.cfg.dist.php';
		$this->configBkpPath  = $basepath . 'wizard/redshop.cfg.bkp.php';
		$this->configTmpPath  = $basepath . 'wizard/redshop.cfg.tmp.php';
		$this->configDefPath  = $basepath . 'wizard/redshop.cfg.def.php';

		if (!defined('JSYSTEM_IMAGES_PATH'))
		{
			define('JSYSTEM_IMAGES_PATH', JURI::root() . 'media/system/images/');
		}

		if (!defined('REDSHOP_ADMIN_IMAGES_ABSPATH'))
		{
			define('REDSHOP_ADMIN_IMAGES_ABSPATH', JURI::root() . 'administrator/components/com_redshop/assets/images/');
		}

		if (!defined('REDSHOP_FRONT_IMAGES_ABSPATH'))
		{
			define('REDSHOP_FRONT_IMAGES_ABSPATH', JURI::root() . 'components/com_redshop/assets/images/');
		}

		if (!defined('REDSHOP_FRONT_IMAGES_RELPATH'))
		{
			define('REDSHOP_FRONT_IMAGES_RELPATH', JPATH_ROOT . '/components/com_redshop/assets/images/');
		}

		if (!defined('REDSHOP_FRONT_VIDEOS_ABSPATH'))
		{
			define('REDSHOP_FRONT_VIDEOS_ABSPATH', JURI::root() . 'components/com_redshop/assets/video/');
		}

		if (!defined('REDSHOP_FRONT_VIDEOS_RELPATH'))
		{
			define('REDSHOP_FRONT_VIDEOS_RELPATH', JPATH_ROOT . '/components/com_redshop/assets/video/');
		}

		if (!defined('REDSHOP_FRONT_DOCUMENT_ABSPATH'))
		{
			define('REDSHOP_FRONT_DOCUMENT_ABSPATH', JURI::root() . 'components/com_redshop/assets/document/');
		}

		if (!defined('REDSHOP_FRONT_DOCUMENT_RELPATH'))
		{
			define('REDSHOP_FRONT_DOCUMENT_RELPATH', JPATH_ROOT . '/components/com_redshop/assets/document/');
		}
	}
}