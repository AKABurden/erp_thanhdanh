function totalOrders()
{
    tb = '#tb-orders tbody tr:not("[class^=not-tr]")';
    var n = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    var total_amount = 0;
    var total_tax_item = 0;
    var total_discount_percent_item = 0;
    var total_discount_direct_item = 0;
    var total_grand_total_item = 0;
    arr_category_id = [];

    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        // quantity = intVal($(element).find('.quantity').val());

        quantity = 0;
        // sample_quantity = intVal($(element).find('.sample_quantity').val());

        conversion_quantity_unit = intVal($('select.unit').select2().find(":selected").data('conversion_quantity_unit'));
        // price = intVal($(element).find('.price').val());
        hand_input_price = $(element).find('.hand_input_price').prop('checked');
        if (!hand_input_price) {
            price_default = intVal($(element).find('.price_default').val());
            price = price_default/conversion_quantity_unit;
            $(element).find('.price').val(tnhFormatMoney(price));
        } else {
            price = intVal($(element).find('.price').val());
        }

        loss_item = intVal($(element).find('.td-loss').html()); 
        total_quantity_item = 0;
        cTempCounter = $(element).find('.counter').val();
        elementSub = $('#tr-child-'+cTempCounter+' tbody tr.tr-sub-items');
        total_quantity_put = 0;
        total_quantity_sample = 0;
        if (typeof elementSub !== "undefined" && elementSub.length > 0) {
            $.each(elementSub, function (index, value) { 
                quantity_put = intVal($(value).find('.quantity_put').val());
                sample_quantity_item = intVal($(value).find('.sample_quantity_item').val());
                // quantity_loss = intVal($(value).find('.quantity_loss').val());
                quantity_loss = tnhToFixedNumber(quantity_put * loss_item/100, 0);
                $(value).find('.quantity_loss').val(tnhFormatNumber(quantity_loss));

                total_quantity_put+= quantity_put;
                total_quantity_item+= quantity_put;
                total_quantity_item+= quantity_loss;
                total_quantity_sample+= sample_quantity_item;
            });
        }

        $(element).find('.sample_quantity').val(tnhFormatNumber(total_quantity_sample));
        total_quantity_item = total_quantity_item + total_quantity_sample;
        quantity = total_quantity_item;
        is_lot = $(element).find('.is_lot').prop('checked');
        if (is_lot) {
            amount = price;
        } else {
            amount = total_quantity_put * price;
        }


        $(element).find('.quantity').val(total_quantity_put);
        $(element).find('.td-total-quantity-put').html(tnhFormatNumber(total_quantity_put));
        $(element).find('.td-total-quantity').html(tnhFormatNumber(total_quantity_item));

        //sub date delivery
        quantity_sub = 0;
        $.each($(element).find('.quantity_sub'), function(index, el) {
            quantity_sub+= intVal($(el).val());
        });
        if (quantity_sub > total_quantity_item) {
            $(element).find('.show-errors').html(lang_core['total_quantity_less']+ formatNumberTnh(total_quantity_item));
            count_errors++;
        } else {
            $(element).find('.show-errors').html('');
        }
        //end sub date delivery

        $(element).find('.td-total-amount').html(tnhFormatMoney(amount));
        total_quantity+= quantity;
        total_amount+= amount;

        grand_total_item = amount;

        discount_percent_item = intVal($(element).find('.discount_percent_item').val());
        discount_percent_item_amount = 0;
        if (discount_percent_item > 0) {
            discount_percent_item_amount = grand_total_item*(discount_percent_item/100);
            total_discount_percent_item+= discount_percent_item_amount;
            grand_total_item-= discount_percent_item_amount;
        }

        discount_direct_item_amount = intVal($(element).find('.discount_direct_item').val());
        total_discount_direct_item+= discount_direct_item_amount;
        grand_total_item-= discount_direct_item_amount;

        tax_rate_item = intVal($(element).find('select.tax_item').select2().find(":selected").data('rate'));
        tax_item_amount = 0;
        if (tax_rate_item > 0) {
            tax_item_amount = grand_total_item*(tax_rate_item/100);
            total_tax_item+= tax_item_amount;
            grand_total_item+= tax_item_amount;
        }

        $(element).find('.td-total-amount').html(tnhFormatMoney(grand_total_item));
        total_grand_total_item+= grand_total_item;

        //
        showExchange = $(element).find('.list-exchange');
        nShowExchange = showExchange.length;
        for (jj = 0; jj < nShowExchange; jj++) {
            elementShowExchange = $(showExchange)[jj];
            numberExchange = intVal($(elementShowExchange).find('.number-exchange').val());
            totalQuantityExchange = quantity/numberExchange;
            $(elementShowExchange).find('.text-number-exchange').html(tnhFormatNumber(totalQuantityExchange));
        }
    }
    $('.total-quantity').html(tnhFormatNumber(total_quantity));
    $('.total-amount').html(tnhFormatMoney(total_amount));
    $('.total-tax').html(tnhFormatMoney(total_tax_item));
    $('.total-discount').html(tnhFormatMoney(total_discount_direct_item + total_discount_percent_item));
    $('.grand-total').html(tnhFormatMoney(total_grand_total_item));

    grand_total_all = total_grand_total_item;

    discount_percent = intVal($('#discount_percent').val());
    discount_percent_amount = 0;
    if (discount_percent > 0) {
        discount_percent_amount = grand_total_all * (discount_percent/100);
    }
    grand_total_all-= discount_percent_amount;

    discount_direct = intVal($('#discount_direct').val());
    grand_total_all-= discount_direct;

    tax_rate = intVal($('select.tax_id').select2().find(":selected").data('rate'));
    tax_amount = 0;
    if (tax_rate > 0) {
        tax_amount = grand_total_all * (tax_rate/100);
    }
    grand_total_all+= tax_amount;

    cost_delivery = intVal($('#cost_delivery').val());
    charge_party = $('#charge_party').val();
    if (charge_party == "customer") {
        grand_total_all+= cost_delivery;
    }
    $('.td-grand-total-all').html(tnhFormatMoney(grand_total_all));

    ktQuantityShip();
}

function addImportColumns() {
    var form = $('#orders'),
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

    formData.append('counter_index', counter);
    formData.append('counter_child', counter_child);
    $.ajax({
        url : site.base_url+'admin/orders/import_columns',
        type : 'POST',
        dataType: 'JSON',
        cache : false,
        contentType : false,
        processData : false,
        data: formData,
    })
    .done(function(data) {
        if (data.result) {
            counter_child = intVal(data.counter_child);
            cur_counter_index = data.counter_index;
            $.each(data.trHtmlChildList, function(index, trHtmlChild) {
                console.log($('#tr-child-'+index+' tbody'))
                $('#tr-child-'+index+' tbody').html(trHtmlChild);
            })

            alert_float('success', data.message);
            totalOrders();
            getPricesQuotes(cur_counter_index);
        } else {
            alert_float('danger', data.message);
        }
    })
    .fail(function() {
        alert_float('danger', 'Vui lòng xóa file chọn lại');
    });
}

function getTaxs(select_id)
{
    var option = '<option value="0">0%</option>';
    $.each(taxs, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option data-rate="'+el.taxrate+'" value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function getSize(select_id)
{
    var option = '<option value=""></option>';
    option+= '<option value="0"></option>';
    $.each(size, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function getColors(select_id)
{
    var option = '<option value=""></option>';
    option+= '<option value="0"></option>';
    $.each(colors, function(index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option+= '<option value="'+el.id+'">'+el.name+'</option>';
    });
    return option;
}

function formatTable(result) {
    if (!result.id) return result.text; // optgroup
    tr = '';
    if (result) {
        tr+= '<td style="width: 33%;">'+fld(result.date)+'</td>';
        tr+= '<td style="width: 33%;">'+result.text+'</td>';
        tr+= '<td style="width: 33%;">'+result.customer_name+'</td>';
    }
    tableSelect = '<table class="tnh-table table-bordered dont-responsive-table">'+
                        '<tbody>'+
                            tr
                        '</tbody>'+
                    '</table>';
    return tableSelect;
}

function addRowShipping(counter, _this)
{
    var div = $(_this).closest('.td-date');

    html = '<div class="sb">'+
    '<div class="col-md-7" style="padding: 0px;"><input type="text" name="date_sub['+counter+'][]" id="input" class="form-control datepicker date_sub" placeholder="'+lang_core['date']+'" value="" style="width: 100%;" title=""></div>'+
    '<div class="col-md-4" style="padding: 0px;"><input type="text" style="width: 100%;" name="quantity_sub['+counter+'][]" id="input" class="form-control quantity_sub number-format" value="0" title=""></div>'+
    '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>'+
    '</div>';
    div.find('.sub').append(html);
    totalOrders();
    formatNumberPlugin();
    init_datepicker();
}

function removeAttachments(_this)
{
    bootbox.confirm({
        message: lang_core['you_want_remove'],
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
                $('#remove_image').val(1);
                $(_this).closest('tr').remove();

                tbAt = '#tb-attachments tbody tr:not("[class^=not-tr]")';
                nAt = $(tbAt).length;
                cAt = 1;
                for (iAt = 0; iAt < nAt; iAt++)
                {
                    elementAt = $(tbAt)[iAt];
                    $(elementAt).find('.stt').html(cAt);
                    cAt++;
                }
            } else {
            }
        }
    });
}

// function loadPromotions()
// {
//     customer_id = $('#customers').val();
//     total_amount = intVal($('.total-amount').html());
//     // if (customer_id) {
//         $.ajax({
//             url: site.base_url+'admin/orders/getPromotionOrders',
//             type: 'POST',
//             dataType: 'JSON',
//             data: {
//                 csrf_token_name: hash,
//                 customer_id: customer_id,
//                 total_amount: total_amount,
//             },
//         })
//         .done(function(data) {
//             if (data.html) {
//                 $('#tb-promotion tbody').html(data.html);
//             } else {
//                 $('#tb-promotion tbody').html('');
//             }
//         })
//         .fail(function() {
//             console.log("error");
//         });
//     // }
// }

function loadGift()
{

}

function loadCostPrice(cItemId, cQuantity, cTr)
{
    cTr.find('.td-total-cost-amount').html('');
    if (edit == 0) order_id = 0;
    if (cItemId) {
        $.ajax({
            url: site.base_url+'admin/orders/showPriceCost',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token_name: hash,
                cItemId: cItemId,
                cQuantity: cQuantity,
                order_id: order_id,
            },
        })
        .done(function(data) {
            if (data) {
                cTr.find('.td-total-cost-amount').html('Giá vốn: '+tnhFormatMoney(data.priceCost));
            }
        })
        .fail(function() {
            console.log("error");
        });
    }
}

function removeChildSize(_this) {
    cTr = $(_this).closest('tr');
    cur_counter = $(cTr).attr('tr-counter');
    cTr.remove();
    totalOrders();
    getPricesQuotes(cur_counter);
}

function addChild(_this, temp_counter) {
    trChild = $(_this).parents('tr');

    tdNumberChild = `<td></td>`;
    tdSizeSPChild = `<td>
        <select name="itemsChild[${temp_counter}][${counter_child}][size]" data-placeholder="Size SP" id="size-child-${counter_child}" style="width: 100%;" class="size_sp">
            ${getSize(0)}
        </select>
    </td>`;
    tdSizeDCChild = `<td>
        <input type="text" name="itemsChild[${temp_counter}][${counter_child}][size_dc]" placeholder="Size ĐC" class="form-control size_dc" value="">
    </td>`;
    tdSizeNumberChild = `<td>
        <input type="text" name="itemsChild[${temp_counter}][${counter_child}][style_number]" placeholder="Style Number" class="form-control style_number" value="">
    </td>`;
    tdColorChild = `<td>
        <select name="itemsChild[${temp_counter}][${counter_child}][color]" data-placeholder="Color" id="color-${counter_child}" style="width: 100%;" class="color">
            ${getColors(0)}
        </select>
    </td>`;
    tdQuantityChild = `<td>
        <input type="text" name="itemsChild[${temp_counter}][${counter_child}][quantity]" class="form-control number-format" value="1">
    </td>`;
    tdActionsChild = `<td class="text-center">
        <a href="javascript:void(0)" class="text-danger" onClick="removeChildSize(this)"><i class="fa fa-remove"></i><a/>
    </td>`;

    trHtmlChild = `<tr class="not-tr">
        ${tdNumberChild}
        ${tdSizeSPChild}
        ${tdSizeDCChild}
        ${tdSizeNumberChild}
        ${tdColorChild}
        ${tdQuantityChild}
        ${tdActionsChild}
    </tr>`;
    trChild.find('.table-child tbody').append(trHtmlChild);
    $('#size-child-'+counter_child).select2();
    $('#color-'+counter_child).select2();
    counter_child++;
}


function totalChildChangeSize(t_temp_counter) {
    tbchangeSize = '.table-child-size-'+t_temp_counter+' tbody tr';
    var nChangeSize = $(tbchangeSize).length;

    cTrSize = $('.counter[value="'+t_temp_counter+'"]').closest('tr'); 
    quantity_child_sheet = intVal(cTrSize.find('.quantity_child_sheet').html());
    quantity_sheet_bale = intVal(cTrSize.find('.quantity_sheet_bale').html());

    for (ii = 0; ii < nChangeSize; ii++)
    {
        element = $(tbchangeSize)[ii];
        quantity_child = intVal($(element).find('.quantity').val());
        if (quantity_child_sheet > 0) {
            quantity_sheet = quantity_child/quantity_child_sheet;
            even_quantity = Math.floor(quantity_sheet);
            quantity_ceil = Math.ceil(quantity_sheet);
            odd_quantity = quantity_ceil - even_quantity;

            $(element).find('.even-sheet').html(even_quantity);
            $(element).find('.odd-sheet').html(odd_quantity);
        }

        if (quantity_sheet_bale > 0) {
            quantity_bale = quantity_child/quantity_sheet_bale;
            even_quantity_bale = Math.floor(quantity_bale);
            quantity_ceil_bale = Math.ceil(quantity_bale);
            odd_quantity_bale = quantity_ceil_bale - even_quantity_bale;

            $(element).find('.even-bale').html(even_quantity_bale);
            $(element).find('.odd-bale').html(odd_quantity_bale);
        }
    }
}

function removeChildChangeSize(_this) {
    cTr = $(_this).closest('tr');
    cTr.remove();
}

function addChildChangeSize(_this, temp_counter) {
    trChild = $(_this).parents('tr');

    tdNumberChild = `<td></td>`;

    tdNumberSizeChild = `<td>
        <input type="text" name="itemsChildSize[${temp_counter}][${counter_child}][number_size]" placeholder="Số Size" class="form-control" value="">
    </td>`;

    tdQuantityChild = `<td>
        <input type="text" name="itemsChildSize[${temp_counter}][${counter_child}][quantity]" onchange="totalChildChangeSize(${temp_counter})" class="form-control number-format quantity" placeholder="Số lượng" value="">
    </td>`;

    tdEvenSheetChild = `<td class="even-sheet text-center">
    </td>`;

    tdOddSheetChild = `<td class="odd-sheet text-center">
    </td>`;

    tdEvenBaleChild = `<td class="even-bale text-center">
    </td>`;

    tdOddBaleChild = `<td class="odd-bale text-center">
    </td>`;

    tdActionsChild = `<td class="text-center">
        <a href="javascript:void(0)" class="text-danger" onClick="removeChildChangeSize(this)"><i class="fa fa-remove"></i><a/>
    </td>`;

    trHtmlChild = `<tr class="not-tr">
        ${tdNumberChild}
        ${tdNumberSizeChild}
        ${tdQuantityChild}
        ${tdEvenSheetChild}
        ${tdOddSheetChild}
        ${tdEvenBaleChild}
        ${tdOddBaleChild}
        ${tdActionsChild}
    </tr>`;
    trChild.find('.table-child-size tbody').append(trHtmlChild);
    $('#size-child-'+counter_child).select2();
    $('#color-'+counter_child).select2();
    counter_child++;
}

function getPricesQuotes(temp_pcounter) {
    cTr = $('.counter[value="'+temp_pcounter+'"]').closest('tr');
    cItemId = cTr.find('input.items_id').val();
    customers = $('#customers').val();
    quantity = intVal(cTr.find('.quantity').val());

    isCheckedPrice = cTr.find('.hand_input_price').prop('checked');
    table_price_id = $('#table_price_id').val();
    if (table_price_id > 0) {
        changePrice(cTr, cItemId, table_price_id);
        return;
    }

    if (!isCheckedPrice) {
        var dataPOST = {};
        dataPOST['cItemId'] = cItemId;
        dataPOST['customers'] = customers;
        dataPOST['quantity'] = quantity;
        dataPOST[csrfData['token_name']] = csrfData['hash'];

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/orders/getPricesQuotes',
            data: dataPOST,
            dataType: 'json',
            success: function (response) {  
                $(cTr).find('.price').val(tnhFormatMoney(response.price));
                // $(cTr).find('.price_default').val(response.price);
            }
        });
    }
}

function exportExcelTemplate() {
    code_import = $('#code_import').val();
    if (!code_import) {
        bootbox.alert('Xin vui lòng nhập mã thành phẩm import');
        return;
    }

    if (code_import) {

        var dataPOST = {};
        dataPOST['code_import'] = code_import;
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['export_excel'] = 1;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/orders/exportExcelTemplate',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
}

async function changePrice(ele, item_id, table_price_id) {
    // if (table_price_id) {
        moq = intVal($(ele).find('.quantity').val());
        _hand_input_price = $(ele).find('input.hand_input_price').prop('checked');
        // console.log(_hand_input_price);
        await $.ajax({
            url: site.base_url+'admin/orders/getPricesForItemsNew',
            type: 'GET',
            dataType: 'JSON',
            data: {
                table_price_id: table_price_id,
                item_id: item_id,
                csrf_token_name: hash,
                moq: moq,
                customers: $('#customers').val(),
            },
        })
        .done(function(response) {
            console.log(response);
            if (typeof response.priceItem != "undefined" && response.priceItem != 'none') {
                price_sell = response.priceItem;
                if (!_hand_input_price) {
                    $(ele).find('.price').val(tnhFormatMoney(price_sell));
                    $(ele).find('.price_default').val(price_sell);
                }
                totalOrders();
            }
        });
    // }
}

$(document).ready(function() {
    bs_input_file_multiple();
    $('#branch').select2();
    $('#employees').select2();
    $('#tax_id').select2();
    $('#charge_party').select2();
    $('#note_default').select2();
    $('#staff_coupon').select2({'allowClear': true});
    $('#status_payment').select2({'allowClear': true});

    ajaxSelectParams('#quote_id_chonse', 'admin/quotes/searchPreReferenceNoQuotes', $('#quote_id_chonse').val(), false, true);
    selectAjax($('select#orders_choose'), false, 'admin/orders/searchOrdersPicker');
    selectAjax($('select#productions_orders_choose'), false, 'admin/orders/searchProductionsOrdersPicker');
    $('#type_orders').change(function(event) {
        var _type_orders = $(this).val();
        if (_type_orders == TYPE_SAMPLE_ORDER || _type_orders == TYPE_PTM) {
            $('.orders-type-sample-order').show();
            $('.orders-type-compensate-order').hide();
        } else if (_type_orders == TYPE_COMPENSATE_ORDER) {
            $('.orders-type-sample-order').hide();
            $('.orders-type-compensate-order').show();
        } else if (_type_orders == TYPE_KH_ORDER) {
            $('.orders-type-sample-order').show();
            $('.orders-type-compensate-order').hide();
        } else {
            $('.orders-type-sample-order').hide();
            $('.orders-type-compensate-order').hide();
        }
    });


    // init_editor('textarea[name="note"]');
    if (edit == 0) {
        ajaxSelectCustomerFormatTableCallBack('#customers', 'admin/clients/searchCustomers', $('#customers').val());
    } else if (edit == 1) {
        customer_id = $('#customers').val();
        ajaxSelectParamsCallback('#address_delivery', 'admin/clients/searchAddressDelivery', $('#address_delivery').val(), {'customer_id': customer_id}, true);
        ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', $('#person_contact').val(), {customer_id: customer_id}, true);
    }

    ajaxSelectParamsCallback('#transporters', 'admin/orders/searchSuppliers', $('#transporters').val(), {type: 1}, true);

    $(document).on('change', '.customers', function(event) {
        event.preventDefault();
        data = event.added;
        discount = (typeof data != "undefined") ? data.discount : 0;
        customer_id = $(this).val();
        table_price_default_id = (typeof data != "undefined") ? data.table_price_id : 0;
        table_discount_default_id = (typeof data != "undefined") ? data.id_discount_client : 0;
        ajaxSelectParamsCallback('#address_delivery', 'admin/clients/searchAddressDelivery', 0, {'customer_id': customer_id}, true);
        ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', 0, {customer_id: customer_id}, true);

        $('.orders-separate-guest').hide();
		if (typeof data !== 'undefined') {
			is_separate_guest = data.is_separate_guest;
			if (is_separate_guest > 0) {
				$('.orders-separate-guest').show();
			} else {
				$('.orders-separate-guest').hide();
			}
		}

        if (data.allowed_vat >= 1) {
            taxId = $('#tax_id option[data-rate="10.00"]').val();
            $('#tax_id').val(taxId).trigger('change');
        }

        $('#address_delivery').val(0);
        $('#person_contact').val(0);
        $('#discount_percent').val(discount);
        $('#table_price_id').val(table_price_default_id);
        $('#table_discount_id').val(table_discount_default_id);
        ajaxSelectParams('#table_price_id', 'admin/orders/getTablePriceByCustomer', table_price_default_id, {'customer_id': customer_id}, true);
        // ajaxSelectParams('#table_discount_id', 'admin/orders/getTableDiscountByCustomer', table_discount_default_id, {'customer_id': customer_id});
        tbb = '#tb-orders tbody tr:not("[class^=not-tr]")';
        var nn = $(tbb).length;
        for (iii = 0; iii < nn; iii++)
        {
            element = $(tbb)[iii];
            item_id = $(element).find('input.items_id').val();
            cur_counter = $(element).find('.counter').val();
            if (item_id) {
                // changePriceList(element, item_id, 0);
                // changePrice(element, item_id, table_price_default_id);
                // changeDiscount(element, item_id, table_discount_default_id);

                getPricesQuotes(cur_counter);
            }
        }
        totalOrders();
    });

    $(document).on('click', '.add-address-delivery', function(event) {
        event.preventDefault();
        el = this;
        customer_id = $('#customers').val();
        link = 'javascript:void(0)';
        if (customer_id) {
            link = site.base_url+'admin/clients/addShipping/'+customer_id;
            $.ajax({
                url: link,
                type: 'GET',
                dataType: 'html',
                data: {
                    token: hash
                },
            })
            .done(function(data) {
                $('#tnhModal').html(data);
            })
            .fail(function() {
                console.log("error");
            });
            $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            // $(el).attr('href', link);
        } else {
            bootbox.alert(lang_orders['tnh_please_chosen_customer']);
            // $(el).attr('href', link);
        }
    });

    async function changePriceList(ele, item_id, cal_orders = 1) {
        customers_price_list = $('#customers').val();
        date = $('#date').val();
        if (!customers_price_list) {
            return;
        }

        await $.ajax({
            url: site.base_url+'admin/price_list/getDataPriceList',
            type: 'POSt',
            dataType: 'JSON',
            data: {
                item_id: item_id,
                customers_price_list: customers_price_list,
                date: date,
                csrf_token_name: hash,
            },
        })
        .done(function(response) {
            if (typeof response.result != "undefined" && response.result == 1) {
                price_sell = response.price;
                $(ele).find('.price').val(tnhFormatMoney(price_sell));
                if (cal_orders == 1) {
                    totalOrders();
                }
            }
        });
    }

    

    function changeDiscount(ele, item_id, table_discount_id, itt = 0) {
        return;
        $.ajax({
            url: site.base_url+'admin/orders/getDiscountForItems',
            type: 'GET',
            dataType: 'JSON',
            data: {
                table_discount_id: table_discount_id,
                item_id: item_id,
                csrf_token_name: hash,
            },
        })
        .done(function(response) {
            if (typeof response.discount != "undefined" && response.discount != 'none') {
                dsc = response.discount;
                $(ele).find('.discount_percent_item').val(dsc);
                totalOrders();
            }
        });
    }

    $(document).on('change', '#table_price_id', function(event) {
        event.preventDefault();
        // return;
        table_price_id = $(this).val();
        bootbox.confirm({
            message: lang_orders['tnh_change_table_prices_when_items_price_change'],
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
                    table_price_default_id = table_price_id;
                    tbb = '#tb-orders tbody tr:not("[class^=not-tr]")';
                    var nn = $(tbb).length;
                    for (iii = 0; iii < nn; iii++)
                    {
                        element = $(tbb)[iii];
                        item_id = $(element).find('input.items_id').val();
                        if (item_id) {
                            changePrice(element, item_id, table_price_id);
                        }
                    }
                    totalOrders();
                } else {
                    // if (edit == 0)
                    // {
                        $('#table_price_id').val(table_price_default_id);
                        ajaxSelectParams('#table_price_id', 'admin/orders/getTablePriceByCustomer', table_price_default_id, {'customer_id': customer_id}, true);
                        totalOrders();
                    // }
                }
            }
        });
    });

    $(document).on('change', '#table_discount_id', function(event) {
        event.preventDefault();
        table_discount_id = $(this).val();
        bootbox.confirm({
            message: lang_orders['tnh_change_table_discount_when_items_discount_change'],
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
                    table_discount_default_id = table_discount_id;
                    tbb = '#tb-orders tbody tr:not("[class^=not-tr]")';
                    var nn = $(tbb).length;
                    for (iii = 0; iii < nn; iii++)
                    {
                        element = $(tbb)[iii];
                        item_id = $(element).find('input.items_id').val();
                        if (item_id) {
                            // elementCurrent = $(tbb)[iii];
                            changeDiscount(element, item_id, table_discount_id);
                        }
                    }
                    totalOrders();
                } else {
                    if (edit == 0)
                    {
                        $('#table_discount_id').val(table_discount_default_id);
                        ajaxSelectParams('#table_discount_id', 'admin/orders/getTableDiscountByCustomer', table_discount_default_id, {'customer_id': customer_id});
                        totalOrders();
                    }
                }
            }
        });
    });

    $(document).on('change', '#currencies', function(event) {
        amount_to_vnd = $(this).select2().find(":selected").data("amount_to_vnd");
        $('#amount_to_vnd').val(tnhFormatMoney(amount_to_vnd));
    });

	// var dt = $('#tb-orders').DataTable({
	// 	"language": lang_datatables,
	// 	'searching': false,
	// 	'ordering': false,
	// 	'paging': false,
    //     "info": false,
    //     // 'fixedHeader': true,
    //     // scrollY: true,
	// 	// scrollY: '150px',
	// 	// scrollX: true,
    //     'fnRowCallback': function (nRow, aData, iDisplayIndex) {
    //     },
	// });

    $(document).on('change', 'input.hand_input_price', function(event) {
        isCheckedPrice = $(this).prop('checked');
        cTr = $(this).closest('tr');
        if (isCheckedPrice) {
            cTr.find('.price').removeAttr('readonly');
        } else {
            cTr.find('.price').attr('readonly', 'readonly');
            cur_counter = cTr.find('.counter').val();
            getPricesQuotes(cur_counter);
        }
    });

    $(document).on('change', '.quantity_put', function(event) {
        totalOrders();
        cTr = $(this).closest('tr');
        cur_counter = $(cTr).attr('tr-counter');
        getPricesQuotes(cur_counter);
    });

    $(document).on('change', '.sample_quantity_item', function(event) {
        totalOrders();
    });

    $(document).on('change', 'select.unit', function(event) {
        totalOrders();
    });

    function addRow() {

        type_orders = $('#type_orders').val();

        // <div class="stt text-center"></div>
        tdNumber = `
            <div class="text-right checkbox checkbox-info">
                <input type="checkbox" name="checkbox_item[${counter}]" id="checkbox_item_${counter}" class="checkbox_item" value="1">
                <label for="checkbox_item_${counter}"></label>
            </div>
        `;
        tdOrderCode = `<div>
            <input type="text" name="order_code[${counter}]" placeholder="Mã đơn đặt" class="form-control order_code" value="">
        </div>`;
        tdCommand = `<div>
            <input type="text" name="command[${counter}]" placeholder="Chỉ lệnh" class="form-control command" value="">
        </div>`;
		tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
            <input type="hidden" name="category_id['+counter+']" id="category_id" class="form-control category_id" value="'+counter+'">\
                <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value=""></div>'+
                '<div class="type-item"></div>'+
                '<div><div class="row-options">' +
                        '<a href="javascript:void(0)"class="text-danger delete-remind remove-row">'+lang_core['delete']+'</a>' +
                    '</div>' +
                '</div>';
        tdImage = '<div class="td-image">'+
                    '<div class="preview_image" style="width: auto;">'+
                        '<div class="display-block contract-attachment-wrapper img">'+
                            '<div style="width:45px; margin: auto;">'+
                                '<a href="'+site.base_url+'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">'+
                                    '<div class="">'+
                                        '<img src="'+site.base_url+'assets/images/tnh/no_image.png" style="border-radius: 50%">'+
                                    '</div>'+
                                '</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
            '</div>';
        tdName = `<div class="">
            <input type="text" placeholder="Tên TP của khách hàng" name="product_name_customer[${counter}]" class="form-control" value="">
        </div>`;
        tdModeProduct = `<div class="td-mode-product text-center"></div>`;

        tdUnit = `<div class="td-unit text-center">
            <select name="unit[${counter}]" data-placeholder="ĐVT" id="unit_${counter}" class="unit" style="width: 100%;">
                <option value=""></option>
            </select>
        </div>`;

        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"><div class="show-warehouses text-danger mtop10"></div><div class="show-exchange text-primary mtop5"></div></div>';

        tdQuantityLoss = `<div>
            <input type="text" name="quantity_loss[${counter}]" class="form-control quantity_loss" value="0">
        </div>`;

        tdSampleQuantity = `<div>
            <input type="hidden" name="quantity[${counter}]" class="form-control quantity number-format" style="width: 100%;" value="0">
            <input type="text" name="sample_quantity[${counter}]" class="form-control sample_quantity" readonly value="0">
        </div>`;

        tdTotalQuantityPut = '<div class="td-total-quantity-put text-center"></div>';

        tdTotalQuantity = '<div class="td-total-quantity text-center"></div>';

        tdPrice = `<div class="td-price">
            <input type="hidden" name="price_default" class="form-control price_default" value="0">
            <input type="text" name="price[${counter}]" id="price[]" class="form-control price money-format" readonly style="width: 100%;" value="0">
            <div class="checkbox checkbox-info" style="margin-top: 5px;">
                <input type="checkbox" id="hand_input_price_${counter}" class="hand_input_price" name="hand_input_price[${counter}]" value="1">
                <label for="hand_input_price_${counter}">Nhập tay</label>
            </div>
            <div class="checkbox checkbox-danger mtop5">
                <input type="checkbox" name="is_lot[${counter}]" onchange="totalOrders()" id="is_lot_${counter}" class="is_lot" value="1">
                <label for="is_lot_${counter}">Giá theo lô</label>
            </div>
        </div>`;
        tdTotalAmount = '<div class="td-total-amount text-right">';
        tdTaxItems = '<div class="td-tax">'+
            '<select name="tax_item['+counter+']" id="tax_item" class="tax_item" data-placeholder="'+lang_core['tax']+'" style="width: 100%;">'+getTaxs(0)+'</select>'+
        '</div>';
        tdDisPercent = '<div class="td-dis-percent">'+
            '<input type="text" name="discount_percent_item['+counter+']" id="discount_percent_item" class="form-control discount_percent_item number-format" value="0" style="width: 100%;">'+
        '</div>';
        tdDisDirect = '<div class="td-dis-direct">'+
            '<input type="text" name="discount_direct_item['+counter+']" id="discount_direct_item[]" class="form-control discount_direct_item money-format" style="width: 100px;" value="0">'+
        '</div>';
        tdGrandTotal = '<div class="td-grand-total text-right"></div></div><div class="td-total-cost-amount text-right text-danger"></div>';

        tdSize = `<div class="text-center td-size"></div>`;
        tdLoss = `<div class="text-center td-loss"></div>`;
        
        // <div class="col-md-5" style="padding: 0px;"><input type="text" style="width: 100%;" name="quantity_sub[${counter}][]" id="input" class="form-control quantity_sub number-format" value="0" title=""></div>
        tdShipping = `<div class="td-date">
            <div class="sub">
                <div class="sb">
                    <div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub[${counter}][]" class="form-control datepicker date_sub" autocomplete="off" placeholder="${lang_core['date']}" value="" style="width: 100%;" title=""></div>
                </div>
            </div>
            <div class="text-danger show-errors"></div>
        </div>`;

        tdShippingQuantity = `<div class="td-date-ship">
                                <div class="subShip" data-counter="${counter}">
                                    <div class="sb mtop5">
                                        <div class="col-md-12 input-group" style="padding: 0px; padding-right: 5px;">
                                            <input type="text" name="ship[${counter}][date][]" class="form-control datepicker date_ship" autocomplete="off" placeholder="${lang_core['date']}" value="" style="width: 150px">
                                            <span class="input-group-addon" style="padding: 0 0;border: 0px solid black;">
                                                <input type="text" name="ship[${counter}][quantity][]" class="form-control quantity_ship" min="0" autocomplete="off" placeholder="Số lượng" value="" style="width: 100px">
                                            </span>
                                            <span class="input-group-addon" style="padding-left: 5px;padding-right: 5px;border: 0px solid black;">
                                                <a class="btn btn-danger removeSubShip"><i class="fa fa-remove"></i></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-danger show-errors"></div>
                                <a class="btn createSubShip">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                            <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                    </svg>
                                </a>
                            </div>`;

        tdNote = '<div class="td-note"><textarea name="note_items['+counter+']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

        // <td>${tdModeProduct}</td>
        // <td>${tdDisPercent}</td>
        // <td>${tdSize}</td>

        trHtml = `<tr>
            <td>${tdNumber}</td>
            <td>${tdCode}</td>
            <td>${tdImage}</td>
            <td>${tdName}</td>
            <td>${tdUnit}</td>
            <td>${tdSampleQuantity}</td>
            <td>${tdTotalQuantityPut}</td>
            <td>${tdTotalQuantity}</td>
            <td>${tdPrice}</td>
            <td>${tdTotalAmount}</td>
            <td>${tdLoss}</td>
            <td>${tdShipping}</td>
            <td>${tdShippingQuantity}</td>
            <td>${tdNote}</td>
            <td>${tdActions}</td>
        </tr>`;
        $('#tb-orders tbody:not("[class^=child]")').append(trHtml);

        if (type_orders == ORDER_CHANGE || type_orders == ORDER_DEFAULT || type_orders) {
            tableChild = `<tr id="tr-child-${counter}" class="not-tr">
                <td colspan="20">
                </td>
            </tr>`;
            $('#tb-orders tbody:not("[class^=child]")').append(tableChild);

            // tableChildSize = `<tr id="tr-child-${counter}" class="not-tr">
            //     <td colspan="20">
            //         <table class="table table-child" style="width: 50%; margin-left: 50px !important;">
            //             <thead>
            //                 <tr class="not-tr">
            //                     <th class="text-center" style="width: 50px;">
            //                         <a href="javascript:void(0)" onclick="addChild(this, ${counter})"><i class="fa fa-plus"></i></a>
            //                     </th>
            //                     <th class="text-center" style="width: 120px;">Size SP</th>
            //                     <th class="text-center" style="width: 120px;">Size ĐC</th>
            //                     <th class="text-center" style="width: 120px;">Style Number</th>
            //                     <th class="text-center" style="width: 120px;">Color</th>
            //                     <th class="text-center" style="width: 100px;">Số lượng</th>
            //                     <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
            //                 </tr>
            //             </thead>
            //             <tbody class="child">
            //             </tbody>
            //         </table>
            //     </td>
            // </tr>`;
            // $('#tb-orders tbody:not("[class^=child]")').append(tableChildSize);
        } else if (type_orders == ORDER_CHANGE_SIZE) {
            tableChildSize = `<tr id="tr-child-${counter}" class="not-tr">
                <td colspan="20">
                    <table class="table table-child-size table-child-size-${counter}" style="width: 50%; margin-left: 50px !important;">
                        <thead>
                            <tr class="not-tr">
                                <th class="text-center" style="width: 50px;">
                                    <a href="javascript:void(0)" onclick="addChildChangeSize(this, ${counter})"><i class="fa fa-plus"></i></a>
                                </th>
                                <th class="text-center" style="width: 120px;">Số Size</th>
                                <th class="text-center" style="width: 120px;">Số lượng</th>
                                <th class="text-center" style="width: 120px;">Tờ chẵn</th>
                                <th class="text-center" style="width: 120px;">Tờ lẻ</th>
                                <th class="text-center" style="width: 100px;">Kiện chẵn</th>
                                <th class="text-center" style="width: 100px;">Kiện lẻ</th>
                                <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                            </tr>
                        </thead>
                        <tbody class="child">
                        </tbody>
                    </table>
                </td>
            </tr>`;
            $('#tb-orders tbody:not("[class^=child]")').append(tableChildSize);
        }

        ajaxSelectCallBack($('#items_'+ counter +''), 'admin/products/searchProductAndGoodsMaterials', 0);
        $('select.tax_item').select2();
        $('#unit_'+counter).select2();
        formatNumberPlugin();
        formatMoneyPlugin();
        counter++;
        totalOrders();
        init_datepicker();
    }

    $('.add-row').on('click', function(event) {
        event.preventDefault();
        addRow();
    });

    $('body').on('click', '.removeSubShip', function() {
        $(this).parents('.sb').remove();
    })


    $('body').on('change', '.quantity_ship', function() {
        var tr = $(this).parents('tr');
        var total_quantity = intVal(tr.find('.td-total-quantity').text());

        var all_quantity = 0;
        var allQuantity = tr.find('.quantity_ship');
        $.each(allQuantity, function(index, value) {
            var quantity = intVal($(value).val());
            all_quantity += quantity;
        })
        if(total_quantity < all_quantity) {
            $('.td-date-ship').find('.show-errors').html('Tổng số lượng chi tiết giao hàng không được lớn hơn tổng số lượng');
            $('.td-date-ship').find('.show-errors').addClass('errors');
        }
        else {
            $('.td-date-ship').find('.show-errors').html('');
            $('.td-date-ship').find('.show-errors').removeClass('errors');
        }
    })




    $('body').on('click', '.createSubShip', function() {
        var divDate = $(this).parents('.td-date-ship');
        var _counter = divDate.find('.subShip').attr('data-counter');
        divDate.find('.subShip').append(`<div class="sb mtop5">
                                            <div class="col-md-12 input-group" style="padding: 0px; padding-right: 5px;">
                                                <input type="text" name="ship[${_counter}][date][]" class="form-control datepicker date_ship" autocomplete="off" placeholder="Ngày" value="" style="width: 150px">
                                                <span class="input-group-addon" style="padding: 0 0;border: 0px solid black;">
                                                    <input type="text" name="ship[${_counter}][quantity][]" class="form-control quantity_ship" min="0" autocomplete="off" placeholder="Số lượng" value="" style="width: 100px">
                                                </span>
                                                <span class="input-group-addon" style="padding-left: 5px;padding-right: 5px;border: 0px solid black;">
                                                    <a class="btn btn-danger removeSubShip"><i class="fa fa-remove"></i></a>
                                                </span>
                                            </div>
                                        </div>`);
        init_datepicker();
    })





    $('#type_orders').on('change', function(event) {
        thNumber = `<th class="text-center" style="width: 30px;">
            <a class="hover-svg dropdown-toggle add-row" id="dropdownMenu-add" data-toggle="dropdown" href="javascript:void(0)">
                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                    <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                </svg>
            </a>
        </th>`;

        thProductCode = `<th style="width: 150px;" class="text-center">${lang_orders["tnh_product_code"]}</th>`;
        thImages = `<th style="width: 50px" class="text-center">${lang_orders["tnh_images"]}</th>`;
        thProductName = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_product_name"]}</th>`;
        thProductUnit = `<th style="width: 50px;" class="text-center">${lang_orders["tnh_unit"]}</th>`;
        thProductQuantity = `<th style="width: 100px;" class="text-center">${lang_orders["quantity"]}</th>`;
        thProductUnitPrice = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_unit_price"]}</th>`;
        thProductDiscountPercent = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_discount_percent"]}</th>`;
        thProductTotalAmount = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_total_amount"]}</th>`;
        thProductSize = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_size"]}</th>`;
        thProductLoss = `<th style="width: 100px;" class="text-center">${lang_orders["tnh_loss"]}</th>`;
        thProductShipment = `<th style="width: 200px;" class="text-center">${lang_orders["cong_shipment_date"]}</th>`;
        thProductNote = `<th style="width: 200px;" class="text-center">${lang_orders["note"]}</th>`;
        thProductActions = `<th style="width: 30px;" class="text-center">${lang_orders["actions"]}</th>`;

        trHead = `<tr>
            ${thNumber}
            ${thProductCode}
            ${thImages}
            ${thProductName}
            ${thProductUnit}
            ${thProductQuantity}
            ${thProductUnitPrice}
            ${thProductDiscountPercent}
            ${thProductTotalAmount}
            ${thProductSize}
            ${thProductLoss}
            ${thProductShipment}
            ${thProductNote}
            ${thProductActions}
        </tr>`;
        // $('#tb-orders thead').html(trHead);
        // $('#tb-orders tbody').html('');
        // addRow();
    });

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        var currentQuantityInput = $(event.currentTarget);
        data = $(currentQuantityInput).select2('data');
        // console.log(data);
        sl = this;
        tr = $(sl).closest('tr');
        item_id = $(sl).val();
        counter_index = tr.find('.counter').val();
        table_price_id = $('#table_price_id').val();
        table_discount_id = $('#table_discount_id').val();

        if (item_id) {
            customers_price_list = $('#customers').val();
            date = $('#date').val();

            $.ajax({
                url: site.base_url+'admin/orders/getPricesForItems',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    table_price_id: table_price_id,
                    item_id: item_id,
                    csrf_token_name: hash,
                    customers_price_list: customers_price_list,
                    date: date,
                    counter_index: counter_index,
                },
            })
            .done(function(response) {
                category_id = data.category_id;
                name = data.name_customer;
                images = data.images;
                unit = data.unit_name;
                sizeN = data.size_name;
                loss = data.loss;
                mode_product = data.mode_product;

                if (typeof response.priceItem != "undefined" && response.priceItem != 'none') {
                    price_sell = response.priceItem;
                } else {
                    price_sell = data.price_sell;
                }
                type_item = item_id.split('__')[1];
                if (images) {
                    tr.find('.td-image a').attr('href', site.base_url+images);
                    tr.find('.td-image img').attr('src', site.base_url+images);
                } else {
                    tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                    tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
                }
                tr.find('.category_id').val(category_id);
                tr.find('.td-item-name').html(name);
                // tr.find('.td-unit').html(unit);

                arrUnit = response.arrUnit;
                optionUnit = '<option></option>';
                if (arrUnit.length > 0) {
                    $.each(arrUnit, function (index, value) { 
                        optionUnit+= '<option data-conversion_quantity_unit="'+value['conversion_quantity_unit']+'" '+value['selected']+' value="'+value.id+'">'+value.text+'</option>';
                    });
                }
                tr.find('select.unit').html(optionUnit);
                tr.find('select.unit').val(response.isSelected).select2();

                tr.find('.price').val(tnhFormatMoney(price_sell));

                var strMoreProduct = '';
                if (type_item == "products") {
                    strMoreProduct = `
                        <div>SL con/tờ: <span class="quantity_child_sheet">${data.quantity_child_sheet}</span></div>
                        <div>SL tờ/kiện: <span class="quantity_sheet_bale">${data.quantity_sheet_bale}</div>
                    `;
                    tr.find('.type-item').html(strMoreProduct+'<span class="label label-success">'+lang_core[type_item]+'</span>');

                    
                } else if (type_item == "items") {
                    tr.find('.type-item').html('<span class="label label-primary">'+lang_core[type_item]+'</span>');
                } else if (type_item == "materials") {
                    tr.find('.type-item').html('<span class="label label-warning">'+lang_core[type_item]+'</span>');
                }

                quantityWarehouse = response.quantity_warehouse;
                tr.find('.show-warehouses').html(lang_orders['tnh_qty_warehoused']+': '+tnhFormatNumber(quantityWarehouse));

                // show-exchange
                tr.find('.show-exchange').html(response.htmlExchange);

                tr.find('.td-size').html(sizeN);
                tr.find('.td-loss').html(loss);
                tr.find('.td-mode-product').html(mode_product);

                //Lấy giá vốn
                // loadCostPrice(item_id, 0, tr);
                //
                // changePriceList(tr, item_id, 1);
                changePrice(tr, item_id, table_price_id);

                c_counter = tr.find('.counter').val();
                $('#tr-child-'+c_counter+ ' td').html(response.html_sub);

                getPricesQuotes(counter_index);

                lastrow = $('#tb-orders tbody tr:not("[class^=not-tr]")')[$('#tb-orders tbody tr:not("[class^=not-tr]")').length - 1];
                if ($(lastrow).find('.items_id').select2('val')) {
                    // $('.add-row').click();
                }
               
            })
            .fail(function() {
                console.log("error");
            });

            changeDiscount(tr, item_id, table_discount_id);
        } else {
            tr.find('.td-item-name').html(lang_core['product_name']);
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');

            tr.find('select.unit').html('<option></option>');
            tr.find('select.unit').val(null).select2();
            // tr.find('.td-unit').html('');
            tr.find('.type-item').html('');
        }

        $('.checkbox_item').trigger('change');
    });


    $(document).on('change', '.quantity, .price, .quantity_sub, .tax_item, .discount_percent_item, .discount_direct_item, .tax_id, #discount_percent, #discount_direct, #cost_delivery, #charge_party, .sample_quantity, .quantity_loss', function(event) {
        totalOrders();
    });

    $(document).on('change', '.quantity', function(){
        ctr = $(this).closest('tr');
        itemId = ctr.find('input.items_id').val();
        tQty = $(this).val();
        loadCostPrice(itemId, tQty, ctr);
    });

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.sb').remove();
        totalOrders();
    });

	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
        tr = $(this).closest('tr');
        counter_index = tr.find('.counter').val();
        tr.remove();
        $('#tr-child-'+counter_index).remove();
		// dt.row( $(this).parents('tr') ).remove().draw();
		totalOrders();
        $('.checkbox_item').trigger('change');
	});

    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        // $('.add-row').click();
    });

    // $(document).on('change', '.checkbox_item', function(event) {
    //     cTr = $(this).closest('tr');
    //     $('#code_import').val('');
    //     isChecked = $(this).prop('checked');
    //     $('input.checkbox_item').prop('checked', false);
    //     if (isChecked) {
    //         $(this).prop('checked', true);
    //         dataItem = $(cTr).find('input.items_id').select2('data');
    //         if (typeof dataItem !== 'undefined') {
    //             item_code = dataItem.item_code;
    //             $('#code_import').val(item_code);
    //         } else {
    //             alert_float('danger', 'Vui lòng chọn mặt hàng trước khi chọn');
    //         }
    //     }
    // });

    $(document).on('change', '.checkbox_item', function(event) {
        var checkbox_item = $('input.checkbox_item:checked');
        var codeStrim = [];
        $('#code_import').val('');
        $.each(checkbox_item, function(index, value) {
            dataItem = $(value).parents('tr').find('input.items_id').select2('data');
            if (typeof dataItem !== 'undefined') {
                try {
                    codeStrim.push(dataItem.item_code);
                }
                catch (e) {

                }

            } else {
                alert_float('danger', 'Vui lòng chọn mặt hàng trước khi chọn');
            }
        })
        if(codeStrim.length > 0) {
            $('#code_import').val(codeStrim);
        }
    });



    if (edit == 0) {
        $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/orders/refereshReferenceOrders',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    token: hash,
                    'referesh': 1
                },
            })
            .done(function(data) {
                if (data) {
                    $('#reference_no').val(data.reference_no);
                    alert_float('success', data.message);
                } else {
                    alert_float('danger', 'fail');
                }
            })
            .fail(function() {
                console.log("error");
            });
        });
    } else if (edit == 1) {
        for (i = 0; i < counter; i++) {
            // ajaxSelectCallBack($('#items_'+ i +''), 'admin/products/searchProductAndGoods', $('#items_'+ i +'').val());
            ajaxSelectCallBack($('#items_'+ i +''), 'admin/products/searchProductAndGoodsMaterials', $('#items_'+ i +'').val());
            init_editor('#info'+i+'');
        }

        customer_id = $('#customers').val();
        ajaxSelectParams('#table_price_id', 'admin/orders/getTablePriceByCustomer', table_price_default_id, {'customer_id': customer_id}, true);
        init_datepicker();
        $('select.tax_item').select2();
        $('select.size_sp').select2();
        $('select.color').select2();
        $('select.unit').select2();
    }


    // $(document).on('click', '.chosen_promotions', function(event) {
    //     if ($(this).is(':checked')) {
    //     }
    // });

    $('select.currencies').select2();
    $('select.type_orders').select2();
    $('select.status_orders').select2({'allowClear': true});
    $('select.type_items').select2({'allowClear': true});

    //validation
	appValidateForm($('#orders'), {
		reference_no: 'required',
        date: 'required',
        customers: 'required',
        currencies: 'required',
        amount_to_vnd: 'required',
        type_orders: 'required',
        id_branch: 'required'
        // employees: 'required',
        // person_contact: 'required',
    }, db);

    //save db
    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', lang_core['check_date_enter']);
            return;
        }

        type_orders = $('#type_orders').val();
        flagCheck = false;
        if (type_orders == TYPE_PTM) {
            tb = '#tb-orders tbody tr:not("[class^=not-tr]")';
            var n = $(tb).length;
            for (ii = 0; ii < n; ii++)
            {
                element = $(tb)[ii];
                _items_id = $(element).find('input.items_id').val();
                total_quantity = intVal($(element).find('.td-total-quantity').html());
                if (total_quantity < QUANTITY_PTM && _items_id) {
                    alert_float('danger', 'Loại đơn hàng phát triển mẫu vui lòng tổng số lượng lớn hơn bằng 200');
                    flagCheck = true;
                    return;
                }
            }
        }

        if (flagCheck) {
            return;
        }

    	$('.add-order').attr('disabled', 'disabled');
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

        ktQuantityShip();
        if($('.td-date-ship').find('.show-errors.errors').length > 0) {
            $('.add-order').removeAttr('disabled', 'disabled');
            return false;
        }

        $.ajax({
            // url : site.base_url+'admin/business_plan/add',
        	url : url,
        	type : 'POST',
        	dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
        	data: formData,
        })
        .done(function(data) {
        	if (data.result) {
        		alert_float('success', data.message);
                if (data?.action == 'add') {
                    new_task(site.base_url+'admin/tasks/task?order_id='+data?.order_id);
                } else {
                    if (data.gift == 1) {
                        window.location.href = data.linkGift;
                    } else {
                        window.location.href = site.base_url+'admin/orders';
                    }
                }
        	} else {
        		alert_float('danger', data.message);
        		$('.add-order').removeAttr('disabled', 'disabled');
        	}
        })
        .fail(function() {
            alert_float('danger', lang_core['errors']);
        	$('.add-order').removeAttr('disabled', 'disabled');
        });
        return false;
    }
});

function ktQuantityShip() {
    var tr = $('#tb-orders').find('tbody').find('tr');
    $.each(tr, function(index, value) {
        var total_quantity = intVal(tr.find('.td-total-quantity').text());
        var all_quantity = 0;
        var allQuantity = tr.find('.quantity_ship');
        $.each(allQuantity, function (index, value) {
            var quantity = intVal($(value).val());
            all_quantity += quantity;
        })
        if (total_quantity < all_quantity) {
            $('.td-date-ship').find('.show-errors').html('Tổng số lượng chi tiết giao hàng không được lớn hơn tổng số lượng');
            $('.td-date-ship').find('.show-errors').addClass('errors');
        } else {
            $('.td-date-ship').find('.show-errors').html('');
            $('.td-date-ship').find('.show-errors').removeClass('errors');
        }
    })
}

$('body').on('change', '#SearchQR', function (e) {
    var code = $(this).val();
    if (code) {
        $.ajax({
            url: site.base_url + 'admin/orders/searchQR',
            type: 'POST',
            dataType: 'JSON',
            data: {
                code: code,
                csrf_token_name: hash,
            },
        })
            .done(function (data) {
               if (data.result){
                   alert_float('success',data.message);
                   createTrItemAuto(data.items);
               } else {
                   alert_float('danger',data.message);
               }
            })
            .fail(function () {
            });
    }
    $('#SearchQR').val('');
})

function createTrItemAuto(item) {
    // console.log(item);
    type_orders = $('#type_orders').val();

    tdNumber = `
        <div class="text-right checkbox checkbox-info">
            <input type="checkbox" name="checkbox_item[${counter}]" id="checkbox_item_${counter}" class="checkbox_item" value="1">
            <label for="checkbox_item_${counter}"></label>
        </div>
    `;
    tdOrderCode = `<div>
        <input type="text" name="order_code[${counter}]" placeholder="Mã đơn đặt" class="form-control order_code" value="">
    </div>`;
    tdCommand = `<div>
        <input type="text" name="command[${counter}]" placeholder="Chỉ lệnh" class="form-control command" value="">
    </div>`;
    tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
        <input type="hidden" name="category_id['+counter+']" id="category_id" class="form-control category_id" value="'+counter+'">\
            <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value=""></div>'+
            '<div class="type-item"></div>'+
            '<div><div class="row-options">' +
                    '<a href="javascript:void(0)"class="text-danger delete-remind remove-row">'+lang_core['delete']+'</a>' +
                '</div>' +
            '</div>';
    tdImage = '<div class="td-image">'+
                '<div class="preview_image" style="width: auto;">'+
                    '<div class="display-block contract-attachment-wrapper img">'+
                        '<div style="width:45px; margin: auto;">'+
                            '<a href="'+site.base_url+'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">'+
                                '<div class="">'+
                                    '<img src="'+site.base_url+'assets/images/tnh/no_image.png" style="border-radius: 50%">'+
                                '</div>'+
                            '</a>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
        '</div>';
    tdName = `<div class="">
        <input type="text" placeholder="Tên TP của khách hàng" name="product_name_customer[${counter}]" class="form-control" value="">
    </div>`;
    tdModeProduct = `<div class="td-mode-product text-center"></div>`;

    tdUnit = `<div class="td-unit text-center">
        <select name="unit[${counter}]" data-placeholder="ĐVT" id="unit_${counter}" class="unit" style="width: 100%;">
            <option value=""></option>
        </select>
    </div>`;

    tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"><div class="show-warehouses text-danger mtop10"></div><div class="show-exchange text-primary mtop5"></div></div>';

    tdQuantityLoss = `<div>
        <input type="text" name="quantity_loss[${counter}]" class="form-control quantity_loss" value="0">
    </div>`;

    tdSampleQuantity = `<div>
        <input type="hidden" name="quantity[${counter}]" class="form-control quantity number-format" style="width: 100%;" value="0">
        <input type="text" name="sample_quantity[${counter}]" class="form-control sample_quantity" readonly value="0">
    </div>`;

    tdTotalQuantityPut = '<div class="td-total-quantity-put text-center"></div>';

    tdTotalQuantity = '<div class="td-total-quantity text-center"></div>';

    tdPrice = `<div class="td-price">
        <input type="text" name="price[${counter}]" id="price[]" class="form-control price money-format" readonly style="width: 100%;" value="0">
        <div class="checkbox checkbox-info" style="margin-top: 5px;">
            <input type="checkbox" id="hand_input_price_${counter}" class="hand_input_price" name="hand_input_price[${counter}]" value="1">
            <label for="hand_input_price_${counter}">Nhập tay</label>
        </div>
        <div class="checkbox checkbox-danger mtop5">
            <input type="checkbox" name="is_lot[${counter}]" onchange="totalOrders()" id="is_lot_${counter}" class="is_lot" value="1">
            <label for="is_lot_${counter}">Giá theo lô</label>
        </div>
    </div>`;
    tdTotalAmount = '<div class="td-total-amount text-right">';
    tdTaxItems = '<div class="td-tax">'+
        '<select name="tax_item['+counter+']" id="tax_item" class="tax_item" data-placeholder="'+lang_core['tax']+'" style="width: 100%;">'+getTaxs(0)+'</select>'+
    '</div>';
    tdDisPercent = '<div class="td-dis-percent">'+
        '<input type="text" name="discount_percent_item['+counter+']" id="discount_percent_item" class="form-control discount_percent_item number-format" value="0" style="width: 100%;">'+
    '</div>';
    tdDisDirect = '<div class="td-dis-direct">'+
        '<input type="text" name="discount_direct_item['+counter+']" id="discount_direct_item[]" class="form-control discount_direct_item money-format" style="width: 100px;" value="0">'+
    '</div>';
    tdGrandTotal = '<div class="td-grand-total text-right"></div></div><div class="td-total-cost-amount text-right text-danger"></div>';

    tdSize = `<div class="text-center td-size"></div>`;
    tdLoss = `<div class="text-center td-loss"></div>`;
    
    tdShipping = `<div class="td-date">
        <div class="sub">
            <div class="sb">
                <div class="col-md-12" style="padding: 0px; padding-right: 5px;"><input type="text" name="date_sub[${counter}][]" class="form-control datepicker date_sub" autocomplete="off" placeholder="${lang_core['date']}" value="" style="width: 100%;" title=""></div>
            </div>
        </div>
        <div class="text-danger show-errors"></div>
    </div>`;

    tdShippingQuantity = `<div class="td-date-ship">
                            <div class="subShip" data-counter="${counter}">
                                <div class="sb mtop5">
                                    <div class="col-md-12 input-group" style="padding: 0px; padding-right: 5px;">
                                        <input type="text" name="ship[${counter}][date][]" class="form-control datepicker date_ship" autocomplete="off" placeholder="${lang_core['date']}" value="" style="width: 150px">
                                        <span class="input-group-addon" style="padding: 0 0;border: 0px solid black;">
                                            <input type="text" name="ship[${counter}][quantity][]" class="form-control quantity_ship" min="0" autocomplete="off" placeholder="Số lượng" value="" style="width: 100px">
                                        </span>
                                        <span class="input-group-addon" style="padding-left: 5px;padding-right: 5px;border: 0px solid black;">
                                            <a class="btn btn-danger removeSubShip"><i class="fa fa-remove"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-danger show-errors"></div>
                            <a class="btn createSubShip">
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="15" cy="15.0001" r="15" fill="#0E5DAB"></circle>
                                        <path d="M13.9048 16.2381L9.52974 16.2381C9.04634 16.2381 8.65448 15.8462 8.65486 15.3632L8.65486 14.78C8.65486 14.2964 9.04672 13.9046 9.52981 13.905L13.9047 13.905L13.9043 9.52957C13.9043 9.04617 14.2962 8.65431 14.7792 8.65469L15.3629 8.65424C15.8464 8.65431 16.2382 9.04617 16.2379 9.52919L16.2379 13.905L20.6128 13.905C21.0963 13.905 21.4882 14.2969 21.4877 14.78L21.4877 15.3632C21.4877 15.8466 21.0959 16.2385 20.6128 16.2381L16.2378 16.2381L16.2379 20.6131C16.2378 21.0966 15.8459 21.4884 15.3629 21.4881H14.7797C14.2962 21.488 13.9043 21.0961 13.9047 20.6131L13.9048 16.2381Z" fill="white"></path>
                                </svg>
                            </a>
                        </div>`;

    tdNote = '<div class="td-note"><textarea name="note_items['+counter+']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
    tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

    $('#tb-orders tbody tr input.items_id').each(function() {
        var selectValue = $(this).val();
        if (selectValue === '') {
            console.log( $(this));
            $(this).closest('tr').find('.remove-row').click();
        }
    });
    trHtml = `<tr>
        <td>${tdNumber}</td>
        <td>${tdCode}</td>
        <td>${tdImage}</td>
        <td>${tdName}</td>
        <td>${tdUnit}</td>
        <td>${tdSampleQuantity}</td>
        <td>${tdTotalQuantityPut}</td>
        <td>${tdTotalQuantity}</td>
        <td>${tdPrice}</td>
        <td>${tdTotalAmount}</td>
        <td>${tdLoss}</td>
        <td>${tdShipping}</td>
        <td>${tdShippingQuantity}</td>
        <td>${tdNote}</td>
        <td>${tdActions}</td>
    </tr>`;
    $('#tb-orders tbody:not("[class^=child]")').append(trHtml);

    if (type_orders == ORDER_CHANGE || type_orders == ORDER_DEFAULT || type_orders) {
        tableChild = `<tr id="tr-child-${counter}" class="not-tr">
            <td colspan="20">
            </td>
        </tr>`;
        $('#tb-orders tbody:not("[class^=child]")').append(tableChild);

    } else if (type_orders == ORDER_CHANGE_SIZE) {
        tableChildSize = `<tr id="tr-child-${counter}" class="not-tr">
            <td colspan="20">
                <table class="table table-child-size table-child-size-${counter}" style="width: 50%; margin-left: 50px !important;">
                    <thead>
                        <tr class="not-tr">
                            <th class="text-center" style="width: 50px;">
                                <a href="javascript:void(0)" onclick="addChildChangeSize(this, ${counter})"><i class="fa fa-plus"></i></a>
                            </th>
                            <th class="text-center" style="width: 120px;">Số Size</th>
                            <th class="text-center" style="width: 120px;">Số lượng</th>
                            <th class="text-center" style="width: 120px;">Tờ chẵn</th>
                            <th class="text-center" style="width: 120px;">Tờ lẻ</th>
                            <th class="text-center" style="width: 100px;">Kiện chẵn</th>
                            <th class="text-center" style="width: 100px;">Kiện lẻ</th>
                            <th class="text-center" style="width: 50px;"><i class="fa fa-trash-o"></i></th>
                        </tr>
                    </thead>
                    <tbody class="child">
                    </tbody>
                </table>
            </td>
        </tr>`;
        $('#tb-orders tbody:not("[class^=child]")').append(tableChildSize);
    }

    if (item.type == 'product') {
        item.type = 'products'
    } else if (item.type == 'materials'){
        item.type = 'items'
    }
    item_id = item.id + '__' + item.type;
    ajaxSelectCallBack($('#items_'+ counter +''), 'admin/products/searchProductAndGoodsMaterials', item_id);
    $('select.tax_item').select2();
    $('#unit_'+counter).select2();
    formatNumberPlugin();
    formatMoneyPlugin();
    var rowCounter = counter;
    counter++;
    totalOrders();
    init_datepicker();

    table_discount_id = $('#table_discount_id').val();
    tr = $('#items_'+ rowCounter +'').closest('tr');
    // tr = $('tr:has(#items_'+ rowCounter +')');
    if (item_id) {
        table_price_id = $('#table_price_id').val();
        customers_price_list = $('#customers').val();
        date = $('#date').val();
        $.ajax({
            url: site.base_url+'admin/orders/getPricesForItems',
            type: 'GET',
            dataType: 'JSON',
            data: {
                table_price_id: table_price_id,
                item_id: item_id,
                csrf_token_name: hash,
                customers_price_list: customers_price_list,
                date: date,
                counter_index: rowCounter,
            },
        }).done(function(response) {
            category_id = item.category_id+'__'+item.type;
            name = item.product_name_customer;
            images = item.avatar;
            unit = item.unit_name;
            sizeN = item.size_name || '';
            loss = item.loss || '';
            mode_product = item.mode_product || '';

            if (typeof response.priceItem != "undefined" && response.priceItem != 'none') {
                price_sell = response.priceItem;
            } else {
                price_sell = item.price_sell || 0;
            }
            type_item = item_id.split('__')[1];
            if (images) {
                tr.find('.td-image a').attr('href', images);
                tr.find('.td-image img').attr('src', images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            }
            tr.find('.category_id').val(category_id);
            tr.find('.td-item-name').html(name);
            // tr.find('.td-unit').html(unit);

            arrUnit = response.arrUnit;
            optionUnit = '<option></option>';
            if (arrUnit.length > 0) {
                $.each(arrUnit, function (index, value) { 
                    optionUnit+= '<option '+value['selected']+' value="'+value.id+'">'+value.text+'</option>';
                });
            }
            tr.find('select.unit').html(optionUnit);
            tr.find('select.unit').val(response.isSelected).select2();

            tr.find('.price').val(tnhFormatMoney(price_sell));

            var strMoreProduct = '';
            if (type_item == "products") {
                strMoreProduct = `
                    <div>SL con/tờ: <span class="quantity_child_sheet">${item.quantity_child_sheet || ''}</span></div>
                    <div>SL tờ/kiện: <span class="quantity_sheet_bale">${item.quantity_sheet_bale || ''}</div>
                `;
                tr.find('.type-item').html(strMoreProduct+'<span class="label label-success">'+lang_core[type_item]+'</span>');

                
            } else if (type_item == "items") {
                tr.find('.type-item').html('<span class="label label-primary">'+lang_core[type_item]+'</span>');
            } else if (type_item == "materials") {
                tr.find('.type-item').html('<span class="label label-warning">'+lang_core[type_item]+'</span>');
            }

            quantityWarehouse = response.quantity_warehouse;
            tr.find('.show-warehouses').html(lang_orders['tnh_qty_warehoused']+': '+tnhFormatNumber(quantityWarehouse));

            // show-exchange
            tr.find('.show-exchange').html(response.htmlExchange);

            tr.find('.td-size').html(sizeN);
            tr.find('.td-loss').html(loss);
            tr.find('.td-mode-product').html(mode_product);

            //Lấy giá vốn
            // loadCostPrice(item_id, 0, tr);
            //
            // changePriceList(tr, item_id, 1);
            changePrice(tr, item_id, table_price_id);

            c_counter = tr.find('.counter').val();
            $('#tr-child-'+c_counter+ ' td').html(response.html_sub);

            getPricesQuotes(rowCounter);

            lastrow = $('#tb-orders tbody tr:not("[class^=not-tr]")')[$('#tb-orders tbody tr:not("[class^=not-tr]")').length - 1];
            if ($(lastrow).find('.items_id').select2('val')) {
                // $('.add-row').click();
            }
           
        })
        .fail(function() {
            console.log("error");
        });

        // changeDiscount(tr, item_id, table_discount_id);
    } else {
        tr.find('.td-item-name').html(lang_core['product_name']);
        tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
        tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');

        tr.find('select.unit').html('<option></option>');
        tr.find('select.unit').val(null).select2();
        // tr.find('.td-unit').html('');
        tr.find('.type-item').html('');
    }
    $('.checkbox_item').trigger('change');
}