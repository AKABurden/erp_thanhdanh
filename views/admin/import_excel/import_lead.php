<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <?php echo form_open(admin_url('import_excel/action_imports_lead'), array('id' => 'import_form', 'enctype' => 'multipart/form-data')); ?>

    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <button class="btn btn-info only-save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_upding')?>">
                <?=_l('submit');?>
            </button>
        </div>
    </div>
    <div class="panel_s">
        <div class="content">

            <h3><?=_l('cong_import_data_client')?></h3>
            <h5 class="text-danger">
                <?=_l('cong_note:')?>
            </h5>
            <h5 class="text-danger">
                <?=_l('cong_note_import_client_cbobox')?>
            </h5>
            <h5 class="text-danger">
                <?=_l('cong_note_import_client_localtion')?>
            </h5>
            <div class="panel-body">
                <div class="col-md-12">
                        <?php
                            $string_option = "<option></option>";
                            $listArray = [];
                        ?>
                        <?php $colum_fields_lead = get_fields_import_lead_excel(); ?>
                        <?php
                            foreach ($colum_fields_lead['colum_client'] as $key => $value) {
                                $listArray[] = ['id' => $value, 'name' => mb_strtoupper(_l('cong__' . $value), 'UTF-8')];
                                $string_option .= "<option value='".$value."'>".mb_strtoupper(_l('cong__' . $value), 'UTF-8')."</option>";
                            }
                        ?>
                        <!--Các trường động ở bảng info-->
                        <?php
                        $TypeFieldForm = [];
                        foreach ($colum_fields_lead['colum_info_lead'] as $key => $value) {
                            $listArray[] = ['id' => $value['id'], 'name' => mb_strtoupper($value['name'], 'UTF-8')];
                            $string_option .= "<option value='".$value['id']."'>".mb_strtoupper($value['name'], 'UTF-8')."</option>";
                            if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple' || $value['type_form'] == 'radio' || $value['type_form'] == 'checkbox')
                            {
                                $TypeFieldForm[$value['id']] = $value['type_form'];
                            }
                        } ?>
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
                    <div class="col-md-3 mbot20">
                        <lable><h4><?=_l('cong_fieldsColums')?></h4></lable>
                    </div>
                    <div class="col-md-2 mbot20">
                        <lable><h4><?=_l('cong_colum')?></h4></lable>
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
                    <div class="col-md-3 mtop20">
                        <?php $selected =  get_option('customer_default_country') ?>
                        <?php echo render_select('country', $country, ['country_id', 'short_name'], 'cong_country', $selected)?>
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
    var TypeFieldForm = <?=json_encode($TypeFieldForm);?>;
    $('body').on('click', '.btn-addRow', function(e){
        var div_one = $('<div class="col-md-3"></div>');
        var div_two = $('<div class="col-md-2"></div>');
        var div_remove = $('<div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>');
        var div_checkbox = $('<div class="col-md-5"></div>');

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

        var DivCol_5 = $('<div class="col-md-6"></div>');
        var CpanelBody = $('<div></div>');

        var CpanelBodyCheck_ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
        CpanelBodyCheck_.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="1" id="type_data_'+DisCount+'_1">');
        CpanelBodyCheck_.append('<label for="type_data_'+DisCount+'_1"><?=_l('cong_search_true_import')?></label>');

        var CpanelBodyCheck__ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
        CpanelBodyCheck__.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="2" id="type_data_'+DisCount+'_2">');
        CpanelBodyCheck__.append('<label for="type_data_'+DisCount+'_2"><?=_l('cong_search_similar')?></label>');

        var CpanelBodyCheck___ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 hide"></div>');
        CpanelBodyCheck___.append('<input type="radio" class="rel_type" name="type_event['+DisCount+']" value="1" id="type_event_'+DisCount+'_1">');
        CpanelBodyCheck___.append('<label for="type_event_'+DisCount+'_1"><?=_l('cong_add_new')?></label>');

        var CpanelBodyCheck____ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
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
        RowItem.append(div_remove);
        RowItem.append(DivCol_5);
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
            file: {
                required: true,
                extension :'xlsx,xls'
            },
            country:'required',
        }, import_manage_excel, {file : '<?=_l('cong_not_format_xlsx_xls')?>'});
    })

    // appValidateForm($('#import_form'),{file_csv:{required:true,extension: "csv"},source:'required',status:'required'});

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
                type: 'post'
            }).done(function (data) {
                    alert_float(data.alert_type, data.message);
            }).fail(function () {
                    alert_float('danger', 'err');
            }).always(function () {
                $('.only-save').button('reset');
            });
        return false;
    }

    $('body').on('change', 'select.fieldsColums', function(e){
        var row_items = $(this).parents('.row-items');
        var ValFields = $(this).val();
        var ListArray = ['dt' ,'type_client', 'kt', 'marriage', 'religion', 'city', 'district', 'ward', 'sources', 'ward'];
        $.each(TypeFieldForm, function(i, v){
            ListArray.push(i);
            console.log(i)
        })
        var StringTrue = 0;
        row_items.find('.checkbox').addClass('hide');
        $.each(ListArray, function(i, v){
            if(ValFields == v)
            {
                row_items.find('.checkbox').removeClass('hide');
                if(ValFields == 'city' || ValFields == 'district' || ValFields == 'ward')
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
                    var div_one = $('<div class="col-md-3"></div>');
                    var div_two = $('<div class="col-md-2"></div>');
                    var div_remove = $('<div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>');

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

                    var DivCol_5 = $('<div class="col-md-6"></div>');
                    var CpanelBody = $('<div></div>');

                    var CpanelBodyCheck_ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
                    CpanelBodyCheck_.append('<input type="radio" class="rel_type" name="type_data[' + DisCount + ']" value="1" checked id="type_data_' + DisCount + '_1">');
                    CpanelBodyCheck_.append('<label for="type_data_' + DisCount + '_1"><?=_l('cong_search_true_import')?></label>');

                    var CpanelBodyCheck__ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
                    CpanelBodyCheck__.append('<input type="radio" class="rel_type" name="type_data[' + DisCount + ']" value="2" id="type_data_' + DisCount + '_2">');
                    CpanelBodyCheck__.append('<label for="type_data_' + DisCount + '_2"><?=_l('cong_search_similar')?></label>');

                    var CpanelBodyCheck___ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 hide"></div>');
                    CpanelBodyCheck___.append('<input type="radio" class="rel_type" name="type_event[' + DisCount + ']" value="1" id="type_event_' + DisCount + '_1">');
                    CpanelBodyCheck___.append('<label for="type_event_' + DisCount + '_1"><?=_l('cong_add_new')?></label>');

                    var CpanelBodyCheck____ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox hide"></div>');
                    CpanelBodyCheck____.append('<input type="radio" class="rel_type" name="type_event[' + DisCount + ']" value="2" id="type_event_' + DisCount + '_2">');
                    CpanelBodyCheck____.append('<label for="type_event_' + DisCount + '_2"><?=_l('cong_skip_row')?></label>');

                    CpanelBody.append(CpanelBodyCheck_);
                    CpanelBody.append(CpanelBodyCheck__);
                    CpanelBody.append(CpanelBodyCheck___);
                    CpanelBody.append(CpanelBodyCheck____);
                    DivCol_5.append(CpanelBody);

                    //Append 2 select, div delete and creafix
                    var RowItem = $('<div class="row-items mtop20"></div>');
                    RowItem.append(div_one);
                    RowItem.append(div_two);
                    RowItem.append(div_remove);
                    RowItem.append(DivCol_5);
                    RowItem.append('<div class="clearfix"></div>');
                    RowItem.find('select').selectpicker('refresh');
                    $('.DivRowColum').append(RowItem);


                    var ListArray = ['dt', 'type_client', 'kt', 'marriage', 'religion', 'city', 'district', 'ward', 'sources', 'ward'];
                    $.each(TypeFieldForm, function(i, v){
                        ListArray.push(i);
                    })
                    $.each(ListArray, function (i, v) {
                        if (vVal.id == v) {
                            var row_items = $('select[name="fieldsColums[' + DisCount + ']"]').parents('div.row-items');
                            row_items.find('.checkbox').removeClass('hide');
                            if (vVal.id == 'city' || vVal.id == 'district' || vVal.id == 'ward') {
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

