
<!--Công bổ sung-->
<script>
    $('#country').change(function (e) {
        var id_country = $(this).val();
        $('#city').html("<option></option>").selectpicker('refresh');
        $.post(admin_url + 'clients/get_province', {
            id_country: id_country,
            [csrfData['token_name']]: csrfData['hash']
        }, function (data) {
            data = JSON.parse(data);
            var option = "<option></option>";
            $.each(data, function (i, v) {
                option += '<option value="' + v.provinceid + '">' + v.name + '</option>';
            })
            $('#city').html(option).selectpicker('refresh');
        })
    })
    $('#city').change(function(e){
        var id_city = $(this).val();
        $('#district').html("<option></option>").selectpicker('refresh');
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
            $('#district').html(option).selectpicker('refresh');
        })
    })

    $('#district').change(function(e){
        var id_district = $(this).val();
        var data = {id_district:id_district};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $('#ward').html("<option></option>").selectpicker('refresh');
        $.post(admin_url+'clients/get_ward',data, function(data){
            data = JSON.parse(data);
            var option = "<option></option>";
            $.each(data, function(i,v){
                option += '<option value="'+v.wardid+'">'+v.name+'</option>';
            })
            $('#ward').html(option).selectpicker('refresh');
        })
    })

    function init_assigned_lead(id)
    {
        if(!$('.table-assigned-leads').hasClass('dataTable'))
        {
            initDataTable('.table-assigned-leads', admin_url + 'leads/table_assigned_lead/' + id,'undefined', 'undefined','undefined',[0,'desc']);
        }
    }
    function AddAssigned_Lead(id = "") {
        $.get(admin_url+'leads/load_lead_assigned/'+id, function(data){
            $('.div_modal_assigned').html(data);
            $('#modal_assigned_lead').modal('show');
        })
    }

    $(document).ready(function(){
        $('[data-toggle="popover"]').popover(
            {html : true}
        );
    });

    function init_advisory_lead(id = "")
    {
        if(!$('.table-advisory_lead_modal').hasClass('dataTable'))
        {
            initDataTable('.table-advisory_lead_modal', admin_url+'leads/table_advisory_lead/'+id, [0], [0], {}, [4, 'desc']);
        }
    }
    $('.update_status_lead').click(function(e){
        var id_assigned  = $(this).attr('id-data');
        var status_procedure  = $(this).attr('status-procedure');
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id_assigned;
        data['status_procedure'] = status_procedure;
        $.post(admin_url+'advisory_lead/update_status/', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead_modal').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        })
    })
    function restore_advisory_lead(id = "")
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        $.post(admin_url+'advisory_lead/restore_advisory_lead', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead_modal').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        })
    }
    function BreakAdvisory(id = "", status)
    {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        $.post(admin_url+'advisory_lead/break_advisory', data, function(data){
            data = JSON.parse(data);
            if(data.success)
            {
                $('.table-advisory_lead_modal').DataTable().ajax.reload();
            }
            alert_float(data.alert_type, data.message);
        })
    }
    $('.rating').click(function(e){
        var id_star = $(this).attr('id-star');
        var div_rating = $(this).parents('#div_rating');
        div_rating.find('.rating').removeClass('checked');
        $(this).addClass('checked');
        for(var i = 1;i < id_star; i++)
        {
            div_rating.find('.rating[id-star="'+i+'"]').addClass('checked');
        }
        $('input[name="vip_rating"]').val(id_star);
    })

    var i = $('#div_contacts').find('.items_contact').length;

    function addContact(){
        var kt_contact = $('#div_contacts').find('.items_contact').length;
        $('#div_contacts').append('<div class="col-md-6 items_contact">'+
        '                               <h5 class="mtop20"><?=_l('cong_contacts')?></h5>'+
        '                               <p class="mborder"></p>'+
        '                               <div class="pborder">'+
        '                                   <div class="text-right">'+
        '                                       <a class="remove_contact_panel text-right pointer text-danger" title="Xóa" name-data="'+i+'"><i class="fa fa-trash gf-icon-hover"></i></a>'+
        '                                    </div>'+
        '                                   <div class="col-md-6 mtop10">'+
        '                                       <div class="form-group" app-field-wrapper="firstname">'+
        '                                           <label for="contacts['+i+'][firstname]" class="control-label"> <?=_l('cong_last_firstname')?></label>'+
        '                                            <input type="text" name="contacts['+i+'][firstname]" id="contacts['+i+'][firstname]"  tabindex='+(1*(i+1))+'  class="form-control" autofocus="1" value="">'+
        '                                        </div>'+
        '                                        <div class="form-group" app-field-wrapper="contacts['+i+'][title]">'+
        '                                            <label for="contacts['+i+'][title]" class="control-label"> <?=_l('cong_title')?></label>'+
        '                                           <input type="text" name="contacts['+i+'][title]" id="contacts['+i+'][title]" tabindex='+(3*(i+1))+' class="form-control" autofocus="1" value="">'+
        '                                        </div>'+
        '                                        <div class="form-group" app-field-wrapper="contacts['+i+'][phonenumber]">'+
        '                                             <label for="contacts['+i+'][phonenumber]" class="control-label"> <?=_l('cong_phonenumber')?></label>'+
        '                                             <input type="text" name="contacts['+i+'][phonenumber]" id="contacts['+i+'][phonenumber]" tabindex='+(5*(i+1))+' class="form-control phonenumber_contacts" autofocus="1" value="">'+
        '                                       </div>'+
        '                                   </div>'+
        '                                   <div class="col-md-6 mtop10">'+
        '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][email]">'+
        '                                           <label for="contacts['+i+'][email]" class="control-label"> <?=_l('cong_email')?></label>'+
        '                                           <input type="text" id="contacts['+i+'][email]" tabindex='+(4*(i+1))+'  name="contacts['+i+'][email]" class="form-control" autofocus="1" value="">'+
        '                                       </div>'+
        '                                       <div class="form-group" app-field-wrapper="contacts['+i+'][birtday]">'+
        '                                           <label for="contacts['+i+'][birtday]" class="control-label"> <?=_l('cong_birtday')?></label>'+
        '                                           <div class="input-group date">'+
        '                                               <input type="text" name="contacts['+i+'][birtday]" id="contacts['+i+'][birtday]" tabindex='+(5*(i+1))+' class="datepicker form-control" autofocus="1" value="">'+
        '                                               <div class="input-group-addon">'+
        '                                                   <i class="fa fa-calendar calendar-icon"></i>'+
        '                                               </div>'+
        '                                           </div>'+
        '                                       </div>'+
        '                                   </div>'+
        '                                   <div class="col-md-12">' +
        '                                        <div class="form-group" app-field-wrapper="contacts['+i+'][note]">'+
        '                                           <label for="contacts['+i+'][note]" class="control-label"><?=_l('cong_note')?></label>'+
        '                                           <textarea id="contacts['+i+'][note]" '+(6*(i+1))+' name="contacts['+i+'][note]" class="form-control" rows="4"></textarea>'+
        '                                       </div>'+
        '                                   </div>'+
        '                                   <div class="clearfix"></div>'+
        '                               </div>'+
        '                           </div>');
        init_datepicker();
        is_required_contact_lead['contacts['+i+'][firstname]']  = 'required';
        is_required_contact_lead['contacts['+i+'][email]']  = 'required';
        is_required_contact_lead['contacts['+i+'][phonenumber]']  = "required";

        validate_lead_form();
        i++;
    }

    function addContact_Full(){
        var kt_contact = $('#div_contacts_row').find('.items_contact').length;
        $('#div_contacts_row').append('<div class="col-md-6 items_contact">'+
        '                               <h5 class="mtop20"><?=_l('cong_contacts')?></h5>'+
        '                               <p class="mborder"></p>'+
        '                               <div class="pborder">'+
        '                                   <div class="text-right">'+
        '                                       <a class="remove_contact_panel text-right pointer text-danger" title="Xóa" name-data="'+i+'"><i class="fa fa-trash gf-icon-hover"></i></a>'+
        '                                    </div>'+
        '                                    <div class="col-md-12 mtop10">'+
        '                                    <div class="form-group" app-field-wrapper="firstname">'+
        '                                         <label for="contacts['+i+'][firstname]" class="control-label"> <?=_l('cong_last_firstname')?></label>'+
        '                                         <input type="text" name="contacts['+i+'][firstname]" id="contacts['+i+'][firstname]"  tabindex='+(1*(i+1))+'  class="form-control" autofocus="1" value="">'+
        '                                    </div>'+
        '                                    <div class="form-group" app-field-wrapper="contacts['+i+'][title]">'+
        '                                         <label for="contacts['+i+'][title]" class="control-label"> <?=_l('cong_title')?></label>'+
        '                                         <input type="text" name="contacts['+i+'][title]" id="contacts['+i+'][title]" tabindex='+(3*(i+1))+' class="form-control" autofocus="1" value="">'+
        '                                    </div>'+
        '                                    <div class="form-group" app-field-wrapper="contacts['+i+'][phonenumber]">'+
        '                                         <label for="contacts['+i+'][phonenumber]" class="control-label"> <?=_l('cong_phonenumber')?></label>'+
        '                                         <input type="text" name="contacts['+i+'][phonenumber]" id="contacts['+i+'][phonenumber]" tabindex='+(5*(i+1))+' class="form-control phonenumber_contacts" autofocus="1" value="">'+
        '                                    </div>'+
        '                                    <div class="form-group" app-field-wrapper="contacts['+i+'][email]">'+
        '                                        <label for="contacts['+i+'][email]" class="control-label"> <?=_l('cong_email')?></label>'+
        '                                        <input type="text" id="contacts['+i+'][email]" tabindex='+(4*(i+1))+'  name="contacts['+i+'][email]" class="form-control" autofocus="1" value="">'+
        '                                    </div>'+
        '                                    <div class="form-group" app-field-wrapper="contacts['+i+'][birtday]">'+
        '                                        <label for="contacts['+i+'][birtday]" class="control-label"> <?=_l('cong_birtday')?></label>'+
        '                                        <div class="input-group date">'+
        '                                            <input type="text" name="contacts['+i+'][birtday]" id="contacts['+i+'][birtday]" tabindex='+(5*(i+1))+' class="datepicker form-control" autofocus="1" value="">'+
        '                                            <div class="input-group-addon">'+
        '                                                <i class="fa fa-calendar calendar-icon"></i>'+
        '                                            </div>'+
        '                                       </div>'+
        '                                    </div>'+
        '                                    <div class="form-group" app-field-wrapper="contacts['+i+'][note]">'+
        '                                        <label for="contacts['+i+'][note]" class="control-label"><?=_l('cong_note')?></label>'+
        '                                        <textarea id="contacts['+i+'][note]" '+(6*(i+1))+' name="contacts['+i+'][note]" class="form-control" rows="4"></textarea>'+
        '                                    </div>'+
        '                                    </div>'+
        '                                    <div class="clearfix"></div>'+
        '                               </div>'+
        '                           </div>');
        init_datepicker();
        is_required_convert['contacts['+i+'][firstname]']  = 'required';
        is_required_convert['contacts['+i+'][email]']  = 'required';
        is_required_convert['contacts['+i+'][phonenumber]']  = "required";
        validate_lead_convert_to_client_form();
        i++;
    }

    $('body').on('click', '.remove_contact_panel', function(e){
        var name_data = $(this).attr('name-data');
        console.log(name_data)
        if($(this).parents('form').attr('id') == 'lead_form')
        {
            delete is_required_contact_lead['contacts['+name_data+'][firstname]'];
            delete is_required_contact_lead['contacts['+name_data+'][phonenumber]'];
            delete is_required_contact_lead['contacts['+name_data+'][email]'];
        }
        if($(this).parents('form').attr('id') == 'lead_to_client_form') {
            delete is_required_convert['contacts[' + name_data + '][firstname]'];
            delete is_required_convert['contacts[' + name_data + '][phonenumber]'];
            delete is_required_convert['contacts[' + name_data + '][email]'];
        }
        validate_lead_form();
        $(this).parents('.items_contact').remove();
    })


    $('#lead_image').change(function(evt){
        var tgt = evt.target || window.event.srcElement,
            files = tgt.files;
        if (FileReader && files && files.length) {
            var fr = new FileReader();
            fr.onload = function () {
                $('#imgLead').attr('src',fr.result);
                $('#imgLead').attr('upload',true);
                $('.info_image').removeClass('hide');
                $('.input_upload').addClass('hide');
            }
            fr.readAsDataURL(files[0]);
        }
        else
        {
            $('.info_image').addClass('hide');
            $('.input_upload').removeClass('hide');
        }
    })
    $('.removeImg').click(function(e){
        if(confirm('<?=_l('you_must_delete_img')?>'))
        {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            var img = $(this).attr('name_img');
            var id = $('input[name="id"]').val();
            if(!$('#imgLead').attr('upload'))
            {
                if(id != "")
                {
                    $.post(admin_url+'leads/unlinkImg/'+id+'/'+img, data, function(data){
                        $('.info_image').addClass('hide');
                        $('.input_upload').removeClass('hide');
                    })
                }
            }
            else
            {
                $('#lead_image').val('').trigger('change');
                $('.input_upload').removeClass('hide');
            }
        }
    })

</script>
<!--End công bổ sung-->