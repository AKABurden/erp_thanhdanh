function totalDeliveries() {
    tb = '#tb-convert-delivery tbody tr.tr_parent:not("[class^=not-tr]")';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;
    arr_id = [];
    arr_info = [];
    for (ii = 0; ii < n; ii++) {
        stt++;
        element = $(tb)[ii];
        $(element).find('.td-number').html(stt);
        order_current_item_id = $(element).find('.order_item_id').val();
        counter_new = $(element).find('.counter').val();
        loss = $(element).find('.loss').val();
        check_delivery = $(element).find('.check_delivery').val();
        check_loss = $(element).find('.check_loss').is(':checked');

        //new
        tb_child = `.tr_child_${order_current_item_id}_${counter_new} tbody tr`;
        var n_child = $(tb_child).length;
        total_quantity_put = 0;
        total_quantity_loss = 0;
        for (i_child = 0; i_child < n_child; i_child++) {
            elementChild = $(tb_child)[i_child];
            quantity_put_check = $(elementChild).find('.quantity_put_check').val();
            quantity_put = intVal($(elementChild).find('.quantity_put').val());
            quantity_loss_new_vs1 = intVal($(elementChild).find('.quantity_loss_new_vs1').val());
            quantity_loss_new_delivery = intVal($(elementChild).find('.quantity_loss_new_delivery').val());

            if (quantity_put_check < quantity_put) {
                $(element).find('.text_error_child_new').html('Số lương phải nhỏ hơn hoặc bằng' + tnhFormatNumber(quantity_put_check));
                count_errors++;
            } else {
                $(element).find('.text_error_child_new').html('');
            }

            if (check_loss == 1) {
                quantity_loss_new_check = 0;
            } else {
                if (check_delivery == 0) {
                    quantity_loss_new_check = quantity_loss_new_vs1 - quantity_loss_new_delivery > 0 ? quantity_loss_new_vs1 - quantity_loss_new_delivery : 0;
                } else {
                    quantity_loss_new_check = 0
                }
            }
            $(elementChild).find('.quantity_loss_new').val(quantity_loss_new_check);
            quantity_loss_new = intVal($(elementChild).find('.quantity_loss_new').val());
            // quantity_loss_child = tnhToFixedNumber((quantity_put * loss) / 100, 0);
            // if (check_delivery == 0) {
            //     quantity_loss_child = quantity_loss_child;
            // } else {
            //     quantity_loss_child = 0;
            // }
            // $(elementChild).find('.quantity_loss_new').val(quantity_loss_child);
            total_quantity_put += quantity_put;
            total_quantity_loss += quantity_loss_new;
        }
        $(element).find('.quantity_delivery').val(tnhFormatNumber(total_quantity_put));
        //end


        // quantity_loss_new = tnhToFixedNumber((total_quantity_put * loss) / 100, 0);
        // if (check_delivery == 0) {
        //     quantity_loss_new = quantity_loss_new;
        // } else {
        //     quantity_loss_new = 0;
        // }
        $(element).find('.quantity_loss').val(tnhFormatNumber(total_quantity_loss));
        quantity_loss = intVal($(element).find('.quantity_loss').val());
        quantity_had_delivery_loss = intVal($(element).find('.quantity_had_delivery_loss').html());
        quantity_delivery_loss = intVal($(element).find('.quantity_delivery_loss').html());

        quantity_sample = intVal($(element).find('.quantity_sample').val());
        quantity_had_delivery_sample = intVal($(element).find('.quantity_had_delivery_sample').html());
        quantity_delivery_sample = intVal($(element).find('.quantity_delivery_sample').html());

        quantity_delivery = intVal($(element).find('.quantity_delivery').val());
        quantity = intVal($(element).find('.div-quantity').html());
        quantity_had_delivery = intVal($(element).find('.td-quantity-had-delivery').html());
        quantity_max = quantity - quantity_had_delivery;

        totalQuantityDelivery = (total_quantity_put - total_quantity_loss);
        totalQuantityDelivery = totalQuantityDelivery > 0 ? totalQuantityDelivery : 0;
        $(element).find('.total_quantity_delivery').html('Tổng SL đặt trừ loss : ' + tnhFormatNumber(totalQuantityDelivery));

        index = jQuery.inArray(order_current_item_id, arr_id);
        if (index !== -1) {
            arr_info[index].quantity_delivery = parseFloat(arr_info[index].quantity_delivery) + parseFloat(quantity_delivery);
            arr_info[index].quantity_delivery_loss = parseFloat(arr_info[index].quantity_delivery_loss) + parseFloat(quantity_loss);
            arr_info[index].quantity_delivery_sample = parseFloat(arr_info[index].quantity_delivery_sample) + parseFloat(quantity_sample);
        } else {
            arr_id.push(order_current_item_id);
            object = {
                "quantity": quantity,
                "quantity_had_delivery": quantity_had_delivery,
                "quantity_delivery": quantity_delivery,
                "quantity_loss": quantity_delivery_loss,
                "quantity_had_delivery_loss": quantity_had_delivery_loss,
                "quantity_delivery_loss": quantity_loss,
                "quantity_sample": quantity_delivery_sample,
                "quantity_had_delivery_sample": quantity_had_delivery_sample,
                "quantity_delivery_sample": quantity_sample
            };
            arr_info.push(object);
        }


        // if (quantity_delivery > quantity_max) {
        // 	$(element).find('.show-error-item').html(lang_orders['tnh_quantity_delivery_less']+' '+quantity_max);
        // 	count_errors++;
        // } else {
        // 	$(element).find('.show-error-item').html('');
        // }
    }

    if (arr_id) {
        $.each(arr_id, function (index, el) {
            quantity = arr_info[index].quantity;
            quantity_had_delivery = arr_info[index].quantity_had_delivery;
            quantity_delivery = arr_info[index].quantity_delivery;
            quantity_max = quantity - quantity_had_delivery;

            quantity_loss = arr_info[index].quantity_loss;
            quantity_had_delivery_loss = arr_info[index].quantity_had_delivery_loss;
            quantity_delivery_loss = arr_info[index].quantity_delivery_loss;
            quantity_max_loss = quantity_loss - quantity_had_delivery_loss;

            quantity_sample = arr_info[index].quantity_sample;
            quantity_had_delivery_sample = arr_info[index].quantity_had_delivery_sample;
            quantity_delivery_sample = arr_info[index].quantity_delivery_sample;
            quantity_max_sample = quantity_sample - quantity_had_delivery_sample;

            trCur = $('.order_item_id[value="' + el + '"]').closest('tr');
            if (quantity_delivery > quantity_max) {
                trCur.find('.show-error-item').html(lang_orders['tnh_quantity_delivery_less'] + ' ' + tnhFormatNumber(quantity_max));
                count_errors++;
            } else {
                trCur.find('.show-error-item').html('');
            }

            if (quantity_delivery_loss > quantity_max_loss) {
                trCur.find('.show-error-item-loss').html(lang_orders['tnh_quantity_delivery_less'] + ' ' + tnhFormatNumber(quantity_max_loss));
                count_errors++;
            } else {
                trCur.find('.show-error-item-loss').html('');
            }

            if (quantity_delivery_sample > quantity_max_sample) {
                trCur.find('.show-error-item-sample').html(lang_orders['tnh_quantity_delivery_less'] + ' ' + tnhFormatNumber(quantity_max_sample));
                count_errors++;
            } else {
                trCur.find('.show-error-item-sample').html('');
            }

            // if ((quantity_delivery_loss + quantity_delivery) > (quantity_max_loss + quantity_max))
            // {
            // 	trCur.find('.show-error-item-loss').html('Tổng SL giao + SL loss phải nhỏ hơn hoặc bằng '+' '+ tnhFormatNumber(quantity_max_loss + quantity_max));
            // 	count_errors++;
            // } else {
            // 	trCur.find('.show-error-item-loss').html('');
            // }
        });
    }
}

function totalChild() {
    tb = '.table_order_child tbody tr';
    var n = $(tb).length;
    arrTotal = [];
    arrCheck = [];
    if (arr_info_new.length > 0) {
        $.each(arr_info_new, function (k, v) {
            object = {"key": v.key, "qty": 0}
            arrTotal.push(object);
            arrCheck.push(v.key);
        });
    }
    total_qty = 0;
    count_errors_new = 0;
    for (ii = 0; ii < n; ii++) {
        element = $(tb)[ii];
        order_item_id = $(element).find('.order_item_id').val();
        code = $(element).find('.code').val();
        command = $(element).find('.command').val();
        counter_items_number = $(element).find('.counter_items_number').val();
        // check_exists_new = `${order_item_id}__${code}__${command}__${counter_items_number}`;
        check_exists_new = `${order_item_id}__${counter_items_number}`;
        quantity_check = intVal($(element).find(`.quantity_check_${check_exists_new}`).val());

        index = jQuery.inArray(check_exists_new, arrCheck);
        if (index !== -1) {
            arrTotal[index].qty = parseFloat(arrTotal[index].qty) + parseFloat(quantity_check);
        } else {
            arrCheck.push(check_exists_new);
            object = {"key": check_exists_new, "qty": 0}
            arrTotal.push(object);
        }
    }
    arrToTalNew = [];
    arrTotal.forEach(function (v, k) {
        object = {"qty": v.qty};
        arrToTalNew[v.key] = (object);
    });
    if (arr_info_new.length > 0) {
        $.each(arr_info_new, function (k, v) {
            quantity_check = v.quantity;
            key_check = v.key;
            if (arrToTalNew[key_check].qty != undefined) {
                if (arrToTalNew[key_check].qty > quantity_check) {
                    $(`.text_error_child_${key_check}`).html('Số lương tổng chi tiết phải nhỏ hơn hoặc bằng ' + tnhFormatNumber(quantity_check));
                    count_errors_new++;
                } else {
                    $(`.text_error_child_${key_check}`).html('');
                }
            } else {
                $(`.text_error_child_${key_check}`).html('');
            }
        });
    }
}

function getWarehouses(select_id) {
    var option = '<option value=""></option>';
    $.each(dataWarehouses, function (index, el) {
        selected = select_id == el.id ? 'selected' : '';
        option += '<option value="' + el.id + '">' + el.name + '</option>';
    });
    return option;
}

function getWarehousesLocation(counter, item_id, type_item, c_order_item_id) {
    optionWh = '';
    $.ajax({
        url: site.base_url + 'admin/orders/getWarehousesLocation',
        type: 'POST',
        dataType: 'json',
        data: {
            csrf_token_name: hash,
            item_id: item_id,
            type_item: type_item,
            order_id: order_id,
            c_order_item_id: c_order_item_id,
        },
    })
        .done(function (data) {
            if (data) {
                optionWh = data.option;
            } else {
                optionWh = '';
            }
            $('#warehouses_' + counter + '').html(optionWh);
            $('#warehouses_' + counter + '').select2();
            $('#warehouses_' + counter + '').val(0).trigger('change');
        })
        .fail(function () {
            console.log("error");
        });
}

function getLocations(locations) {
    var option = '<option value=""></option>';
    $.each(locations, function (index, el) {
        option += '<option data-quantity="' + el.product_quantity + '" value="' + el.localtion + '">' + el.location_name + '</option>';
    });
    return option;
}

function removeRow(el) {
    order_item_id = $(el).closest('tr').find('.order_item_id').val();
    counter_new = $(el).closest('tr').find('.counter').val();
    $(el).closest('tr').remove();
    $(`.tr_child_${order_item_id}_${counter_new}`).remove();
    totalDeliveries();
    totalChild();
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
                $('#tb-convert-delivery tbody').html('');
            }
        }
    });
}

function changeWarehouse(_this) {
    trCurrent = $(_this).closest('tr');
    elm = $(_this).select2().find(":selected");
    quantity_warehouse = elm.data("quantity");
    trCurrent.find('.quantity-warehouse').html('' + langDelivery['tnh_qty_warehoused'] + ': ' + tnhFormatNumber(quantity_warehouse));

    // warehouseId = $(_this).val();
    // itemId = trCurrent.find('.item_id').val();
    // trCurrent.find('select.locations').val('').trigger('change');
    // $.ajax({
    // 	url: site.base_url+'admin/releases/rowItemLocationWarehouse',
    // 	type: 'POST',
    // 	dataType: 'json',
    // 	data: {
    // 		csrf_token_name: hash,
    // 		item_id: itemId,
    // 		warehouse_id: warehouseId,
    // 	}
    // })
    // .done(function(data) {
    // 	trCurrent.find('.quantity-warehouse').html('');
    // 	if (data) {
    // 		trCurrent.find('select.locations').html(getLocations(data.warehouses));
    // 	}
    // })
    // .fail(function() {
    // 	console.log("error");
    // })
}

function changeLocation(_this) {
    trCurrent = $(_this).closest('tr');
    locationId = $(_this).val();
    if (locationId > 0) {
        elm = $(_this).select2().find(":selected");
        quantity_warehouse = elm.data("quantity");
        trCurrent.find('.quantity-warehouse').html('' + langDelivery['tnh_qty_warehoused'] + ': ' + tnhFormatNumber(quantity_warehouse));
    }
}

function createdRowOrderItem(order_item, counter) {
    trItem = '';
    if (order_item) {
        tdNumber = '<td class="text-center td-number"></td>';
        tdCode = '<td class="td-code">' +
            '<input type="hidden" name="counter[]" id="counter[' + counter + ']" class="form-control counter" value="' + counter + '">' +
            '<input type="hidden" name="item_id[]" id="item_id[' + counter + ']" class="form-control item_id" value="' + order_item.type_item + '__' + order_item.item_id + '">' +
            '<input type="hidden" name="order_item_id[' + counter + ']" id="order_item_id[' + counter + ']" class="form-control order_item_id" value="' + order_item.id + '">' +
            '<input type="hidden" name="loss[' + counter + ']" id="loss[' + counter + ']" class="form-control loss" value="' + order_item.loss + '">' +
            '<input type="hidden" name="check_delivery[' + counter + ']" id="check_delivery[' + counter + ']" class="form-control check_delivery" value="' + order_item.check_delivery + '">' +
            order_item.item_code +
            '</td>';
        tdName = '<td class="td-name">' + order_item.item_name + '</td>';

        tdUnit = '<td class="td-unit text-center">' + order_item.unit_name + '</td>';

        tdQuantity = '<td class="td-quantity text-center"><div class="div-quantity">' + tnhFormatNumber(order_item.quantity) + '</div><div class="text-danger quantity-warehouse"></div></td>';


        tdWarehouse = '<td class="td-warehouse">' +
            '<select name="warehouses[' + counter + ']" style="width: 100%;" onChange="changeWarehouse(this)" data-placeholder="Kho hàng" id="warehouses_' + counter + '" class="warehouses modal-select2">' +
            '</select>' +
            '<div class="total_quantity_delivery hide" style="color: red;margin-top: 10px"></div></td>';

        tdLocation = '<td class="td-location">' +
            '<select name="locations[' + counter + ']" data-placeholder="Vị trí" onChange="changeLocation(this)" id="locations" class="locations" style="width: 100%;">' +
            '<option value=""></option>' +
            '</select>' +
            '</td>';

        tdQuantityHadDelivery = `<td class="td-quantity-had-delivery text-center">${tnhFormatNumber(order_item.quantity_delivery)}</td>`;
        quantityDelivery = intVal(order_item.quantity) - intVal(order_item.quantity_delivery);
        if (quantityDelivery < 0) quantityDelivery = 0;
        readonly = '';
        if (order_item.type_item == 'products') {
            readonly = 'readonly';
        }
        tdQuantityDelivery = '<td class="td-quantity-delivery"><input type="text" ' + readonly + ' name="quantity_delivery[' + counter + ']" id="quantity_delivery[]" onchange="totalDeliveries()" class="form-control quantity_delivery number-format" value="' + tnhFormatNumber(0) + '"><div class="show-error-item text-danger"></div></td>';
        quantityDeliveryLoss = (intVal(order_item.quantity_loss)) - intVal(order_item.quantity_delivery_loss);
        if (quantityDeliveryLoss < 0) quantityDeliveryLoss = 0;
        tdQuantityDeliveryLoss = `<td class="td-quantity-loss">
		<div class="">SL: <span class="quantity_delivery_loss">${tnhFormatNumber(intVal(order_item.quantity_loss))}</span></div>
		<div class="" style="color: green">SL đã giao: <span class="quantity_had_delivery_loss">${tnhFormatNumber(intVal(order_item.quantity_delivery_loss))}</span></div>
		<input type="text" name="quantity_loss[${counter}]" ${readonly} id="quantity_loss[]" onchange="totalDeliveries()" class="form-control quantity_loss number-format" value="${tnhFormatNumber(quantityDeliveryLoss)}"><div class="show-error-item-loss text-danger"></div>
		<input type="checkbox" onchange="totalDeliveries()" name="check_loss[${counter}]" id="check_loss${counter}" class="check_loss" value="1">
        <label for="check_loss${counter}">K Giao Loss</label>
		</td>`;
        quantityDeliverySample = (intVal(order_item.sample_quantity)) - intVal(order_item.quantity_sample);
        if (quantityDeliverySample < 0) quantityDeliverySample = 0;
        tdQuantityDeliverySample = `<td class="td-quantity-sample">
		<div class="">SL: <span class="quantity_delivery_sample">${tnhFormatNumber(intVal(order_item.sample_quantity))}</span></div>
		<div class="" style="color: green">SL đã giao: <span class="quantity_had_delivery_sample">${tnhFormatNumber(intVal(order_item.quantity_sample))}</span></div>
		<input type="text" name="quantity_sample[${counter}]" id="quantity_sample[]" onchange="totalDeliveries()" class="form-control quantity_sample number-format" value="${tnhFormatNumber(quantityDeliverySample)}"><div class="show-error-item-sample text-danger"></div></td>`;
        tdNote = '<td class="td-note">' +
            '<textarea name="note_item[' + counter + ']" id="note_item[]" class="form-control" rows="3"></textarea>' +
            '</td>';
        tdActions = '<td class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></i></td>';


        htmlOption = '';
        if (order_item.orderItemsColumnsNew.length > 0) {
            $.each(order_item.orderItemsColumnsNew, function (k, v) {
                htmlOption += `<option value="${v.id_check}" data-subtext="Chỉ lệnh: ${v.command} - SL: ${tnhFormatNumber(v.quantity_put)}">${v.code}</option>`;
            })
        }

        trItem = `<tr class="tr_parent" data-order-item-id="${order_item.id}">
			${tdNumber}
			${tdCode}
			${tdName}
			${tdUnit}
			${tdWarehouse}
			${tdQuantity}
			${tdQuantityHadDelivery}
			${tdQuantityDelivery}
			${tdQuantityDeliveryLoss}
			${tdQuantityDeliverySample}
			${tdNote}
			${tdActions}
		</tr><tr class="tr_child_${order_item.id}_${counter}">
			<td colspan="12">
				<div class="row" style="width: 50%;margin-bottom: 10px;display: flex;align-items: end">
					<div class="col-md-9">
						<label>Đơn đặt</label>
						<select class="form-control selectpicker select_child_${order_item.id}_${counter}"   
							data-live-search="true"
							title='Đơn đặt'
							data-actions-box="1"
							multiple 
							data-none-selected-text=""
							
							>
							${htmlOption}
						</select>
						</div>
					<div class="col-md-3">
						<button class="btn btn-primary" type="button" onclick="clickChosen(this,${order_item.id},${counter})">Chọn</button>
					</div>
				</div>
				<div class="clearfix"></div>
				<div>
				<table class="table_order_child" style="width: 50%">
					<thead>
						<th>STT</th>
						<th>Mã đơn đặt</th>
						<th>Chỉ lệnh</th>
						<th>SL đặt</th>
						<th></th>
					</thead>
					<tbody class="body_order_child_${order_item.id}_${counter}"></tbody>
				</table>
				</div>
			</td>
		</tr>`;
        getWarehousesLocation(counter, order_item.item_id, order_item.type_item, order_item.id);
    }

    return trItem;
}

$(document).ready(function () {
    $('#items').change(function (event) {
        order_item_id = $(this).val();
        if (order_item_id) {
            $.ajax({
                url: site.base_url + 'admin/orders/getOrderItems',
                type: 'POST',
                dataType: 'json',
                data: {
                    order_item_id: order_item_id,
                    csrf_token_name: hash,
                },
            })
                .done(function (data) {
                    if (data) {
                        // elTr = $('tr[data-order-item-id="'+order_item_id+'"]');
                        // if(elTr.length > 0) {
                        // 	quantity_delivery_current = intVal(elTr.find('.quantity_delivery').val()) + 1;
                        // 	elTr.find('.quantity_delivery').val(tnhFormatNumber(quantity_delivery_current));
                        // } else {
                        trItem = createdRowOrderItem(data.order_item, counter);
                        $('#tb-convert-delivery tbody.tbody').append(trItem);
                        $('select.warehouses').select2();
                        $('select.locations').select2();
                        counter++;
                        setTimeout(function () {
                            init_selectpicker();
                        }, 500);
                        // }
                        totalDeliveries();
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
        $('#items').val('');
    });

    $('.ev-all').click(function (event) {
        order_id = $('#order_id_save').val();
        if (order_id) {
            $.ajax({
                url: site.base_url + 'admin/orders/getOrderItems',
                type: 'POST',
                dataType: 'json',
                data: {
                    order_id: order_id,
                    csrf_token_name: hash,
                },
            })
                .done(function (data) {
                    if (data) {
                        console.log(data);
                        htmlItem = '';
                        $.each(data.items, function (index, el) {
                            trItem = createdRowOrderItem(el, counter);
                            htmlItem += trItem;
                            counter++;
                        });
                        $('#tb-convert-delivery tbody.tbody').html(htmlItem);
                        $('select.warehouses').select2();
                        $('select.locations').select2();
                        setTimeout(function () {
                            init_selectpicker();
                        }, 500);
                        totalDeliveries();
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    });
});

function clickChosen(_this, order_item_id, counter) {
    dtSelect = $(`select.select_child_${order_item_id}_${counter}`).val();
    trItemChild = '';
    if (dtSelect.length > 0) {
        $.ajax({
            url: site.base_url + 'admin/orders/getDetailColumsOrders',
            type: 'POST',
            dataType: 'json',
            data: {
                dtSelect: dtSelect,
                order_item_id: order_item_id,
                csrf_token_name: hash,
            },
        }).done(function (data) {
                if (data) {
                    trItemChild = '';
                    if (data.orderItemsColumnsNew.length > 0) {
                        $.each(data.orderItemsColumnsNew, function (k, vv) {
                            v = JSON.parse(vv.json);
                            // check_exists = `${v.order_item_id}__${v.code}__${v.command}__${v.counter_items_number}`;
                            check_exists = `${v.order_item_id}__${v.counter_items_number}`;
                            index = jQuery.inArray(check_exists, arrNew);
                            if (index !== -1) {
                            } else {
                                arrNew.push(check_exists);
                                object = {"key": check_exists, "quantity": v.quantity_put};
                                arr_info_new.push(object);
                            }
                            trItemChild += `<tr>
								<td class="text-center center">${++k}
								<input type="hidden" class="order_item_id" value="${order_item_id}">
								<input type="hidden" class="code" value="${v.code}">
								<input type="hidden" class="command" value="${v.command}">
								<input type="hidden" class="counter_items_number" value="${v.counter_items_number}">
								<input type="hidden" class="quantity_put_check" value="${v.quantity_put}">
								<input type="hidden" class="quantity_loss_new_vs1" value="${v.quantity_loss_new_vs1}">
								<input type="hidden" class="quantity_loss_new_delivery" value="${v.quantity_loss_new}">
								<input type="hidden" name="quantity_loss_new[${counter}][]" class="quantity_loss_new" value="${v.quantity_loss_new_vs1 - v.quantity_loss_new}">
								<input type="hidden" name="json[${counter}][]" value="${htmlEntities(JSON.stringify(v))}">
								</td>
								<td>${v.code}
								<div style="color: green">Số lượng : ${tnhFormatNumber(v.quantity_put)}</div>
								</td>
								<td>${v.command}</td>
								<td style="width: 150px">
								<input onchange="totalDeliveries();totalChild()" type="text" class="quantity_put form-control number-format quantity_check_${check_exists}" name="quantity_put[${counter}][]" value="${tnhFormatNumber(v.quantity_put)}">
								<div class="text_error_child_${check_exists}" style="color: red"></div>
								<div class="text_error_child_new" style="color: red"></div>
								</td>
								<td class="text-center" style="color: red"><a onclick="removeRowChild(this)" href="javascript:void(0)" class="fa fa-remove remove-row_child"></i></td>
							</tr>`;
                        })
                    }
                    $(`.body_order_child_${order_item_id}_${counter}`).html(trItemChild);
                    totalDeliveries();
                    totalChild();
                }
            })
            .fail(function () {
                console.log("error");
            });
    }
    $(`select.select_child_${order_item_id}_${counter}`).selectpicker("val", "-1");
}

function removeRowChild(_this) {
    $(_this).closest('tr').remove();
    totalChild();
    totalDeliveries();
}
