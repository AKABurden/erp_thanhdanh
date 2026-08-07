<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <a href="<?=admin_url('list_protocol/detail')?>" class="btn btn-info mright5 test pull-right H_action_button">
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
							_l('c_code_list_protocol'),
							_l('c_name_list_protocol'),
							_l('date_create'),
							_l('create_by'),
							_l('options'),
						),'list_protocol'); ?>
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
        tAPI = initDataTable('.table-list_protocol', admin_url + 'list_protocol/table', [0], [0], filterList, [0, 'desc']);
    });

    $.each(filterList, function(i, filter){
        $(filter).on('change', function(e){
            if($('.table-list_protocol').hasClass('dataTable')) {
                $('.table-list_protocol').DataTable().ajax.reload();
            }
        })
    })
</script>
