<?php echo form_open('admin/outsource/import_outsource/'.$id, array('id'=>'add-outsource')); ?>
<div class="modal-dialog modal-lg" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= _l('tnh_import_outsource'); ?> (<?= $outsource['reference_no'] ?>)</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('date', 'date') ?>
                        <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : date('d/m/Y h:i:s')), 'placeholder="'.lang('date').'" id="date" required class="form-control input-tip datepicker"'); ?>
                    </div>
                </div>
                <div class="col-md-4 hide">
                    <div class="form-group">
                        <?= lang('tnh_warehouse_from', 'warehouse_from') ?>
                        <select name="warehouse_from" id="warehouse_from"
                            data-placeholder="<?= lang('tnh_warehouse_from') ?>" class="modal-select2"
                            style="width: 100%;" readonly>
                            <option value=""></option>
                            <?php foreach ($warehouses as $key => $value): ?>
                            <option <?= $transfer['warehouse_to'] == $value['id'] ? 'selected' : '' ?>
                                value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('Kho đến', 'warehouse_to') ?>
                        <div class="form-group">
                            <select name="warehouse_to" id="warehouse_to"
                                data-placeholder="<?= lang('tnh_warehouse_to') ?>" class="modal-select2"
                                style="width: 100%;" required="required">
                                <option value=""></option>
                                <?php foreach ($warehouses as $key => $value): ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <div class="error_warehouse text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?= lang('note', 'note') ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ''), 'placeholder="'.lang('note').'" id="note" class="form-control input-tip" style="height: 50px;"'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 hide">
                    <div class="form-group">
                        <?= lang('tnh_enter_name', 'enter_name') ?>
                        <input type="text" name="enter_name" id="enter_name" class="form-control"
                            placeholder="<?= lang('tnh_enter_name') ?>" value="">
                    </div>
                </div>
                <div class="col-md-4 hide">
                    <div class="form-group">
                        <?= lang('staff_admin', 'staff_admin') ?>
                        <select name="staff_admin" id="staff_admin" data-placeholder="<?= lang('staff_admin') ?>"
                            style="width: 100%;" class="modal-select2">
                            <option value=""></option>
                            <?php foreach ($staff as $key => $value): ?>
                            <option value="<?= $value['staffid'] ?>"><?= $value['firstname'] ?>
                                <?= $value['lastname'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mbot10">
                <div class="col-md-8">
                    <?= lang('tnh_items', 'tnh_items') ?>
                    <!-- <select name="" id="items" class="modal-select2" style="width: 100%;"
                        data-placeholder="<?= lang('chosen') ?>">
                        <option value=""></option>
                        <?php foreach ($items as $key => $value): ?>
                        <option  value="<?= $value['id'] ?>">
                            <?= $value['item_code'] ?>(<?= $value['item_name'] ?>)
                        </option>
                        <?php endforeach ?>
                    </select> -->
                    <input class="items modal-select2" type="text" name="items" id="items"
                        data-placeholder="<?= lang('chosen') ?>" style="width: 100%;">
                </div>
                <div class="col-md-4">
                    <button type="button" style="margin-top: 25px;"
                        class="btn btn-success ev-all"><?= lang('tnh_check_all') ?></button>
                    <button type="button" style="margin-top: 25px;" onclick="refershTable()"
                        class="btn btn-danger ev-referesh"><?= lang('tnh_referesh') ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="marzen" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>

                        <div class="tab-panels">
                            <section id="marzen" class="tab-panel">
                                <div class="table-responsive">
                                    <table id="tb-import-outsource"
                                        class="dt-tnh tnh-table table-bordered table-hover dont-responsive-table"
                                        style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 40px;" class="text-center"><?= lang('tnh_numbers') ?>
                                                </th>
                                                <th style="width: 130px;"><?= lang('Tên/Mã mặt hàng') ?></th>
                                                <!-- <th style="width: 100px;"><?= lang('tnh_item_name') ?></th> -->
                                                <!-- <th style="width: 100px;"><span class="red">*</span>-->
                                                <?//= lang('tnh_location_from') ?>
                                                <!--</th>-->
                                                <!-- <th style="width: 100px;"><?= lang('tnh_location_to') ?></th> -->
                                                <th style="width: 100px;"><?= lang('Số lượng chi tiết') ?></th>
                                                <!-- <th style="width: 100px;">
                                                    <?= lang('tnh_quantity_had_import_outsource') ?></th> -->
                                                <th style="width: 100px;"><?= lang('tnh_quantity_import') ?></th>
                                                <!-- <th style="width: 100px;"><?= lang('price') ?></th> -->
                                                <!-- <th style="width: 100px;"><?= lang('tnh_subtotal') ?></th> -->
                                                <th style="width: 100px;"><?= lang('note') ?></th>
                                                <th style="width: 50px;"><?= lang('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bold uppercase">
                                                <th colspan="2" class="text-center"><?= lang('tnh_grand_total') ?></th>
                                                <th></th>
                                                <!-- <th></th> -->
                                                <th class="th-total-quantity text-center"></th>
                                                <!-- <th></th>
                                                <th class="th-grand-total text-right"></th> -->
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="save" id="save" class="form-control" value="1">
                <button type="submit" class="btn btn-primary add"><?= _l('save') ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close') ?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
    <script type="text/javascript">
    counter = 0;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var count_errors = 0;
    var outsource_id = '<?= $id ?>';
    var locationFrom = <?= !empty($locationWarehouseFrom) ? json_encode($locationWarehouseFrom) : '{}' ?>;
    var locationTo = false;
    </script>
    <script>
    function getLocations(listLocation, selected_id = 0) {
        var option = '<option value="0"></option>';
        $.each(listLocation, function(index, el) {
            selected = selected_id == el.pod_id ? 'selected' : '';
            // selected = (index == 0) ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function totalImportOutsource() {
        tb = '#tb-import-outsource tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        var totalQuantity = 0;
        var grandTotal = 0;
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.td-number').html(stt);
            quantity_outsource = intVal($(element).find('.quantity_outsource').val());
            quantity = intVal($(element).find('.td-quantity').html());
            quantity_had_outsource = intVal($(element).find('.td-quantity-had-outsource').html());
            quantity_max = quantity - quantity_had_outsource;
            price_outsource = intVal($(element).find('.price').val());
            amount_outsource = price_outsource * quantity_outsource;

            $(element).find('.td-amount').html(tnhFormatMoney(amount_outsource));
            if (quantity_outsource > quantity_max) {
                $(element).find('.show-error-item').html('<?= lang('tnh_quantity_import_less') ?>' + ' ' +
                    quantity_max);
                count_errors++;
            } else {
                $(element).find('.show-error-item').html('');
            }
            totalQuantity += quantity_outsource;
            grandTotal += amount_outsource;
        }
        if (n > 0) {
            $('#warehouse_to').select2('readonly', true);
        } else {
            $('#warehouse_to').select2('readonly', false);
        }
        $('.th-total-quantity').html(tnhFormatNumber(totalQuantity));
        $('.th-grand-total').html(tnhFormatMoney(grandTotal));
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        totalImportOutsource();
    }

    function refershTable() {
        bootbox.confirm({
            message: lang_core['tnh_you_are_referesh'],
            buttons: {
                confirm: {
                    label: lang_core['yes'],
                    className: 'btn-success'
                },
                cancel: {
                    label: lang_core['no'],
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result) {
                    $('#tb-import-outsource tbody').html('');
                    totalImportOutsource();
                }
            }
        });
    }

    function createdRowOutsourceItem(outsource_item, counter) {
        warehouse_to = $('#warehouse_to').val();
        $(".error_warehouse").html('');
        if (!warehouse_to) {
            $(".error_warehouse").html('Chọn kho');
            // bootbox.alert('<?= lang('tnh_please_chonse_warehouse_to') ?>');
            $('#items').val('').trigger('change');
            return;
        }

        trItem = '';
        if (outsource_item) {
            tdNumber = '<td class="text-center td-number"></td>';
            tdCode = '<td class="td-code">' +
                '<input type="hidden" name="counter[]" id="counter[' + counter +
                ']" class="form-control outsource_item_id" value="' + counter + '">' +
                '<input type="hidden" name="outsource_item_id[' + counter + ']" id="outsource_item_id[' + counter +
                ']" class="form-control outsource_item_id" value="' + outsource_item.id + '">' +
                outsource_item.item_name + '(' + outsource_item.item_code + ')' +
                '<div style="color:red">' + outsource_item.title + ': ' + outsource_item.reference_no + '</div>' +
                '</td>';
            tdName = '<td class="td-name">' + outsource_item.item_name + '</td>';

            tdLocationFrom = '<td class="td-location-from">' +
                '<select name="location_from[' + counter +
                ']" id="location_from" style="width: 100%;" class="location_from">' + getLocations(locationFrom) +
                '</select>' +
                '</td>';

            tdLocationTo = '<td class="td-location-to">' +
                '<select name="location_to[' + counter +
                ']" id="location_to" style="width: 100%;height:35px" class="location_to none-event">' + getLocations(
                    locationTo,
                    outsource_item.pod_id) +
                '</select>' +
                '</td>';

            tdQuantity =
                '<td class="text-left"><div style="text-transform: uppercase;"> Số lượng gia công: <span class="td-quantity bold">' +
                tnhFormatNumber(
                    outsource_item
                    .quantity) +
                '</span></div><div style="text-transform: uppercase;"> Số lượng đã nhập: <span class="td-quantity-had-outsource bold">' +
                tnhFormatNumber(
                    outsource_item.qty_ip_outsource) + '</span></div></td>';

            tdQuantityHadIpOutsource = '<td class="td-quantity-had-outsource text-center">' + tnhFormatNumber(
                outsource_item.qty_ip_outsource) + '</td>';
            quantityOutsource = intVal(outsource_item.quantity) - intVal(outsource_item.qty_ip_outsource);
            if (quantityOutsource < 0) quantityOutsource = 0;
            priceOutsource = outsource_item.price;
            amountOutsource = priceOutsource * quantityOutsource;

            tdQuantityIpOutsource =
                '<td class="td-quantity-outsource"><input style="width:100%" type="text" name="quantity_outsource[' +
                counter +
                ']" id="quantity_outsource[]" onchange="totalImportOutsource()" class="form-control quantity_outsource number-format" value="' +
                tnhFormatNumber(quantityOutsource) + '"><div class="show-error-item text-danger"></div></td>';
            tdPrice = '<td class="td-price">' +
                '<input type="text" name="price[' + counter +
                ']" onchange="totalImportOutsource()" id="price" class="form-control price money-format" value="' +
                tnhFormatMoney(priceOutsource) + '">' +
                '</td>'
            tdAmount = '<td class="td-amount text-right">' + tnhFormatMoney(amountOutsource) + '</td>'
            tdNote = '<td class="td-note">' +
                '<textarea name="note_item[' + counter +
                ']" id="note_item[]" class="form-control" rows="3"></textarea>' +
                '</td>';
            tdActions =
                '<td class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></i></td>';

            trItem = '<tr data-outsource-item-id="' + outsource_item.id + '">' +
                tdNumber +
                tdCode +
                // tdName +
                // tdLocationFrom+
                // tdLocationTo +
                tdQuantity +
                // tdQuantityHadIpOutsource +
                tdQuantityIpOutsource +
                // tdPrice +
                // tdAmount +
                tdNote +
                tdActions +
                '</tr>';
        }
        return trItem;
    }

    $(function() {
        // $('#items').select2();
        ajaxSelectParamsCallbackProduction('#items', 'admin/outsource/searchImportOutsouce', 0, {
            'outsource_id': <?= $id ?>
        });
        init_datepicker();
        $('#staff_admin').select2();
        $('#warehouse_from').select2();
        $('#warehouse_from').select2('readonly', true);
        $('#warehouse_to').select2();

        $('#warehouse_to').change(function(event) {
            warehouse_to = $(this).val();
            $.ajax({
                    url: site.base_url + 'admin/outsource/getLocationWarehouses',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        warehouse: warehouse_to,
                        csrf_token_name: hash,
                    },
                })
                .done(function(data) {
                    locationTo = false;
                    if (data) {
                        locationTo = data.location;
                    }
                })
                .fail(function() {
                    console.log("error");
                });
        });

        $('#items').change(function(event) {
            outsource_item_id = $(this).val();
            $(this).select2("val", "");
            if (outsource_item_id) {
                $.ajax({
                        url: site.base_url + 'admin/outsource/getOutsourceItems',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            outsource_item_id: outsource_item_id,
                            csrf_token_name: hash,
                        },
                    })
                    .done(function(data) {
                        if (data) {
                            elTr = $('tr[data-outsource-item-id="' + outsource_item_id + '"]');
                            if (elTr.length > 0) {
                                quantity_outsource_current = intVal(elTr.find('.quantity_outsource')
                                    .val()) + 1;
                                elTr.find('.quantity_outsource').val(tnhFormatNumber(
                                    quantity_outsource_current));
                            } else {
                                trItem = createdRowOutsourceItem(data.outsource_item, counter);
                                $('#tb-import-outsource').append(trItem);
                                $('select.location_from').select2();
                                $('select.location_to').select2();
                                counter++;
                            }
                            totalImportOutsource();
                        }
                    })
                    .fail(function() {
                        console.log("error");
                    });
            }
            $('#items').val('');
        });

        $('.ev-all').click(function(event) {
            if (outsource_id) {
                $.ajax({
                        url: site.base_url + 'admin/outsource/getOutsourceItems',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            outsource_id: outsource_id,
                            csrf_token_name: hash,
                        },
                    })
                    .done(function(data) {
                        if (data) {
                            htmlItem = '';
                            $.each(data.items, function(index, el) {
                                trItem = createdRowOutsourceItem(el, counter);
                                if (trItem != undefined) {
                                    htmlItem += trItem;
                                    counter++;
                                }
                            });

                            if (htmlItem != '') {
                                $('#tb-import-outsource tbody').html(htmlItem);
                                $('select.location_from').select2();
                                $('select.location_to').select2();
                                totalImportOutsource();
                            }
                        }
                    })
                    .fail(function() {
                        console.log("error");
                    });
            }
        });

        appValidateForm($('#add-outsource'), {
            'date': 'required',
            'warehouse_to': 'required',
        }, convert);

        function convert(form) {
            if (count_errors > 0) {
                alert_float('danger', '<?= lang('tnh_check_quantity_outsource') ?>');
                return;
            }
            $('.add').attr('disabled', 'disabled');
            var url = form.action;
            // var data = $(form).serialize();
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
                        if (typeof oTable != 'undefined' && oTable != '') {
                            oTable.draw();
                        }
                        $('.modal-dialog .close').trigger('click');
                    } else {
                        alert_float('danger', data.message);
                        $('.add').removeAttr('disabled', 'disabled');
                    }
                })
                .fail(function() {
                    alert_float('danger', 'error');
                    $('.add').removeAttr('disabled', 'disabled');
                });
            return false;
        }
    })

    function repoFormatSelectionProduction(state) {
        if (!state.id) return state.text;
        if (state.img) {
            var img = '<img class="img_option" src="' + site.base_url + state.img + '"/> ';
        } else {
            var img = '<img class="img_option" src="' + site.base_url + 'download/preview_image"/> ';
        }
        if (state.color == null) {
            state.color = '';
        }
        if (!state.reference_no) {
            state.reference_no = '';
        }
        if (state.type == 'nvl') {
            var tr = '' +
                '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
                '<div>Loại: ' + state.new_type + ' - Quy cách: ' + state.specification + '</div>' +
                '<div>Khổ: ' + state.suffering + ' - Màu sắc: ' + state.color + '</div>' +
                '';
            tableSelect = tr;
            return tableSelect;
        } else {
            title = '';
            reference_no = '';
            if (state.object_type == "business_plan") {
                title = ' KHKD';
                reference_no = state.reference_no_plan;
            } else if (state.object_type == "orders") {
                title = 'Đơn hàng';
                reference_no = state.reference_no;
            }
            var tr = '' +
                '<div class="bold" style="font-size: 14px;">' + state.item_name + '( ' + state.item_code + ' ) </div>' +
                '<div>' + title + ': ' + reference_no +
                '</div>' +
                '';
            tableSelect = tr;
            return tableSelect;
        }
    }

    function ajaxSelectParamsCallbackProduction(
        element,
        url,
        id,
        params = false,
        clearSl2 = false
    ) {
        if (id != 0) {
            $(element)
                .val(id)
                .select2({
                    // minimumInputLength: 1,
                    width: "resolve",
                    allowClear: clearSl2,
                    initSelection: function(element, callback) {
                        $.ajax({
                            type: "get",
                            async: false,
                            url: site.base_url + url + "/" + $(element).val(),
                            dataType: "json",
                            success: function(data) {
                                callback(data.row);
                            },
                        });
                    },
                    ajax: {
                        url: site.base_url + url,
                        dataType: "json",
                        quietMillis: 15,
                        data: function(term, page) {
                            return {
                                customer_id: $('#customer_id').val(),
                                id_branch: $("#id_branch").val(),
                                params: params,
                                term: term,
                                limit: 50,
                            };
                        },
                        results: function(data, page) {
                            if (data.results != null) {
                                return {
                                    results: data.results
                                };
                            } else {
                                return {
                                    results: [{
                                        id: "",
                                        text: "No Match Found"
                                    }],
                                };
                            }
                        },
                    },
                    formatResult: repoFormatSelectionProduction,
                });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: "resolve",
                allowClear: clearSl2,
                ajax: {
                    url: site.base_url + url,
                    dataType: "json",
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            customer_id: $('#customer_id').val(),
                            id_branch: $("#id_branch").val(),
                            params: params,
                            term: term,
                            limit: 50,
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return {
                                results: data.results
                            };
                        } else {
                            return {
                                results: [{
                                    id: "",
                                    text: "No Match Found"
                                }],
                            };
                        }
                    },
                },
                formatResult: repoFormatSelectionProduction,
            });
        }
    }
    </script>