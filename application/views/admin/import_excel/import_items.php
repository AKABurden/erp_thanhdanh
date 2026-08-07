<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <?php echo form_open(admin_url('import_excel/action_imports_items'), array('id' => 'import_form', 'enctype' => 'multipart/form-data')); ?>

    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <button class="btn btn-info only-save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_upding')?>">
                <?=_l('submit');?>
            </button>
        </div>
    </div>
    <div class="panel_s">
        <div class="content">

            <h3><?=_l('ch_items_import')?></h3>
            <h5 style="color:red">Các trường bắt buộc cần có: Tên mặt hàng, Đơn vị tính, Danh mục hàng hóa!</h5>
            <div class="panel-body">
                <div class="col-md-12">
                        <?php
                            $string_option = "<option></option>";
                            $listArray = [];
                        ?>
                        <?php $colum_info_suppliers = get_fields_import_items_excel_hau(); ?>
                        <?php
                            foreach ($colum_info_suppliers['colum_items'] as $key => $value) {
                                $listArray[] = ['id' => $value, 'name' => mb_strtoupper(_l('ch_items_' . $value), 'UTF-8')];
                                $string_option .= "<option value='".$value."'>".mb_strtoupper(_l('ch_items_' . $value), 'UTF-8')."</option>";
                            }
                        ?>
                    <div class="clearfix"></div>
                    <div class="col-md-12 mbot20">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <span class="btn btn-default btn-file col-md-6">
                                <span>Choose file</span>
                                <input  type="file" name="file" class="mbot10 btn" style="width:100%" id="file_import" required />
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_input('row_start','cong_start_row', '1') ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_input('row_end','cong_end_row') ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2 mbot20">
                        <lable><h4><?=_l('cong_fieldsColums')?></h4></lable>
                    </div>
                    <div class="col-md-2 mbot20">
                        <lable><h4><?=_l('cong_colum')?></h4></lable>
                    </div>
                    <div class="col-md-3 mbot20">
                        <lable><h4><?=_l('Ghi chú')?></h4></lable>
                    </div>
                    <div class="col-md-4 mbot20">
                        <lable><h4><?=_l('Tùy chỉnh')?></h4></lable>
                    </div>
                    <div class="col-md-1 mbot20">
                        <lable><h4><?=_l('ch_option')?></h4></lable>
                    </div>    
                    <div class="clearfix"></div>
                    <div class="DivRowColum">
                        <?php if(!empty($bianroi)){ ?>
                            <div class="row-items mtop20">
                                <div class="col-md-3">
                                    <select name="fieldsColums[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?=_l('cong_select_colun_import')?>" data-live-search="true" tabindex="-98">
                                        <?=$string_option?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="Colum[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?=_l('cong_colum')?>" data-live-search="true" tabindex="-98">
                                        <?php
                                            if(!empty($columsExcel))
                                            {
                                                foreach($columsExcel as $key => $value)
                                                {
                                                    echo "<option value='".$key."'>".$value."</option>";
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>
                                <div class="clearfix"></div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-plus-footer mtop20">
                            <button type="button" class="btn btn-info btn-addRow">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i> <?=_l('cong_add_colum')?>
                            </button>

                            <button type="button" class="btn btn-success btn-addRow-all" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_createing_colum')?>">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i> <?=_l('cong_add_colum_auto')?>
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <?php echo form_close(); ?>
</div>

<?php init_tail(); ?>
<script>
    var DisCount = 0;
    var fieldsColumsItem = <?=json_encode($listArray);?>;
    var columsExcelItem = <?=json_encode($columsExcel);?>;
    $('body').on('click', '.btn-addRow', function(e){
        var div_one = $('<div class="col-md-2"></div>');
        var div_two = $('<div class="col-md-2"></div>');
        var div_remove = $('<div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>');
        var div_note = $('<div class="div_note col-md-3"></div>');
        var div_checkbox = $('<div class="col-md-3"></div>');

        //select 1
        var fieldsColums = $('<select></select>');
        fieldsColums.attr('name','fieldsColums['+DisCount+']').attr('class','selectpicker fieldsColums').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_select_colun_import')?>').attr('data-live-search',true).attr('tabindex','-98');
        fieldsColums.append('<option></option>');
        $.each(fieldsColumsItem, function(key, Val){
            fieldsColums.append('<option value="'+ Val.id +'">'+ Val.name +'</option>');
        })
        div_one.append(fieldsColums);

        //select 2
        var Colum = $('<select></select>');
        Colum.attr('name','Colum['+DisCount+']').attr('class','selectpicker Colum').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_colum')?>').attr('data-live-search',true).attr('tabindex','-98');
        Colum.append('<option></option>');
        $.each(columsExcelItem, function(key, Val){
            Colum.append('<option value="'+ key +'">'+ Val +'</option>');
        })
        div_two.append(Colum);

        var DivCol_5 = $('<div class="col-md-4"></div>');
        var CpanelBody = $('<div></div>');

        var CpanelBodyCheck_ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
        CpanelBodyCheck_.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="1" id="type_data_'+DisCount+'_1">');
        CpanelBodyCheck_.append('<label for="type_data_'+DisCount+'_1"><?=_l('cong_search_true_import')?></label>');

        var CpanelBodyCheck__ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
        CpanelBodyCheck__.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="2" id="type_data_'+DisCount+'_2">');
        CpanelBodyCheck__.append('<label for="type_data_'+DisCount+'_2"><?=_l('cong_search_similar')?></label>');

        var CpanelBodyCheck___ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 hide"></div>');
        CpanelBodyCheck___.append('<input type="radio" class="rel_type" name="type_event['+DisCount+']" value="1" id="type_event_'+DisCount+'_1">');
        CpanelBodyCheck___.append('<label for="type_event_'+DisCount+'_1"><?=_l('cong_add_new')?></label>');

        var CpanelBodyCheck____ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
        CpanelBodyCheck____.append('<input type="radio" class="rel_type" name="type_event['+DisCount+']" value="2" id="type_event_'+DisCount+'_2">');
        CpanelBodyCheck____.append('<label for="type_event_'+DisCount+'_2"><?=_l('cong_skip_row')?></label>');

        CpanelBody.append(CpanelBodyCheck_);
        CpanelBody.append(CpanelBodyCheck__);
        CpanelBody.append(CpanelBodyCheck___);
        CpanelBody.append(CpanelBodyCheck____);
        DivCol_5.append(CpanelBody);

        //Append 2 select, div delete and creafix
        var RowItem = $('<div class="row-items mtop20"></div>');
        RowItem.append(div_one);
        RowItem.append(div_two);
        RowItem.append(div_note);
        RowItem.append(DivCol_5);
        RowItem.append(div_remove);
        RowItem.append('<div class="clearfix"></div>');
        RowItem.find('select').selectpicker('refresh');
        $('.DivRowColum').append(RowItem);
        DisCount++;
    })

    $('body').on('click', '.deleteRow', function(e){
        $(this).parents('.row-items').remove();
    })




    //POST IMPORT FILE
    window.addEventListener('load',function(event) {
        appValidateForm($('#import_form'), {
            file:'required',
            country:'required',
        }, import_manage_excel);
    })
    function import_manage_excel(form) {
        var data = $(form).serializeArray();
        var url = form.action;

        var file_data = $('input#file_import').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('csrf_token_name', csrfData.hash);
        $.each(data, function(key, Val){
            form_data.append(Val.name, Val.value);
        })
        $.ajax({
                url: url,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(data) {
                     alert_float(data.alert_type, data.message);
                  },
              error: function() {
                alert_float('danger', 'err');
              }
            }).always(function () {
                $('.only-save').button('reset');
            });
        return false;
    }

    $('body').on('change', 'select.fieldsColums', function(e){
        var row_items = $(this).parents('.row-items');
        var ValFields = $(this).val();
        var ListArray = ['unit', 'brand_id', 'group_id','category_id','color_id','packaging_id'];
       
        var StringTrue = 0;
        row_items.find('.checkbox').addClass('hide');
        var div_note = '';
                    if(ValFields == 'code')
                    {
                      div_note = 'Vui lòng nhập mã, không được trùng (Bỏ trống sẽ lấy mã tự động của hệ thống)';  
                    }
                    if(ValFields == 'name')
                    {
                      div_note = 'Không được bỏ trống';  
                    }
                    if(ValFields == 'unit')
                    {
                      div_note = 'Không được bỏ trống';  
                    }
                    if(ValFields == 'group_id')
                    {
                      div_note = 'Không được bỏ trống';  
                    }
                    if(ValFields == 'price')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }
                    if(ValFields == 'price_single')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }
                    if(ValFields == 'minimum_quantity')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }
                    if(ValFields == 'maximum_quantity')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }
                    if(ValFields == 'price_import')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }
                    if(ValFields == 'price_manufacturing')
                    {
                      div_note = 'Dạng số không dấu "," dấu "."';  
                    }

                    if(ValFields == 'category_id')
                    {
                      div_note = 'Không được bỏ trống . Phải tạo trước trong phần mềm';  
                    }
                    if(ValFields == 'color_id')
                    {
                      div_note = 'Nhập mã';  
                    }
                    if(ValFields == 'packaging_id')
                    {
                      div_note = 'Nhập mã';  
                    }
                    if(ValFields == 'category_id')
                    {
                      div_note = 'Không được bỏ trống';  
                    }
                    if(ValFields == 'type')
                    {
                      div_note = 'Chọn đúng định dạng "1: Tiêu chuẩn" và "2: Combo"';  
                    }
                    row_items.find('div.div_note').html(div_note);
        $.each(ListArray, function(i, v){
            if(ValFields == v)
            {
                row_items.find('.checkbox').removeClass('hide');
                if (vVal.id == 'category_id' || vVal.id == 'color_id'|| vVal.id == 'addedfrom') 
                {
                    row_items.find('.checkbox').not('.cbobox').addClass('hide');
                }
                StringTrue = true;

            }
        })
        row_items.find('.checkbox').find('input').prop('checked', false);
    })

    $('body').on('click', '.btn-addRow-all', function(e){
        if(confirm('<?=_l('cong_you_create_will_delete_all_row')?>?'))
        {
            setTimeout(function()
            {
                $('.DivRowColum').find('.row-items').remove();
                $.each(fieldsColumsItem, function (iKey, vVal) {
                    var div_one = $('<div class="col-md-2"></div>');
                    var div_two = $('<div class="col-md-2"></div>');
                    var div_remove = $('<div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>');
                    var div_note = $('<div class="col-md-3"></div>');
                    //select 1
                    var fieldsColums = $('<select></select>');
                    fieldsColums.attr('name', 'fieldsColums[' + DisCount + ']').attr('class', 'selectpicker fieldsColums').attr('data-width', '100%').attr('data-none-selected-text', '<?=_l('cong_select_colun_import')?>').attr('data-live-search', true).attr('tabindex', '-98');
                    fieldsColums.append('<option></option>');
                    $.each(fieldsColumsItem, function (key, Val) {
                        fieldsColums.append('<option value="' + Val.id + '" ' + (vVal.id == Val.id ? "selected" : '') + '>' + Val.name + '</option>');
                    })
                    div_one.append(fieldsColums);

                    //select 2
                    var Colum = $('<select></select>');
                    Colum.attr('name', 'Colum[' + DisCount + ']').attr('class', 'selectpicker Colum').attr('data-width', '100%').attr('data-none-selected-text', '<?=_l('cong_colum')?>').attr('data-live-search', true).attr('tabindex', '-98');
                    Colum.append('<option></option>');
                    $.each(columsExcelItem, function (key, Val) {
                        Colum.append('<option value="' + key + '" ' + (iKey == key ? "selected" : '') + '>' + Val + '</option>');
                    })
                    div_two.append(Colum);

                    var DivCol_5 = $('<div class="col-md-4"></div>');
                    var CpanelBody = $('<div></div>');

                    var CpanelBodyCheck_ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
                    CpanelBodyCheck_.append('<input type="radio" class="rel_type" name="type_data[' + DisCount + ']" value="1" checked id="type_data_' + DisCount + '_1">');
                    CpanelBodyCheck_.append('<label for="type_data_' + DisCount + '_1"><?=_l('cong_search_true_import')?></label>');

                    var CpanelBodyCheck__ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
                    CpanelBodyCheck__.append('<input type="radio" class="rel_type" name="type_data[' + DisCount + ']" value="2" id="type_data_' + DisCount + '_2">');
                    CpanelBodyCheck__.append('<label for="type_data_' + DisCount + '_2"><?=_l('cong_search_similar')?></label>');

                    var CpanelBodyCheck___ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 hide"></div>');
                    CpanelBodyCheck___.append('<input type="radio" class="rel_type" name="type_event[' + DisCount + ']" value="1" id="type_event_' + DisCount + '_1">');
                    CpanelBodyCheck___.append('<label for="type_event_' + DisCount + '_1"><?=_l('cong_add_new')?></label>');

                    var CpanelBodyCheck____ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox hide"></div>');
                    CpanelBodyCheck____.append('<input type="radio" class="rel_type" name="type_event[' + DisCount + ']" value="2" checked id="type_event_' + DisCount + '_2">');
                    CpanelBodyCheck____.append('<label for="type_event_' + DisCount + '_2"><?=_l('cong_skip_row')?></label>');

                    CpanelBody.append(CpanelBodyCheck_);
                    CpanelBody.append(CpanelBodyCheck__);
                    CpanelBody.append(CpanelBodyCheck___);
                    CpanelBody.append(CpanelBodyCheck____);
                    DivCol_5.append(CpanelBody);
                    if(vVal.id == 'code')
                    {
                      div_note.append('Vui lòng nhập mã, không được trùng (Bỏ trống sẽ lấy mã tự động của hệ thống)');  
                    }
                    if(vVal.id == 'name')
                    {
                      div_note.append('Không được bỏ trống');  
                    }
                    if(vVal.id == 'unit')
                    {
                      div_note.append('Không được bỏ trống');  
                    }
                    if(vVal.id == 'group_id')
                    {
                      div_note.append('Không được bỏ trống');  
                    }
                    if(vVal.id == 'price')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'price_single')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'minimum_quantity')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'maximum_quantity')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'price_manufacturing')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'price_import')
                    {
                      div_note.append('Dạng số không dấu "," dấu "."');  
                    }
                    if(vVal.id == 'category_id')
                    {
                      div_note.append('Không được bỏ trống. Phải tạo trước trong phần mềm');  
                    }
                    if(vVal.id == 'type')
                    {
                      div_note.append('Chọn đúng định dạng "1: Tiêu chuẩn" và "2: Combo"');  
                    }


                    //Append 2 select, div delete and creafix
                    var RowItem = $('<div class="row-items mtop20"></div>');
                    RowItem.append('<hr>');
                    RowItem.append(div_one);
                    RowItem.append(div_two);
                    RowItem.append(div_note);
                    RowItem.append(DivCol_5);
                    RowItem.append(div_remove);
                    RowItem.append('<div class="clearfix"></div>');
                    RowItem.find('select').selectpicker('refresh');
                    $('.DivRowColum').append(RowItem);


                    var ListArray = ['unit', 'brand_id', 'group_id','category_id','color_id','packaging_id'];
                    
                    $.each(ListArray, function (i, v) {
                        if (vVal.id == v) {
                            var row_items = $('select[name="fieldsColums[' + DisCount + ']"]').parents('div.row-items');
                            row_items.find('.checkbox').removeClass('hide');
                            if (vVal.id == 'category_id' || vVal.id == 'color_id'|| vVal.id == 'addedfrom') {
                                row_items.find('.checkbox').not('.cbobox').addClass('hide');
                            }
                            return false;
                        }
                    })
                    DisCount++;
                })

                $('.btn-addRow-all').button('reset');
            }, 1000);
        }
        else
        {
            $('.btn-addRow-all').button('reset');
        }
    })
    $('body').on('click', '.only-save', function(){
        setTimeout(function()
        {
            if($('#file_import').parent().find('p').html() != "" || $('#country-error').html() != "")
            {
                console.log($('#file_import').parent().find('p').html());
                $('.only-save').button('reset');
            }
        }, 1000);
    })

</script>

