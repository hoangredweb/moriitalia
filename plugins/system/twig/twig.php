<?php
/**
 * @package     Twig
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * Plugin for enabling Twig library on files render.
 *
 * @package  Twig.Plugin
 *
 * @since    1.0.0
 */
class PlgSystemTwig extends JPlugin
{
	/**
	 * Twig render event. Used for processing content via Twig.
	 *
	 * @param   string  &$content  Content to process
	 * @param   string  $name      Name of the page
	 * @param   array   $data      Data to assign.
	 *
	 * @return  void
	 */
	public function onTwigRender(&$content, $name, $data)
	{
		$extensions = explode(',', $this->params->get('extensions', ''));
		$debug      = $this->params->get('debug', 0);
		$loader     = new Twig_Loader_Array(array($name => $content));

		if ($debug)
		{
			$twig = new Twig_Environment($loader, array('debug' => true));
			$twig->addExtension(new Twig_Extension_Debug);
		}
		else
		{
			$twig = new Twig_Environment($loader);
		}

		foreach ($extensions as $extension)
		{
			if (!empty($extension))
			{
				if (JFile::exists(JPATH_PLUGINS . '/system/twig/extensions/' . strtolower($extension) . '.php'))
				{
					require_once JPATH_PLUGINS . '/system/twig/extensions/' . $extension . '.php';
					$extension = ucfirst($extension . '_Twig_Extension');
					$twig->addExtension(new $extension);
				}
				else
				{
					try
					{
						$twig->addExtension(new $extension);
					}
					catch (Exception $e)
					{
						JLog::add($e->getMessage());
					}
				}
			}
		}

		$content = $twig->render($name, $data);
	}
}
