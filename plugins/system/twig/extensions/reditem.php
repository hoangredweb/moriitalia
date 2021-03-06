<?php
/**
 * @package     RedITEM.Plugins
 * @subpackage  System.Twig
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;
defined('JPATH_REDITEM_LIBRARY') or die('redITEM library missing!');

/**
 * Class Reditem_Twig_Extension for generating custom functions, filters, tokens and other Twig stuff.
 *
 * @package     RedITEM.Plugins
 * @subpackage  System.Twig
 * @since       1.0.1
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
					if (empty($itemImage))
					{
						return '';
					}

					if (strpos("\\/", $itemImage))
					{
						$tmp = explode("\\/", str_replace(array('[',']','"'), '', $itemImage));
					}
					else
					{
						$tmp = explode('/', str_replace(array('[',']','"'), '', $itemImage));
					}

					$id    = (int) $tmp[0];
					$image = isset($tmp[1]) ? $tmp[1] : '';
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
				'categoryCustomfieldImage',
				function($catImage, $pathOnly = false, $width = 0, $height = 0)
				{
					if (empty($catImage))
					{
						return '';
					}

					if (strpos("\\/", $catImage))
					{
						$tmp = explode("\\/", str_replace(array('[',']','"'), '', $catImage));
					}
					else
					{
						$tmp = explode('/', str_replace(array('[',']','"'), '', $catImage));
					}

					$id    = (int) $tmp[0];
					$image = isset($tmp[1]) ? $tmp[1] : '';
					$model = RModelAdmin::getInstance('Category', 'ReditemModel', array('ignore_request' => true));
					$item  = $model->getItem($id);

					if ($pathOnly && !$width && !$height)
					{
						return ReditemHelperImage::getImageLink($item, 'categoryfield', $image, '', $width, $height, $pathOnly);
					}

					return ReditemHelperImage::getImageLink($item, 'categoryfield', $image, 'twigFilter', $width, $height, $pathOnly);
				},
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFilter(
				'jsonGet',
				function($json, $param = '')
				{
					$decode = json_decode($json, true);

					if (is_array($decode) && !empty($decode[$param]))
					{
						return $decode[$param];
					}

					return $decode;
				}
			),
			new Twig_SimpleFilter(
				'formatDate',
				function($date, $format, $tz = '')
				{
					$date = JDate::getInstance($date);

					if (!empty($tz))
					{
						$date->setTimezone(new DateTimeZone($tz));
					}

					return $date->format($format, true);
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
						case 'field':
							if (is_string($obj))
							{
								$decode = json_decode($obj, true);
								$decode = (array) $decode;
								$link   = new stdClass;

								if (!empty($decode['link']))
								{
									$link->link = $decode['link'];
								}
								elseif (!empty($decode[0]))
								{
									$link->link = $decode[0];
								}
								else
								{
									$link->link = '';
								}

								if (!empty($decode['title']))
								{
									$link->title = $decode['title'];
								}
								elseif (!empty($decode[1]))
								{
									$link->title = $decode[1];
								}
								else
								{
									$link->title = '';
								}

								if (!empty($decode['target']))
								{
									$link->target = $decode['target'];
								}
								elseif (!empty($decode[2]))
								{
									$link->target = $decode[2];
								}
								else
								{
									$link->target = '';
								}
							}

							break;
						default:
							break;
					}

					return $link;
				}
			),
			new Twig_SimpleFilter(
				'valueSort',
				function($items, $value, $reverse = false)
				{
					uasort($items, array(new OrderingClass($value, (bool) $reverse), 'fieldOrdering'));

					return $items;
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
					while ($min == 0 && $excludeNull && !empty($arr));

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
			),
			new Twig_SimpleFunction(
				'jtext',
				function ($string = '', $function = '_')
				{
					JFactory::getApplication()->loadLanguage();

					return JText::$function($string);
				},
				array('is_safe' => array('html'))
			),
			new Twig_SimpleFunction(
				'userInGroup',
				function ($groups)
				{
					$user       = JFactory::getUser();
					$userGroups = $user->getAuthorisedGroups();
					$groups     = is_array($groups) ? $groups : array($groups);

					if (!empty(array_intersect($userGroups, $groups)))
					{
						return true;
					}

					return false;
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

/**
 * Class OrderingClass for ordering an array by element field.
 *
 * @package     RedITEM.Plugins
 * @subpackage  System.Twig
 * @since       1.0.1
 */
class OrderingClass
{
	/**
	 * Ordering field.
	 *
	 * @var string
	 * @since 1.0.1
	 */
	private $orderingField = '';

	/**
	 * Reverse ordering switch.
	 *
	 * @var bool
	 * @since 1.0.1
	 */
	private $reverse = false;

	/**
	 * RedItemItems constructor.
	 *
	 * @param   string  $field    Field to order by.
	 * @param   int     $reverse  Reverse switch.
	 *
	 * @since   1.0.1
	 */
	public function __construct($field, $reverse)
	{
		$this->orderingField = $field;
		$this->reverse       = $reverse;
	}

	/**
	 * Function for ordering items by any of its field.
	 *
	 * @param   object  $a  1st element for ordering.
	 * @param   object  $b  2nd element for ordering.
	 *
	 * @return  int
	 *
	 * @since 1.0.1
	 */
	public function fieldOrdering($a, $b)
	{
		if ($a->{$this->orderingField} == $b->{$this->orderingField})
		{
			return 0;
		}

		if ($this->reverse)
		{
			return ($a->{$this->orderingField} > $b->{$this->orderingField}) ? -1 : 1;
		}

		return ($a->{$this->orderingField} < $b->{$this->orderingField}) ? -1 : 1;
	}
}
