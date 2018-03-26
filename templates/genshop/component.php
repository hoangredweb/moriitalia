<?php
/**
 * @package    Template.Function
 *
 * @copyright  Copyright (C) 2005 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<!DOCTYPE html>
<html>
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/style.css" type="text/css" />
	</head>
	<style type="text/css">
		html{
			overflow: auto;
		}
	</style>
	<body class="contentpane">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
		<jdoc:include type="modules" name="debug" />
	</body>
</html>
