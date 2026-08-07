
function totalProductionsOrders() {
    tb = '#tb-productions-orders tbody tr:not("[not-tr^=true]")';
    var table = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    count_errors = 0;
    var flagChonsePL = 0;
    for (ii = 0; ii < table; ii++) {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);

        // quantity = intVal($(element).find('.quantity').val());
        tempCounter = $(element).find('.counter').val();
        totalBOM = 0;
        tb_counter = $('.tb_counter_'+tempCounter+' tbody tr');
        if (tb_counter.length > 0) {
            $.each(tb_counter, function (i, v) { 
                quantity_bom = intVal($(v).find('.quantity_bom').val());
                quantity_multiples = intVal($(v).find('.quantity_multiples').val());
                quantity_cal = quantity_bom * quantity_multiples;
                totalBOM+= quantity_cal;
            });
        }

        $(element).find('.quantity').val(tnhFormatNumber(totalBOM));

        // if (quantity != totalBOM) {
        //     $(element).find('.show-errors').html('Vui lòng kiểm tra số lượng');
        // } else {
        //     $(element).find('.show-errors').html('');
        // }
    }

    disibleLSX(table);
}

function disibleLSX(table) {
    if (table > 0) {
        $('select#id_production_detail').closest('div').css({"cursor": "not-allowed", "pointer-events": "none"});
    } else {
        $('select#id_production_detail').closest('div').css({"cursor": "", "pointer-events": ""});
    }
}

function getUnits(units, select = '') {
    options = '<option value=""></option>';
    $.each(units, function (index, el) {
        selected = select == el.unitid ? 'selected' : '';
        options += '<option ' + selected + ' value="' + el.unitid + '">' + el.unit + '</option>';
    });
    return options;
}

function addRowShipping(counter, _this) {
    var div = $(_this).closest('.td-date');

    html = '<div class="row">' +
        '<div class="col-md-7" style="padding: 0px;"><input type="text" name="date_sub[' + counter + '][]" id="input" class="form-control datepicker date_sub" placeholder="' + lang_core['date'] + '" value="" style="width: 100%;" title=""></div>' +
        '<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub[' + counter + '][]" id="input" class="form-control quantity_sub" value="0" title=""></div>' +
        '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>' +
        '</div>';
    div.find('.sub').append(html);
    totalProductionsOrders();
    init_datepicker();
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
        callback: function (result) {
            if (result) {
                dt.rows().remove().draw();
            }
        }
    });
}

function totalBom(c_temp_counter) {
    tbBom = '.tb_counter_' + c_temp_counter + ' tbody tr';
    var tableBom = $(tbBom).length;
    var total_quantity_bom = 0;
    for (iB = 0; iB < tableBom; iB++) {
        elementBom = $(tbBom)[iB];
        quantityBom = intVal($(elementBom).find('.quantity_bom').val());
        total_quantity_bom += quantityBom;
    }

    for (iB = 0; iB < tableBom; iB++) {
        elementBom = $(tbBom)[iB];
        quantityBom = intVal($(elementBom).find('.quantity_bom').val());
        if (total_quantity_bom > 0) {
            ratio = quantityBom / total_quantity_bom * 100;
            $(elementBom).find('.td-ratio').html(tnhFormatNumber(ratio) + '%');
        }
    }
}

function removeQuota(_this, c_temp_counter) {
    $(_this).closest('tr').remove();
    // $('#coll_'+c_temp_counter).remove();
    totalProductionsOrders();
}

function removeOrdersItems(_this, c_temp_counter) {
    $(_this).closest('tr').remove();
    $('#coll_' + c_temp_counter).remove();
    totalProductionsOrders();
}

function changeQuota(_this, temp_counter) {
    item_id = $(_this).val();
    id_production_detail = $('#id_production_detail').val();

    if (item_id) {
        $.ajax({
            type: "POST",
            url: site.base_url + '/admin/manufacture/getItemsById',
            data: {
                csrf_token_name: hash,
                item_id: item_id,
                id_production_detail: id_production_detail,
                'type': 'materials'
            },
            dataType: "json",
            success: function (data) {
                data = data.item;
                if (data) {

                    info_bom = data.info_bom;
                    strMaterial = '';
                    if (info_bom) {
                        strMaterial+= `
                            <div class="text-danger">Khổ in ngang (tờ) = cm: ${info_bom.landscape_print_size}</div>
                            <div class="text-danger">Khổ in dọc (tờ) = cm: ${info_bom.vertical_print_size}</div>
                            <div class="text-danger">SL con/ khổ in: ${info_bom.number_children_size}</div>
                            <div class="text-danger">Giá trị quy đổi: ${info_bom.paper_exchange}</div>
                            <div class="text-danger">Số tờ quy đổi: ${tnhFormatNumber(info_bom.quantity)}</div>
                        `;
                    }

                    tdType = `<td>
                        ${data.type_name}
                    </td>`;
                    tdMaterial = `<td>
                        <input type="hidden" name="item_id_bom[${temp_counter}][]" class="form-control" value="${data.item_id}">
                        ${data.item_code}(${data.item_name})
                        ${strMaterial}
                    </td>`;
                    tdWarehouseItems = `<td>
                        <select data-placeholder="Kho hàng" name="warehouses_items[${temp_counter}][]" class="modal-select2 warehouse_items" style="width: 100%;">${data.option_warehouses}</select>
                    </td>`;
                    tdQuantity = `<td>
                        <input type="text" onchange="totalProductionsOrders()" name="quantity_bom[${temp_counter}][]" onchange="totalBom(${temp_counter})" class="form-control number-format quantity_bom" value="1">
                    </td>`;

                    tdQuantityMultiples = `<td>
                        <input type="text" onchange="totalProductionsOrders()" name="quantity_multiples[${temp_counter}][]" class="form-control quantity_multiples" value="1">
                    </td>`;

                    tdActions = `<td class="text-center">
                        <a onclick="removeQuota(this, ${temp_counter})" class="fa fa-remove text-danger"></a>
                    </td>`;

                    trQuota = `<tr not-tr="true">
                        ${tdMaterial}
                        ${tdWarehouseItems}
                        ${tdQuantity}
                        ${tdQuantityMultiples}
                        ${tdActions}
                    </tr>`;
                    $('.tb_counter_' + temp_counter).prepend(trQuota);
                    // $('select.warehouse_items').select2();
                    $('select.warehouse_items').select2({
                        formatResult: repoFormatHtml,
                        formatSelection: repoFormatHtml,
                        dropdownCssClass: "bigdrop",
                        escapeMarkup: function (m) {
                            return m;
                        }
                    });
                    totalBom(temp_counter);
                    totalProductionsOrders();
                }
                $(_this).val(0);
            }
        });
    }
}
function repoFormatHtml(item) {
    var originalOption = item.element;
    if ($(originalOption).data('check') == 1) {
        return "<b>" + $(originalOption).data('text') + "</b>"
    }
    if ($(originalOption).data('type') == 'nvl' || $(originalOption).data('type') == 'product') {
        return "<b>" + $(originalOption).data('text') + "</b>" +
            "<span style='font-style: italic'><br>" + lang_core['Lot'] + ": </span>" + $(originalOption).data('lot') +
            "<span style='font-style: italic'><br>" + lang_core['ch_date_of_manufacture'] + ": </span>" + $(originalOption).data('date_sx') +
            "<span style='font-style: italic'><br>" + lang_core['ch_items_dateed'] + ": </span>" + $(originalOption).data('date_sd')
    } else {
        return "<b>" + $(originalOption).data('text') + "</b>" +
            "<span style='font-style: italic'><br>" + lang_core['Lot'] + ": </span>" + $(originalOption).data('lot')
    }
}

function formatPOManu(result) {
    if (!result.id) return result.text; // optgroup
    txtPOManu = '<div class="bold">'+result.text+'</div>';
    if (typeof result.items !== 'undefined' && result.items.length > 0) {
        $.each(result.items, function (index, value) { 
            txtPOManu+= `<div>${value.item_name}</div><div>${value.item_code}</div><div>SL: ${tnhFormatNumber(value.quantity)}</div>`;
        });
    }
    return txtPOManu;
}

function ajaxSelectParamsCallbackPOManu(element, url, id, params = false, clearSl2 = false, txtJson = false)
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            formatResult: formatPOManu,
            formatSelection: formatPOManu,
            escapeMarkup: function(m) {
                return m;
            },
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
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            formatResult: formatPOManu,
            formatSelection: formatPOManu,
            escapeMarkup: function(m) {
                return m;
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
            }
        });
    }
}

function ajaxSelectParamsCallbackPOManuTotal(element, url, id, params = false, clearSl2 = false, txtJson = false)
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            formatResult: formatPOManu,
            // formatSelection: formatPOManu,
            escapeMarkup: function(m) {
                return m;
            },
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
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            formatResult: formatPOManu,
            // formatSelection: formatPOManu,
            escapeMarkup: function(m) {
                return m;
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
            }
        });
    }
}

function changeType() {
    type = $('#type').val();
    console.log(type)
    if (type == 2) {
        $('.manufactures-total').hide();
        $('.manufactures-detail').show();
        ajaxSelectParamsCallbackPOManu('#id_production_detail', 'admin/Manufacture/searchProductions', 0, {type: type});

    } else if (type == 1) {
        $('.manufactures-total').show();
        $('.manufactures-detail').hide();
        ajaxSelectParamsCallbackPOManuTotal('#id_production_total', 'admin/Manufacture/searchProductions', 0, {type: type});
    }

    $('#tb-productions-orders tbody').html('');
}

$(document).ready(function () {
    // init_editor('textarea[name="note"]');
    ajaxSelectParamsCallbackPOManuTotal('#po_id', 'admin/Manufacture/searchProductions', po_id_link, {type: 1});
    if (po_id_link) {
        $(document).ready(function () {
            $('#po_id').change();
        });
    }

    $(document).on('change', '.items_id', function (event) {
        event.preventDefault();
        data = event.added;
        sl = this;
        item_id = $(sl).val();
        if (item_id) {
            tr = $(sl).closest('tr');
            subtext = data.item_name;
            images = data.images;
            if (images) {
                tr.find('.td-image a').attr('href', site.base_url + images);
                tr.find('.td-image img').attr('src', site.base_url + images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url + 'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url + 'assets/images/tnh/no_image.png');
            }
            tr.find('.td-item-name').html(subtext);

            lastrow = $('#tb-productions-orders tbody tr')[$('#tb-productions-orders tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html(lang_core['product_name']);
            tr.find('.td-image a').attr('href', site.base_url + 'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url + 'assets/images/tnh/no_image.png');
        }
    });

    // select2-selecting
    $('#productions_plan').on('change', function (event) {
        // var productions_plan_id = event.object.id;
        var productions_plan_id = $(this).val();
        $('#items').val('');
        ajaxSelectParamsCallback('#items', 'admin/manufactures/searchProductionsPlanItem', 0, { productions_plan_id: productions_plan_id });
        $('.tr-production-plan').find('.remove-row').trigger('click');
        return;
    });

    $(document).on('click', '.ev-all', function (event) {
        event.preventDefault();
        productions_plan_id = $('#productions_plan').val();
        if (productions_plan_id) {
            dt.rows().remove().draw();
            $.ajax({
                url: site.base_url + 'admin/manufactures/getProductionsPlanItem',
                type: 'POST',
                dataType: 'json',
                data: {
                    productions_plan_id: productions_plan_id,
                    csrf_token_name: hash,
                },
            })
                .done(function (data) {
                    if (data.result) {

                        $.each(data.result, function (index, el) {
                            production_reference_no = el.reference_no;
                            production_plan_item_id = el.production_plan_item_id;
                            items_id = el.product_id;
                            code = el.code;
                            name = el.name;
                            images = el.images;
                            quantity = el.total_quantity;
                            if (quantity < 0) {
                                quantity = 0;
                            }

                            if (images) {
                                images = site.base_url + images;
                            } else {
                                images = site.base_url + 'assets/images/tnh/no_image.png';
                            }
                            td1 = '<div class="stt text-center"></div>';
                            tdProductionPlan = '<div class="td-production-plan">' + production_reference_no + '</div>';
                            td2 = '<input type="hidden" name="counter[]" id="input" class="form-control" value="' + counter + '">\
                            <input type="hidden" name="production_plan_item[]" id="production_plan_item" class="form-control production_plan_item" value="'+ production_plan_item_id + '">\
                            <input type="hidden" name="items_id[]" id="items_'+ counter + '" class="items_id" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value="' + items_id + '">' + code;
                            td3 = '<div class="td-image">' +
                                '<div class="preview_image" style="width: auto;">' +
                                '<div class="display-block contract-attachment-wrapper img">' +
                                '<div style="width:45px; margin: auto;">' +
                                '<a href="' + images + '" data-lightbox="customer-profile" class="display-block mbot5">' +
                                '<div class="">' +
                                '<img src="' + images + '" style="border-radius: 50%">' +
                                '</div>' +
                                '</a>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                            td4 = '<div class="td-item-name">' + name + '</div>';
                            td5 = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="' + tnhFormatNumber(quantity) + '"></div><input type="hidden" name="quantity_produce" id="quantity_produce" class="form-control quantity_produce" value="' + quantity + '"><div class="text-warning mtop5 text-quantity_produce">SL được sản xuất: ' + tnhFormatNumber(quantity) + '</div><div class="error-quantity_produce text-danger"></div>';
                            td6 = '<div class="td-note"><textarea name="note_items[]" id="note_items[]" class="form-control" rows="3"></textarea></div>';
                            td7 = '<div class="text-center"><i onclick="removeOrdersItems(this, )" class="fa fa-remove btn btn-danger remove-row"></i></div>';
                            rowNode = dt.row.add([
                                td1,
                                tdProductionPlan,
                                td2,
                                td3,
                                td4,
                                td5,
                                td6,
                                td7
                            ]).draw(false).node();

                            $(rowNode).addClass('tr-production-plan');
                            counter++;
                        });
                        totalProductionsOrders();
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    });


    $('#enquiery').on('select2-removed', function (e) {
        // enquiery = e.val;
    });

    $('#items').change(function (event) {
        item_id = $(this).val();
        // type = $('#type').val();
        // id_production_detail = $('#id_production_detail').val();
        // if (!id_production_detail && type == 2) {
        //     alert_float('danger', 'Vui lòng chọn lệnh sản xuất chi tiết');
        //     return;
        // } else if (!id_production_detail && type == 1) { 
        //     alert_float('danger', 'Vui lòng chọn lệnh sản xuất tổng');
        //     return;
        // }

        po_id = $('#po_id').val();
        if (item_id) {
            dtData = event.added;

            quantity = intVal(dtData.quantity);
            quantity_compensation = intVal(dtData.quantity_compensation);
            item_code = dtData.code;
            item_name = dtData.name;
            unit_id_manufactures = dtData.unit_id_manufactures;
            unit_name_manufactures = dtData.unit_name_manufactures;
            quantity_use = tnhToFixedNumber(quantity + quantity_compensation, 0);

            $.ajax({
                type: "POST",
                url: site.base_url + '/admin/manufacture/getItemsByPOId',
                data: {
                    csrf_token_name: hash,
                    item_id: item_id,
                    po_id: po_id,
                },
                dataType: "json",
                success: function (response) {
                    tdNumber = `<td class="text-center stt"></td>`;
                    tdCode = `<td class="text-center">${item_code}</td>`;
                    tdName = `<td class="text-center">
                        
                        <input type="hidden" name="items[${counter}][item_id]" class="form-control" value="${item_id}">
                        <input type="hidden" name="items[${counter}][quantity]" class="form-control" value="${quantity}">
                        <input type="hidden" name="items[${counter}][quantity_compensation]" class="form-control" value="${quantity_compensation}">
                        <input type="hidden" name="items[${counter}][unit_id_manufactures]" class="form-control" value="${unit_id_manufactures}">
                        <input type="hidden" name="items[${counter}][quantity_use]" class="form-control" value="${quantity_use}">
                        
                        ${item_name}
                    </td>`;
                    tdUnits = `<td class="text-center">${unit_name_manufactures}</td>`;
                    tdQuantityCompensation = `<td class="text-center">${tnhFormatNumber(quantity_compensation)}</td>`;
                    tdQuantityUse = `<td class="text-center">${tnhFormatNumber(quantity_use)}</td>`;
                    tdNote = `<td class="text-center">
                        <textarea name="items[${counter}][note]" class="form-control" rows="3"></textarea>
                    </td>`;
                    tdActions = '<td><div class="text-center"><i onclick="removeOrdersItems(this, ' + counter + ')" class="fa fa-remove btn btn-danger remove-row"></i></div></td>';

                    trItem = `<tr>
                        ${tdNumber}
                        ${tdCode}
                        ${tdName}
                        ${tdUnits}
                        ${tdQuantityCompensation}
                        ${tdQuantityUse}
                        ${tdNote}
                        ${tdActions}
                    </tr>`;

                    trItem+= `<tr not-tr="true" id="coll_${counter}">
                        <td colspan="99">
                            <table class="table table-hover">
                                <thead>
                                    <tr not-tr="true">
                                        <th class="text-center" style="background: #ddd !important;">Mã thành phẩm</th>
                                        <th class="text-center" style="background: #ddd !important;">Tên thành phẩm</th>
                                        <th class="text-center" style="background: #ddd !important;">Số lượng đơn hàng</th>
                                        <th class="text-center" style="background: #ddd !important;">Khổ in ngang - dọc (tờ) = cm</th>
                                        <th class="text-center" style="background: #ddd !important;">SL con/ khổ in</th>
                                        <th class="text-center" style="background: #ddd !important;">Giá trị quy đổi(tính trên tờ in)</th>
                                        <th class="text-center" style="background: #ddd !important;">Số tờ quy đổi</th>
                                        <th class="text-center" style="background: #ddd !important;">Số NPL cần</th>
                                        <th class="text-center" style="background: #ddd !important;">Số lượng bù hao(khổ liệu)</th>
                                        <th class="text-center" style="background: #ddd !important;">Công đoạn</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    if (response.productions_orders_items_sub) {
                        $.each(response.productions_orders_items_sub, function (index, value) { 
                            tdProductCode = `<td class="text-center">
                                <input type="hidden" name="items[${counter}][detail][${index}][poi_id]" class="form-control" value="${value.poi_id}">
                                <input type="hidden" name="items[${counter}][detail][${index}][product_id]" class="form-control" value="${value.product_id}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity_order]" class="form-control" value="${value.quantity_order}">
                                <input type="hidden" name="items[${counter}][detail][${index}][landscape_print_size]" class="form-control" value="${value.landscape_print_size}">
                                <input type="hidden" name="items[${counter}][detail][${index}][vertical_print_size]" class="form-control" value="${value.vertical_print_size}">
                                <input type="hidden" name="items[${counter}][detail][${index}][number_children_size]" class="form-control" value="${value.number_children_size}">
                                <input type="hidden" name="items[${counter}][detail][${index}][paper_exchange]" class="form-control" value="${value.paper_exchange}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity_single]" class="form-control" value="${value.quantity_single}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity_primary]" class="form-control" value="${value.quantity_primary}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity]" class="form-control" value="${value.quantity}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity_compensation]" class="form-control" value="${value.quantity_compensation}">
                                <input type="hidden" name="items[${counter}][detail][${index}][quantity_compensation_primary]" class="form-control" value="${value.quantity_compensation_primary}">
                                <input type="hidden" name="items[${counter}][detail][${index}][stage_id]" class="form-control" value="${value.stage_id}">
                                ${value.product_code}
                            </td>`;
                            tdProductName = `<td class="text-center">${value.product_name}</td>`;
                            tdQuantityOrders = `<td class="text-center">${value.quantity_order}</td>`;
                            tdLandscapePrintSize = `<td class="text-center">${value.landscape_print_size}</td>`;
                            td_number_children_size = `<td class="text-center">${value.number_children_size}</td>`;
                            td_exchange_value = `<td class="text-center">${tnhFormatNumber(value.quantity_single)}</td>`;
                            td_paper_exchange = `<td class="text-center">${tnhFormatNumber(value.paper_exchange)}</td>`;
                            td_quantity_npl_need = `<td class="text-center">${tnhFormatNumber(value.quantity, 0)}</td>`;
                            td_quantity_compensation = `<td class="text-center">${value.quantity_compensation}</td>`;
                            td_stage = `<td class="text-center">${value.stage_name}</td>`;

                            trItem+= `<tr not-tr="true">
                                ${tdProductCode}
                                ${tdProductName}
                                ${tdQuantityOrders}
                                ${tdLandscapePrintSize}
                                ${td_number_children_size}
                                ${td_exchange_value}
                                ${td_paper_exchange}
                                ${td_quantity_npl_need}
                                ${td_quantity_compensation}
                                ${td_stage}
                            </tr>`;
                        });
                    }
                    trItem+= `</tbody></table></td></tr>`;
                    $('#tb-productions-orders tbody.tbody-items').append(trItem);
                    counter++;
                    totalProductionsOrders();
                }
            });
            $('#items').val('');

            return
            $.ajax({
                // url: site.base_url + '/admin/manufactures_temp/getItemsById',
                url: site.base_url + '/admin/manufacture/getItemsById',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    item_id: item_id,
                    id_production_detail: id_production_detail,
                    type: 'materials'
                },
            })
            .done(function (data) {
                if (data.item) {
                    items_id = data.item.item_id;
                    code = data.item.item_code;
                    name = data.item.item_name;

                    info_bom = data.item.info_bom;
                    tdNumber = `<td class="text-center stt">
                    </td>`;
                    
                    height = intVal(data.item.height);
                    quantity_compensation = intVal(info_bom.quantity_compensation) + intVal(info_bom.quantity_compensation_sm);
                    total_height = height * (quantity_compensation + intVal(info_bom.quantity));
                    quantity_need = tnhToFixedNumber(quantity_compensation + intVal(info_bom.quantity), 0);

                    quantity_single = info_bom.quantity_single;
                    number_paper = quantity_need * quantity_single;
                    paper_exchange = tnhToFixedNumber(quantity_need/info_bom.quantity_single, 0);

                    tdCode = `<td>
                        <input type="hidden" name="counter[${counter}]" id="counter_${counter}" class="form-control counter" value="${counter}">
                        <input type="hidden" name="items_id[${counter}]" id="items_${counter}" class="items_id" style="width: 100%;" value="${data.item.item_id}">
                        ${code}
                    </td>`;
                    tdName = `<td>${name}</td>`;



                    tdLandscapePrintSize = `<td class="tdLandscapePrintSize text-center">${(info_bom.landscape_print_size)}</td>`;
                    tdNumberPager = `<td class="tdNumberPager text-center">${tnhFormatNumber(number_paper)}</td>`;
                    tdVerticalPrintSize = `<td class="tdVerticalPrintSize text-center">${tnhFormatNumber(info_bom.vertical_print_size)}</td>`;
                    tdNumberChildrenSize = `<td class="tdNumberChildrenSize text-center">${tnhFormatNumber(info_bom.number_children_size)}</td>`;
                    tdExchangeValue = `<td class="tdExchangeValue text-center">${tnhFormatNumber(info_bom.quantity_single)}</td>`;
                    tdPaperExchange = `<td class="tdPaperExchange text-center">${tnhFormatNumber(paper_exchange)}</td>`;
                    tdQuantityCompensation = `<td class="tdQuantityCompensation text-center">${tnhFormatNumber(quantity_compensation)}</td>`;
                    tdQuantityNeed = `<td class="tdQuantityNeed text-center">${tnhFormatNumber(quantity_need)}</td>`;
                    
                    tdHeight = `<td class="tdHeight text-center">${tnhFormatNumber(data.item.height)}</td>`;
                    tdTotalHeight = `<td class="tdTotalHeight text-center">${tnhFormatNumber(total_height)}</td>`;

                    tdNote = `<td>
                        <div class="td-note"><textarea name="note_items[${counter}]" class="form-control" rows="3"></textarea></div>
                    </td>`;

                    tdActions = '<td><div class="text-center"><i onclick="removeOrdersItems(this, ' + counter + ')" class="fa fa-remove btn btn-danger remove-row"></i></div></td>';

                    trHtml = `<tr>
                        ${tdNumber}
                        ${tdCode}
                        ${tdName}
                        ${tdHeight}
                        ${tdTotalHeight}
                        ${tdQuantityNeed}
                        ${tdLandscapePrintSize}
                        ${tdNumberChildrenSize}
                        ${tdExchangeValue}
                        ${tdPaperExchange}
                        ${tdNote}
                        ${tdActions}
                    </tr>`;

                    $('#tb-productions-orders tbody.tbody-items').append(trHtml);
                    counter++;
                    totalProductionsOrders();

                //     items_id = data.item.item_id;
                //     code = data.item.item_code;
                //     name = data.item.item_name;
                //     images = data.item.images;
                //     quantity = 1;

                //     if (images) {
                //         images = images;
                //     } else {
                //         images = site.base_url + 'assets/images/tnh/no_image.png';
                //     }

                //     tdNumber = `<td class="text-center">
                //         <i class="btn btn-primary" data-toggle="collapse" data-target="#coll_${counter}"><div class="stt text-center"></div></i>
                //     </td>`;
                    
                //     tdCode = `<td>
                //         <input type="hidden" name="counter[${counter}]" id="counter_${counter}" class="form-control counter" value="${counter}">
                //         <input type="hidden" name="items_id[${counter}]" id="items_${counter}" class="items_id" style="width: 100%;" value="${data.item.item_id}">
                //         ${data.item.name}(${data.item.code})
                //     </td>`;

                //     tdImage = `<td><div class="td-image">
                //         <div class="preview_image" style="width: auto;">
                //             <div class="display-block contract-attachment-wrapper img">
                //                 <div style="width:45px; margin: auto;">
                //                     <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                //                         <div class="">
                //                             <img src="${images}" style="border-radius: 50%">
                //                         </div>
                //                     </a>
                //                 </div>
                //             </div>
                //         </div>
                //     </td>`;

                //     tdName = '<td><div class="td-item-name">' + name + '</div></td>';
                //     tdWarehouses = `<td>
                //         <select name="warehouses[${counter}]" style="width: 100%;" data-placeholder="Kho hàng" id="warehouses_${counter}" class="warehouses modal-select2">
                //             ${data.item.option_warehouses}
                //         </select>
                //     </td>`;

                //     tdQuantity = `<td>
                //         <div class="td-quantity">
                //             <input readonly type="text" name="quantity[${counter}]" onchange="totalProductionsOrders()" class="form-control quantity number-format" value="0">
                //             <div class="show-errors text-danger"></div>
                //         </div>
                //     </td>`;

                //     tdNote = '<td><div class="td-note"><textarea name="note_items[' + counter + ']" class="form-control" rows="3"></textarea></div></td>';
                //     tdActions = '<td><div class="text-center"><i onclick="removeOrdersItems(this, ' + counter + ')" class="fa fa-remove btn btn-danger remove-row"></i></div></td>';

                //     //----------------------------------------

                //     trHtmlBom = '';
                //     if (typeof data.productsBom !== 'undefined' && data.productsBom != null) {
                //         $.each(data.productsBom, function (index, value) {
                //             optionsUnitsBom = getUnits(value.units, value.selected);
                //             tdMaterialBom = `<td>
                //             <input type="hidden" name="type_bom[${counter}][]" class="form-control" value="${value.type}">
                //             <input type="hidden" name="item_id_bom[${counter}][]" class="form-control" value="${value.item_id}">
                //             ${value.code}(${value.name})
                //         </td>`;
                //             tdUnitsBom = `<td>
                //             <select data-placeholder="ĐVT" name="units_bom[${counter}][]" class="modal-select2 units" style="width: 100%;">${optionsUnitsBom}</select>
                //         </td>`;
                //         tdWarehousesItems = `<td class="">
                //             <select name="warehouses_items[${counter}][]" class="form-control" required="required">
                //                 <option value=""></option>
                //             </select>
                //         </td>`;

                //         tdQuantityBom = `<td>
                //             <input type="text" name="quantity_bom[${counter}][]" onchange="totalBom(${counter})" class="form-control number-format quantity_bom" value="${value.quantity}">
                //         </td>`;

                //         tdQuantityMultiplesBOM = `<td>
                //             <input type="text" name="quantity_multiples[${counter}][]" class="form-control quantity_multiples" value="1">
                //         </td>`;
                            
                //         tdActionsBom = `<td class="text-center">
                //             <a onclick="removeQuota(this, ${counter})" class="fa fa-remove text-danger"></a>
                //         </td>`;

                //             trHtmlBom += `<tr not-tr="true">
                //             ${tdMaterialBom}
                //             ${tdWarehousesItems}
                //             ${tdQuantityBom}
                //             ${tdActionsBom}
                //         </tr>`;
                //         });
                //     }

                //     trHtml = `<tr>
                //         ${tdNumber}
                //         ${tdImage}
                //         ${tdCode}
                //         ${tdQuantity}
                //         ${tdNote}
                //         ${tdActions}
                //     </tr>
                // <tr id="coll_${counter}" class="collapse in" not-tr="true">
                //     <td colspan="8" style="padding-left: 100px !important;">
                //         <div class="">
                //             <input type="text" id="add_element_${counter}" onchange="changeQuota(this, ${counter})" class="add_element" data-placeholder="Nguyên phụ liệu xả khổ" style="width: 100%;" value="" title="">
                //         </div>
                //         <table class="table tb_counter_${counter} tb_counter" style="margin-top: 5px;">
                //             <thead>
                //                 <tr not-tr="true" style="background: #ddd; font-weight: bold;">
                //                     <td class="text-center">Nguyên phụ liệu xả khổ</td>
                //                     <td class="text-center" style="width: 350px;">Kho hàng</td>
                //                     <td class="text-center" style="width: 150px;">Số lượng (ĐV kho)</td>
                //                     <td class="text-center" style="width: 150px;">Bội số xả /1SL</td>
                //                     <td class="text-center" style="width: 50px;">Tác vụ</td>
                //                 </tr>
                //             </thead>
                //             <tbody>
                //                 ${trHtmlBom}
                //             </tbody>
                //         </table>
                //     </td>
                // </tr>`;

                //     $('#tb-productions-orders tbody.tbody-items').append(trHtml);
                //     // ajaxSelectItemsCallBack('#add_element_' + counter, 'admin/manufacture/searchProductAndGoodsExport', 0);
                //     // ajaxSelectItemsCallBack('#add_element_' + counter, 'admin/manufacture/searchProductAndGoodsExport', 0);
                //     ajaxSelectParamsCallback('#add_element_' + counter, 'admin/manufactures_temp/searchMaterialPOD', 0, {id_production_detail: id_production_detail});
                //     totalBom(counter);
                //     $('select.units').select2();
                //     $('#warehouses_' + counter + '').select2();
                //     counter++;
                //     totalProductionsOrders();
                }
            })
            .fail(function () {
                console.log("error");
            });
        }
        $('#items').val('');
    });


    $(document).on('change', '.quantity, .quantity_sub', function (event) {
        totalProductionsOrders();
    });

    $(document).on('change', 'select#id_production_detail', function(event) {
        $('#tb-productions-orders tbody').html('');

        id_production_detail = $(this).val();
        ajaxSelectParamsCallback('#items', 'admin/manufactures_temp/searchMaterialPOD', 0, {id_production_detail: id_production_detail});
    });

    $(document).on('change', 'input#id_production_detail', function(event) {
        $('#tb-productions-orders tbody').html('');
        id_production_detail = $(this).val();
        ajaxSelectParamsCallback('#items', 'admin/manufactures_temp/searchMaterialPOD', 0, {id_production_detail: id_production_detail});
    });


    // $(document).on('click', '.remove-row', function(event) {
    //     event.preventDefault();

    // 	// dt.row( $(this).parents('tr') ).remove().draw();
    // 	totalProductionsOrders();
    // });

    $(document).on('click', '.remove-sub', function (event) {
        event.preventDefault();
        $(this).closest('.row').remove();
        totalProductionsOrders();
    });


    $(document).on('click', '.add-row-foot', function (event) {
        event.preventDefault();
        $('.add-row').click();
    });

    if (edit == 0) {
        $('.add-row').click();
        $(document).on('click', '.referesh-reference', function (event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url + 'admin/manufactures/refereshReferenceProductionsOrders',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    token: hash,
                    'referesh': 1
                },
            })
                .done(function (data) {
                    if (data) {
                        $('#reference_no').val(data.reference_no);
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', 'fail');
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        });

        changeType();
    } else {
        $('select.warehouses').select2();
        // $('select.warehouse_items').select2();
        $('select.warehouse_items').select2({
            formatResult: repoFormatHtml,
            formatSelection: repoFormatHtml,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) {
                return m;
            }
        });

        id_production_detail = $('select#id_production_detail').val();
        // ajaxSelectParamsCallback('input.add_element', 'admin/manufactures_temp/searchMaterialPOD', 0, {id_production_detail: id_production_detail});
        // ajaxSelectItemsCallBack('input.add_element', 'admin/manufacture/searchProductAndGoodsExport', 0);

        ajaxSelectParamsCallback('#items', 'admin/manufactures_temp/searchMaterialPOD', 0, {id_production_detail: id_production_detail});
        disibleLSX(1);
    }

    $(document).on('change', 'select.type', function(event) {
        changeType();
    });

    $(document).on('hidden.bs.modal', '#tnhModal', function () {
        window.location.href = site.base_url + 'admin/manufactures/productions_orders';
    });

    // ajaxSelectCallBack($('#items'), 'admin/products/searchProductsSelect2Manufactures', 0);
    // ajaxSelectItemsCallBack($('#items'), 'admin/manufacture/searchProductAndGoods', 0, { id_production_detail: $('#id_production_detail').val() });
    // ajaxSelectCallBack($('#items'), 'admin/items/searchSelect2Materials', 0);

    $(document).on('change', '#po_id', function(event) {
        po_id = $(this).val();
        ajaxSelectParamsCallback('#items', 'admin/manufacture/searchMaterialPO', 0, {po_id: po_id});
    });

    $('.type').select2();
    appValidateForm($('#add-productions-orders'), {
        reference_no: 'required',
        date: 'required',
        location: 'required',
        type: 'required',
        po_id: 'required'
        // id_production_detail: 'required'
    }, db);

    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', 'Vui lòng kiểm tra lại số lượng nhập');
            return;
        }
        $('.add').attr('disabled', 'disabled');
        // tinymce.get('note').save();
        // var data = $(form).serialize();
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
            // url : site.base_url+'admin/business_plan/add',
            url: url,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        })
            .done(function (data) {
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url + 'admin/manufacture';
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function () {
                alert_float('danger', 'error');
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
});
function formatItemsSelect2(result) {
    if (!result.id) return result.text; // optgroup
    htmlItem = `<div>${result.text}(<span style="color: red;">${tnhFormatNumber(result.total_quantity)}</span>)</div>`;
    return htmlItem;
}

function ajaxSelectItemsCallBack(element, url, id, get_new = {}) {
    if (id) {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatItemsSelect2,
            formatSelection: formatItemsSelect2,
            escapeMarkup: function (m) {
                return m;
            },
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + url + '/' + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.row);
                    }
                });
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                }
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            //allowClear: true,
            formatResult: formatItemsSelect2,
            formatSelection: formatItemsSelect2,
            escapeMarkup: function (m) {
                return m;
            },
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    var data_returen = {
                        term: term,
                        limit: 50
                    };
                    if (get_new) {
                        $.each(get_new, function (i, v) {
                            data_returen[i] = v;
                        })
                    }
                    return data_returen;
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{ id: '', text: 'No Match Found' }] };
                    }
                }
            }
        });
    }
}
// function setserial(_this) {
$(document).on('change', '.serial', (e) => {
    var currentQuantityInput = $(e.currentTarget);
    var item_id = currentQuantityInput.parents('tr').find('.items_id').val();
    // var table = $('#tb-productions-orders tbody tr').find('input[value=' + item_id + '].items_id');
    var table = $('input.serial');
    var check = 0;
    $.each(table, function (key, value) {
        var tr = $(value).val();
        if (tr === serial) {
            check++;

        }
    });
    if (check > 1) {
        alert_float('danger',
            "Sản phẩm thuộc serial này đã được thêm, vui lòng kiểm tra lại!");
        currentQuantityInput.parents('tr').find('input.serial').val('');
        return;
    }
});

