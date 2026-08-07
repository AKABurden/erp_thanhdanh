<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <?php if(has_permission('contracts_sales','','create')){ ?>
                <div class="line-sp"></div>
                <a href="<?php echo admin_url('contracts_sales/template_detail'); ?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="clearfix mtop20"></div>
                    <?php $table_data = array(
                        _l('#'),
                        _l('cong_name_template'),
                        _l('ch_option')
                    );
                    render_datatable($table_data, 'contracts_sales_template dont-responsive-table');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
    var tAPI = initDataTable('.table-contracts_sales_template', admin_url+'contracts_sales/table_contracts_sales_template', [0], [0], [],[1, 'desc']);
});
</script>
</body>
</html>
