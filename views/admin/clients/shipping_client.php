
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="shipping_client_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('cong_shipping_client_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('cong_shipping_client_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/shipping_client', array('id'=>'shipping-client-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-success">
                          <div class="panel-heading">
                              <h3 class="panel-title"><?php echo _l('cong_billing_shipping'); ?></h3>
                          </div>
                          <div class="panel-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <?php echo render_input('address', 'cong_shipping'); ?>
                                    <?php echo render_input('name', 'ch_contact_shiping'); ?>
                                    <?php echo render_input('delivery_area', 'ch_elivery_area'); ?>
                                    <?php echo render_select( 'city_shipping', array(), array('provinceid', 'name'), 'cong_client_city'); ?>
                                    <?php echo render_select( 'district_shipping', array(), array('districtid', 'name'), 'cong_client_district'); ?>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-info">
                          <div class="panel-heading">
                              <h3 class="panel-title"><?php echo _l('ch_receiving_information'); ?></h3>
                          </div>
                          <div class="panel-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <?php echo render_input('name_v2', 'ch_recipient_name'); ?>
                                    <?php echo render_input('address_v2', 'cong_address_shipping_client'); ?>
                                    <?php echo form_hidden('id'); ?>
                                    <?php echo form_hidden('client'); ?>
                                    <?php echo render_input('phone', 'cong_phone_shipping_client'); ?>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="col-md-6">
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

$('body').on('change', '#city_shipping', function(e){
    var id_city = $(this).val();
    $('#district_shipping').html("<option></option>").selectpicker('refresh');
    var data = {id_province:id_city};
    if (typeof(csrfData) !== 'undefined') {
        data[csrfData['token_name']] = csrfData['hash'];
    }
    $.post(admin_url+'clients/get_district', data, function(data){
        data = JSON.parse(data);
        var option = "<option></option>";
        $.each(data, function(i,v){
            option += '<option value="'+v.districtid+'">'+v.name+'</option>';
        })
        $('#district_shipping').html(option).selectpicker('refresh');
    })
})
    window.addEventListener('load',function(){
           appValidateForm($('#shipping-client-modal'), {
            name: 'required',
            address_v2: 'required',
            name_v2: 'required',
            delivery_area: 'required',
            address: 'required',
            phone: 'required'
        }, manage_shipping_client);
   });

    function ChangeShippingClient(id = "")
    {
        if(id == "")
        {
            $('#shipping_client_modal').find('.edit-title').addClass('hide');
            $('#shipping_client_modal').find('.add-title').removeClass('hide');

            $('#shipping-client-modal').find('input[name="id"]').val('');
            $('#shipping-client-modal').find('input[name="name"]').val('');
            $('#shipping-client-modal').find('input[name="address"]').val('');
            $('#shipping-client-modal').find('input[name="phone"]').val('');

            $('#shipping-client-modal').find('input[name="name_v2"]').val('');
            $('#shipping-client-modal').find('input[name="address_v2"]').val('');
            $('#shipping-client-modal').find('input[name="delivery_area"]').val('');
            $('#shipping-client-modal').find('input[name="address_primary"]').prop('checked',false);
                var data = {id_country:243};
                $('#city_shipping').html("<option></option>").selectpicker('refresh');
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'clients/get_province', data, function(data){
                    data = JSON.parse(data);
                    var option = "<option></option>";
                    $.each(data, function(i,v){
                        var selected = '';
                        if(v.provinceid == 79)
                        {
                         selected = 'selected';
                        }
                        option += '<option '+selected+' value="'+v.provinceid+'">'+v.name+'</option>';
                    })
                    $('#city_shipping').html(option).selectpicker('refresh');
                });
                $('#district_shipping').html("<option></option>").selectpicker('refresh');
                var data = {id_province:79};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'clients/get_district', data, function(data){
                    data = JSON.parse(data);
                    var option = "<option></option>";
                    $.each(data, function(i,v){
                        option += '<option value="'+v.districtid+'">'+v.name+'</option>';
                    })
                    $('#district_shipping').html(option).selectpicker('refresh');
                })
        }
        else
        {

            $('#shipping_client_modal').find('.edit-title').removeClass('hide');
            $('#shipping_client_modal').find('.add-title').addClass('hide');

            $('#shipping-client-modal').find('input[name="address_primary"]').prop('checked',false);
            $.post(admin_url+'clients/GetShippingClient/'+id,{csrf_token_name:$('input[name="csrf_token_name"]').val()},function(data){
                data = JSON.parse(data);
                $('#shipping-client-modal').find('input[name="id"]').val(data.id);
                $('#shipping-client-modal').find('input[name="name"]').val(data.name);
                $('#shipping-client-modal').find('input[name="address"]').val(data.address);
                $('#shipping-client-modal').find('input[name="phone"]').val(data.phone);

                $('#shipping-client-modal').find('input[name="name_v2"]').val(data.name_v2);
                $('#shipping-client-modal').find('input[name="address_v2"]').val(data.address_v2);
                $('#shipping-client-modal').find('input[name="delivery_area"]').val(data.delivery_area);    
                if(data.address_primary == 1)
                {
                    $('#shipping-client-modal').find('input[name="address_primary"]').prop('checked',true);
                }
                    var datas = {id_country:243};
                    $('#city_shipping').html("<option></option>").selectpicker('refresh');
                    if (typeof(csrfData) !== 'undefined') {
                        datas[csrfData['token_name']] = csrfData['hash'];
                    }
                    $.post(admin_url+'clients/get_province', datas, function(data_s){
                        data_s = JSON.parse(data_s);
                        var option = "<option></option>";
                        $.each(data_s, function(i,v){
                            var selected = '';
                            if(v.provinceid == data.city_shipping)
                            {
                             selected = 'selected';
                            }
                            option += '<option '+selected+' value="'+v.provinceid+'">'+v.name+'</option>';
                        })
                        $('#city_shipping').html(option).selectpicker('refresh');
                    });
                    $('#district_shipping').html("<option></option>").selectpicker('refresh');
                    var datas = {id_province:data.city_shipping};
                    if (typeof(csrfData) !== 'undefined') {
                        datas[csrfData['token_name']] = csrfData['hash'];
                    }
                    $.post(admin_url+'clients/get_district', datas, function(data_s){
                        data_s = JSON.parse(data_s);
                        var option = "<option></option>";
                        $.each(data_s, function(i,v){
                            var selected = '';
                            if(v.districtid == data.district_shipping)
                            {
                             selected = 'selected';
                            }
                            option += '<option '+selected+' value="'+v.districtid+'">'+v.name+'</option>';
                        })
                        $('#district_shipping').html(option).selectpicker('refresh');
                    })
            })
        }

        if($('input[name="userid"]').length)
        {
            $('#shipping-client-modal').find('input[name="client"]').val($('input[name="userid"]').val());
        }
        $('#shipping_client_modal').modal('show');
                
    }
    function manage_shipping_client(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-shipping_client')){
                    $('.table-shipping_client').DataTable().ajax.reload();
                }
                alert_float('success', response.message);
            }
            $('#shipping_client_modal').modal('hide');
        });
        return false;
    }

</script>
