<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
        <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        <!-- <?php if(has_permission('invoice_items','','create')){ ?> -->

        

        <a href="#" class="btn btn-info mright5 test pull-right H_action_button" data-toggle="modal" data-target="#customer_group_modal">
                   <?php echo _l('create_add_new'); ?>
                </a>
        <!-- <?php } ?> -->

    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
       </div>
       <!-- /.modal -->
       <div class="modal fade" id="customer_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('sum_edit_status_customer'); ?></span>
                    <span class="add-title"><?php echo _l('sum_add_status_customer'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/add_status_client',array('id'=>'customer-status_client-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','customer_group_name'); ?>

                        <labeld for="color" class="bold mbot10 inline-block"><?=_l('cong_color')?></label>
                        <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                            <input type="text" value="" name="color" id="color" class="form-control colorpicker">
                            <span class="input-group-addon">
                                <i class="i_color" style=""></i>
                            </span>
                        </div>

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

<!-- ===== -->
     <?php hooks()->do_action('before_items_page_content'); ?>
    <?php
    $table_data = array(
      _l('Tên'),
      _l('Màu sắc'),
      _l('Thuộc tính'),
    );
    render_datatable($table_data,'status_client'); ?>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
        $(function(){
            initDataTable('.table-status_client', window.location.href, [1], [1]);
        });
        appValidateForm($('#customer-status_client-modal'), {
            name: 'required',
            color: 'required'
        }, manage_customer_status_client);

        function manage_customer_status_client(form) {
        var button = $(form).find('button[type="submit"]');
        button.button({loadingText: 'please wait...'});
        button.button('loading');
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-status_client')){
                    $('.table-status_client').DataTable().ajax.reload();
                }
                alert_float('success', response.message);
            }
            $('#customer_group_modal').modal('hide');
        }).always(function() {
            button.button('reset')
        });
        return false;
    }
       $('#customer_group_modal').on('show.bs.modal', function(e) {
            var invoker = $(e.relatedTarget);
            var group_id = $(invoker).data('id');
            $('#color').colorpicker();
            $('#customer_group_modal .add-title').removeClass('hide');
            $('#customer_group_modal .edit-title').addClass('hide');
            $('#customer_group_modal input[name="id"]').val('');
            $('#customer_group_modal input[name="name"]').val('');
            $('#customer_group_modal input[name="color"]').val('');


            if (typeof(group_id) !== 'undefined')
            {
                $('#customer_group_modal input[name="id"]').val(group_id);
                $('#customer_group_modal .add-title').addClass('hide');
                $('#customer_group_modal .edit-title').removeClass('hide');
                $('#customer_group_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
                $('#customer_group_modal input[name="color"]').val($(invoker).parents('tr').find('td').eq(1).text());
                $('#customer_group_modal input[name="color"]').parent('div').find('i:nth-child(1)').css('background-color', $(invoker).parents('tr').find('td').eq(1).text());
                $('#customer_group_modal input[name="color"]').val($(invoker).parents('tr').find('td').eq(1).text());
            }
        });
        $(document).on('click', '.delete-remind', function () {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function (response) {
                alert_float('success', response.message);
                if($.fn.DataTable.isDataTable('.table-status_client')){
                    $('.table-status_client').DataTable().ajax.reload();
                }
            }, 'json');
        }
        return false;
    });
</script>
</body>
</html>
