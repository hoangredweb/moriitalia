<?php
/**
 * @package     RedITEM2
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

/*
 * $config       Configuration of this filter.
 * $jsCallback   Javascript call back
 * $ranges       Available ranges
 * $category     Category object.
 * $value        Current value of this filter.
 */
extract($displayData);
?>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$("#reditemFilterAlphabet-<?php echo $category->id ?> .reditem-filter-alphabet-list li a").click(function(event){
				event.preventDefault();

				// Remove old active
				$("#reditemFilterAlphabet-<?php echo $category->id ?> .reditem-filter-alphabet-list li.active").each(function(index) {
					$(this).removeClass('active');
				});

				// Add active class
				$(this).parent().addClass('active');

				$("#reditemFilterAlphabet-<?php echo $category->id ?>-value").val($(this).text().trim());
				<?php echo $jsCallback; ?>();
			});
		});
	})(jQuery);
</script>

<div class="reditem-filter-alphabet" id="reditemFilterAlphabet-<?php echo $category->id ?>">
<?php if (!empty($ranges)): ?>
	<ul class="reditem-filter-alphabet-list">
	<?php foreach ($ranges as $range): ?>
		<li class="reditem-filter-alphabet-char <?php echo ($value == $range->char) ? 'active' : '' ?>">
		<?php if ($range->hasItem): ?>
			<a href="javascript:void(0);"><?php echo $range->char ?></a>
		<?php else: ?>
			<?php echo $range->char ?>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
	<input id="reditemFilterAlphabet-<?php echo $category->id ?>-value"
		type="hidden" name="filter_alphabet" value="<?php echo $value ?>" class="readonly hidden" />
<?php endif; ?>
</div>
