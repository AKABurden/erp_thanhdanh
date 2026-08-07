<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <?php echo form_open(admin_url('import_excel/action_imports_client'), array('id' => 'import_form', 'enctype' => 'multipart/form-data')); ?>
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <button class="btn btn-info only-save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=_l('cong_upding')?>">
                <?=_l('submit');?>
            </button>
            <div class="checkbox checkbox-primary hide">
                <input type="checkbox" name="saveImport" id="saveImport">
                <label for="saveImport"><?=_l('cong_save_template_import')?></label>
            </div>
        </div>
    </div>
    <div class="panel_s">
        <div class="content">
            <h3><?=_l('cong_import_data_client')?></h3>
            <h5 class="text-danger">
                <div>
                    - Xin vui lòng tải xuống tập tin mẫu:
                    <a href="<?=base_url('uploads/template/')?>MẪU IMPORT KHÁCH HÀNG.xlsx?vs=1.1" title="">File mẫu</a>
                </div>
            </h5>
            <div class="panel-body">
                <div class="col-md-12">
                    <?php
                        $string_option = "<option></option>";
                        $listArray = [];
                    ?>
                    <?php $colum_fields_client = get_fields_import_client_excel(); ?>
                    <?php
                        $filedCustomer = [];
                        foreach ($colum_fields_client['colum_client'] as $key => $value) {
                            $listArray[] = ['id' => $value, 'name' => mb_strtoupper(_l('cong__' . $value), 'UTF-8')];
                            $string_option .= "<option value='".$value."'>".mb_strtoupper(_l('cong__' . $value), 'UTF-8')."</option>";
							$filedCustomer[] = ['field' => $value, 'rowExcel' => $key];
                        }
                    ?>
                    <!--Các trường động ở bảng info-->
                    <?php
                    $TypeFieldForm = [];
                    foreach ($colum_fields_client['colum_info_client'] as $key => $value) {
                        $listArray[] = ['id' => $value['id'], 'name' => mb_strtoupper($value['name'], 'UTF-8')];
                        $string_option .= "<option value='".$value['id']."'>".mb_strtoupper($value['name'], 'UTF-8')."</option>";
                        if($value['type_form'] == 'select' || $value['type_form'] == 'select multiple' || $value['type_form'] == 'radio' || $value['type_form'] == 'checkbox')
                        {
                            $TypeFieldForm[$value['id']] = $value['type_form'];
                        }
                    }
					$filedContacts = [
                            ['start' => count($filedCustomer), 'end' => count($filedCustomer) + 3, 'field' => 'firstname'],
                            ['field' => 'title'],
                            ['field' => 'email'],
                            ['field' => 'phonenumber'],
                    ];

                    ?>

                    <div class="clearfix"></div>
	                <?php $template_import = get_table_where('tbltemplate_import', ['type' => 'client']); ?>
                    <div class="form-group col-md-6 hide" app-field-wrapper="template_import">
                        <label for="template_import" class="control-label"><?=_l('cong_template_import')?></label>
                        <select id="template_import" class="selectpicker" data-width="100%" data-none-selected-text="Không có mục nào được chọn" data-live-search="true" tabindex="-98">
                            <option value=""></option>
			                <?php foreach($template_import as $keyTemplate => $valTemplate) { ?>
                                <option value="<?=$valTemplate['id']?>"><?= _dt($valTemplate['date_create']) ?></option>
			                <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-6">
                        <div class="col-md-4">
                            <div class="radio radio-primary">
                                <input type="radio" name="action" class="checkActive" id="check_add" value="1" checked>
                                <label for="check_add"><?=_l('cong_add')?></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="radio radio-primary">
                                <input type="radio" name="action" class="checkActive" id="check_update" value="2">
                                <label for="check_update"><?=_l('cong_update')?></label>
                            </div>
                        </div>
                        <div class="col-md-4 hide">
                            <div class="radio radio-primary">
                                <input type="radio" name="action" class="checkActive" id="check_update" value="3">
                                <label for="check_update"><?=_l('cong_add')?> and <?=_l('cong_update')?></label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6 mbot20" id="div_colum_update"></div>
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
                        <lable>
                            <h4>
                                <?=_l('cong_fieldsColums')?>
                            </h4>
                        </lable>
                    </div>
                    <div class="col-md-2 mbot20">
                        <lable>
                            <h4>
                                <?=_l('cong_colum')?>
                            </h4>
                        </lable>
                    </div>
                    <div class="clearfix"></div>
                    <div class="DivRowColum">
                        <div class="RowContact"></div>
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
                    <hr/>
                    <h5><?=_l('custom_field_contacts')?></h5>
                    <div class="DivRowContact"></div>
                    <div class="clearfix"></div>
                    <button type="button" class="btn btn-info btn-addRow-contact mtop20 mleft10">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> <?=_l('cong_add_colum')?>
                    </button>
                    <hr/>

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
<?php  $columUnique = [
        ['id' => 'zcode', 'name' => _l('cong__zcode')],
	    ['id' =>'userid', 'name' => _l('cong__userid')],
	    ['id' =>'email_client', 'name' => _l('cong__email_client')],
];?>
<?php  $columExcelItemContact = [
        'title' => _l('cong_contact__title'),
        'firstname' => _l('cong_contact__firstname'),
        'email' => _l('cong_contact__email'),
        'phonenumber' =>_l('cong_contact__phonenumber'),
        'birtday' => _l('cong_contact__birtday'),
        'note' => _l('cong_contact__note'),
        'contact_gender' => _l('cong_gender')
];?>
<?php
    $template_import = [
            'id' => 1,
            'setup_colums' => ($filedCustomer),
            'setup_contact' => ($filedContacts),
            'date_create' => date('Y-m-d H:i:s')
    ];
?>
<?php init_tail(); ?>
<script>
    var dataColumsDefault = <?=json_encode($template_import)?>;

    var DisCount = 0;
    var fieldsColumsItem = <?=json_encode($listArray);?>;
    var columsExcelItem = <?=json_encode($columsExcel);?>;
    var columExcelItemContact = <?=json_encode($columExcelItemContact);?>;
    var TypeFieldForm = <?=json_encode($TypeFieldForm);?>;
    var TypeFieldUnique = <?=json_encode($columUnique);?>;

    $('body').on('click', '.btn-addRow', function(e){
        addRowItem();
    })
    function addRowItem(fields = '', colum = '', type_data = '', type_event = '') {
        var div_one = $('<div class="col-md-3"></div>');
        var div_two = $('<div class="col-md-2"></div>');
        var div_remove = $('<div class="col-md-1 mtop10 text-danger deleteRow pointer">X</div>');
        var div_checkbox = $('<div class="col-md-5"></div>');

        //select 1
        var fieldsColums = $('<select></select>');
        fieldsColums.attr('name','fieldsColums['+DisCount+']').attr('class','selectpicker fieldsColums').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_select_colun_import')?>').attr('data-live-search',true).attr('tabindex','-98');
        fieldsColums.append('<option></option>');
        $.each(fieldsColumsItem, function(key, Val){
            fieldsColums.append('<option value="'+ Val.id +'" '+(fields == Val.id ? 'selected' : '')+'>'+ Val.name +'</option>');
        })
        div_one.append(fieldsColums);

        //select 2
        var Colum = $('<select></select>');
        Colum.attr('name','Colum['+DisCount+']').attr('class','selectpicker Colum').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_colum')?>').attr('data-live-search',true).attr('tabindex','-98');
        Colum.append('<option></option>');
        $.each(columsExcelItem, function(key, Val){
            Colum.append('<option value="'+ key +'" '+(parseFloat(colum) == parseFloat(key) ? 'selected' : '')+'>'+ Val +'</option>');
        })
        div_two.append(Colum);

        var DivCol_5 = $('<div class="col-md-6"></div>');
        var CpanelBody = $('<div></div>');
        var CpanelBodyCheck_ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox  "></div>');
        CpanelBodyCheck_.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="1"  id="type_data_'+DisCount+'_1">');
        CpanelBodyCheck_.append('<label for="type_data_'+DisCount+'_1"><?=_l('cong_search_true_import')?></label>');

        var CpanelBodyCheck__ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox "></div>');
        CpanelBodyCheck__.append('<input type="radio" class="rel_type" name="type_data['+DisCount+']" value="2"  id="type_data_'+DisCount+'_2">');
        CpanelBodyCheck__.append('<label for="type_data_'+DisCount+'_2"><?=_l('cong_search_similar')?></label>');

        var CpanelBodyCheck___ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 "></div>');
        CpanelBodyCheck___.append('<input type="radio" class="rel_type" name="type_event['+DisCount+']" value="1"  id="type_event_'+DisCount+'_1">');
        CpanelBodyCheck___.append('<label for="type_event_'+DisCount+'_1"><?=_l('cong_add_new')?></label>');

        var CpanelBodyCheck____ = $('<div class="checkbox checkbox-info mbot20 no-mtop col-md-3 cbobox"></div>');
        CpanelBodyCheck____.append('<input type="radio" class="rel_type" name="type_event['+DisCount+']" value="2"  id="type_event_'+DisCount+'_2">');
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
        var discount_old = DisCount;
        setTimeout(function(e){
            if(type_data) {
                console.log($('#type_data_'+discount_old+'_'+type_data))
                $('#type_data_'+discount_old+'_'+type_data).prop('checked', true);
            }

            if(type_event) {
                $('#type_event_'+discount_old+'_'+type_event).prop('checked', true);
            }
        }, 1000)
        DisCount++;
    }

    var NumColum = 0;
    $('body').on('click', '.btn-addRow-contact', function(e){
        addRowItemContact();
    })


    function addRowItemContact(fieldContact = '', start = '', end = '') {
        if($('select[name="ColumContact[start]"]').length == 0) {
            var div_start = $('<div class="col-md-3 mtop20"><label for="row_start" class="control-label"><?=_l('cong_start_row')?></label></div>');
            var div_end = $('<div class="col-md-3 mtop20"><label for="row_start" class="control-label"><?=_l('cong_end_row')?></label></div>');
            var div_checkbox = $('<div class="col-md-6"></div>');
            var ColumStart = $('<select></select>');
            ColumStart.attr('name', 'ColumContact[start]').attr('class','selectpicker fieldsColums').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_colum')?>').attr('data-live-search',true).attr('tabindex','-98');
            ColumStart.append('<option></option>');
            $.each(columsExcelItem, function(key, Val){
                ColumStart.append('<option value="'+ key +'" '+(parseFloat(start) == parseFloat(key) ? 'selected' : "")+'>'+ Val +'</option>');
            })
            div_start.append(ColumStart);
            var ColumEnd = $('<select></select>');
            ColumEnd.attr('name', 'ColumContact[end]').attr('class','selectpicker fieldsColums').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_colum')?>').attr('data-live-search',true).attr('tabindex','-98');
            ColumEnd.append('<option></option>');
            $.each(columsExcelItem, function(key, Val){
                ColumEnd.append('<option value="'+ key +'" '+(parseFloat(end) == parseFloat(key) ? 'selected' : "")+'>'+ Val +'</option>');
            })
            div_end.append(ColumEnd);
            $('.DivRowContact').append(div_start);
            $('.DivRowContact').append(div_end);
            $('.DivRowContact').append(div_checkbox);
            $('.DivRowContact').append('<div class="clearfix"></div>');
        }
        var div_remove = $('<div class="col-md-1 mtop30 text-danger deleteRow pointer">X</div>');
        var div_colum = $('<div class="col-md-3 mtop20"></div>');
        var ColumContact = $('<select></select>');
        ColumContact.attr('name', 'fieldContact['+NumColum+']').attr('class','selectpicker fieldsColums').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_add_colum_auto')?>').attr('data-live-search',true).attr('tabindex','-98');
        ColumContact.append('<option></option>');
        $.each(columExcelItemContact, function(key, Val){
            console.log(Val)
            ColumContact.append('<option value="'+ key +'" '+(fieldContact == key ? 'selected' : "")+'>'+ Val +'</option>');
        })
        div_colum.append(ColumContact);
        var row_items = $('<div class="row-items"></div>');
        row_items.append(div_colum);
        row_items.append(div_remove);
        row_items.append($('<div class="clearfix"></div>'));
        $('.DivRowContact').append(row_items);
        $('.DivRowContact').find('select').selectpicker('refresh');
        NumColum++;
    }

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
        }, '', {file : '<?=_l('cong_not_format_xlsx_xls')?>'});
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
        var ListArray = ['dt' ,'type_client', 'kt', 'marriage', 'religion', 'city', 'customer_id', 'district', 'ward', 'sources', 'ward', 'groups_in'];
        $.each(TypeFieldForm, function(i, v){
            ListArray.push(i)
        })
        var StringTrue = 0;
        row_items.find('.checkbox').addClass('hide');
        $.each(ListArray, function(i, v){
            if(ValFields == v)
            {
                row_items.find('.checkbox').removeClass('hide');
                if(ValFields == 'city' || ValFields == 'customer_id' || ValFields == 'district' || ValFields == 'ward') {
                    row_items.find('.checkbox').not('.cbobox').addClass('hide');
                    row_items.find('.checkbox').find('input.rel_type').attr('type','checkbox');
                }
                else {
                    row_items.find('.checkbox').find('input.rel_type').attr('type','radio');
                }
                StringTrue = true;

            }
        })
        row_items.find('.checkbox').find('input').prop('checked', false);
    })

    $(document).on('change', 'input[name="action"]', function(event) {
        if ($('input[name="action"]:checked').val() == 1) {
            $('.btn-addRow-all').click();
            $('.btn-addRow').css({'display': 'none'});
        } else {
            $('.DivRowColum').find('.row-items').remove();
            $('.deleteRow').css({'display': ''});
            $('.DivRowColum').css({'pointer-events': ''});
            $('.btn-addRow').css({'display': ''});
        }
    });

    $('body').on('click', '.btn-addRow-all', function(e){
        // if(confirm('<?=_l('cong_you_create_will_delete_all_row')?>?'))
        // {
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


                    var ListArray = ['dt', 'type_client', 'kt', 'marriage', 'religion', 'city','customer_id', 'district', 'ward', 'sources', 'ward', 'groups_in'];
                    $.each(TypeFieldForm, function(i, v){
                        ListArray.push(i);
                    })
                    $.each(ListArray, function (i, v) {
                        if (vVal.id == v) {
                            var row_items = $('select[name="fieldsColums[' + DisCount + ']"]').parents('div.row-items');
                            row_items.find('.checkbox').removeClass('hide');
                            if (vVal.id == 'city' || vVal.id == 'customer_id' || vVal.id == 'district' || vVal.id == 'ward') {
                                row_items.find('.checkbox').not('.cbobox').addClass('hide');
                            }
                            return false;
                        }
                    })
                    DisCount++;
                })

                $('.deleteRow').css({'display': 'none'});
                $('.DivRowColum').css({'pointer-events': 'none'});

                $('.btn-addRow-all').button('reset');
            }, 1000);
        // }
        // else
        // {
        //     $('.btn-addRow-all').button('reset');
        // }
    })

    $('body').on('click', '.only-save', function(){
        setTimeout(function() {
            if($('#file_import').parent().find('p').html() != "" || $('#country-error').html() != "")
            {
                console.log($('#file_import').parent().find('p').html());
                $('.only-save').button('reset');
            }
        }, 1000);
    })

    $('body').on('change', '#template_import', function(e){
        var template = $(this).val();
        var data = {id : template};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'import_excel/getTemplateImport', data, function(result){
            result = JSON.parse(result);
            if(result.success) {
                $('.DivRowColum .row-items').remove();
                $.each(result.setup_colums, function(i, v){
                    addRowItem(v.field, v.rowExcel, v.type_data , v.type_event);
                })
                $('select.fieldsColums').trigger('change');

                $.each(result.setup_contact, function(i, v){
                    addRowItemContact(v.field, v.start, v.end);
                })
            }
        })
    })

    $('body').on('change', '.checkActive', function(e){
        var checkActive = $('.checkActive:checked').val();
        if(checkActive == 2 || checkActive == 3) {
            var div_one = $('<div class="col-md-6"><label class="control-label"><?=_l('cong_select_colun_unique')?></label></div>');

            //select 1
            var fieldsColums = $('<select></select>');
            fieldsColums.attr('name','fieldsUnique').attr('class','selectpicker fieldsUnique').attr('data-width','100%').attr('data-none-selected-text','<?=_l('cong_select_colun_unique')?>').attr('data-live-search',true).attr('tabindex','-98');
            fieldsColums.append('<option></option>');
            $.each(TypeFieldUnique, function(key, Val){
                fieldsColums.append('<option value="'+ Val.id +'">'+ Val.name +'</option>');
            })
            div_one.append(fieldsColums);

            //Append 2 select, div delete and creafix
            var RowItem = $('<div class="row-items mtop20"></div>');
            RowItem.append(div_one);
            RowItem.append('<div class="clearfix"></div>');
            RowItem.find('select').selectpicker('refresh');
            $('#div_colum_update').html(RowItem);
            $('select[name="fieldsUnique"]').selectpicker('refresh');
        }
        else
        {
            $('#div_colum_update').html('');
        }
    })

</script>


<script type="text/javascript">
    Pusher.logToConsole = true;
	<?php $pusher_options_My['cluster'] = 'ap1'; ?>
    var pusher_options_My = <?php echo json_encode($pusher_options_My); ?>;
    var pusher_My = new Pusher("60e05f534e4a79eff6f8", pusher_options_My);
    var MySendchannel = pusher_My.subscribe('404960666902794');

    MySendchannel.bind('GetComment', function(data) {
        console.log(data);

        console.log(data.item)
        console.log(data.post)
        console.log(data.from)
    });
</script>
<script>
    $(document).ready(function () {
        $('.DivRowColum .row-items').remove();
        result = dataColumsDefault;
        $.each(result.setup_colums, function(i, v){
            addRowItem(v.field, v.rowExcel, v.type_data , v.type_event);
        })
        $('select.fieldsColums').trigger('change');

        $.each(result.setup_contact, function(i, v){
            addRowItemContact(v.field, v.start, v.end);
        })



        // $('.btn-addRow-all').click();
        // $('.btn-addRow').css({'display': 'none'});
        // $('.btn-addRow-all').css({'display': 'none'});
    });
</script>

