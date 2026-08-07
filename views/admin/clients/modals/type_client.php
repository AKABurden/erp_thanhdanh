<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="type_client_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('cong_type_client_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('cong_type_client_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/type_client', array('id'=>'type-client-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'cong_name_type_client'); ?>
                        <?php echo form_hidden('id'); ?>
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
    window.addEventListener('load',function(){
       appValidateForm($('#type-client-modal'), {
        name: 'required'
    }, manage_type_client);

       $('#type_client_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var type_client_id = $(invoker).data('id');
        $('#type_client_modal .add-title').removeClass('hide');
        $('#type_client_modal .edit-title').addClass('hide');
        $('#type_client_modal input[name="id"]').val('');
        $('#type_client_modal input[name="name"]').val('');
        // is from the edit button
        if (typeof(type_client_id) !== 'undefined') {
            $('#type_client_modal input[name="id"]').val(type_client_id);
            $('#type_client_modal .add-title').addClass('hide');
            $('#type_client_modal .edit-title').removeClass('hide');
            $('#type_client_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_type_client(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-type_client')){
                    $('.table-type_client').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var groups = $('select[name="type_client"]');
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#type_client_modal').modal('hide');
        });
        return false;
    }

</script>
