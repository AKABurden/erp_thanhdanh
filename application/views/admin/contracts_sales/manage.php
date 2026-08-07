<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .table-contracts_sales thead tr th{
        text-align: center;
    }
    .table-contracts_sales tbody tr td:nth-child(2){
        white-space: inherit;
        min-width: 125px;
        text-align: center;
    }
    .table-contracts_sales tbody tr td:nth-child(3){
        white-space: inherit;
        min-width: 250px;
    }
    .table-contracts_sales tbody tr td:nth-child(4){
        white-space: inherit;
        min-width: 200px;
    }
    .table-contracts_sales tbody tr td:nth-child(5){
        white-space: inherit;
        min-width: 85px;
        text-align: center;
    }
    .table-contracts_sales tbody tr td:nth-child(6){
        white-space: inherit;
        min-width: 100px;
    }
    .table-contracts_sales tbody tr td:nth-child(7){
        white-space: inherit;
        min-width: 120px;
        text-align: right;
    }
    .table-contracts_sales tbody tr td:nth-child(8){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
    .table-contracts_sales tbody tr td:nth-child(9){
        white-space: inherit;
        min-width: 100px;
        text-align: center;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <?php if(has_permission('contracts_sales','','create')){ ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?php echo admin_url('contracts_sales/detail'); ?>" class="btn btn-info H_action_button">
                        <?php echo _l('create_add_new'); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="horizontal-scrollable-tabs">
                        <div class="scroller scroller-left arrow-left disabled" style="display: block;"><i class="fa fa-angle-left"></i></div>
                        <div class="scroller scroller-right arrow-right" style="display: block;"><i class="fa fa-angle-right"></i></div>
                        <div class="horizontal-tabs">
                            <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                <li class="active">
                                    <a class="H_filter" data-id="">
                                        <?=_l('cong_all')?> <b class="filter_"></b>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" id="filterStatus" name="filterStatus" value=""/>
                    <div class="clearfix mtop20"></div>
                    <?php $table_data = array(
                        _l('#'),
                        _l('code_contract_sales'),
                        _l('clients'),
                        _l('title_contract_sales'),
                        _l('cong_code_orders'),
                        _l('als_staff'),
                        _l('contract_value'),
                        _l('date_start'),
                        _l('date_end'),
                        _l('ch_option'),
                    );
                    render_datatable($table_data, 'contracts_sales');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
var tAPI;
$(function(){
    var CustomersServerParams = {
      'filterStatus' : '[name="filterStatus"]',
    };
    tAPI = initDataTableCustom('.table-contracts_sales', admin_url+'contracts_sales/table', [0], [0], CustomersServerParams,[1, 'desc']);
    $.each(CustomersServerParams, function(filterIndex, filterItem){
      $('' + filterItem).on('change', function(){
        tAPI.draw('page');
      });
    });
});
$(document).on('click', '.delete-remind', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            tAPI.draw('page');
        }, 'json');
    }
    return false;
});
</script>
</body>
</html>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>