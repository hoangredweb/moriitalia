<?php

class WrightAdapterJoomlaNav
{
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
			$nav .= '<div class="navbar-header">
						<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Menu
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						
					</div>';

			$collapse = 'class="collapse navbar-collapse"';
		}

		$nav .= '<div ' . $collapse . ' id="nav-' . $args['name'] . '" >
					<jdoc:include type="modules" name="' . $args['name'] . '" style="' . $args['style'] . '" />
				</div></div></nav></div>';

		return $nav;
	}
}
