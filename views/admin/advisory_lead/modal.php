<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">
                <span class="title">
                    <?=(!empty($advisory_lead) ? _l('cong_update_advisory') : _l('cong_add_advisory') )?>
                </span>
            </h4>
        </div>
        <?php echo form_open('admin/advisory_lead/detail',array('id' => 'advisory-modal')); ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        if(empty($id_lead))
                        {
                            $value = !empty($advisory_lead) ? $advisory_lead->lead : '';
                            echo render_select('lead', $lead, array('id', 'name'), 'cong_lead', $value);
                        }
                        else
                        {
                            echo form_hidden('lead', $id_lead);
                        }
                    ?>
                    <?php if(empty($advisory_lead->count_advisory->count_id)){ ?>
                        <?php $value = !empty($advisory_lead->date) ? _d($advisory_lead->date) : ''?>
                        <?php echo render_date_input('date','cong_date_start', $value); ?>
                        <?php $value = !empty($advisory_lead->status_first) ? $advisory_lead->status_first : ''?>
                        <?php echo render_select('status_first', $procedure_detail, array('id', 'name'), 'cong_status_procedure', $value); ?>
                    <?php } ?>

                    <?php $value = !empty($advisory_lead->product_other_buy) ? $advisory_lead->product_other_buy : ''?>
                    <?php echo render_input('product_other_buy', 'cong_product_other_buy', $value);?>

                    <?php $value = !empty($advisory_lead->address_other_buy) ? $advisory_lead->address_other_buy : ''?>
                    <?php echo render_input('address_other_buy', 'cong_address_other_buy', $value);?>

                    <?php $id = !empty($advisory_lead->id) ? $advisory_lead->id : '' ?>
                    <?php echo form_hidden('id', $id); ?>
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
       appValidateForm($('#advisory-modal'), {
           lead: 'required',
           date: 'required',
           status_first: 'required'
        }, manage_advisory);

        function manage_advisory(form) {
            var button = $('#advisory-modal').find('button[type="submit"]');
            button.button({loadingText: '<?=_l('cong_please_wait')?>'});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.success == true) {
                    if($.fn.DataTable.isDataTable('.table-advisory_lead')){
                        $('.table-advisory_lead').DataTable().ajax.reload();
                    }
                    var id_facebook = $('#id_facebook').val();
                    if(id_facebook)
                    {
                        varInfoUser(id_facebook);
                    }
                    alert_float('success', response.message);
                }
                $('#modal_advisory_lead').modal('hide');
            }).always(function() {
                button.button('reset')
            });
            return false;
        }
        $('#advisory-modal').find('.selectpicker').selectpicker('refresh');
        init_datepicker();
    })

</script>
