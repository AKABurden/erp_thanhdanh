<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php $checkExists = get_table_where('tbldelivery_policy_market',array(),'','row'); ?>
            <?php $checkExists2 = get_table_where('tbldelivery_policy_products',array(),'','row'); ?>
            <?php if (!$checkExists && !$checkExists2) { ?>
                <a href="<?=admin_url('delivery_policy/add');?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?>
                </a>
            <?php } else { ?>
                <a href="<?=admin_url('delivery_policy/edit');?>" class="btn btn-info pull-right H_action_button">
                    <?php echo _l('update'); ?>
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                            _l('#'),
                            _l('name_set_prices'),
                            _l('number_item'),
                            _l('date_active'),
                            _l('status'),
                            _l('range_customer'),
                            _l('range_item'),
                            _l('apply'),
                            _l('ch_option'),
                        ),'set_prices'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
</script>
</body>
</html>
