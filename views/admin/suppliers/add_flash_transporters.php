<div class="modal fade in" id="add_transporters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                  <span class="book-title"><?php echo _l('ch_add_transporters'); ?> </span>
                </h4>
            </div>
            <?php echo form_open('admin/suppliers/add_transporters',array('id'=>'add_transporters-from')); ?>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <?php echo render_input('company', 'ch_name_suppliers'); ?>
                </div>
                <div class="col-md-12">
                    <?php echo render_input('phone', 'clients_phone'); ?>
                </div>
                <div class="col-md-12">
                    <?php echo render_input('email', 'clients_email'); ?>
                </div>
                <div class="col-md-12">
                    <?php echo render_textarea( 'address', 'client_address'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button group="submit" id="" class="bt-view btn btn-info" ><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
            </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    appValidateForm($('#add_transporters-from'), {
       company: 'required',
       phone: 'required',
       email: {
            email: true,
            remote: {
                url: admin_url + "misc/suppliers_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#add_transporters-from input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="suppliers__id"]').val();
                    },
                    [csrfData['token_name']] : csrfData['hash']
                }
            }
        },
    },manage_suppliers);
    function manage_suppliers(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            alert_float(response.alert_type, response.message);
            if(response.id)
            {
                ajaxSelectParams('#transporters', 'admin/orders/searchSuppliers',response.id, {type: 1});
            }
            $('#add_transporters').modal('hide');
        })
        return false;
    } 
    $('body').on('hidden.bs.modal', '#add_transporters', function() {
        $('#add_flash_transporters').html('');
    });
</script>
