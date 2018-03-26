<?php

defined('_JEXEC') or die('You are not allowed to directly access this file');

require_once dirname(__FILE__) . '/lessc.inc.php';

class BuildBootstrap extends lessc_rw
{
	static function getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new Wright;
		}

		return $instance;
	}

	function start()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$document = JFactory::getDocument();

		$less_path = JPATH_THEMES . '/' . $document->template . '/less';

		// Check rebuild less
		$rebuild = $document->params->get('build', false);

		if (is_file(JPATH_THEMES . '/' . $document->template . '/css/style.css'))
		{
			$cachetime = filemtime(JPATH_THEMES . '/' . $document->template . '/css/style.css');

			$files = JFolder::files($less_path, '.less', true, true);

			if (count($files) > 0)
			{
				foreach ($files as $file)
				{
					if (filemtime($file) > $cachetime)
					{
						$rebuild = true;
						break;
					}
				}
			}
		}
		else
		{
			$rebuild = true;
		}

		// Build LESS
		if ($rebuild)
		{
			$this->setFormatter("compressed");

			$columnsNumber = $document->params->get('columnsNumber', 12);
			$this->setVariables(array('grid-columns' => $columnsNumber));

			if (is_file(JPATH_THEMES . '/' . $document->template . '/css/style.css'))
			{
				unlink(JPATH_THEMES . '/' . $document->template . '/css/style.css');
			}

			$df = dirname(__FILE__) . '/less/build.less';

			$ds = '@import "../../../less/variables.less"; ';
			$ds .= '@import "../less/bootstrap.less"; ';
			$ds .= '@import "../less/joomla.less"; ';

			if (version_compare(JVERSION, '3.0', 'lt'))
			{
				$ds .= '@import "../less/joomla25.less"; ';
			}
			else
			{
				$ds .= '@import "../less/joomla30.less"; ';
			}

			$ds .= '@import "../less/typography.less"; ';
			$ds .= '@import "../../../less/template.less"; ';

			if ($document->params->get('responsive', 1))
			{
				$ds .= '@import "../../../less/template-responsive.less"; ';
				$ds .= '@import "../libraries/redcomponent/redcomponent-responsive.less"; ';
			}
			else
			{
				$ds .= '.container{width:@container-desktop !important} .navbar-nav > li {float: left;} ';
			}

			file_put_contents($df, $ds);

			$this->compileFile($df, JPATH_THEMES . '/' . $document->template . '/css/style.css');

			unlink($df);

			$document->params->set('build', false);

			$newParams = new JRegistry(json_decode($document->params));

			$templateId = JFactory::getApplication()->getTemplate(true)->id;

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->update($db->quoteName('#__template_styles'))->set($db->quoteName('params') . ' = ' . $db->q($newParams))->where($db->quoteName('id') . ' = ' . $db->q($templateId));

			$db->setQuery($query);
			$db->execute();
		}
	}
}
