<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$redhelper       = new redhelper;
$app      = JFactory::getApplication();
$menus    = $app->getMenu('site');
$items = $menus->getItems($attributes, $values);
$Itemid = 0;
$arr = array('option' => 'com_content', 'view' => 'category', 'layout' => 'blog' );

foreach ($items as $value) {

	if ($redhelper->checkMenuQuery($value, $arr ))
	{
		$Itemid = $value->id;

		break;
	}
}

?>
<div class="mod_reditem_items_wrapper">
	<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) :  ?>
		<?php
			$images  = json_decode($item->images);
			$link = JRoute::_('index.php?option=com_content&view=article&id='.$item->id.'&Itemid='.$Itemid);
		?>
		<li itemscope itemtype="http://schema.org/Article">
			<div class="row homeblog">
				<div class="col-sm-6 i-image">
					<div class="reditem_image reditem_image_16 " style="background: url(<?php echo $images->image_intro; ?>) top center;">
						<a href="<?php echo $link; ?>" itemprop="url">
							<img class="" src="<?php echo $images->image_intro; ?>"  style="visibility: hidden;" alt="blogimage">
						</a>
					</div>
				</div>
				<div class="col-sm-6 s-content">	
					<label>Blog</label>		
					<span itemprop="name">
						<div class="title"><?php echo $item->title; ?></div>
						<?php echo JHTML::_('string.truncate', ($item->introtext), 100) ;?>
						<a class="btn btn-info btn-sm" href="<?php echo $link; ?>">discover</a>
					</span>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
