<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="modal_bot_fanpage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            <?php echo form_open('admin/bot_fanpage/detail',array('id' => 'bot_fanpage-from')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            $value = (!empty($bot_fanpage->name) ? $bot_fanpage->name : '');
                            echo render_input('name',  'cong_name', $value)
                        ?>
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
</div>
<script>

    $(function(){

        $('#modal_bot_fanpage').modal('show');
       appValidateForm($('#bot_fanpage-from'), {
           name: 'required',
        }, manage_fanpage);

        function manage_fanpage(form) {
            var button = $('#bot_fanpage-from').find('button[type="submit"]');
            button.button({loadingText: '<?=_l('cong_please_wait')?>'});
            button.button('loading');
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function(response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.success == true) {
                    if($.fn.DataTable.isDataTable('.table-bot_fanpage')){
                        $('.table-bot_fanpage').DataTable().ajax.reload();
                    }
                    alert_float('success', response.message);
                }
                $('#modal_bot_fanpage').modal('hide');
            }).always(function() {
                button.button('reset')
            });
            return false;
        }
        $('#bot_fanpage-from').find('.selectpicker').selectpicker('refresh');
        init_datepicker();
    })

</script>
