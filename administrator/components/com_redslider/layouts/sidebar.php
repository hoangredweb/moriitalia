<?php
/**
 * @package     RedSLIDER
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDCORE') or die;

JLoader::import('helper', JPATH_ADMINISTRATOR . '/components/com_redslider/helpers');

$active = null;
$data = $displayData;

if (isset($data['active']))
{
	$active = $data['active'];
}

$sidebars = array(
	array('view' => 'cpanel', 'icon' => 'icon-home', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_DASHBOARD')),
	array('view' => 'galleries', 'icon' => 'icon-sitemap', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_GALLERIES')),
	array('view' => 'slides', 'icon' => 'icon-file-text', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_SLIDES')),
	array('view' => 'templates', 'icon' => 'icon-desktop', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_TEMPLATES')),
	array('view' => 'configuration', 'icon' => 'icon-cog', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_CONFIGURATION')),
	array('view' => 'help', 'icon' => 'icon-question-sign', 'text' => JText::_('COM_REDSLIDER_SIDEBAR_HELP'), 'target' => '_blank')
);

// Configuration link
$uri = JUri::getInstance();
$return = base64_encode('index.php' . $uri->toString(array('query')));
$configurationLink = 'index.php?option=com_redcore&view=config&layout=edit&component=com_redslider&return=' . $return;

// Help link
$helpLink = "http://wiki.redcomponent.com/index.php?title=RedSLIDER";
$target = '';
?>

<ul class="nav nav-pills nav-stacked redslider-sidebar">
	<li class="nav-header"><?php echo JText::_('COM_REDSLIDER_SIDEBAR_CPANEL'); ?></li>
	<?php foreach ($sidebars as $sidebar) : ?>
		<?php $class = ($active === $sidebar['view']) ? 'active' : ''; ?>
		<?php if(isset($sidebar['target'])) : ?>
			<?php $target= 'target="' . $sidebar['target'] . '"'; ?>
		<?php endif; ?>
		<li class="<?php echo $class; ?>">
			<?php if ($sidebar['view'] == 'configuration'): ?>
				<?php $link = $configurationLink; ?>
			<?php elseif ($sidebar['view'] == 'help'): ?>
				<?php $link = $helpLink; ?>
			<?php else: ?>
				<?php $link = JRoute::_('index.php?option=com_redslider&view=' . $sidebar['view']); ?>
			<?php endif; ?>
			<a href="<?php echo $link; ?>" <?php echo $target; ?>>
				<i class="<?php echo $sidebar['icon']; ?>"></i>
				<?php echo $sidebar['text']; ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
