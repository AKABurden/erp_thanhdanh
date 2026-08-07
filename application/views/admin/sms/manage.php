<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .border-name{
        border-radius: 9px;
        padding-left: 7px;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button">
                   <?php echo _l('Export excel'); ?></a>
            </div>
            <div class="pull-right mright5 H_border">
                <a href="<?=admin_url('sms/send_sms')?>" class="btn btn-info H_action_button">
                   <?php echo _l('create_add_new'); ?>
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('id'),
                        _l('cong_phone_sms'),
                        _l('cong_client_contact'),
                        _l('brand_name'),
                        _l('cong_datecreated'),
                        _l('cong_message'),
                        _l('cong_sms_create_by'),
                        _l('cong_date_send'),
                        _l('cong_status'),
                        _l('cong_active'),
                        ),'sms'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-sms', admin_url+'sms/table', [1], [1], {}, ['0', 'desc']);
   });
</script>
</body>
</html>
