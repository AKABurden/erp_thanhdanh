<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="H_scroll">
		<div class="panel-body _buttons">
			<?php if(is_admin()) { ?>
			<a href="<?php echo admin_url('announcements/announcement'); ?>" class="btn btn-info pull-left H_action_button">
				<i class="lnr lnr-plus-circle" aria-hidden="true"></i>
	     		<?php echo _l('create_add_new'); ?>
			</a>
			<div class="line-sp"></div>
			<?php } else { echo '<h4 class="no-margin bold">'._l('announcements').'</h4>';} ?>
		</div>
	</div>
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<?php render_datatable(array(_l('name'),_l('announcement_date_list')),'announcements'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
