<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .uppercase{
        text-transform: uppercase;
        border-bottom: 1px solid #cecece;
    }
    .bold {
        font-weight: bold;
    }
    .red {
        color: red;
    }
    .notification-pad {
        padding: 15px;
        background: #ffc379;
        margin-top: 15px;
    }
    .title-templates {
        background: #b1b1b1;
    }
    .content-templates {
        background: #c1e8ff;
    }
    .padding10 {
        padding: 10px;
    }
    .scroll-auto {
        height: 300px;
        overflow: auto;
    }
    .padding0 {
        padding: 0 !important;
    }
    .content-templates:hover {
        cursor: pointer;
    }
    .wrap-templates:hover {
        position: relative;
        top: -5px;
        transition: all 1s;
    }
    .color000 {
        color: #fff0;
    }
    .wrap-templates:hover .color000{
        color: #717171;
    }
    .color000{
        font-size: 20px;
    }
</style>
<div id="wrapper" class="customer_profile">
    <div class="hide">
        <form action="<?=admin_url('sms/read_excel_phone')?>" id="form_excel" autocomplete="off" enctype="multipart/form-data" method="post" accept-charset="utf-8">
            <input type="file" name="file_csv" onchange="add_products_excel()">
        </form>
    </div>


    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <?php echo form_open($this->uri->uri_string(),array('class'=>'sms-form','autocomplete'=>'off')); ?>
                    <div class="panel-body">
                        <div class="col-md-6">
                            <div class="uppercase bold mbot10">
                                <?=_l('brand_name')?>
                            </div>
                            <?php
                                $brand_name = [
                                        ['name' => 'FOSOSOFT'],
                                        ['name' => 'VIETPRO']
                                ];
                                echo render_select('brand_name', $brand_name, array('name', 'name'),'brand_name', '');
                            ?>


                            <div class="uppercase bold mtop20 mbot10">
                                <?=_l('cong_client_to_excel')?>
                            </div>

                            <!-- load dữ liệu từ file excel -->
                            <label class="bold mbot10 inline-block"><?=_l('get_excel')?></label>
                            <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
<!--                                <input type="text" value="" name="" class="form-control">-->
                                <span class="input-group-addon add_phone_excel">
                                    <i class="fa fa-plus" style=""></i>
                                </span>
                            </div>
                            <div class="form-group">
                                <p>
                                    <b>
                                        <?= _l('list_phone_excel') ?>:
                                    </b>
                                </p>
                                <input type="text" class="phonesinput" name="phone" id="phone" data-role="phonesinput" lbl="Số điện thoại">
                            </div>

                            <!--Danh sách khách hàng lọc từ group-->
                            <div class="uppercase bold mbot10 mtop30">
                                <?=_l('cong_client_group')?>
                            </div>
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" id="send_all_client" name="send_all_client" class="is_primary"   value="1">
                                <label for="send_all_client" data-toggle="tooltip" data-original-title="" title="">
                                    <?=_l('send_all_client_contact')?>
                                </label>
                            </div>
                            <div class="contact_primary hide">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" id="contact_is_primary" name="contact_is_primary" class="is_primary"   value="1">
                                    <label for="contact_is_primary" data-toggle="tooltip" data-original-title="" title="">
                                        <?=_l('cong_send_contact_is_primary')?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" app-field-wrapper="group_client[]">
                                <label for="group_client[]" class="control-label">Nhóm khách hàng</label>
                                <select id="group_client[]" name="group_client[]" class="selectpicker" multiple="1" data-actions-box="1" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                    <?php foreach($group_client as $key => $value){?>
                                        <option value="<?=$value['id']?>"><?=$value['full_option']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!--END Danh sách khách hàng lọc từ group-->


                            <!--Danh sách khách hàng từ dữ liệu khách hàng-->

                            <div class="uppercase bold mbot10 mtop30">
                                <?=_l('cong_client_table')?>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info" type="button" onclick="ModalTableClient()"><?=_l('invoice_bill_to')?></button>
                            </div>

                            <div class="form-group">
                                <p>
                                    <b>
                                        <?= _l('list_user_send') ?>:
                                    </b>
                                </p>
                                <input type="text" class="phonesinput" id="phone_client" name="phone_client" data-role="phonesinput" lbl="Tên khách hàng">
                            </div>
                            <!--END Danh sách khách hàng từ dữ liệu khách hàng-->

                            <div class="uppercase bold">
                                SMS setup
                            </div>
                            <div class="radio radio-primary mtop10">
                              <input type="radio" name="type" id="single_0" value="0" checked>
                              <label for="single_0"><?=_l('send_now')?></label>
                            </div>
                            <div class="radio radio-primary mtop10">
                              <input type="radio" name="type" id="single_1" value="1">
                              <label for="single_1"><?=_l('send_in')?></label>
                            </div>
                            <div class="div_date_send hide">
                                <?php echo render_datetime_input('date_send','cong_date_send')?>
                            </div>
                            <div class="form-group notification-pad">
                                <p class="bold red"><?=_l('invoice_note')?></p>
                                <p>- {fullcode_client} : <?=_l('cong_code_client')?></p>
                                <p>- {company} : <?=_l('cong_name_client_contact')?></p>
                                <p>- {birtday} : <?=_l('cong_birtday')?></p>
                                <p>- {address} : <?=_l('cong_address')?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="uppercase bold">
                                <?=_l('send_sms')?>
                            </div>
                            <div class="mtop10">
                                <button class="btn btn-info open-templates" type="button" data-toggle="collapse" data-target="#templates"><?=_l('sms_templates')?></button>
                            </div>
                            <div id="templates" class="collapse templates mtop10">
                                <div class="panel_s">
                                    <div class="panel-body padding0 scroll-auto div_template">
                                        <?php foreach($template as $key => $value){?>
                                            <div class="col-md-4 mtop10 wrap-templates">
                                                <div class="title-templates padding10">
                                                    <?=$value['name']?>
                                                    <a onclick="DeleteTemplate(<?=$value['id']?>)"><i class="fa fa-trash pull-right color000"></i></a>
                                                    <a  onclick="GetTemplate(<?=$value['id']?>)"><i class="fa fa-pencil pull-right color000"></i></a>
                                                </div>
                                                <div class="content-templates padding10 content_<?=$value['id']?>">
                                                    <?=$value['content']?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="mtop10">
                                        <button class="btn btn-info" type="button" onclick="GetTemplate('')"><?=_l('add_templates')?></button>
                                        <button class="btn btn-default close-templates" type="button"><?=_l('close')?></button>
                                    </div>
                                </div>
                            </div>

                            <div class="uppercase bold mtop10">
                                <?=_l('announcement_message')?>
                            </div>
                            <div class="mtop10">
                                <?php echo render_textarea('content','','',array('rows'=>10)) ?>
                            </div>


                        </div>
                    </div>
                    <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                        <button class="btn btn-info" type="submit"><?=_l('submit')?></button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="list_client_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">
                        <?php echo _l('list_client'); ?>
                    </span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mbot20">
                        <button class="btn btn-info" data-toggle="collapse" data-target="#collapse_filter"><?=_l('cong_filter');?></button>
                        <button class="btn btn-info" type="button" onclick="LoadTableClient()">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div id="collapse_filter" class="collapse">
                        <div class="col-md-12">
                            <div class="uppercase bold mbot10">
                                <?=_l('cong_filter');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('email_client', 'cong_email');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('phone', 'cong_phone');?>
                            </div>
                            <div class="col-md-3">
                                <?php $customer_default_country = get_option('customer_default_country');?>
                                <?php echo render_select('country', $country, array('country_id', 'short_name'), 'cong_country', $customer_default_country);?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('city', $city, array('provinceid', 'name'), 'cong_city');?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-3">
                                <?php echo render_select('district', array(), array(), 'cong_district');?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('vat', 'cong_vat');?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="uppercase bold mbot10">
                            <?=_l('cong_client');?>
                        </div>
                        <div class="col-md-12">
                            <?php render_datatable(array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="list_client"><label></label></div>',
                                _l('cong_company'),
                                _l('cong_contact')
                            ),'list_client table-bordered'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-info" onclick="AddClientList()"><?php echo _l('cong_add_list_send'); ?></button>
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="modal_template"></div>


<?php init_tail(); ?>
<script>

    $(function()
    {
        var vRules = {};
        if (app.options.company_is_required == 1) {
            vRules = {
                brand_name: 'required',
                content: 'required'
            }
        }
        appValidateForm($('.sms-form'), vRules);
    })


    // hỗ trợ css
    $(".close-templates").click(function() {
        $(".open-templates").click();
    });
    //end
    //lấy text đưa vào textarea nội dung
    $(".content-templates").click(function(e) {
        var current = $(e.currentTarget);
        var content = current.text();
        $('#content').text($.trim(content));
    });
    //end



    // lấy dữ liệu từ file excel

    function add_products_excel() {
        var form = $('#form_excel');
        var file_data = $('input[type="file"]').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file_csv', file_data);
        form_data.append([csrfData['token_name']], csrfData['hash']);
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function (data) {
                alert_float(data.alert_type, data.message);
                $.each(data.result, function(i,v){
                    $('#phone').tagit('createTag',v);
                })
            }
        });
    }
    $('body').on('click', '.add_phone_excel', function(e){
        $('input[name="file_csv"]').click();
        $('#form_excel')[0].reset();
    })

    $('body').on('change', '#send_all_client', function(e){
        if($(this).prop('checked'))
        {
            $('.contact_primary').removeClass('hide');
        }
        else
        {
            $('.contact_primary').addClass('hide');
        }
    })

    function ModalTableClient()
    {
        $('#list_client_modal').modal('show');
    }

    var CustomersServerParams = {
        'phone'     :    '[name="phone"]',
        'email'     :   '[name="email"]',
        'country'   :    '[name="country"]',
        'city'      :   '[name="city"]',
        'district'  :   '[name="district"]',
    };

    $.each(CustomersServerParams, function(filterIndex, filterItem){
        $(filterItem).on('change', function()
        {
            if($('.table-list_client').hasClass('dataTable')) {
                $('.table-list_client').DataTable().ajax.reload();
            }
        });
    });
    function LoadTableClient()
    {
        if(!$('.table-list_client').hasClass('dataTable'))
        {
            initDataTable('.table-list_client', admin_url+'sms/get_table_client', [0], [0], CustomersServerParams, [1,'asc']);
        }
        else
        {
            $('.table-list_client').DataTable().ajax.reload();
        }
    }



    /*
        Lấy quận huyện
     */
    $('body').on('change', '#city', function(e){
        var id_city = $(this).val();
        $('#district').html("<option></option>").selectpicker('refresh');
        $.post(admin_url+'clients/get_district',{id_province:id_city, [csrfData['token_name']]:csrfData['hash']}, function(data){
            data = JSON.parse(data);
            var option = "<option></option>";
            $.each(data, function(i,v){
                option += '<option value="'+v.districtid+'">'+v.name+'</option>';
            })
            $('#district').html(option).selectpicker('refresh');
        })
    })
    
    /*
        Lấy qthành phố
     */

    $('body').on('change', '#country', function(e){
        var id_country = $(this).val();
        $('#city').html("<option></option>").selectpicker('refresh');
        $.post(admin_url+'clients/get_province', {id_country:id_country, [csrfData['token_name']]:csrfData['hash']}, function(data){
            data = JSON.parse(data);
            var option = "<option></option>";
            $.each(data, function(i,v){
                option += '<option value="'+v.provinceid+'">'+v.name+'</option>';
            })
            $('#city').html(option).selectpicker('refresh');
        })
    })
    /*
        LẤy danh sách khách hàng vào input
     */
    function AddClientList()
    {
        var checkClient = $('.checkClient');
        var string_phone = "";
        $.each(checkClient, function(i,v){
            $('#phone_client').tagit('createTag',$(v).prop('name'));
        })
        $('input[name="phone_client"]').val(string_phone);
    }
    
    
    function GetTemplate(id = "") {
        $.get(admin_url+'sms/get_modal_template/'+id, function(data){
            $('#modal_template').html(data);
            $('#add_template_modal').modal('show');

            var vRules = {};
            vRules = {
                name: 'required',
                content: 'required'
            }
            appValidateForm($('.template-form'), vRules, manage_template);
        })
    }
    function manage_template(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                if(response.type == 'update')
                {
                    $('.content_'+response.id).html(response.content);
                }
                else
                {
                    var tempalte_add ='<div class="col-md-4 mtop10 wrap-templates">'+
                                      '     <div class="title-templates padding10">'+response.name+
                                      '         <a onclick="DeleteTemplate('+response.id+')"><i class="fa fa-trash pull-right color000"></i></a>'+
                                      '         <a onclick="GetTemplate('+response.id+')"><i class="fa fa-pencil pull-right color000"></i></a>'+
                                      '     </div>'+
                                      '     <div class="content-templates padding10 content_'+response.id+'">'+response.content+
                                      '     </div>'+
                                      '</div>';
                    $('.div_template').append(tempalte_add);
                }
            }
            $('#add_template_modal').modal('hide');
        });
        return false;
    }
    function DeleteTemplate(id = "")
    {
        if(id != "")
        {
            if(confirm('<?=_l('you_need_templete')?>?'))
            {
                $.get(admin_url+'sms/DeleteTemplate/'+id).done(function(response) {
                    response = JSON.parse(response);
                    alert_float('success', response.message);
                    if(response.success)
                    {
                        $('.content_'+id).parent('.wrap-templates').remove();
                    }
                })
            }
        }
    }


    $('body').on('change', 'input[name="type"]:checked', function(e){
        if($(this).val() == 0)
        {
            $('.div_date_send').addClass('hide');
            $('#date_send').val('');
        }
        else
        {
            $('.div_date_send').removeClass('hide');
        }
    })
</script>
</body>
</html>
