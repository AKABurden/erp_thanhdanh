<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="suppliers_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('ch_suppliers_group_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('ch_suppliers_group_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/suppliers/group',array('id'=>'suppliers-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','ch_suppliers_group_name'); ?>
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
       appValidateForm($('#suppliers-group-modal'), {
        name: 'required'
    }, manage_suppliers_groups);

       $('#suppliers_group_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#suppliers_group_modal .add-title').removeClass('hide');
        $('#suppliers_group_modal .edit-title').addClass('hide');
        $('#suppliers_group_modal input[name="id"]').val('');
        $('#suppliers_group_modal input[name="name"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#suppliers_group_modal input[name="id"]').val(group_id);
            $('#suppliers_group_modal .add-title').addClass('hide');
            $('#suppliers_group_modal .edit-title').removeClass('hide');
            $('#suppliers_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_suppliers_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-suppliers-groups')){
                    $('.table-suppliers-groups').DataTable().ajax.reload();
                }
                if(typeof(response.id) != 'undefined') {
                    var groups = $('select[name="groups_in[]"]');
                    console.log(groups);
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#suppliers_group_modal').modal('hide');
        });
        return false;
    }

</script>
