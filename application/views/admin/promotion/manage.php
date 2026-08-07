<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('fixdatatable.css') ?>">
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<div id="wrapper">
    <?php if(has_permission('promotion','','create')){ ?>
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a class="btn btn-info pull-right H_action_button" onclick="add(); return false;">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('create_add_new'); ?>
            </a>
        </div>
    </div>
    <?php } ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php render_datatable(array(
                            _l('#'),
                            _l('promotion_list_name'),
                            _l('ch_option'),
                        ),'promotion_list'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="promotion_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="add-title"><?php echo _l('add_promotion_list'); ?></span>
                    <span class="edit-title"><?php echo _l('edit_promotion_list'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/promotion/group_detail',array('id'=>'promotion-modal')); ?>
            <div class="modal-body">
                <?php echo render_input('name','promotion_list_name'); ?>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTableCustom('.table-promotion_list', admin_url+'promotion/table_promotion_list', [0], [0], [],<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
        appValidateForm($('#promotion-modal'), {name: 'required'}, manage_promotion);
    });
    function add() {
        $('.add-title').removeClass('hide');
        $('.edit-title').addClass('hide');
        $('input[name=name]').val('');

        $('#promotion-modal').attr("action","<?=admin_url('promotion/group_detail')?>");
        $('#promotion_modal').modal({backdrop: 'static', keyboard: false});
    }
    function edit_promotion(id) {
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
        $('#promotion-modal').attr("action","<?=admin_url('promotion/group_detail/')?>"+id);

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'promotion/getData_promotion_list/'+id, data).done(function(response){
            response = JSON.parse(response);
            $('input[name=name]').val(response.name);
            $('#promotion_modal').modal({backdrop: 'static', keyboard: false});
        });
    }

    function delete_promotion_list(id) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
          data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'promotion/delete_promotion_list/'+id, data).done(function(response){
            response = JSON.parse(response);
            alert_float(response.alert_type,response.message);
            $('.table-promotion_list').DataTable().ajax.reload();
        });
    }

    function manage_promotion(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float(response.alert_type, response.message);
                $('.table-promotion_list').DataTable().ajax.reload();
            }
            $('#promotion_modal').modal('hide');
        });
        return false;
    }
</script>
</body>
</html>
