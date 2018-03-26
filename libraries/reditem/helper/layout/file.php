<?php
/**
 * @package     Reditem
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base class for rendering a display layout
 * loaded from from a layout file
 *
 * @package     Reditem
 * @subpackage  Layout
 * @see         http://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since       2.2.0
 */
class ReditemHelperLayoutFile extends RLayoutFile
{
	/**
	 * Get the default array of include paths
	 *
	 * @return  array
	 *
	 * @since   2.5.2
	 */
	public function getDefaultIncludePaths()
	{
		$template = JFactory::getApplication()->getTemplate();

		// Reset includePaths
		$paths = array();

		// (1 - highest priority) Received a custom high priority path
		if (!is_null($this->basePath))
		{
			$paths[] = rtrim($this->basePath, DIRECTORY_SEPARATOR);
		}

		// Component layouts & overrides if exist
		$component = $this->options->get('component', null);

		if (!empty($component))
		{
			// (2) Component template overrides path
			$paths[] = JPATH_THEMES . '/' . $template . '/html/layouts/' . $component;

			// (3) Specific type
			if (!empty($this->options['type']) && isset($this->options['type']->table_name))
			{
				$paths[] = JPATH_THEMES . '/' . $template . '/html/layouts/' . $component . '/type_' . $this->options['type']->table_name;
			}

			// (4) Component path
			if ($this->options->get('client') == 0)
			{
				$paths[] = JPATH_SITE . '/components/' . $component . '/layouts';
			}
			else
			{
				$paths[] = JPATH_ADMINISTRATOR . '/components/' . $component . '/layouts';
			}
		}

		// (4) Standard Joomla! layouts overriden
		$paths[] = JPATH_THEMES . '/' . $template . '/html/layouts';

		// (5) Our library path
		$paths[] = JPATH_LIBRARIES . '/reditem/layouts';

		// (6) Library path
		$paths[] = JPATH_LIBRARIES . '/redcore/layouts';

		// (7 - lower priority) Frontend base layouts
		$paths[] = JPATH_ROOT . '/layouts';

		return $paths;
	}

	/**
	 * Refresh the list of include paths.
	 * This override is required for older versions of Joomla (3.2.x ~ 3.4.x)
	 *
	 * @return  void
	 *
	 * @since   2.5.2
	 */
	protected function refreshIncludePaths()
	{
		$this->includePaths = $this->getDefaultIncludePaths();
	}
}
