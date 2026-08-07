<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                     <div class="_buttons">
                        <a onclick="new_branch(); return false;" class="btn btn-info pull-left display-block">
                            <?php echo _l('h_branch_new'); ?>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('id'),
                        _l('h_branch_name'),
                        _l('h_branch_address'),
                        _l('tnh_phone'),
                        _l('options')
                        ),'branch'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="branch" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('branch/detail')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('h_branch_edit'); ?></span>
                    <span class="add-title"><?php echo _l('h_branch_add'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name','h_branch_name'); ?>
                        <?php echo render_input('address','h_branch_address'); ?>
                        <?php echo render_input('number_phone','tnh_phone'); ?>
                    </div>
                    <!-- end -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-branch', window.location.href, [0], [0], undefined, [1, 'desc']);
        appValidateForm($('form'),{name: 'required'}, manage_branch);
        $('#branch').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#branch input[type="text"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });
    function manage_branch(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
            }
            $('.table-branch').DataTable().ajax.reload();
            $('#branch').modal('hide');
        }).fail(function(data){
            var error = JSON.parse(data.responseText);
            alert_float('danger',error.message);
        });
        return false;
    }
    function new_branch(){
        $('#branch').modal('show');
        $('.edit-title').addClass('hide');
    }
    function edit_branch(invoker, id){
        $('#additional').append(hidden_input('id',id));
        $('#branch input[name="name"]').val($(invoker).data('name'));
        $('#branch input[name="address"]').val($(invoker).data('address'));
        $('#branch input[name="number_phone"]').val($(invoker).data('number_phone'));
        $('#branch').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
</body>
</html>
