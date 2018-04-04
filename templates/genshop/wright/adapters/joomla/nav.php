<?php
/**
 * @package     Wright
 * @subpackage  Adapters
 *
 * @copyright   Copyright (C) 2005 - 2016 redCOMPONENT.com.  All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Wright Adapters
 *
 * @package     Wright
 * @subpackage  Adapters
 * @since       3.0
 */
class WrightAdapterJoomlaNav
{
	/**
	 * Render function
	 *
	 * @param   array  $args  Array
	 *
	 * @return  string
	 */
	public function render($args)
	{
		$doc = JFactory::getDocument();
		$responsive = $doc->params->get('responsive', 1); 

		// Set module name
		if (!isset($args['name']))
			$args['name'] = 'menu';

		// Set module name
		if (!isset($args['style']))
			$args['style'] = 'raw';

		// Set module name
		if (!isset($args['wrapClass']))
			$args['wrapClass'] = 'navbar-default';

		if (!isset($args['wrapper']))
			$args['wrapper'] = 'wrapper-' . $args['name'];

		if (!isset($args['type']))
			$args['type'] = 'menu';

		if (!isset($args['containerClass']))
			$args['containerClass'] = '';

		$collapse = "";

		$nav = '<div class="' . $args['wrapper'] . '">
					<nav id="' . $args['name'] . '" class="navbar ' . $args['wrapClass'] . '"  role="navigation">
						<div class="' . $args['containerClass'] . '">';

		if ($responsive)
		{
			$nav .= '<div class="navbar-header hidden-md hidden-lg hidden-sm">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-' . $args['name'] . '">
						  <span class="sr-only">Toggle navigation</span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						  <span class="icon-bar"></span>
						</button>
					</div>';

			$collapse = 'class="collapse navbar-collapse"';
		}

		$nav .= '<div ' . $collapse . ' id="nav-' . $args['name'] . '" >
					<jdoc:include type="modules" name="' . $args['name'] . '" style="' . $args['style'] . '" />
				</div></div></nav></div>';

		return $nav;
	}
}
