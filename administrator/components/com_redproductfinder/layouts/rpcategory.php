<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$categories = $displayData["categories"];
$selected = $displayData["selected"];
$catid = $displayData["catid"];
$input = JFactory::getApplication()->input;

?>
			<input type="hidden" name="catid"
				value="<?php
							if ($selected)
							{
								echo $selected[0];
							}
							else
							{
								echo "0";
							}
						?>">

			<select id="select-categories" onchange="submitform('association.setProduct');" name="jform[category_id]">
				<option value="0"><?php echo JText::_('COM_REDPRODUCTFINDER_MODELS_FORMS_ASSOCIATION_CATEGORY_ID_LABEL') ?></option>
				<?php foreach ($categories as $category_id => $category) : ?>
					<?php
						$isSelected = "";

						if ($catid) :
							if ($catid == (int) $category['category_id']) :
								$isSelected = "selected";
							endif;
						else :
							foreach ($selected as $select) :

								if ($select == (int) $category['category_id']) :
									$isSelected = "selected";
								endif;

							endforeach;
						endif;
					?>
				<option value="<?php echo $category['category_id'] ?>" <?php echo $isSelected; ?>><?php echo $category['category_name'] ?></option>
				<?php endforeach; ?>
			</select>