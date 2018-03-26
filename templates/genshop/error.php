<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


foreach (debug_backtrace() as $bug)
{
    echo '<pre>';
    echo print_r($bug);
    echo '</pre>';
}

if (!isset($this->error))
{
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
//get language and direction
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage()); ?></title>

	<?php
		$debug = JFactory::getConfig()->get('debug_lang');
		if (JDEBUG || $debug)
		{
	?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/media/cms/css/debug.css" type="text/css" />
	<?php
		}
	?>
</head>
<style type="text/css">
#stage #main h1{
	color: #333333;
	font-size: 42px;
}
.text404 ul{
	padding: 20px 0;
}
.text404 ul li{
	list-style: none;
	display: inline-block;
	color: #000000;
	font-size: 250px;
	font-weight: bold;
}
.text404 ul li:last-child{
	color: #000000;
}
.text404 ul li.mid{
	color: #FF6C00;
	}
#stage a{
	color: #474749;
	text-decoration: none;
}
#stage a:hover{
	text-decoration: underline;
	}
</style>
<body style="background:#fff;">

	<div class="container" >
		<div id="stage" style="text-align:center;">

			<div id="main" style="text-align:center;">
				<h1><?php echo JText::_('PAGE_404_TITLE');?></h1>
				<div class="text404">
				<ul>
					<li>4</li>
					<li class="mid">0</li>
					<li>4</li>
				</ul>
				</div>
			</div><!--main-->
			<a style="font-family: openSans;font-size:15px;text-transform: uppercase;"
			href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>"><?php echo JText::_('PAGE_404_TYPE');?></a>
		</div>

		<jdoc:include type="modules" name="debug" />
</body>
</html>
