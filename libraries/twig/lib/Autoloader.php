<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Autoloads Twig classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Autoloader
{
	/**
	 * Registers Twig_Autoloader as an SPL autoloader.
	 *
	 * @param bool $prepend Whether to prepend the autoloader or not.
	 */
	public static function register($prepend = false)
	{
		if (PHP_VERSION_ID < 50300)
		{
			spl_autoload_register(array(__CLASS__, 'autoload'));
		}
		else
		{
			spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
		}
	}

	/**
	 * Handles autoloading of classes.
	 * Function updated by redCOMPONENT for its needs.
	 *
	 * @param string $class A class name.
	 */
	public static function autoload($class)
	{
		if (0 !== strpos($class, 'Twig'))
		{
			return;
		}

		$tmp = explode('_', $class);
		array_shift($tmp);
		$file = dirname(__FILE__) . '/' . implode('/', $tmp) . '.php';

		if (is_file($file))
		{
			require $file;
		}
	}
}
