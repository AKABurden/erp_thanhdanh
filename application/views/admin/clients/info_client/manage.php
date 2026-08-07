<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
  .wap-btn:hover {
    cursor: pointer;
    background: #ffffff42;
    border-radius: 50%;
  }
  .wap-btn {
    margin-left: 10px;
    background: #fff0;
    border: 0;
    outline: 0;
  }
  td.sorting_1.one-control {
      background: url(<?=base_url('assets/images/details_open.png')?>) no-repeat center center;
      cursor: pointer;
  }

  tr.shown td.sorting_1.one-control {
      background: url(<?=base_url('assets/images/details_close.png')?>) no-repeat center center;
  }
  td.two-control {
      background: url(<?=base_url('assets/images/details_open.png')?>) no-repeat center left;
      cursor: pointer;
  }

  tr.shown td.two-control.two-control {
      background: url(<?=base_url('assets/images/details_close.png')?>) no-repeat center left;
  }
  .treegrid-indent {
      width: 16px;
      height: 16px;
      display: inline-block;
      position: relative;
  }
  .treegrid-expander {
      width: 16px;
      height: 16px;
      display: inline-block;
      position: relative;
      cursor: pointer;
  }
  .buttons-collection.btn-default-dt-options{
        display: none;
    }
    .not-control{
        background: none!important;
    }
</style>
<div id="wrapper">
  <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <div class="panel-body _buttons">
        <span class="bold uppercase fsize18 H_title"><?=$title?></span>
        <div class="pull-right mright5 H_border">
            <a class="btn btn-info test H_action_button">
                   <?php echo _l('Export excel'); ?></a>
        </div>
        <div class="pull-right mright5 H_border">
            <a href="#" class="btn btn-info H_action_button" onclick="add(); return false;">
                <?php echo _l('create_add_new'); ?>
            </a>
        </div>
    </div>
  </div>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php render_datatable(array(
              _l('#'),
              _l('name'),
              _l('kb_group_color'),
              _l('cong_type'),
              _l('cong_is_required'),
              _l('options'),
            ),'info_client_group'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="div_input_action" class="hide">
    <select id="child_one_search" name="child_one[]">

    </select>
    <select id="child_two_search" name="child_two[]">

    </select>
</div>
<div class="modal fade" id="info_client_group_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="info-group-add-title"><?php echo _l('add_info_client'); ?></span>
                    <span class="info-group-edit-title"><?php echo _l('edit_info_client'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/info_client_group',array('id'=>'info-client-group-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','name'); ?>
                        <label class="bold mbot10 inline-block"><?=_l('kb_group_color')?></label>
                        <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                            <input type="text" value="" name="color" id="color" class="form-control colorpicker">
                            <span class="input-group-addon">
                                <i class="i_color"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<div class="modal fade" id="add_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="info-detail-add-title"><?php echo _l('add_info_client'); ?></span>
                    <span class="info-detail-edit-title"><?php echo _l('edit_info_client'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/info_client_detail',array('id'=>'info-client-detail-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('id');?>
                        <?php echo render_select('info_group', $info_group, array('id', 'name'), 'als_kb_groups'); ?>
                        <?php echo render_select('type_form', $type_form, array('name', 'name'), 'cong_type_form'); ?>
                        <?php echo render_input('name_detail','name'); ?>
                        <div class="checkbox">
                            <input type="checkbox" id="is_required" name="is_required" value="1">
                            <label for="is_required"><?=_l('cong_is_required')?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="add_detail_value_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="info-detail-value-add-title"><?php echo _l('add_info_value_client'); ?></span>
                    <span class="info-detail-value-edit-title"><?php echo _l('edit_info_value_client'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/info_client_detail_value',array('id'=>'info-client-detail-value-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" app-field-wrapper="info_detail">
                            <label for="info_group_detal" class="control-label"><?=_l('als_kb_groups')?></label>
                            <select id="info_group_detal" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                                <option value=""></option>
                                <?php 
                                    foreach($info_group as $key => $value)
                                    {
                                        echo "<option value='".$value['id']."'>".$value['name']."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <?php echo render_select('info_detail', array(), array('id', 'name'), 'als_info_client'); ?>
                        <?php echo render_input('name_value','name'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        var CustomersServerParams = {
            'child_one[]' : '[name="child_one[]"]',
            'child_two[]' : '[name="child_two[]"]',
        };
        initDataTable('.table-info_client_group', admin_url + 'clients/table_info_client_group', [], [], CustomersServerParams, [0, 'asc']);

        $.each(CustomersServerParams, function(filterIndex, filterItem){
            $(filterItem).on('change', function(){
                $('.table-info_client_group').DataTable().ajax.reload();
            });
        });

        appValidateForm($('#info-client-group-modal'), {name: 'required'}, manage_info_client_group);
        appValidateForm($('#info-client-detail-modal'), {
                info_group: 'required',
                name_detail: 'required',
                type_form: 'required'
        }, manage_client_detail);

        appValidateForm($('#info-client-detail-value-modal'), {
            info_group_detal: 'required',
            info_detail: 'required',
            name_value: 'required'
        }, manage_client_detail_value);
    });

    function add() {
        $('.info-group-add-title').removeClass('hide');
        $('.info-group-edit-title').addClass('hide');
        $('#info-client-group-modal').attr("action", "<?=admin_url('clients/info_client_group')?>");
        $('#name').val('');
        $('#color').val('');
        $(".i_color").attr("css", {backgroundColor: "#fff"});
        $('#color').colorpicker();
        $('#info_client_group_modal').modal('show');
    }
    function edit(id) {
        $('.info-group-add-title').addClass('hide');
        $('.info-group-edit-title').removeClass('hide');
        $('#info-client-group-modal').attr("action", "<?=admin_url('clients/info_client_group/')?>" + id);
        $('#color').colorpicker();
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'clients/getData_infoClientGroup/' + id, data).done(function (response) {
            response = JSON.parse(response);
            $('#name').val(response.name);
            $('#color').val(response.color);
            $(".i_color").attr("style", "background: " + response.color);
            $('#info_client_group_modal').modal('show');
        });
    }
    function delete_main(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        } else {
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url + 'clients/delete_info_client_group/' + id, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-info_client_group').DataTable().ajax.reload();
                    alert_float(response.alert_type, response.message)
                } else {
                    alert_float(response.alert_type, response.message)
                }
            });
        }
        return false;
    }
    function manage_info_client_group(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#name').val('');
                $('#color').val('');
                $('.table-info_client_group').DataTable().ajax.reload();
                $('#info_group').append('<option value="'+response.data.id+'">'+response.data.name+'</option>').selectpicker('refresh');
                alert_float(response.alert_type, response.message);
            }
            $('#info_client_group_modal').modal('hide');
        });
        return false;
    }



    //Thêm sửa xóa Detail Group

    function addOne(id_group = "") {
        $('.info-detail-add-title').removeClass('hide');
        $('.info-detail-edit-title').addClass('hide');
        $('#info-client-detail-modal').attr("action","<?=admin_url('clients/info_client_detail')?>");
        $('#info_group').selectpicker('val',id_group);
        $('#type_form').selectpicker('val','');
        $('#name_detail').val('');
        $('#is_required').prop('checked', false);
        $('#info-client-detail-modal').find('input[name="id"]').val('');
        $('#add_detail_modal').modal('show');
    }
    function editOne(id) {
        $('.info-detail-add-title').addClass('hide');
        $('.info-detail-edit-title').removeClass('hide');
        $('#info-client-detail-modal').find('input[name="id"]').val(id);
        $('#info-client-detail-modal').attr("action","<?=admin_url('clients/info_client_detail/')?>"+id);
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'clients/getData_client_detail/'+id, data).done(function(response){
            response = JSON.parse(response);
            $('#info_group').selectpicker('val',response.id_info_group);
            $('#type_form').selectpicker('val',response.type_form);
            $('#name_detail').val(response.name);
            if(response.is_required == 1)
            {
                $('#is_required').prop('checked', true);
            }
            else
            {
                $('#is_required').prop('checked', false);
            }
            $('#add_detail_modal').modal('show');
        });
    }
    function manage_client_detail(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#info_group').selectpicker('val','');
                $('#name_detail').val('');
                $('.table-info_client_detail').DataTable().ajax.reload();
                alert_float(response.alert_type, response.message);
            }
            $('#add_detail_modal').modal('hide');
        });
        return false;
    }


    function addTwo(id_group = "", id_detail = "") {
        $('.info-detail-value-add-title').removeClass('hide');
        $('.info-detail-value-edit-title').addClass('hide');
        $('#info-client-detail-value-modal').attr("action","<?=admin_url('clients/info_client_detail_value')?>");
        $('#info_group_detal').selectpicker('val', id_group);
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'clients/GetChangeGroup/'+id_group, data).done(function(response){
            response = JSON.parse(response);
            var option = "<option></option>";
            $.each(response, function(i, v){
                option += '<option value="'+v.id+'" '+(v.id == id_detail && $.isNumeric(id_detail) ? 'selected' : '')+'>'+v.name+'</option>';
            })
            $('#info_detail').html(option).selectpicker('refresh');
        });

        // $('#info_detail').html('<option></option>').selectpicker('refresh');
        $('#info_detail').selectpicker('val',id_detail);
        $('#name_value').val('');
        $('#add_detail_value_modal').modal('show');
    }
    function editTwo(id) {
        $('.info-detail-value-add-title').addClass('hide');
        $('.info-detail-value-edit-title').removeClass('hide');
        $('#info-client-detail-value-modal').attr("action","<?=admin_url('clients/info_client_detail_value/')?>"+id);
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'clients/getData_client_detail_value/'+id, data).done(function(response){
            response = JSON.parse(response);
            var response_data = response.data;
            var response_data_detail = response.data_detail;
            $('#info_detail').html('<option></option>');
            $.each(response_data_detail, function(i, v){
                $('#info_detail').append('<option value="'+v.id+'" '+(v.id == response.data.id_info_detail ? 'selected' : '')+'>'+v.name+'</option>');
            })
            $('#info_detail').selectpicker('refresh');
            $('#info_detail').selectpicker('val',response.data.id_info_detail);
            $('#name_value').val(response.data.name);
            $('#info_group_detal').selectpicker('val',response.data.id_info_group);
            $('#add_detail_value_modal').modal('show');
        });
    }
    function manage_client_detail_value(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('#name_value').val('');
                if($.isNumeric(response.detail_id))
                {
                    $('.two-control[id-data = "'+response.detail_id+'"]').trigger('click').trigger('click');
                }
                if(!$.isNumeric(response.detail_id) || response.detail_id != $('#info_detail').val())
                {
                    $('.two-control[id-data = "'+$('#info_detail').val()+'"]').trigger('click').trigger('click');
                }

                $('#info_detail').selectpicker('val','');
                alert_float(response.alert_type, response.message);
            }
            $('#add_detail_value_modal').modal('hide');
        });
        return false;
    }

    // end thêm sửa xóa Detail group
    $('body').on('change', '#info_group_detal', function(e){
        var id = $(this).val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'clients/GetChangeGroup/'+id, data).done(function(response){
            response = JSON.parse(response);
            var option = "<option></option>";
            $.each(response, function(i, v){
                option += '<option value="'+v.id+'">'+v.name+'</option>';
            })
            $('#info_detail').html(option).selectpicker('refresh');
        });
    })



    $('.table-info_client_group').on('draw.dt', function() {
        var invoiceReportsTable = $(this).DataTable();
        var tdTable = $('.table-info_client_group tbody tr').find('td:nth-child(1)');
        $.each(tdTable, function(i,v){
            $(v).addClass('one-control');
            $(v).attr('id-data', $(v).find('a').text());
            $(v).attr('data-loading-text', "<i class='fa fa-spinner fa-spin'></i>");
            $(v).html('');
        })
        var child_one = $('#child_one_search').find('option');
        if(child_one.length > 0)
        {
            $('#child_one_search').html('');
            var count_length = child_one.length
            var child_two = $('#child_two_search').find('option');
            $.each(child_one, function(i,v){
                $('.one-control[id-data="'+$(v).attr('value')+'"]').trigger('click');
                if(i == count_length - 1)
                {
                    if(child_two.length > 0)
                    {
                        $('#child_two_search').html('');
                        $.each(child_two, function(i,v){
                            if($('.two-control[id-data="'+$(v).attr('value')+'"]').length == 0)
                            {
                                setTimeout(function(){
                                    $('.two-control[id-data="'+$(v).attr('value')+'"]').trigger('click');
                                    }, 1000);
                            }
                            else
                            {
                                $('.two-control[id-data="'+$(v).attr('value')+'"]').trigger('click');
                            }
                        })
                    }
                }
            });

        }
        else
        {
            $('#child_two_search').html('');
        }


    })

    $(document).on('click', '.one-control', function(e){
        var id_data = $(this).attr('id-data');
        var TrTable = $(this).parents('tr');
        var one_control = $(this);
        if(!TrTable.hasClass('shown'))
        {
            $('#child_one_search').append('<option value="'+id_data+'"></option>');
            TrTable.addClass('shown');
            var data = {id : id_data, colspan : colspan};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }

            var colspan = TrTable.find('td').length;
            $.post(admin_url+'clients/GetTableChild', data, function(data) {
                one_control.button('reset');
                TrTable.after(data);
            })
        }
        else
        {
            var Dtr = $('.child-row-'+id_data).find('.two-control');
            $.each(Dtr, function(i,v){
                console.log($(v).attr('id-data'))
                $('tr.child-row-two-'+$(v).attr('id-data')).remove();
            })
            $('#child_one_search').find('option[value="'+id_data+'"]').remove();
            $('.child-row-'+id_data).remove();
            TrTable.removeClass('shown');
            one_control.button('reset');
        }

    })

    $(document).on('click', '.two-control', function(e){
        var id_data = $(this).attr('id-data');
        var TrTable = $(this).parents('tr');
        var two_control = $(this);
        if(!TrTable.hasClass('shown'))
        {
            $('#child_two_search').append('<option value="'+id_data+'"></option>');
            TrTable.addClass('shown');
            var data = {id : id_data, colspan : colspan};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }

            var colspan = TrTable.find('td').length;
            $.post(admin_url+'clients/GetTableChildTwo', data, function(data) {
                TrTable.after(data);
                two_control.button('reset')
            })
        }
        else
        {
            $('.child-row-two-'+id_data).remove();
            $('#child_two_search').find('option[value="'+id_data+'"]').remove();
            TrTable.removeClass('shown');
            two_control.button('reset')
        }

    })


    function deleteTwo(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        }
        else
        {
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url + 'clients/DeleteClientInfoDetailValue/' + id, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-info_client_group').DataTable().ajax.reload();
                    alert_float(response.alert_type, response.message)
                } else {
                    alert_float(response.alert_type, response.message)
                }
            });
        }
        return false;
    }
    function deleteOne(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt');?>");
        if (r == false) {
            return false;
        }
        else
        {
            var data = {};
            if (typeof (csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
            $.post(admin_url + 'clients/delete_info_detail/' + id, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    $('.table-info_client_group').DataTable().ajax.reload();
                    alert_float(response.alert_type, response.message)
                } else {
                    alert_float(response.alert_type, response.message)
                }
            });
        }
        return false;
    }



</script>
</body>
</html>
