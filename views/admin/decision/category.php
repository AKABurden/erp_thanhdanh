<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <a href="<?=admin_url('decision/detail_category')?>" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
					<?php echo _l('create_add_new'); ?>
                </a>
                <div class="line-sp"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
						<?php render_datatable(array(
							_l('#'),
							_l('c_code_category_decision'),
							_l('c_name_category_decision'),
							_l('date_create'),
							_l('create_by'),
							_l('options'),
						),'category_decision'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var filterList = {
        'datestart' : '[name="date_start"]',
        'dateend' : '[name="date_end"]',
    };
    var tAPI;
    $(function(){
        tAPI = initDataTable('.table-category_decision', admin_url + 'decision/table_category', [0], [0], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-category_decision').hasClass('dataTable')) {
                $('.table-category_decision').DataTable().ajax.reload();
            }
        })
    })
</script>
