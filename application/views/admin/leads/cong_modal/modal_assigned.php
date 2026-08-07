<div class="modal fade" id="modal_assigned_lead">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('leads/AddAssigned_lead/'.$leadid),array('id'=>'lead_assigned-form','autocomplete'=>'off')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <?=_l('cong_add_lead_assigned')?>
                    </h4>
                </div>
                <div class="modal-body">
                    <?php
                        $selected = !empty($staff_assigned) ? $staff_assigned : [];
                        echo render_select('staff[]', $staff_member, array('staffid', ['lastname','firstname']),'cong_staff',$selected, array('multiple'=>true, 'data-actions-box'=>true));
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=_l('close')?></button>
                    <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#lead_assigned-form">
                        <?php echo _l('submit'); ?>
                    </button>

                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    $(function(){
        $('#lead_assigned-form').find('.selectpicker').selectpicker('refresh');
        appValidateForm($('#lead_assigned-form'), {
            '[name="staff[]"]': 'required'
        }, manage_assigned_form);
        function manage_assigned_form(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    if($.fn.DataTable.isDataTable('.table-assigned-leads')){
                        $('.table-assigned-leads').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                }
                $('#modal_assigned_lead').modal('hide');
            });
            return false;
        }
    })
</script>