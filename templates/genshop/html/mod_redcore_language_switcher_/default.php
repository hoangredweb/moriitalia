<?php
/**
 * @package     Redcore.Module.LanguageSwitcher
 * @subpackage  mod_redcore_language_switcher
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$language = JFactory::getLanguage();
$menu = $app->getMenu();
$input = $app->input;

$items = $menu->getMenu();
$data = array();

foreach ($items as $key => $item)
{
	if (count(array_diff($item->query, $menu->getActive()->query)) === 0)
	{
		$data[$item->language] = $item->id;
	}
}

foreach ($list as $k => $value)
{
	if ($value->sef == 'vi')
	{
		$list[$k]->link = RRoute::_($menu->getActive()->link . '&Itemid=' . $data['*'] . '&lang=' . $value->sef);
	}
	else
	{
		$list[$k]->link = RRoute::_($menu->getActive()->link . '&Itemid=' . $data['en-GB'] . '&lang=' . $value->sef);
	}
}

?>
<div class="mod-languages<?php echo $moduleclass_sfx ?>">
<?php if ($headerText) : ?>
	<div class="pretext"><p><?php echo $headerText; ?></p></div>
<?php endif; ?>

<?php if ($params->get('dropdown', 1)) : ?>
	<form name="lang" method="post" action="<?php echo htmlspecialchars(JUri::current()); ?>">
	<select class="inputbox" onchange="document.location.replace(this.value);" >
	<?php foreach ($list as $language) : ?>
		<option
			dir=<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? '"rtl"' : '"ltr"'?>
			value="<?php echo $language->link;?>" <?php echo $language->active ? 'selected="selected"' : ''?>>
			<?php echo $language->title_native;?>
		</option>
	<?php endforeach; ?>
	</select>
	</form>
<?php else : ?>
	<ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block';?>">
	<?php foreach ($list as $language) : ?>
		<?php if ($params->get('show_active', 0) || !$language->active):?>
			<li
				class="<?php echo $language->active ? 'lang-active' : '';?>"
				dir="<?php echo JLanguage::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr' ?>">
				<a href="<?php echo $language->link;?>">
					<?php if ($params->get('image', 1)):?>
						<?php echo JHtml::_(
							'image',
							'mod_languages/' . $language->image . '.gif',
							$language->title_native,
							array('title' => $language->title_native),
							true
						);?>
					<?php else : ?>
						<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef);?>
					<?php endif; ?>
				</a>
			</li>
		<?php endif;?>
	<?php endforeach;?>
	</ul>
<?php endif; ?>

<?php if ($footerText) : ?>
	<div class="posttext"><p><?php echo $footerText; ?></p></div>
<?php endif; ?>
</div>
