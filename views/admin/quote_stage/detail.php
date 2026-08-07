<?php init_head(); ?>
<style>
    .bg-sive {
        background: #a7a7a7;
    }

    .bg-sive td {
        padding-top: 1px !important;
    }

    #table-quote_stage_detail tr td {
        white-space: nowrap;
        vertical-align: bottom;
    }

    .width120 {
        width: 120px;
        text-align: right !important;
    }
    .width200 {
        width: 200px;
    }
    .width60 {
        width: 60px;
        /*text-align: right !important;*/
    }
    .select2-choice{
        height: 100%!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/quote_stage/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
					<?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
		<?php echo form_open($this->uri->uri_string(), array('id' => 'quote_stage-form', 'class' => '_transaction_form orders-form')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Thông tin</h3>
                            </div>
                            <div class="panel-body">
                                <table class="tnh-tb table-bordered table-hover dont-responsive-table">
                                    <tbody>
                                    <tr>
                                        <td style="width: 20%;">
                                            <label for="code">Mã bảng giá</label>
                                        </td>
                                        <td style="width: 30%;">
											<?php $value = !empty($quote_stage->code) ? $quote_stage->code : '' ?>
											<?php echo render_input('code', '', $value) ?>
                                        </td>
                                        <td style="width: 20%;">
                                            <label for="name">Tên bảng giá</label>
                                        </td>
                                        <td style="width: 30%;">
											<?php $value = !empty($quote_stage->name) ? $quote_stage->name : '' ?>
											<?php echo render_input('name', '', $value) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= lang('Cost of Brand') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="cost_of_brand" id="cost_of_brand" class="form-control cost_of_brand number-format" value="<?= !empty($quote_stage->cost_of_brand) ? $quote_stage->cost_of_brand : '' ?>">
                                        </td>
                                        <td>
                                            <?= lang('Labor cost + Management Cost') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="labor_cost" id="labor_cost" class="form-control labor_cost number-format" value="<?= !empty($quote_stage->labor_cost) ? $quote_stage->labor_cost : '' ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= lang('Loss Cost') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="loss_cost" id="loss_cost" class="form-control labor_cost number-format" value="<?= !empty($quote_stage->loss_cost) ? $quote_stage->loss_cost : '' ?>">
                                        </td>
                                        <td>
                                            <?= lang('Profit') ?>
                                        </td>
                                        <td>
                                            <input type="text" name="profit" id="profit" class="form-control profit number-format" value="<?= !empty($quote_stage->profit) ? $quote_stage->profit : '' ?>">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="div_table">
                            <div class="clearfix"></div>
                            <table class="table dont-responsive-table mbot40" id="table-quote_stage_detail" style="margin-bottom: 50px;">
                                <thead>
                                <tr>
                                    <th class="width60 text-center"><a class="btn btn-icon btn-info" onclick="createItems()"><i class="fa fa-plus"></i></a></th>
                                    <th class="text-center">Công đoạn</th>
                                    <th class="text-center width200">Đơn vị tính</th>
                                    <th class="text-center width200">Height</th>
                                    <th class="text-center width200">Width</th>
                                    <th class="text-center width200">Giá</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php if (!empty($quote_stage->items)) {
									foreach ($quote_stage->items as $key => $value) { ?>
                                        <tr>
                                            <td class="stt width60 text-center"><?=($key + 1)?></td>
                                            <td>
                                                <input name="items[<?=$key?>][id]" type="hidden" style="width: 100%;" value="<?=!empty($value['id']) ? $value['id'] : ''?>">
                                                <input name="items[<?=$key?>][id_stage]" type="hidden" style="width: 100%;" value="<?=$value['id_stage']?>">
                                                <b><?=$value['name_stages']?> (<?=$value['code_stages']?>)</b>
                                            </td>
                                            <td><input name="items[<?=$key?>][unit_id]" data-json="<?=htmlentities(json_encode(['id' => $value['unit_id'], 'text' => $value['unit']]))?>" class="height H_input unit_id" value="<?=$value['unit_id']?>"></td>
                                            <td><input name="items[<?=$key?>][height]" class="height width200 H_input money-format" value="<?=number_format_data($value['height'])?>"></td>
                                            <td><input name="items[<?=$key?>][width]" class="width width200 H_input money-format" value="<?=number_format_data($value['width'])?>"></td>
                                            <td><input name="items[<?=$key?>][price]" class="price width200 H_input money-format" value="<?=number_format_data($value['price'])?>"></td>
                                            <td class="width60 text-center"><a class="btn btn-icon btn-danger RemoveTr"><i class="fa fa-remove"></i></a></td>
                                        </tr>
									<?php }
								} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                <button class="btn btn-info only-save customer-form-submiter">
					<?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
		<?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    appValidateForm($('#quote_stage-form'), {
        code: 'required',
        name: 'required'
    }, manageStage);
    function manageStage(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
        }).done(function (data) {
            alert_float(data.alert_type, data.message);
            if (data.success) {
                setTimeout(function() {
                    window.location.href = admin_url + 'quote_stage';
                }, 1500)

            }
        })
        .fail(function (err) {
            alert_float('danger', err.responseText);
        });
        return false;
    }

    $(function() {
        var select_stage = $('input.select_stage');
        $.each(select_stage, function(index, value) {
            ajaxSelectParamsCode($(value), 'admin/quote_stage/searchStage', $(value).val(), true, true);
        })

        var select_unit = $('input.unit_id');
        $.each(select_unit, function(index, value) {
            ajaxSelectParamsC($(value), 'admin/quote_stage/searchUnit', $(value).val(), $(value).attr('data-json'), true, true);
        })
    })

    var countItems = <?=!empty($quote_stage->items) ? count($quote_stage->items) : 0?>;

    <?php if(empty($quote_stage->items)) {?>
        createItems();
    <?php } ?>

    function createItems(Items = {}) {
        var Tr = $(`<tr></tr>`);
        var tdSTT = $(`<td class="stt width60 text-center"></td>`);
        var tdStage = $(`<td></td>`);
        var input_stage = $(`<input name="items[${countItems}][id_stage]" class="select_stage" style="width: 100%;" value="${Items.id_stage ? Items.id_stage : ''}"/>`);
        tdStage.append(input_stage);

        var tdUnit = $(`<td></td>`);
        var input_unit = $(`<input name="items[${countItems}][unit_id]" class="unit_id" style="width: 100%;" value="${Items.unit_id ? Items.unit_id : ''}"/>`);
        tdUnit.append(input_unit);

        var tdHeight = $(`<td></td>`);
        var input_height = $(`<input name="items[${countItems}][height]" class="height width200 H_input money-format" value="${Items.height ? tnhFormatNumber(Items.height) : ''}"/>`);
        tdHeight.append(input_height);

        var tdWidth = $(`<td></td>`);
        var input_width = $(`<input name="items[${countItems}][width]" class="width width200 H_input money-format" value="${Items.width ? tnhFormatNumber(Items.width) : ''}"/>`);
        tdWidth.append(input_width);

        var tdPrice = $(`<td></td>`);
        var input_price = $(`<input name="items[${countItems}][price]" class="price width200 H_input money-format" value="${Items.price ? tnhFormatNumber(Items.price) : ''}"/>`);
        tdPrice.append(input_price);

        var tdRemove = $(`<td class="width60 text-center"><a class="btn btn-icon btn-danger RemoveTr"><i class="fa fa-remove"></i></a></td>`);


        Tr.append(tdSTT);
        Tr.append(tdStage);
        Tr.append(tdUnit);
        Tr.append(tdHeight);
        Tr.append(tdWidth);
        Tr.append(tdPrice);
        Tr.append(tdRemove);
        $('#table-quote_stage_detail').find('tbody').append(Tr);
        SttOrders();

        if (Items.id_stage > 0) {
            var jsonStages = {'id': Items.id_stage, 'text': Items.stage_code, 'name': Items.stage_name};
            var jsonUnits = {'id': Items.unit_id, 'text': Items.unit};

            ajaxSelectParamsCode(`input[name="items[${countItems}][id_stage]"]`, 'admin/quote_stage/searchStage', $(`input[name="items[${countItems}][id_stage]"]`).val(), true, true, jsonStages);
            ajaxSelectParamsCallback(`input[name="items[${countItems}][unit_id]"]`, 'admin/quote_stage/searchUnit', $(`input[name="items[${countItems}][unit_id]"]`).val(), true, true, jsonUnits);
        } else {
            ajaxSelectParamsCode(`input[name="items[${countItems}][id_stage]"]`, 'admin/quote_stage/searchStage', $(`input[name="items[${countItems}][id_stage]"]`).val(), true, true);
            ajaxSelectParams(`input[name="items[${countItems}][unit_id]"]`, 'admin/quote_stage/searchUnit', $(`input[name="items[${countItems}][unit_id]"]`).val(), true, true);
        }
        // ajaxSelectParams(`input[name="items[${countItems}][unit_id]"]`, 'admin/quote_stage/searchUnit', $(`input[name="items[${countItems}][unit_id]"]`).val(), true, true);

        countItems++;
    }

    $('body').on('click', '.RemoveTr', function () {
        $(this).parents('tr').remove();
    })

    $('body').on('change', '.select_stage', function () {
        createItems();
    })
    function SttOrders() {
        var list_tr = $('#table-quote_stage_detail').find('tbody').find('tr');
        $.each(list_tr, function (index, value) {
            $(value).find('td.stt').text(index + 1);
        })
    }


    function ajaxSelectParamsC(element, url, id, data_json = false, params = false, clearSl2 = false)
    {
        if (id)
        {
            if(data_json) {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: clearSl2,
                    initSelection: function (element, callback) {
                        data_json = JSON.parse(data_json);
                        if(data_json) {
                            callback(data_json);
                        }
                        else {
                            $.ajax({
                                type: "get", async: false,
                                url: site.base_url + url + '/' + $(element).val(),
                                dataType: "json",
                                success: function (data) {
                                    callback(data.row);
                                }
                            });
                        }

                    },
                    ajax: {
                        url: site.base_url + url,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function (term, page) {
                            return {
                                params: params,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function (data, page) {
                            if (data.results != null) {
                                return {results: data.results};
                            } else {
                                return {results: [{id: '', text: 'No Match Found'}]};
                            }
                        }
                    }
                });
            }
            else {
                $(element).val(id).select2({
                    // minimumInputLength: 1,
                    width: 'resolve',
                    allowClear: clearSl2,
                    initSelection: function (element, callback) {
                        $.ajax({
                            type: "get", async: false,
                            url: site.base_url + url + '/' + $(element).val(),
                            dataType: "json",
                            success: function (data) {
                                callback(data.row);
                                if (data.row) {
                                    if (data.row.id === 0) {
                                        $(element).val(0);
                                    }
                                }
                            }
                        });
                    },
                    ajax: {
                        url: site.base_url + url,
                        dataType: 'json',
                        quietMillis: 15,
                        data: function (term, page) {
                            return {
                                params: params,
                                term: term,
                                limit: 50
                            };
                        },
                        results: function (data, page) {
                            if (data.results != null) {
                                return {results: data.results};
                            } else {
                                return {results: [{id: '', text: 'No Match Found'}]};
                            }
                        }
                    }
                });
            }
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                }
            });
        }
    }

    function ajaxSelectParamsCode(element, url, id, params = false, clearSl2 = false, txtJson = false)
    {
        console.log(clearSl2);
        if (id)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                initSelection: function (element, callback) {
                    if (txtJson) {
                        callback(txtJson);
                    } else {
                        $.ajax({
                            type: "get", async: false,
                            url: site.base_url + url + '/' + $(element).val(),
                            dataType: "json",
                            success: function (data) {
                                callback(data.row);
                                if (data.row) {
                                    if (data.row.id === 0) {
                                        $(element).val(0);
                                    }
                                }
                            }
                        });
                    }

                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: formatSelectCode,
                formatSelection: formatSelectCode,
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                allowClear: clearSl2,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
                        }
                    }
                },
                formatResult: formatSelectCode,
                formatSelection: formatSelectCode,
            });
        }
    }

    function formatSelectCode(result)
    {
        if (!result.id) return result.text; // optgroup
        tr = '';
        if (result) {
            tr+= '<div><b>Mã:</b> ' + result.text + '</div>';
            tr+= '<div><b>Tên:</b> '+ result.name + '</div>';
        }
        return tr;
    }
</script>
