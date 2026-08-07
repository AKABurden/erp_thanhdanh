<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    i.check-danger {
        color: #eb0d17!important;;
        border: 1px dashed #eb0d17!important;;
    }

    i.check-success{
        color: #84c529!important;
        border: 1px dashed #84c529!important;;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button">
                   <?php echo _l('Export excel'); ?></a>
            </div>
            <div class="pull-right mright5 H_border">
                <a href="<?=admin_url('automations/detail')?>" class="btn btn-info H_action_button">
                   <?php echo _l('create_add_new'); ?>
                </a>
            </div>
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
                        _l('cong_name'),
                        _l('cong_object'),
                        _l('cong_note'),
                        _l('cong_create_by'),
                        _l('cong_status'),
                        _l('date_create'),
                        _l('cong_active'),
                        ),'automations'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-automations', admin_url+'automations/table', [1], [1], {}, ['0','desc']);
   });

   $('body').on('click', '.check_status', function(e)
   {
       var id = $(this).attr('id-data');
       var status = $(this).attr('id-status');
       $.post(admin_url+'automations/update_status', {id : id, status : status, [csrfData.token_name] : csrfData.hash}, function(data){
            data = JSON.parse(data);
            alert_float(data.alert_type, data.message);
            if(data.success)
            {
                $('.table-automations').DataTable().ajax.reload()
            }
       })
   })

    function deleteAutomation(id)
    {
        if(id != "")
        {
            if(confirm('<?=_l('cong_you_must_delete')?>?'))
            {
                $.post(admin_url+'automations/deleteAutomation', {id : id, [csrfData.token_name] : csrfData.hash}, function(data){
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success)
                    {
                        $('.table-automations').DataTable().ajax.reload()
                    }
                })
            }
        }
    }
</script>
</body>
</html>
