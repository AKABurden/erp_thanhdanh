<div <?= !empty($pull_right) && $pull_right == "not" ? '' : 'class="pull-right"' ?>>
	<ol class="breadcrumb" <?= !empty($pull_right) && $pull_right == "not" ? 'style="margin-bottom: 5px"' : '' ?>>
		<li>
			<a href="<?= base_url('admin/') ?>"><?= lang('home') ?></a>
		</li>
		<?php
		foreach ($breadcrumb as $b) {
			if ($b['link'] === '#') {
				echo '<li class="active">' . $b['page'] . '</li>';
			} else {
				echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
			}
		}
		?>
	</ol>
</div>