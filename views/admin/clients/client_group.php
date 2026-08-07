<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="customer_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_group_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('customer_group_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/group',array('id'=>'customer-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','customer_group_name'); ?>
                        <?php
                            if(empty($parent_group)) {
								$parent_group = get_table_where('tblcustomers_groups', ['id_parent' => 0]);
                            }
                        ?>
                        <?php echo render_select('id_parent', (!empty($parent_group) ? $parent_group : []), ['id', 'name'], 'Nhóm cha'); ?>

<!--                        <label class="bold mbot10 inline-block">--><?//=_l('cong_color')?><!--</label>-->
<!--                        <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">-->
<!--                            <input type="text" value="" name="color" id="color" class="form-control colorpicker">-->
<!--                            <span class="input-group-addon">-->
<!--                                <i class="i_color" style=""></i>-->
<!--                            </span>-->
<!--                        </div>-->
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>


    window.addEventListener('load',function(){
        appValidateForm($('#customer-group-modal'), {
            name: 'required'
        }, manage_customer_groups);
       $('#customer_group_modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#color').colorpicker();
            $('#customer_group_modal .add-title').removeClass('hide');
            $('#customer_group_modal .edit-title').addClass('hide');
            $('#customer_group_modal input[name="id"]').val('');
            $('#customer_group_modal input[name="name"]').val('');

           $('#customer_group_modal #id_parent').val('').selectpicker('refresh');

            if (typeof(group_id) !== 'undefined') {
                $('#customer_group_modal input[name="id"]').val(group_id);
                $('#customer_group_modal .add-title').addClass('hide');
                $('#customer_group_modal .edit-title').removeClass('hide');
                $('#customer_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
                $('#customer_group_modal input[name="color"]').val($(invoker).parents('tr').find('td').eq(0).find('.rowTD').attr('data-color'));
                $('#customer_group_modal input[name="color"]').parent('div').find('i:nth-child(0)').css('background-color', $(invoker).parents('tr').find('td').eq(0).find('.rowTD').attr('data-color'));
                $('#customer_group_modal input[name="color"]').val($(invoker).parents('tr').find('td').eq(0).find('.rowTD').attr('data-color'));
                $('#customer_group_modal #id_parent').val($(invoker).parents('tr').find('td').eq(0).find('.rowTD').attr('data-parent')).selectpicker('refresh');
            }
        });

        $('body').on('click','.colorpicker-with-alpha',function(){
            $.each($('input.colorpicker'), function(i,v){
                $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
            })
        })

   });

   function manage_customer_groups(form) {
        var button = $(form).find('button[type="submit"]');
        button.button({loadingText: 'please wait...'});
        button.button('loading');
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    var groups = $('select[name="groups_in[]"]');
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
                if(response.data_group) {
                    $('#id_parent').append(`<option value="${response.data_group.id}">${response.data_group.name}</option>`);
                    $('#id_parent').selectpicker('refresh');
                }
            }


            $('#customer_group_modal').modal('hide');
        }).always(function() {
            button.button('reset')
        });
        return false;
    }
</script>
