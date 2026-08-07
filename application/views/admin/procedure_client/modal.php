<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <span class="title">
                    <?=(!empty($procedure_detail) ? _l('cong_update_procedure') : _l('cong_add_procedure') )?>
                </span>
            </h4>
        </div>
        <?php echo form_open('admin/procedure_client/detail',array('id'=>'procedure-modal')); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php $value = !empty($procedure_detail->name) ? $procedure_detail->name : ''?>
                    <?php echo render_input('name','cong_name_procedure_detail', $value); ?>

                    <?php $value = !empty($procedure_detail->staff_id) ? $procedure_detail->staff_id : ''?>
                    <?php echo render_select('staff_id[]',$staff,array('staffid','fullname'),'staff',explode(',', $value),array('multiple'=>true, 'data-actions-box'=>true),array(),'','',false); ?>

                    <?php $value = !empty($procedure_detail->leadtime) ? $procedure_detail->leadtime : ''?>

                    <?php echo form_hidden('leadtime','cong_leadtime', $value); ?>
                    <?php $value = !empty($procedure_detail->id) ? $procedure_detail->id : ''?>
                    <?php echo form_hidden('id', $value); ?>

                    <?php $id_detail = !empty($procedure_detail->id_detail) ? $procedure_detail->id_detail : $id_detail ?>
                    <?php echo form_hidden('id_detail', $id_detail); ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('select[name="staff_id[]"]').selectpicker('refresh');
        appValidateForm($('#procedure-modal'), {
           name: 'required'
        }, manage_customer_groups);
        function manage_customer_groups(form) {

            var button = $('#care_of_from').find('button[type="submit"]');
            button.button({loadingText: '<?=_l('cong_please_wait')?>'});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    if($.fn.DataTable.isDataTable('.table-procadure_detail_<?=$id_detail?>')){
                        $('.table-procadure_detail_<?=$id_detail?>').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                }
                $('#modal_procedure_client').modal('hide');
            }).always(function() {
                button.button('reset')
            });
            return false;
        }
    })

</script>
