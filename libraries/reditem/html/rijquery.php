<?php
/**
 * @package     Reditem
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * jQuery HTML class.
 *
 * @package     Reditem
 * @subpackage  Html
 * @since       1.0
 */
abstract class JHtmlRijquery
{
	/**
	 * Extension name to use in the asset calls
	 * Basically the media/com_xxxxx folder to use
	 */
	const EXTENSION = 'com_reditem';

	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Load the chosen library
	 *
	 * @param   string  $selector  CSS Selector to initialise selects
	 * @param   array   $options   Optional array parameters
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function chosen($selector = '.chosen', $options = array())
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		// Add chosen.jquery.js language strings
		JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
		JText::script('JGLOBAL_SELECT_AN_OPTION');
		JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');

		RHelperAsset::load('chosen/chosen.min.js', self::EXTENSION);
		RHelperAsset::load('chosen/chosen.min.css', self::EXTENSION);

		if (empty($options['disable_search_threshold']))
		{
			$options['disable_search_threshold'] = 10;
		}

		if (empty($options['allow_single_deselect']))
		{
			$options['allow_single_deselect'] = true;
		}

		if (empty($options['width']))
		{
			$options['width'] = 'auto';
		}

		$options = static::options2Jregistry($options)->toString();

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $selector . "').chosen(" . $options . ");
				});
			})(jQuery);
		");

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}

	/**
	 * Load the select2 library
	 *
	 * @param   string   $selector          CSS Selector to initialise selects
	 * @param   array    $options           Optional array with options
	 * @param   boolean  $bootstrapSupport  Load Twitter Bootstrap integration CSS
	 * @param   string   $lang              Language file to load.
	 *
	 * @return  void
	 */
	public static function select2($selector = '.select2', $options = null, $bootstrapSupport = true, $lang = '')
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		RHelperAsset::load('select2/select2.min.js', self::EXTENSION);
		RHelperAsset::load('select2/select2.min.css', self::EXTENSION);

		if ($bootstrapSupport)
		{
			RHelperAsset::load('select2/select2-bootstrap.min.css', self::EXTENSION);
		}

		if (!empty($lang) && JFile::exists(JPATH_REDITEM_MEDIA . 'js/select2/i18n/' . $lang . '.js'))
		{
			RHelperAsset::load('select2/i18n/' . $lang . '.js', self::EXTENSION);
		}

		// Generate options with default values
		$options = static::formatSelect2Options($options);

		JFactory::getDocument()->addScriptDeclaration("
			(function($){
				$(document).ready(function () {
					$('" . $selector . "').select2(
						" . $options . "
					);
				});
			})(jQuery);
		");

		static::$loaded[__METHOD__][$selector] = true;

		return;
	}

	/**
	 * Function to receive & pre-process select2 options
	 *
	 * @param   mixed  $options  Associative array/JRegistry object with options
	 *
	 * @return  json             The options ready for the select2() function
	 */
	private static function formatSelect2Options($options)
	{
		// Support options array
		if (is_array($options))
		{
			$options = new JRegistry($options);
		}

		if (!($options instanceof Jregistry))
		{
			$options = new JRegistry;
		}

		// Fix the width to resolve by default
		if ($options->get('width', null) === null)
		{
			$options->set('width', 'resolve');
		}

		return $options->toString();
	}

	/**
	 * Function to receive & pre-process javascript options
	 *
	 * @param   mixed  $options  Associative array/JRegistry object with options
	 *
	 * @return  JRegistry        Options converted to JRegistry object
	 */
	private static function options2Jregistry($options)
	{
		// Support options array
		if (is_array($options))
		{
			$options = new JRegistry($options);
		}

		if (!($options instanceof Jregistry))
		{
			$options = new JRegistry;
		}

		return $options;
	}
}
