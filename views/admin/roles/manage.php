<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?=base_url('assets/treegrid/')?>css/jquery.treegrid.css">

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_role'); ?></a>
                            <?php if(has_permission('roles', '', 'export')) { ?>
							    <a onclick="export_excel();"  class="btn btn-info mright5 test pull-right H_action_button"><?php echo _l('c_export_excel'); ?></a>
                            <?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>

						<table class="table tree">
                          	<thead>
                              	<th class="text-center"><?=_l('roles_dt_name')?></th>
                              	<th class="text-center"><?=_l('ch_categories_level')?></th>
                              	<th class="text-center"><?=_l('Phòng ban')?></th>
                              	<th class="text-center" style="width: 150px;"><?=_l('tnh_coefficient')?></th>
                              	<th class="text-center" style="width: 150px;"><?=_l('tnh_monthly_salary')?></th>
                              	<th class="text-center" style="width: 150px;"><?=_l('tnh_annual_salary')?></th>
                              	<th class="text-center" style="width: 100px;"><?=_l('options')?></th>
                          	</thead>
                          	<tbody>
                          		<?php @get_categories_roles($full_categories);?>
                          	</tbody>
                      	</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?=base_url('assets/treegrid/')?>js/jquery.treegrid.js"></script>
<script type="text/javascript">
  	$('.tree').treegrid({
    	initialState: 'collapsed',
  	});

    function export_excel() {
        var get = "?data=true";
        window.open(admin_url + 'roles/export_excel' + get, '_blank');
    }
</script>

<script>
	// initDataTable('.table-roles', window.location.href, [1], [1]);
</script>
</body>
</html>
