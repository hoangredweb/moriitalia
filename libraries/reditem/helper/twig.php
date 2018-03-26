<?php
/**
 * @package     RedITEM.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

/**
 * Twig helper for loading custom functions.
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Twig
 * @since       2.1.19
 *
 */
class ReditemHelperTwig
{
	/**
	 * Reditem_Twig_Extension reference.
	 *
	 * @var Reditem_Twig_Extension
	 */
	private static $extension = null;

	/**
	 * Get Twig extension for adding it to Twig environment.
	 *
	 * @return   Reditem_Twig_Extension  Twig extension object.
	 */
	public static function getExtension()
	{
		if (is_null(self::$extension))
		{
			self::$extension = new Reditem_Twig_Extension;
		}

		return self::$extension;
	}
}

/**
 * Class Reditem_Twig_Extension for generating custom functions, filters, tokens and other Twig stuff.
 *
 * @package     RedITEM.Libraries
 * @subpackage  Helper.Twig
 * @since       2.1.19
 *
 */
class Reditem_Twig_Extension extends Twig_Extension
{
	/**
	 * Initializes the runtime environment.
	 * This is where you can load some file that contains filter functions for instance.
	 *
	 * @param   Twig_Environment  $environment  The current Twig_Environment instance
	 *
	 * @return  void
	 */
	public function initRuntime(Twig_Environment $environment)
	{
	}

	/**
	 * Returns the token parser instances to add to the existing list.
	 *
	 * @return   array  An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
	 */
	public function getTokenParsers()
	{
		return array();
	}

	/**
	 * Returns the node visitor instances to add to the existing list.
	 *
	 * @return   array  An array of Twig_NodeVisitorInterface instances
	 */
	public function getNodeVisitors()
	{
		return array();
	}

	/**
	 * Returns a list of filters to add to the existing list.
	 *
	 * @return   array  An array of filters
	 */
	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter(
				'shuffle',
				function($array)
				{
					if ($array instanceof Traversable)
					{
						$array = iterator_to_array($array, false);
					}

					shuffle($array);

					return $array;
				}
			),
			new Twig_SimpleFilter(
				'itemCustomfieldImage',
				function($itemImage, $pathOnly = false, $width = 0, $height = 0)
				{
					$tmp   = explode("\\/", str_replace(array('[',']','"'), '', $itemImage));
					$id    = (int) $tmp[0];
					$image = $tmp[1];
					$model = RModelAdmin::getInstance('Item', 'ReditemModel', array('ignore_request' => true));
					$item  = $model->getItem($id);

					if ($pathOnly && !$width && !$height)
					{
						return ReditemHelperImage::getImageLink($item, 'customfield', $image, '', $width, $height, $pathOnly);
					}

					return ReditemHelperImage::getImageLink($item, 'customfield', $image, 'twigFilter', $width, $height, $pathOnly);
				},
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFilter(
				'jsonGet',
				function($json, $param)
				{
					$decode = json_decode($json);

					if (isset($decode->$param))
					{
						return $decode->$param;
					}

					return '';
				}
			),
			new Twig_SimpleFilter(
				'getLink',
				function($obj, $type)
				{
					$link = '';

					switch ($type)
					{
						case 'item':
							$link = JRoute::_(ReditemHelperRouter::getItemRoute($obj->id), false);

							break;
						case 'category':
							$link = JRoute::_(ReditemHelperRouter::getCategoryRoute($obj->id), false);

							break;
						default:
							break;
					}

					return $link;
				}
			)
		);
	}

	/**
	 * Returns a list of tests to add to the existing list.
	 *
	 * @return   array  An array of tests
	 */
	public function getTests()
	{
		return array(
			new Twig_SimpleTest(
				'mobileView',
				function ($page)
				{
					return ReditemHelperMobiledetect::isMobile($page);
				}
			),
			new Twig_SimpleTest(
				'ajaxView',
				function ($page)
				{
					if (isset($page['HTTP_X_REQUESTED_WITH']) && strtolower($page['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
					{
						return true;
					}

					return false;
				}
			),
			new Twig_SimpleTest(
				'tabletView',
				function ($page)
				{
					return ReditemHelperMobiledetect::isTablet($page);
				}
			)
		);
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return   array  An array of functions
	 */
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction(
				'loadPosition',
				function ($position)
				{
					$modules  = JModuleHelper::getModules($position);
					$renderer = JFactory::getDocument()->loadRenderer('module');
					$html     = '';

					foreach ($modules as $module)
					{
						$html .= $renderer->render($module);
					}

					return $html;
				},
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFunction(
				'loadModule',
				function ($name = '', $title = '')
				{
					$html = '';

					if (!empty($name) && !empty($title))
					{
						$module   = JModuleHelper::getModule($name, $title);
						$renderer = JFactory::getDocument()->loadRenderer('module');
						$html    .= $renderer->render($module);
					}
					elseif (!empty($name))
					{
						$modules  = JModuleHelper::getModuleList();
						$clean    = array();
						$renderer = JFactory::getDocument()->loadRenderer('module');

						foreach ($modules as $module)
						{
							if ($module->module == $name)
							{
								$clean[] = $module;
							}
						}

						$clean = JModuleHelper::cleanModuleList($clean);

						foreach ($clean as $module)
						{
							$html .= $renderer->render($module);
						}
					}
					elseif (!empty($title))
					{
						$modules  = JModuleHelper::getModuleList();
						$clean    = array();
						$renderer = JFactory::getDocument()->loadRenderer('module');

						foreach ($modules as $module)
						{
							if ($module->title == $title)
							{
								$clean[] = $module;
							}
						}

						$clean = JModuleHelper::cleanModuleList($clean);

						foreach ($clean as $module)
						{
							$html .= $renderer->render($module);
						}
					}

					return $html;
				},
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFunction(
				'array_min',
				function ($arr, $excludeNull = true)
				{
					do
					{
						$ele = array_shift($arr);

						if (is_string($ele))
						{
							$ele = trim($ele);
						}

						$min = (int) $ele;
					}
					while($min == 0 && $excludeNull && !empty($arr));

					foreach ($arr as $val)
					{
						if (is_string($val))
						{
							$ele = trim($val);
							$v   = (int) $ele;
						}
						else
						{
							$v = $val;
						}

						if ($excludeNull)
						{
							if ($v !== 0 && $v < $min)
							{
								$min = $v;
							}
						}
						elseif ($v < $min)
						{
							$min = $v;
						}
					}

					return $min;
				}
			)
		);
	}

	/**
	 * Returns a list of operators to add to the existing list.
	 *
	 * @return   array  An array of operators
	 */
	public function getOperators()
	{
		return array();
	}

	/**
	 * Returns a list of global variables to add to the existing list.
	 *
	 * @return   array  An array of global variables
	 */
	public function getGlobals()
	{
		return array();
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return   string  The extension name
	 */
	public function getName()
	{
		return 'redITEM Twig extension';
	}
}
