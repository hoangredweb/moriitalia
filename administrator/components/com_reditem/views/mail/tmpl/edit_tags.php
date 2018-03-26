<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Mail
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
?>
<?php if (!empty($this->mailTags)) : ?>
	<?php $i = 0; ?>
	<?php foreach ($this->mailTags as $section => $tags) : ?>
		<div class="accordion" id="accordion_tag_default">
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_tag_default" href="#collapse<?php echo $i ?>">
						<?php echo $section ?>
					</a>
				</div>
				<?php $cls = ($i == 0) ? ' in' : ''; ?>
				<div id="collapse<?php echo $i ?>" class="accordion-body collapse<?php echo $cls ?>">
					<div class="accordion-inner">
						<ul>
						<?php foreach ($tags as $tag => $description) : ?>
							<li>
								<span><?php echo $tag ?></span>
								<?php echo $description ?>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php $i++; ?>
	<?php endforeach; ?>
<?php endif; ?>