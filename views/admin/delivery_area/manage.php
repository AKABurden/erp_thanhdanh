<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
      <div class="panel-body _buttons">
         <div class="_buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
           
<!--            --><?php //if (has_permission('units','','create')) { ?>
            <div class="line-sp"></div>
            <a href="#"  onclick="new_unit(); return false;" id="suppliers_modal" class="btn btn-info mright5 test pull-right H_action_button">
               <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
               <?php echo _l('create_add_new'); ?></a>
<!--            --><?php //} ?>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('id'),
                            _l('Mạ khu vực'),
                            _l('Tỉnh/TP'),
                            _l('Quận/Huyện'),
                            _l('Ghi chú'),
                            _l('options')
                        ),'delivery_area'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('delivery_area/add_delivery_area'),array('id'=>'id_unit')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Sửa khu vực giao hàng'); ?></span>
                    <span class="add-title"><?php echo _l('Thêm khu vực giao hàng'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('code','Mã khu vực'); ?>
                        <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('cong_client_city'); ?>
                        </label>
                        <select style="width: 100%" id="city" name="city"  data-width="100%" data-none-selected-text="Không có mục nào được chọn"  tabindex="-98">
                        </select>
                        <br>
                        <div class="clearfix"></div>
                        <br>
                        <label for="number" class="control-label">
                                <small class="req text-danger">* </small>
                                <?php echo _l('cong_client_district'); ?>
                        </label>
                        <select id="district" name="district[]" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" multiple data-actions-box="1" tabindex="-98">
                            </select>
                        <br>
                        <div class="clearfix"></div>
                        <br>
                        <?php echo render_textarea('note','ch_note','',array('rows'=>5)); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>


    $( "#city" ).select2();
    $('body').on('change', '#city', function(e){
        var id_city = $(this).val();
        $('#district').html("<option></option>");
        var data = {id_province:id_city};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'delivery_area/get_district', data, function(data){
            data = JSON.parse(data);
            var option = "<option></option>";
            $.each(data, function(i,v){
                option += '<option value="'+v.districtid+'">'+v.name+'</option>';
            })
            $('#district').html(option);
            $('#district').selectpicker('refresh');
        })
    })
    function view_init_department(id)
    {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        $.ajax({
                url : admin_url + 'delivery_area/get_row_delivery_area/' + id,
                dataType : 'json',
            })
            .done(function(_data){
               if(_data!="")
                {   
                    $('#code').val(_data.main.code);
                        var data = {id_country:243};
                        $('#city').html("<option></option>");
                        if (typeof(csrfData) !== 'undefined') {
                            data[csrfData['token_name']] = csrfData['hash'];
                        }
                        $.post(admin_url+'clients/get_province', data, function(data){
                            data = JSON.parse(data);
                            var option = "<option></option>";
                            $.each(data, function(i,v){
                                var selected = '';
                                option += '<option '+selected+' value="'+v.provinceid+'">'+v.name+'</option>';
                            })
                            $('#city').html(option);
                            $('#city').select2('val',_data.main.city).trigger('refresh');    
                        });

                            $('#district').html("<option></option>").selectpicker('refresh');
                            var data = {id_province:_data.main.city,id:_data.main.id};
                            if (typeof(csrfData) !== 'undefined') {
                                data[csrfData['token_name']] = csrfData['hash'];
                            }
                            $.post(admin_url+'delivery_area/get_district', data, function(data){
                                data = JSON.parse(data);
                                var option = "<option></option>";
                                $.each(data, function(i,v){
                                    option += '<option value="'+v.districtid+'">'+v.name+'</option>';
                                })
                                $('#district').html(option).selectpicker('refresh');
                                $("#district").val(_data.main_detail_v2);
                                $("#district").selectpicker('refresh');
                            })
                    $('#note').val(_data.main.note);
                    $('#id_unit').prop('action',admin_url+'delivery_area/update_delivery_area/'+id);
                }
            });
    }
   $(function(){
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';

       var tAPI = initDataTable('.table-delivery_area', admin_url+'delivery_area/table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
   });
    $(function(){
        appValidateForm($('#id_unit'),{code:'required',district:'required',city:'required'},manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });
    function manage_contract_types(form) {
        var data = $(form).serialize();
        
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
            }
            $('.table-delivery_area').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }
    $(document).on('click', '.delete-reminds', function() {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.get($(this).attr('href'), function(response) {
          alert_float(response.alert_type, response.message);
            $('.table-delivery_area').DataTable().ajax.reload();
        }, 'json');
    }
    return false;
    });
    function new_unit(){
        $('#code').val('');
        $('#note').val('');
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        $('#unit').val('');
        $('#id_type').attr('action',admin_url+'units/add_unit');
                var data = {id_country:243};
                $('#city').html("<option></option>");
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'clients/get_province', data, function(data){
                    data = JSON.parse(data);
                    var option = "<option></option>";
                    $.each(data, function(i,v){
                        var selected = '';
                        option += '<option '+selected+' value="'+v.provinceid+'">'+v.name+'</option>';
                    })
                    $('#city').html(option);
                    $('#city').select2('val',79).trigger('refresh');    
                    $('#city').trigger('change'); 
                });
                
                $('#district').html("<option></option>").selectpicker('refresh');
                var data = {id_province:79};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(admin_url+'delivery_area/get_district', data, function(data){
                    data = JSON.parse(data);
                    var option = "<option></option>";
                    $.each(data, function(i,v){
                        option += '<option value="'+v.districtid+'">'+v.name+'</option>';
                    })
                    $('#district').html(option).selectpicker('refresh');
                })
    }
    function edit_type(invoker,id){
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id',id));
        $('#type input[name="unit"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }

    

</script>
