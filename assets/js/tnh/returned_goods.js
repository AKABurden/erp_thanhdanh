function totalReturnedGoods()
{
    tb = '#tb-returned-goods tbody tr';
    var n = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    var total_amount = 0;
    var total_tax_item = 0;
    var total_discount_percent_item = 0;
    var total_discount_direct_item = 0;
    var total_grand_total_item = 0;
    arr_category_id = [];
    arr_id = [];
    arr_info = [];
    
    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        quantity = intVal($(element).find('.quantity').val());
        price = intVal($(element).find('.price').val());
        amount = quantity * price;
        console.log(123);

        //sub date delivery
        quantity_sub = 0;
        $.each($(element).find('.quantity_sub'), function(index, el) {
            quantity_sub+= intVal($(el).val());
        });
        if (quantity_sub > quantity) {
            $(element).find('.show-errors').html(lang_core['total_quantity_less']+ formatNumberTnh(quantity));
            count_errors++;
        } else {
            $(element).find('.show-errors').html('');
        }
        //end sub date delivery

        $(element).find('.td-total-amount').html(tnhFormatMoney(amount));
        total_quantity+= quantity;
        total_amount+= amount;

        //
        order_current_item_id = $(element).find('.delivery_item_id').val();
        if (order_current_item_id > 0)
        {
            quantity_had = intVal($(element).find('.quantity-orders').attr('value')) - intVal($(element).find('.quantity-returns').attr('value'));
            index = jQuery.inArray(order_current_item_id, arr_id);
            if (index !== -1)
            {
                arr_info[index].quantity = parseFloat(arr_info[index].quantity) + parseFloat(quantity);
            } else {
                arr_id.push(order_current_item_id);
                object = {"quantity": quantity, "quantity_had": quantity_had};
                arr_info.push(object);
            }
        }
        //

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

        $(element).find('.td-grand-total').html(tnhFormatMoney(grand_total_item));
        total_grand_total_item+= grand_total_item;
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

    if (arr_id) {
		$.each(arr_id, function(index, el) {
			quantity = arr_info[index].quantity;
			quantity_had = arr_info[index].quantity_had;
			trCur = $('.delivery_item_id[value="'+el+'"]').closest('tr');
			if (quantity > quantity_had)
			{
				trCur.find('.show-error-item').html('Số lượng trả <= '+quantity_had);
				count_errors++;
			} else {
				trCur.find('.show-error-item').html('');
			}
		});
    }

    if (edit == 0) {
        if (n > 0) {
            $('#customers').select2('readonly', true);
            $('#order_id').select2('readonly', true);
        } else {
            $('#customers').select2('readonly', false);
            $('#order_id').select2('readonly', false);
        }
    }
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

function loadCurrentDebit()
{
    customer_id = $('#customers').val();
    $.ajax({
        type: 'GET',
        url: site.base_url+'admin/returned_goods/getCustomerReturned',
        data: {
            customer_id: customer_id,
            return_goods_id: return_goods_id
        },
        dataType: "json",
        success: function (response) {
            if (response) {
                currentDebt = response.currentDebt;
                $('.td-current-debt').html(tnhFormatMoney(currentDebt));
            }
        }
    });
}

$(document).ready(function() {
    $('#handling_solution').select2();
    $('#employees').select2();
    $('#tax_id').select2();

    if (edit == 0) {
        ajaxSelectCustomerFormatTableCallBack('#customers', 'admin/clients/searchOnlyClients', $('#customers').val());
    } else if (edit == 1) {
        ajaxSelectCustomerFormatTableCallBack('#customers', 'admin/clients/searchOnlyClients', $('#customers').val());
        customer_id = $('#customers').val();
        ajaxSelectParamsCallback("#order_id", "admin/returned_goods/searchOrdersGiveReturnedGoods", $('#order_id').val(), {customer_id: customer_id}, true);
        $('#customers').select2('readonly', true);
        $('#order_id').select2('readonly', true);

        loadCurrentDebit();
    }

    $(document).on('change', '.customers', function(event) {
        event.preventDefault();
        customer_id = $(this).val();
        ajaxSelectParamsCallback("#order_id", "admin/returned_goods/searchOrdersGiveReturnedGoods", 0, {customer_id: customer_id}, true);
        $('#order_id').val(0);

        loadCurrentDebit();
    });

    $(document).on('change', '#order_id', function(event) {
        event.preventDefault();
        data = event.added;
        order_id = $(this).val();
        if (order_id) {
            tax_id = data.tax_id;
            discount_percent = data.discount_percent;
            discount_direct = data.discount_direct;
            $('#discount_percent').val(discount_percent);
            $('#tax_id').val(tax_id).trigger('change');
            // $('#tax_id').select2('readonly', true);
            // $('#discount_percent').attr('readonly', true);
        } else {
            $('#tax_id').val(0).trigger('change');
            // $('#tax_id').select2('readonly', false);
            $('#discount_percent').val(0);
            $('#discount_direct').val(0);
            // $('#discount_percent').removeAttr('readonly');
        }
    });

	// var dt = $('#tb-returned-goods').DataTable({
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
    //         $(nRow).addClass('tnh-data');
    //     },
	// });

	$('.add-row').on('click', function(event) {
		event.preventDefault();

        customer_id = $('#customers').val();
        order_id = $('#order_id').val();
        if (!customer_id || order_id == 0) {
            bootbox.alert('Vui lòng chọn đơn hàng và khách hàng !');
            return;
        }

		tdNumber = '<div class="stt text-center"></div>';
		tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
            <input type="hidden" name="category_id['+counter+']" id="category_id" class="form-control category_id" value="'+counter+'">\
            <input type="hidden" name="order_item_id['+counter+']" id="order_item_id" class="form-control order_item_id" value="0">\
            <input type="hidden" name="lot_code['+counter+']" id="lot_code" class="form-control lot_code" value="0">\
            <input type="hidden" name="date_sx['+counter+']" id="date_sx" class="form-control date_sx" value="0">\
            <input type="hidden" name="date_sd['+counter+']" id="date_sd" class="form-control date_sd" value="0">\
            <input type="hidden" name="date_use['+counter+']" id="date_use" class="form-control date_use" value="0">\
            <input type="hidden" name="unit_id['+counter+']" id="unit_id" class="form-control unit_id" value="0">\
            <input type="hidden" name="conversion_quantity_unit['+counter+']" id="conversion_quantity_unit" class="form-control conversion_quantity_unit" value="0">\
            <input type="hidden" name="delivery_item_id['+counter+']" id="delivery_item_id" class="form-control delivery_item_id" value="0">\
            <input type="hidden" name="warehouse_id['+counter+']" id="warehouse_id" class="form-control warehouse_id" value="0">\
            <input type="hidden" name="location_id['+counter+']" id="location_id" class="form-control location_id" value="0">\
            <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value=""></div>'+
            '<div class="type-item"></div>'+
            '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row">'+lang_core['delete']+'</a></div></div>';
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
        tdName = '<div class="td-item-name">'+lang_core['product_name']+'</div>';
        tdUnit = '<div class="td-unit"></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="1"><div class="show-info text-primary"></div><div class="show-error-item text-danger"></div></div>';
        tdPrice = '<div class="td-price"><input type="text" name="price['+counter+']" id="price[]" class="form-control price money-format" style="width: 100%;" value="0"></div>';
        tdTotalAmount = '<div class="td-total-amount text-right"></div>';

        tdDisPercent = '<div class="td-dis-percent">'+
            '<input type="number" name="discount_percent_item['+counter+']" id="discount_percent_item" class="form-control discount_percent_item" value="0" style="width: 100%;">'+
        '</div><div class="mtop5 text-dis-percent text-warning"></div>';
        tdDisDirect = '<div class="td-dis-direct">'+
            '<input type="text" name="discount_direct_item['+counter+']" id="discount_direct_item[]" class="form-control discount_direct_item money-format" style="width: 100%;" value="0">'+
        '</div><div class="mtop5 text-dis-direct text-warning"></div>';
        tdGrandTotal = '<div class="td-grand-total text-right"></div></div><div class="td-total-cost-amount text-right text-danger"></div>';

        tdNote = '<div class="td-note"><textarea name="note_items['+counter+']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

		// rowNode = dt.row.add( [
        //     tdNumber,
        //     tdCode,
        //     tdImage,
        //     tdName,
        //     tdUnit,
        //     tdQuantity,
        //     tdPrice,
        //     tdTotalAmount,
        //     tdDisPercent,
        //     tdDisDirect,
        //     tdGrandTotal,
        //     tdNote,
        //     tdActions
        // ] ).draw( false ).node();

        // <td>${tdDisPercent}</td>
        // <td>${tdDisDirect}</td>
        // <td>${tdGrandTotal}</td>

        tdQuantityLoss = `
            <input type="text" name="quantity_loss[${counter}]" class="form-control quantity_loss number-format" value="0">
        `;

        tdSampleQuantity = `
            <input type="text" name="quantity_sample[${counter}]" class="form-control quantity_sample number-format" value="0">
        `;

        cTr = `<tr>
            <td>${tdNumber}</td>
            <td>${tdCode}</td>
            <td>${tdImage}</td>
            <td>${tdName}</td>
            <td>${tdUnit}</td>
            <td>${tdQuantity}</td>
            <td>${tdQuantityLoss}</td>
            <td>${tdSampleQuantity}</td>
            <td>${tdPrice}</td>
            <td>${tdTotalAmount}</td>
            <td>${tdNote}</td>
            <td>${tdActions}</td>
        </tr>`;
        $('#tb-returned-goods tbody').append(cTr);

        // ajaxSelectCallBack($('#items_'+ counter +''), 'admin/returned_goods/searchProductAndGoodsGiveReturnedGoods', 0);

        order_id = $('#order_id').val();
        if (order_id > 0)
        {
            ajaxSelectParamsCallback($('#items_'+ counter +''), 'admin/returned_goods/getItemsByOrders', 0, {order_id: order_id});
        } else {
            ajaxSelectParamsCallback($('#items_'+ counter +''), 'admin/returned_goods/searchProductAndGoodsGiveReturnedGoods', 0);
        }

        counter++;
        totalReturnedGoods();
	});

    $(document).on('change', '.items_id11', function(event) {
        event.preventDefault();
        data = event.added;
        sl = this;
        tr = $(sl).closest('tr');
        item_id = $(sl).val();
        counter_index = tr.find('.counter').val();
        table_price_id = $('#table_price_id').val();
        table_discount_id = $('#table_discount_id').val();
        if (item_id) {

            name = data.item_name;
            images = data.images;
            unit = data.unit_name;
            price_sell = data.price_sell;
            lot_code = data.lot_code;
            date_sx = data.date_sx;
            date_sd = data.date_sd;
            date_use = data.date_use;
            type_item = item_id.split('__')[1];
            if (images) {
                tr.find('.td-image a').attr('href', site.base_url+images);
                tr.find('.td-image img').attr('src', site.base_url+images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            }
            tr.find('.td-item-name').html(name);
            tr.find('.td-unit').html(unit);
            tr.find('.price').val(tnhFormatMoney(price_sell));

            if (type_item == "products") {
                htmlLotCode = `
                <div><span>Lot code:</span><span>${lot_code == '' ? lot_code : ''}</span></div>
                <div><span>Ngày SX:</span><span>${date_sx == '' ? date_sx : ''}</span></div>
                <div><span>Ngày SD:</span><span>${date_sd == '' ? date_sd : ''}</span></div>
                `;
                tr.find('.type-item').html(htmlLotCode);
            }

            lastrow = $('#tb-returned-goods tbody tr')[$('#tb-returned-goods tbody tr').length - 1];
            if ($(lastrow).find('.items_id').select2('val')) {
                $('.add-row').click();
            }

        } else {
            tr.find('.td-item-name').html(lang_core['product_name']);
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-unit').html('');
            tr.find('.type-item').html('');
        }
    });

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        data = event.added;
        sl = this;
        tr = $(sl).closest('tr');
        item_id = $(sl).val();
        counter_index = tr.find('.counter').val();
        table_price_id = $('#table_price_id').val();
        table_discount_id = $('#table_discount_id').val();
        if (item_id) {

            name = data.item_name;
            images = data.images;
            unit = data.unit_name;
            price_sell = data.price_sell;
            lot_code = data.lot_code;
            date_sx = data.date_sx;
            date_sd = data.date_sd;
            date_use = data.date_use;
            unit_id = data.unit_id;
            delivery_item_id = data.delivery_item_id;
            warehouse_id = data.warehouse_id;
            location_id = data.location_id;
            conversion_quantity_unit = data.conversion_quantity_unit;
            type_item = item_id.split('__')[1];
            if (images) {
                tr.find('.td-image a').attr('href', site.base_url+images);
                tr.find('.td-image img').attr('src', site.base_url+images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            }
            tr.find('.td-item-name').html(name);
            tr.find('.td-unit').html(unit);
            tr.find('.price').val(tnhFormatMoney(price_sell));

            if (type_item == "products") {
                htmlLotCode = `
                <div style="color: green"><span>Lot code:</span><span>${lot_code == '' ? lot_code : ''}</span></div>
                <div style="color: green"><span>Ngày SX:</span><span>${date_sx == '' ? date_sx : ''}</span></div>
                <div style="color: green"><span>Ngày SD:</span><span>${date_sd == '' ? date_sd : ''}</span></div>
                `;
                tr.find('.type-item').html(htmlLotCode);
            }

            order_id = $('#order_id').val();
            if (order_id > 0)
            {
                orderItemId = data.order_item_id;
                quantityOrders = data.quantity_orders; 
                quantityReturns = data.quantity_returns;
                discount_direct_amount_item = data.discount_direct_amount_item;
                discount_percent_item = data.discount_percent_item;

                showInfo = '\
                    <div class="quantity-orders" value="'+quantityOrders+'">SL đã giao: '+tnhFormatNumber(quantityOrders)+'</div>\
                    <div class="quantity-returns" value="'+quantityReturns+'">SL đã trả: '+tnhFormatNumber(quantityReturns)+'</div>\
                ';
                tr.find('.show-info').html(showInfo);
                tr.find('.order_item_id').val(orderItemId);
                tr.find('.lot_code').val(lot_code);
                tr.find('.date_sx').val(date_sx);
                tr.find('.date_sd').val(date_sd);
                tr.find('.date_use').val(date_use);
                tr.find('.unit_id').val(unit_id);
                tr.find('.delivery_item_id').val(delivery_item_id);
                tr.find('.warehouse_id').val(warehouse_id);
                tr.find('.location_id').val(location_id);
                tr.find('.conversion_quantity_unit').val(conversion_quantity_unit);
                tr.find('#discount_percent_item').val(discount_percent_item);
                // tr.find('.discount_direct_item').val(tnhFormatMoney(discount_direct_amount_item));
                // tr.find('#discount_percent_item').attr('readonly', true);
            }

            lastrow = $('#tb-returned-goods tbody tr')[$('#tb-returned-goods tbody tr').length - 1];
            if ($(lastrow).find('.items_id').select2('val')) {
                $('.add-row').click();
            }
            totalReturnedGoods();
        } else {
            tr.find('.td-item-name').html(lang_core['product_name']);
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-unit').html('');
            tr.find('.type-item').html('');
            tr.find('.view-info').html('');
            tr.find('.order_item_id').val(0);
        }
    });


    $(document).on('change', '.quantity, .price, .quantity_sub, .tax_item, .discount_percent_item, .discount_direct_item, .tax_id, #discount_percent, #discount_direct, #cost_delivery, #charge_party', function(event) {
        totalReturnedGoods();
    });

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.sb').remove();
        totalReturnedGoods();
    });

	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
        tr = $(this).closest('tr');
        counter_index = tr.find('.counter').val();
        $(this).parents('tr').remove();
		// dt.row( $(this).parents('tr') ).remove().draw();
		totalReturnedGoods();
	});

    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        // $('.add-row').click();
    });

    if (edit == 0) {
        // $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/returned_goods/refereshReferenceReturnedGoods',
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
            ajaxSelectCallBack($('#items_'+ i +''), 'admin/products/searchProductAndGoods', $('#items_'+ i +'').val());
            init_editor('#info'+i+'');
        }
        init_datepicker();
        $('select.tax_item').select2();
    }

    //validation
	appValidateForm($('#returned-goods'), {
		reference_no: 'required',
        date: 'required',
        customers: 'required',
        employees: 'required',
        handling_solution: 'required'
    }, db);

    //save db
    function db(form) {

        if (count_errors > 0)
        {
            alert_float('danger', 'Vui lòng kiểm tra lại dữ liệu');
            return;
        }

        handling_solution = $('#handling_solution').val();
        if (handling_solution == "debt_reduction") {
            grand_total = intVal($('.td-grand-total-all').html());
            current_debt = intVal($('.td-current-debt').html());

            if (grand_total > current_debt) {
                alert_float('danger', 'Giảm trừ công nợ tổng tiền phải nhỏ hơn công nợ khách hàng ['+tnhFormatMoney(current_debt)+']');
                return;
            }
        }

    	$('.add').attr('disabled', 'disabled');
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
                if (data.gift == 1) {
                    window.location.href = data.linkGift;
                } else {
        		    window.location.href = site.base_url+'admin/returned_goods';
                }
        	} else {
        		alert_float('danger', data.message);
        		$('.add').removeAttr('disabled', 'disabled');
        	}
        })
        .fail(function() {
            alert_float('danger', lang_core['errors']);
        	$('.add').removeAttr('disabled', 'disabled');
        });
        return false;
    }
});

