<?php init_head(); ?>
<style type="text/css">
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), array('id' => 'compose-form', 'class' => '_transaction_form invoice-form'));
            ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="additional"></div>
                    <div class="panel-body">
                        <h4 class="bold no-margin font-medium">
                            <?php echo $title; ?>
                        </h4>
                        <hr />
                        <!-- <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="alert alert-warning text-center total_debt hide"></div>
                            <div class="panel panel-primary">
                                <div class="panel-heading"><?= _l('lead_general_info') ?></div>
                                <div class="panel-body">
                                    <table class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_name_compose'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 75%;">
                                                    <?php $value = (isset($main) ? ($main->name) : ''); ?>
                                                    <?php echo render_input('name', '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 10%;">
                                                    <label for="date" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('ch_date_p'); ?>
                                                    </label>
                                                </td>
                                                <td style="width: 30%;">
                                                    <?php $value = (isset($main) ? _d($main->date) : _d(date('Y-m-d'))); ?>
                                                    <?php echo render_date_input('date', '', $value); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="reason" class="control-label">
                                                        <?php echo _l('ch_note_t'); ?>
                                                    </label>
                                                </td>
                                                <td colspan="3">
                                                    <?php $value = (isset($main) ? $main->note : ""); ?>
                                                    <?php echo render_textarea('reason', '', $value, array('rows' => 2)); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="panel panel-danger">
                                <div class="panel-heading"><?= _l('From import') ?></div>
                                <div class="panel-body">
                                    <table style="width: 100%;float: right;table-layout: fixed;" class="tnh-tb table-bordered table-hover dont-responsive-table m-group0">
                                        <tbody>
                                            <tr>

                                                <td style="width: 50%">
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('File From MO'); ?>
                                                    </label>
                                                    <?php echo render_input('file_csv_mo', '', '', 'file'); ?>
                                                </td>
                                                <td>
                                                    <div class="col-md-6">
                                                        <?php echo render_input('row_start', 'cong_start_row', '58') ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php echo render_input('row_end', 'cong_end_row') ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 40%">
                                                    <label for="number" class="control-label">
                                                        <small class="req text-danger">* </small>
                                                        <?php echo _l('File From PO'); ?>
                                                    </label>
                                                    <?php echo render_input('file_csv_po', '', '', 'file'); ?>
                                                </td>
                                                <td style="width: 10%">
                                                    <a href="#" onclick="import_export_client(this);return false;" id="import_export_client" class="btn btn-warning btn-icon" style="float: right;"><?= _l('import FILE') ?></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mbot50">
                            <ul class="nav nav-tabs hide ch_tab" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#money_goods" class="money_goods" onclick="" aria-controls="money_goods" role="tab" data-toggle="tab">
                                        <?php echo _l('Mặt hàng'); ?>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#import_error" style="color: red;" class="count_error" onclick="" aria-controls="import_error" role="tab" data-toggle="tab">
                                        <?php echo _l('Import lỗi'); ?>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="money_goods">
                                    <div class="panel panel-info" style="min-height: auto;">
                                        <div class="panel-heading">
                                            <?= lang('tnh_info_items') ?>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive" style="max-height: 310px;">
                                                <table class="dt-tnh table item-inventory table-bordered table-hover mtop0 mbot0" style="width: 100%;table-layout: fixed;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 50px"></th>
                                                            <th style="width: 250px" class="text-center"><?php echo _l('PO'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('Style Number'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('COLOR NAME'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('PRIMARY SIZE'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('UPC/EAN CODE'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('SL'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('Trim card'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('Sample 1'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('Loss'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('1%'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('SL QC'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('Dán nhãn thực tế'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('QC Sample'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('TC'); ?></th>
                                                            <th style="width: 300px" class="text-center"><?php echo _l('LAYOUT NO.'); ?></th>
                                                            <th style="width: 150px" class="text-center"><?php echo _l('REMARK#'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 0;
                                                        $totalQuantity_approve = 0;
                                                        $totalQuantity = 0;
                                                        if (isset($main) && count($main->items) > 0) {
                                                            foreach ($main->items as $value) {
                                                        ?>
                                                                <tr class="sortable item">
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control code" name="items[<?php echo $i; ?>][code]" value="<?php echo $value['code']; ?>"><?php echo $value['code']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control style_number" name="items[<?php echo $i; ?>][style_number]" value="<?php echo $value['style_number']; ?>"><?php echo $value['style_number']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control color_name" name="items[<?php echo $i; ?>][color_name]" value="<?php echo $value['color_name']; ?>"><?php echo $value['color_name']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control primary_size" name="items[<?php echo $i; ?>][primary_size]" value="<?php echo $value['primary_size']; ?>"><?php echo $value['primary_size']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control upc" name="items[<?php echo $i; ?>][upc]" value="<?php echo $value['upc']; ?>"><?php echo $value['upc']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control quantity" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value['quantity']; ?>"><?php echo $value['quantity']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control trim_card" name="items[<?php echo $i; ?>][trim_card]" value="<?php echo $value['trim_card']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control Sample" name="items[<?php echo $i; ?>][Sample]" value="<?php echo $value['Sample']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input type="hidden" class="form-control loss" name="items[<?php echo $i; ?>][loss]" value="<?php echo $value['loss']; ?>"><?php echo $value['loss']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control one" name="items[<?php echo $i; ?>][one]" value="<?php echo $value['one']; ?>"><?php echo $value['one']; ?>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control slqc" name="items[<?php echo $i; ?>][slqc]" value="<?php echo $value['slqc']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control stickers" name="items[<?php echo $i; ?>][stickers]" value="<?php echo $value['stickers']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control qc_sample" name="items[<?php echo $i; ?>][qc_sample]" value="<?php echo $value['qc_sample']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control tc" name="items[<?php echo $i; ?>][tc]" value="<?php echo $value['tc']; ?>">
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <textarea style="width: 100%;" class="form-control layout_no" name="items[<?php echo $i; ?>][layoutno]" value="<?php echo $value['layoutno']; ?>" /><?php echo $value['layoutno']; ?></textarea>
                                                                    </td>
                                                                    <td style="text-align: center;">
                                                                        <input style="width: 100%;" class="form-control remark" name="items[<?php echo $i; ?>][remark]" value="<?php echo $value['remark']; ?>">
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td></td>
                                                            <td>Tổng</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style="text-align: center;" class="total_quan"></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
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
                                <div role="tabpanel" class="tab-pane" id="import_error">
                                    <div class="panel panel-info" style="min-height: auto; margin-bottom: 100px;">
                                        <div class="panel-heading">
                                            <?= lang('Các dòng bị lỗi') ?>
                                        </div>
                                        <div class="panel-body">
                                            <table class="dt-tnh table  table-bordered table-hover mtop0 mbot0" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 100px"><?php echo _l('import_count'); ?></th>
                                                        <th style="width: 300px" class="text-center"><?php echo _l('import_error'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="import_error">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <button type="submit" class="pull-right btn btn-info add-compose">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    function formatNumber(nStr, decSeperate = ".", groupSeperate = ",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        x2 = x2.substr(0, 2);
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    };

    function getTotalPrice() {
        var items = $('table.item-inventory tbody').find('tr.item');
        var totalQuantity = 0;
        $.each(items, (index, value) => {
            totalQuantity += parseFloat($(value).find('input.quantity').val());
        });
        $('.total_quan').text(formatNumber(Number(totalQuantity)));
    }

    function import_export_client() {

        var file_datas_mo = $('input#file_csv_mo').val();
        if (empty(file_datas_mo)) {
            alert('<?= _l('alert_file_mo') ?>');
            return;
        }
        var file_datas_po = $('input#file_csv_po').val();
        if (empty(file_datas_po)) {
            alert('<?= _l('alert_file_po') ?>');
            return;
        }
        if ($('table.item-inventory tbody').find('tr').length) {
            var r = confirm("<?php echo _l('ch_note_load'); ?>");
            if (r == false) {
                return false;
            }
        }
        var row_start = $('input#row_start').val();
        if (empty(row_start)) {
            alert('<?= _l('Bạn chưa nhập hàng bắt đầu') ?>');
            return;
        }
        var row_end = $('input#row_end').val();
        if (empty(row_end)) {
            alert('<?= _l('Bạn chưa nhập hàng kết thúc') ?>');
            return;
        }
        $('table.item-inventory tbody').html('');
        var file_csv_mo = $('input#file_csv_mo').prop('files')[0];
        var file_csv_po = $('input#file_csv_po').prop('files')[0];
        var button = $('#import_export_client');
        var form_data = new FormData();
        form_data.append('row_start', row_start);
        form_data.append('row_end', row_end);
        form_data.append('file_mo', file_csv_mo);
        form_data.append('file_csv_po', file_csv_po);
        form_data.append('csrf_token_name', csrfData.hash);
        $.ajax({
            url: "<?= admin_url() ?>compose/import_items/",
            type: 'POST',
            data: form_data,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,

        }).done(function(data) {
            data = JSON.parse(data);
            if (data.success) {
                $.each(data.list_data, (index, value) => {
                    createTrItemfist_load(value);
                });
                if (!empty(data.list_data_orr)) {
                    $('.ch_tab').removeClass('hide');
                    createTrItemfist_error(data.list_data_orr);
                    $('.count_error').html('<?= _l('Import lỗi') ?> (' + (data.list_data_orr).length + ')')
                } else {
                    $('.money_goods').click();
                }
                getTotalPrice()
                button.button('reset');
                // $('input#file_csv').val('');
            }
        }).fail(function() {
            alert_float('danger', 'err');
        }).always(function() {
            // $('input#file_csv').val('');

        });
        return false;
    }
    var uniqueArray = <?= $i ?>;

    function createTrItemfist_error(data) {
        $('.import_error').html('');
        var html = '';
        $.each(data, function(key, value) {
            html += '<tr>';
            html += '<td>Dòng ' + value.count + '</td>';
            html += '<td>' + value.title + '</td>';
            html += '/<tr>';
        });
        $('.import_error').html(html);
    }

    function createTrItemfist_load(data) {
        var newTr = $('<tr class="sortable item"></tr>');
        var td1 = $('<td class="text-center"><input class="hide code" name="items[' + uniqueArray + '][code]" value="' + data.code + '" />' + data.code + '</td>');
        var td2 = $('<td class="text-center"><input class="hide style_number" name="items[' + uniqueArray + '][style_number]" value="' + data.style_number + '" />' + data.style_number + '</td>');
        var td3 = $('<td class="text-center"><input class="hide color_name" name="items[' + uniqueArray + '][color_name]" value="' + data.color_name + '" />' + data.color_name + '</td>');
        var td4 = $('<td class="text-center"><input class="hide primary_size" name="items[' + uniqueArray + '][primary_size]" value="' + data.primary_size + '" />' + data.primary_size + '</td>');
        var td5 = $('<td class="text-center"><input class="hide upc" name="items[' + uniqueArray + '][upc]" value="' + data.upc + '" />' + data.upc + '</td>');
        var td6 = $('<td class="text-center"><input class="hide quantity" name="items[' + uniqueArray + '][quantity]" value="' + data.quantity + '" />' + formatNumber(data.quantity) + '</td>');
        var td7 = $('<td class="text-center"><input style="width: 100%;" class="form-control trim_card" name="items[' + uniqueArray + '][trim_card]" value="" /></td>');
        var td8 = $('<td class="text-center"><input style="width: 100%;" class="form-control Sample" name="items[' + uniqueArray + '][Sample]" value="" /></td>');
        var td9 = $('<td class="text-center"><input class="hide form-control  loss" name="items[' + uniqueArray + '][loss]" value="' + data.loss + '" />' + data.loss + '</td>');

        var td10 = $('<td class="text-center"><input style="width: 100%;" class="form-control one" name="items[' + uniqueArray + '][one]" value="" /></td>');
        var td11 = $('<td class="text-center"><input style="width: 100%;" class="form-control slqc" name="items[' + uniqueArray + '][slqc]" value="" /></td>');
        var td12 = $('<td class="text-center"><input style="width: 100%;" class="form-control stickers" name="items[' + uniqueArray + '][stickers]" value="" /></td>');
        var td13 = $('<td class="text-center"><input style="width: 100%;" class="form-control qc_sample" name="items[' + uniqueArray + '][qc_sample]" value="" /></td>');
        var td14 = $('<td class="text-center"><input style="width: 100%;" style="width: 100%;" class="form-control tc" name="items[' + uniqueArray + '][tc]" value="" /></td>');
        var td15 = $('<td class="text-center"><textarea style="width: 100%;" class="form-control layout_no" name="items[' + uniqueArray + '][layoutno]" value="" /></textarea></td>');
        var td16 = $('<td class="text-center"><input style="width: 100%;" class="form-control remark" name="items[' + uniqueArray + '][remark]" value="" /></td>');
        var td17 = '<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>'
        newTr.append(td17);
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        newTr.append(td10);
        newTr.append(td11);
        newTr.append(td12);
        newTr.append(td13);
        newTr.append(td14);
        newTr.append(td15);
        newTr.append(td16);
        $('table.item-inventory tbody').append(newTr);
        uniqueArray++;
    };

    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        $(trItem).parent().parent().remove();
        getTotalPrice()
    };
    //validation
    appValidateForm($('#compose-form'), {

    }, db);

    //save db
    function db(form) {
        $('.add-compose').attr('disabled', 'disabled');
        for (var i = 0; i < tinymce.editors.length; i++) {
            tinymce.editors[i].save();
        }
        var url = form.action;
        var form = $(form),
            formData = new FormData(),
            formParams = form.serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
                // url : site.base_url+'admin/business_plan/add',
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url + 'admin/compose';
                } else {
                    alert_float('danger', data.message);
                    $('.add-compose').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', lang_core['errors']);
                $('.add-compose').removeAttr('disabled', 'disabled');
            });
        return false;
    }
</script>