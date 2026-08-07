<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <?php echo form_open('admin/items/import_category', array('id' => 'add-product')); ?>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="text-danger">
                            <?= lang('data_fields_required') ?>: <?= implode(', ', $required) ?>
                            <div>Cập nhật dựa vào cột mã để cập nhật, các trường chưa thể cập nhật được(Mã , loại thành
                                phẩm, màu sắc)
                            </div>
                        </div>
                        <div class="text-danger mbot10">
                            <div>
                                - <?= lang('tnh_please_download_template_sample') ?>:
                                <a href="<?= base_url('file/Mẫu Thành Phẩm.xlsx?vs=1.3') ?>" title=""><?= lang('tnh_file_sample') ?></a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading"/>
                        <?php
                        $template_import = get_table_where('tbltemplate_import', ['type' => 'product']);
                        ?>
                        <div class="row hide" style="display: flex;align-items: center">
                            <div class="col-md-4">
                                <div class="form-group " app-field-wrapper="template_import">
                                    <label for="template_import"
                                           class="control-label"><?= _l('cong_template_import') ?></label>
                                    <select id="template_import" class="selectpicker" data-width="100%"
                                            data-none-selected-text="Không có mục nào được chọn" data-live-search="true"
                                            tabindex="-98">
                                        <option value=""></option>
                                        <?php foreach ($template_import as $keyTemplate => $valTemplate) { ?>
                                            <option value="<?= $valTemplate['id'] ?>"><?= _dt($valTemplate['date_create']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="saveImport" id="saveImport">
                                    <label for="saveImport"><?=_l('cong_save_template_import')?></label>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="mbot10 hide">
                            <button type="button"
                                    class="btn btn-primary btn-automatic"><?= lang('tnh_auto_data_fields') ?></button>
                            <button type="button"
                                    class="btn btn-warning btn-referesh"><?= lang('tnh_referesh') ?></button>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mbot10">
                                <?= lang('tnh_row_start', 'row_start') ?>
                                <input type="number" name="row_start" id="row_start" class="form-control" value="2"
                                       min="1">
                            </div>
                            <div class="col-md-2 mbot10">
                                <?= lang('tnh_row_end', 'row_end') ?>
                                <input type="number" name="row_end" id="row_end" class="form-control" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <div class="radio radio-info">
                                    <input type="radio" name="actions" id="actions_1" value="add" checked="checked">
                                    <label for="actions_1"><?= lang('add') ?></label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="radio radio-info">
                                    <input type="radio" name="actions" id="actions_2" value="updated">
                                    <label for="actions_2"><?= lang('update') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mbot10">
                                <div class="">
                                    <div class="input-group input-file" name="file">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-choose"
                                                    type="button"><?= lang('file') ?></button>
                                        </span>
                                        <input type="text" name="text_file" class="form-control"
                                               placeholder='<?= lang('choose') ?>'/>
                                        <span class="input-group-btn">
                                           <button class="btn btn-warning btn-reset"
                                                   type="button"><?= lang('reset') ?></button>
                                       </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mbot10">
                                <button type="submit" class="btn btn-success add" name="save"
                                        value="1"><?= lang('save') ?></button>
                            </div>
                        </div>
                        <div class="alert alert-danger alert-dismissible show-alert" style="display: none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close"
                               style="right: 0;">&times;</a>
                            <div class="show-errors">
                            </div>
                        </div>
                        <div class="">
                            <table class="tnh-tb table-hover table-bordered table-condensed table-import"
                                   style="width: 100%;">
                                <thead>
                                <tr>
                                    <th class="primary-table" style="width: 5%; text-align: center;">
                                        <button type="button" class="btn btn-danger btn-add"><i class="fa fa-plus"></i>
                                        </button>
                                    </th>
                                    <th class="primary-table" style="width: 20%;"><?= lang('tnh_data_fields') ?></th>
                                    <th class="primary-table" style="width: 20%;"><?= lang('tnh_column') ?></th>
                                    <th class="primary-table" style="width: 20%;"><?= lang('note') ?></th>
                                    <th class="primary-table" style="width: 20%;"><?= lang('tnh_options') ?></th>
                                    <th class="primary-table" style="width: 5%;"><?= lang('actions') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td class="" style="width: 5%; text-align: center;">
                                        <button type="button" class="btn btn-danger btn-add"><i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?php init_tail(); ?>
<?php $this->load->view('loader') ?>
<!-- plugin tnh -->
<!-- <link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>"> -->
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<!-- end -->
<script type="text/javascript">
    var cloumn_excel = <?= json_encode(cloumns_excel()) ?>;
    var fields = <?= json_encode($list) ?>;
    var list_auto_add = <?= json_encode($list_auto_add) ?>;
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var radio = 0;

    function totalStages() {
        var table = $('.table-import tbody tr').length;
        var stt = 0;
        for (ii = 0; ii < table; ii++) {
            stt++;
            element = $('.table-import tbody tr')[ii];
            $(element).find('.stt').html(stt);
        }
    }

    function addRow(selected_fields = 0, selected_cloumn = 0) {
        tr_html = '';
        tr_html += '<tr>';
        tr_html += '<td class="text-center stt"></td>';
        tr_html += '<td>' +
            '<select name="fields[]" id="fields" class="form-control fields" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>" required="required">' +
            optionFields(fields, selected_fields) +
            '</select>' +
            '</td>';
        tr_html += '<td>' +
            '<select name="cloumn_excel[]" id="cloumn_excel" class="form-control cloumn_excel" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>" required="required">' +
            optionCloumnExcel(cloumn_excel, selected_cloumn) +
            '</select>' +
            '</td>';
        tr_html += '<td class="td-note"></td>';
        tr_html += '<td class="td-options"></td>';
        tr_html += '<td class="text-center">' +
            '<button type="button" class="btn btn-warning btn-remove"><i class="fa fa-remove"></i></button>' +
            '</td>';
        tr_html += '</tr>';

        return tr_html;
    }

    function radioChosse(field,checked = '',checked1 = '') {
        radio_1 = radio++;
        radio_2 = radio++;
        radio_3 = radio++;
        radio_4 = radio++;
        return '<div class="">' +
            '<fieldset id="' + field + '_1">' +
            '<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox">' +
            '<input type="radio" class="rel_type" name="' + field + '_1" value="where" '+( checked == '' ? 'checked' : (checked == 'where' ? 'checked' : ''))+' id="radio_' + radio_1 + '">' +
            '<label for="radio_' + radio_1 + '"><?= lang('tnh_find_exact') ?></label>' +
            '</div>' +
            '<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox">' +
            '<input type="radio" class="rel_type" name="' + field + '_1" value="like" '+(checked == 'like' ? 'checked' : '')+' id="radio_' + radio_2 + '">' +
            '<label for="radio_' + radio_2 + '"><?= lang('tnh_find_approximate') ?></label>' +
            '</div>' +
            '</fieldset>' +
            '<fieldset id="' + field + '_2">' +
            '<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox">' +
            '<input type="radio" class="rel_type" name="' + field + '_2" value="add" '+(checked1 == '' ? 'checked' : '')+' id="radio_' + radio_3 + '">' +
            '<label for="radio_' + radio_3 + '"><?= lang('tnh_add_new') ?></label>' +
            '</div>' +
            '<div class="checkbox checkbox-info mbot20 no-mtop col-md-6 cbobox">' +
            '<input type="radio" class="rel_type" name="' + field + '_2" value="continue" '+( checked1 == 'add' ? 'checked' : (checked1 == 'continue' ? 'checked' : ''))+' id="radio_' + radio_4 + '">' +
            '<label for="radio_' + radio_4 + '"><?= lang('tnh_skip') ?></label>' +
            '</div>' +
            '</fieldset>' +
            '</div>';
    }

    function checkWarning(field, row, checked = '',checked1 = '') {
        row.find('.td-options').html('');
        if (field == "code") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_required_unique') ?>'));
        } else if (field == "name") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_required') ?>'));
        } else if (field == "type_products") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_type_products_required') ?>'));
        } else if (field == "bom_id") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_please_enter_code_category_bom') ?>'));
        } else if (field == "category_id") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_enter_code_required') ?>'));
            row.find('.td-options').html(radioChosse('category_id',checked, checked1));
        } else if (field == "unit_id") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_enter_code_required') ?>'));
            row.find('.td-options').html(radioChosse('unit_id',checked, checked1));
        } else if (field == "size") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_enter_name') ?>'));
            row.find('.td-options').html(radioChosse('size',checked, checked1));
        } else if (field == "exchange") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_import_exchange') ?>'));
            row.find('.td-options').html(radioChosse('exchange',checked, checked1));
        } else if (field == "species") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_enter_code') ?>'));
            row.find('.td-options').html(radioChosse('species',checked, checked1));
        } else if (field == "customer") {
            row.find('.td-note').html(textErrors('<?= lang('Mã khách hàng hoặc tên và phải có trong phần mềm') ?>'));
        } else if (field == "colors") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_import_colors') ?>'));
            row.find('.td-options').html(radioChosse('colors',checked, checked1));
        } else if (field == "type_print") {
            row.find('.td-note').html(textErrors('1: OS, 2: gia công, 3: BC, 4: HP, 5: Ko in, 6: FX, 7: 003Lua, 8: Ruban, 9: Barcode'));
        } else if (field == "columns_id") {
            row.find('.td-note').html(textErrors('Vui lòng nhập mã Columns cách nhau bởi dấu , (VD: 7H-From,7H-Atas/Sol)'));
        } else if (field == "conversion_unit") {
            row.find('.td-note').html(textErrors('<?= lang('tnh_enter_code_required') ?>'));
            row.find('.td-options').html(radioChosse('conversion_unit', checked, checked1));
        } else if (field == "id_branch") {
            row.find('.td-note').html(textErrors('Vui lòng nhập tên chi nhánh xưởng'));
        } else if (field == "brand_id") {
            row.find('.td-note').html(textErrors('Vui lòng nhập mã brand'));
            row.find('.td-options').html(radioChosse('brand_id',checked, checked1));
        } else if (field == "unit_measure") {
            row.find('.td-note').html(textErrors('Vui lòng nhập mã đơn vị đo'));
            row.find('.td-options').html(radioChosse('unit_measure',checked, checked1));
        }
        else if (field == "id_standard_carry") {
            row.find('.td-options').html(radioChosse('id_standard_carry',checked, checked1));
        }
        else if (field == "id_standard_sample_cover") {
            row.find('.td-options').html(radioChosse('id_standard_sample_cover',checked, checked1));
        }
        else if (field == "id_standard_smooth_shine") {
            row.find('.td-options').html(radioChosse('id_standard_smooth_shine',checked, checked1));
        }
        else if (field == "id_standard_fsc") {
            row.find('.td-options').html(radioChosse('id_standard_fsc',checked, checked1));
        }
        else if (field == "id_standard_delivery_package") {
            row.find('.td-options').html(radioChosse('id_standard_delivery_package',checked, checked1));
        }
        else if (field == "id_standard_membrane") {
            row.find('.td-options').html(radioChosse('id_standard_membrane',checked, checked1));
        }
        else if (field == "id_standard_template") {
            row.find('.td-options').html(radioChosse('id_standard_template',checked, checked1));
        }
        else if (field == "id_standard_condition_color") {
            row.find('.td-options').html(radioChosse('id_standard_condition_color',checked, checked1));
        }
        else if (field == "id_standard_color") {
            row.find('.td-options').html(radioChosse('id_standard_color',checked, checked1));
        }
        else if (field == "id_standard_bin_carton") {
            row.find('.td-options').html(radioChosse('id_standard_bin_carton',checked, checked1));
        }
        else if (field == "id_standard_trame") {
            row.find('.td-options').html(radioChosse('id_standard_trame',checked, checked1));
        }
        else if (field == "id_standard_sample_code") {
            row.find('.td-options').html(radioChosse('id_standard_sample_code',checked, checked1));
        }
        else if (field == "id_standard_methods") {
            row.find('.td-options').html(radioChosse('id_standard_methods',checked, checked1));
        }
        else if (field == "id_standard_quality_standards") {
            row.find('.td-options').html(radioChosse('id_standard_quality_standards',checked, checked1));
        }
        else {
            row.find('.td-note').html('');
        }
    }

    function autoFieldProducts() {
        $('.table-import tbody').html('');
        // button.button({loadingText: '<i class="fa fa-spinner fa-spin"></i> <?= lang('please_waiting') ?>'});
        // button.button('loading');
        var k = 0;
        $.each(list_auto_add, function (index, el) {
            cl = cloumn_excel[k];
            tr_html = addRow(index, cl);
            $('.table-import tbody').append(tr_html);
            row = $($('.table-import tbody tr')[k]);
            checkWarning(index, row);
            k++;
        });
        $('.cloumn_excel').selectpicker();
        $('.fields').selectpicker();

        if ($('input[name="actions"]:checked').val() == 'add') {
            $('.btn-add').css({'display': 'none', 'pointer-events': 'none'});
            $('.btn-remove').css({'display': 'none', 'pointer-events': 'none'});
            $('.table-import').css({'pointer-events': 'none'});
        } else {
            $('.btn-add').css({'display': '', 'pointer-events': ''});
            $('.btn-remove').css({'display': '', 'pointer-events': ''});
            $('.table-import').css({'pointer-events': ''});
        }
        // button.button('reset');
        totalStages();
    }

    $(document).ready(function () {
        bs_input_file();
        autoFieldProducts();
        $('.btn-add').click(function (event) {
            tr_html = addRow();

            $('.table-import tbody').append(tr_html);
            $('.cloumn_excel').selectpicker();
            $('.fields').selectpicker();
            totalStages();
        });

        $(document).on('change', 'input[name="actions"]', function(event) {
            if ($(this).prop('checked')) {
                if ($(this).val() == 'updated') {
                    $('.table-import tbody').html('');
                    $('.btn-add').css({'display': '', 'pointer-events': ''});
                    $('.btn-remove').css({'display': '', 'pointer-events': ''});
                    $('.table-import').css({'pointer-events': ''});
                    totalStages();
                } else {
                    autoFieldProducts();
                }
            }
        });

        $(document).on('click', '.btn-remove', function (event) {
            event.preventDefault();
            $(this).closest('tr').remove();
            totalStages();
        });

        $(document).on('changed.bs.select', 'select.fields', function (e, clickedIndex, isSelected, previousValue) {
            row = $(this).closest('tr');
            field = $(this).val();
            checkWarning(field, row);
            lastrow = $('.table-import tbody tr')[$('.table-import tbody tr').length - 1];
            if ($(lastrow).find('select.fields').val()) {
                $('.table-import thead tr th .btn-add').trigger('click');
            }
        });

        $(document).on('click', '.btn-automatic', function (event) {
            event.preventDefault();
            var button = $(this);
            var k = 0;
            bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_automatically_create_field') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        autoFieldProducts();
                        // $('.table-import tbody').html('');
                        // button.button({loadingText: '<i class="fa fa-spinner fa-spin"></i> <?= lang('please_waiting') ?>'});
                        // button.button('loading');
                        // $.each(fields, function (index, el) {
                        //     cl = cloumn_excel[k];
                        //     tr_html = addRow(index, cl);
                        //     $('.table-import tbody').append(tr_html);
                        //     row = $($('.table-import tbody tr')[k]);
                        //     checkWarning(index, row);
                        //     k++;
                        // });
                        // $('.cloumn_excel').selectpicker();
                        // $('.fields').selectpicker();
                        // button.button('reset');
                        // totalStages();
                    }
                }
            });
        });

        $(document).on('click', '.btn-referesh', function (event) {
            bootbox.confirm({
                message: '<?= lang('tnh_you_are_referesh') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $('.table-import tbody').html('');
                    }
                }
            });
        });
    });

    $(document).ready(function () {
        appValidateForm($('#add-product'),
            {
                text_file: {required: true, extension: "xlsx,xls"},
            },
            importExcel,
            {text_file: '<?= lang('tnh_please_choose_excel') ?>'}
        );

        function importExcel(form) {
            $('.add').attr('disabled', 'disabled');
            var form = $(form),
                formData = new FormData(),
                formParams = form.serializeArray();

            $.each(form.find('input[type="file"]'), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(formParams, function (i, val) {
                formData.append(val.name, val.value);
            });
            //
            var url = form.action;
            $.ajax({
                url: site.base_url + 'admin/products/import_products',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
                .done(function (data) {
                    $('.show-errors').html('');
                    $('.add').removeAttr('disabled', 'disabled');
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                    if (typeof data.errors != "undefined" && data.errors) {
                        $('.show-alert').show();
                        $('.show-errors').html(data.errors);
                    }
                })
                .fail(function () {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    });

    $('body').on('change', '#template_import', function(e){

        var template = $(this).val();
        var data = {id : template};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url+'import_excel/getTemplateImport', data, function(result){
            result = JSON.parse(result);
            if(result.success) {
                $('.table-import tbody').html('');
                tr_html = '';
                $.each(result.setup_colums, function(i, v){
                    tr_html = addRow(v.field,v.rowExcel);
                    $('.table-import tbody').append(tr_html);
                    row = $($('.table-import tbody tr')[i]);
                    checkWarning(v.field, row, v.type_data1, v.type_data2);
                })
                $('.cloumn_excel').selectpicker();
                $('.fields').selectpicker();

                if ($('input[name="actions"]:checked').val() == 'add') {
                    $('.btn-add').css({'display': 'none', 'pointer-events': 'none'});
                    $('.btn-remove').css({'display': 'none', 'pointer-events': 'none'});
                    $('.table-import').css({'pointer-events': 'none'});
                } else {
                    $('.btn-add').css({'display': '', 'pointer-events': ''});
                    $('.btn-remove').css({'display': '', 'pointer-events': ''});
                    $('.table-import').css({'pointer-events': ''});
                }
                

                totalStages();
            }
        })
    })

    function totalStagesNew() {
        $('select.fields').trigger('change');
    }

</script>

