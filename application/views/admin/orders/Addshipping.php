<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="shipping_client_orders_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="add-title"><?php echo _l('cong_shipping_client_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/shipping_client', array('id' => 'shipping-client')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'cong_name_shipping_client'); ?>
                        <?php echo render_input('address', 'cong_address_shipping_client'); ?>
                        <?php $value = (isset($client) ? $client : '')?>
                        <?php echo form_hidden('client', $value); ?>
                        <?php echo render_input('phone', 'cong_phone_shipping_client'); ?>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="address_primary" class="address_primary" name="address_primary" value="1">
                            <label for="address_primary" data-toggle="tooltip"><?=_l('cong_address_primary')?></label>
                        </div>
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
    var idSelect = "<?php echo !empty($idSelect) ? $idSelect : 0 ?>";
       appValidateForm($('#shipping-client'), {
        name: 'required',
        address: 'required',
        phone: 'required'
    }, manage_shipping_client);
    function manage_shipping_client(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                if(response)
                {
                    console.log($(response.shipping))
                    console.log(response.shipping)
                    console.log(response.shipping.id)
                    $('#'+idSelect).append('<option value="'+response.shipping.id+'" data-subtext="'+response.shipping.address+'">'+response.shipping.name+'</option>').selectpicker('val', response.shipping.id).selectpicker('refresh');
                }
            }
            $('#shipping_client_orders_modal').modal('hide');
        });
        return false;
    }
    $('#shipping_client_orders_modal').modal('show');

</script>
