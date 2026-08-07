function totalDeliveries()
{
	tb = '#tb-deliveries tbody tr.tnh-handling';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.td-number').html(stt);
        quantity_delivery = intVal($(element).find('.quantity_delivery').val());
        quantity = intVal($(element).find('.td-quantity').html());
        quantity_had_delivery = intVal($(element).find('.td-quantity-had-delivery').html());
        quantity_max = quantity - quantity_had_delivery;
        if (quantity_delivery > quantity_max) {
        	$(element).find('.show-error-item').html(lang_delivery['tnh_quantity_delivery_less']+' '+quantity_max);
        	count_errors++;
        } else {
        	$(element).find('.show-error-item').html('');
        }
    }

    if (n > 0) {
        $('#reference_orders').select2('readonly', true);
        $('#customers').select2('readonly', true);
    } else {
        $('#reference_orders').select2('readonly', false);
        $('#customers').select2('readonly', false);
    }
}

function removeRow(el)
{
	dt.row( $(el).parents('tr') ).remove().draw();
	totalDeliveries();
}

function refershTable()
{
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

$(document).ready(function() {
	$('#employees').select2();
	// init_editor('textarea[name="note"]');

	if (edit == 0) {
        ajaxSelectCustomerFormatTableCallBack('#customers', 'admin/clients/searchCustomers', $('#customers').val());
    } else {
        customer_id = $('.customers').val();
        orders_id = $('.reference_orders').val();
        ajaxSelectParams('#address_delivery', 'admin/clients/searchAddressDelivery', $('#address_delivery').val(), {'customer_id': customer_id});
        ajaxSelectParams('#items', 'admin/releases/getOrdersItemsByOrderId', 0, {'orders_id': orders_id, 'edit': edit, 'delivery_id': delivery_id});
        ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', $('#person_contact').val(), {customer_id: customer_id});
    }

    $(document).on('change', '.customers', function(event) {
        event.preventDefault();
        customer_id = $(this).val();
        ajaxSelectParams('#address_delivery', 'admin/clients/searchAddressDelivery', 0, {'customer_id': customer_id});
        ajaxSelectMultipleParams('#reference_orders', 'admin/releases/getOrdersByDelivery', 0, {'customer_id': customer_id});
        ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', 0, {customer_id: customer_id}, true);
        ajaxSelectParams('#items', 'admin/releases/getOrdersItemsByOrderId', 0, {'orders_id': 0, 'edit': edit});
        dt.rows().remove().draw();

        $('#address_delivery').val('');
        $('#reference_orders').val('');
        $('#person_contact').val('');
        $('#items').val('');
        // ajaxSelectParams('#reference_orders', 'admin/releases/getOrdersByDelivery', 0, {'customer_id': customer_id});
    });

    $(document).on('change', '#reference_orders', function(event) {
    	event.preventDefault();
    	orders_id = $(this).val();
	    ajaxSelectParams('#items', 'admin/releases/getOrdersItemsByOrderId', 0, {'orders_id': orders_id, 'edit': edit});
        // ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', 0, {customer_id: customer_id}, true);
        // ajaxSelectParams('#address_delivery', 'admin/clients/searchAddressDelivery', 0, {'customer_id': customer_id});

        $.ajax({
            url: site.base_url+'admin/releases/getOrdersByOrderId',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token_name: hash,
                orders_id: orders_id,
            },
        })
        .done(function(data) {
            if (data) {
                flagAddressDelivery = false;
                flagContract = false;
                noti_phone = false;
                noti_email = false;
                noti_zalo = false;
                noti_note_other = false;
                $.each(data.results, function(index, el) {
                    if (!flagAddressDelivery && el.address_delivery_id) {
                        ajaxSelectParams('#address_delivery', 'admin/clients/searchAddressDelivery', el.address_delivery_id, {'customer_id': customer_id});
                        flagAddressDelivery = true;
                    }
                    if (!flagContract && el.person_contact_id) {
                        ajaxSelectParamsCallback('#person_contact', 'admin/clients/searchContract', 'customers__'+el.person_contact_id, {customer_id: customer_id}, true);
                        flagContract = true;
                    }
                    if (el.noti_phone) noti_phone+= ','+el.noti_phone;
                    if (el.noti_email) noti_email+= ','+el.noti_email;
                    if (el.noti_zalo) noti_zalo+= ','+el.noti_zalo;
                    if (el.noti_note_other) noti_note_other+= ','+el.noti_note_other;
                });
                if (noti_phone) {
                    noti_phone = noti_phone.split(',');
                    $('#noti_phone').select2('val', noti_phone);
                }
                if (noti_email) {
                    noti_email = noti_email.split(',');
                    $('#noti_email').select2('val', noti_email);
                }
                if (noti_zalo) {
                    noti_zalo = noti_zalo.split(',');
                    $('#noti_zalo').select2('val', noti_zalo);
                }
                if (noti_note_other) {
                    noti_note_other = noti_note_other.split(',');
                    $('#noti_note_other').select2('val', noti_note_other);
                }
            }
        })
        .fail(function() {
            console.log("error");
        });
    });

    dt = $('#tb-deliveries').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        // 'fixedHeader': true,
        // scrollY: true,
		// scrollY: '150px',
		// scrollX: true,
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            $(nRow).addClass('tnh-handling');
        },
	});

	function createdRowOrderItem(order_item, counter)
	{
		elTr = $('.order_item_id[value="'+order_item.id+'"]').closest('tr');
		if(elTr.length > 0) {
			quantity_delivery_current = intVal(elTr.find('.quantity_delivery').val()) + 1;
			elTr.find('.quantity_delivery').val(tnhFormatNumber(quantity_delivery_current));
		} else {
			tdNumber = '<div class="td-number text-center"></div>';
			tdReferenceOrder = '<div class="td-referece-order">'+order_item.reference_no+'</div>';
			tdCode = '<div class="td-code mbot10">'+
					'<input type="hidden" name="order_item_id['+counter+']" id="order_item_id" class="form-control order_item_id" value="'+order_item.id+'">'+
					'<input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">'+
					order_item.item_code+
	                '<div class="type-item"></div>'+
	        '</div>';
	        tdImage = '<div class="td-image">'+
	                    '<div class="preview_image" style="width: auto;">'+
	                        '<div class="display-block contract-attachment-wrapper img">'+
	                            '<div style="width:45px;">'+
	                                '<a href="'+order_item.images+'" data-lightbox="customer-profile" class="display-block mbot5">'+
	                                    '<div class="">'+
	                                        '<img src="'+order_item.images+'" style="border-radius: 50%">'+
	                                    '</div>'+
	                                '</a>'+
	                            '</div>'+
	                        '</div>'+
	                    '</div>'+
	            '</div>';
	        tdName = '<div class="td-item-name">'+order_item.item_name+'</div>';
	        tdUnit = '<div class="td-unit">'+order_item.unit+'</div>';
	        tdQuantity = '<div class="td-quantity text-center">'+tnhFormatNumber(order_item.quantity)+'</div>';
			tdQuantityHadDelivery = '<div class="td-quantity-had-delivery text-center">'+tnhFormatNumber(order_item.quantity_delivery)+'</div>';
			quantityDelivery = intVal(order_item.quantity) - intVal(order_item.quantity_delivery);
			if (quantityDelivery < 0) quantityDelivery = 0;
			tdQuantityDelivery = '<div class="td-quantity-delivery"><input type="text" name="quantity_delivery['+counter+']" id="quantity_delivery[]" onchange="totalDeliveries()" class="form-control quantity_delivery number-format" value="'+tnhFormatNumber(quantityDelivery)+'"><div class="show-error-item text-danger"></div></div>';
			tdNote = '<div class="td-note">'+
				'<textarea name="note_item['+counter+']" id="note_item[]" class="form-control" rows="3"></textarea>'+
			'</div>';
			tdActions = '<div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row btn btn-danger"></i></div>';

			rowNode = dt.row.add( [
	            tdNumber,
	            tdReferenceOrder,
	            tdCode,
	            tdImage,
	            tdName,
	            tdUnit,
	            tdQuantity,
	            tdQuantityHadDelivery,
	            tdQuantityDelivery,
	            tdNote,
	            tdActions
	        ] ).draw( false ).node();
		}
        totalDeliveries();
	}

    $(document).on('change', '#items', function(event) {
    	order_item_id = $(this).val();
		$.ajax({
			url: site.base_url+'admin/releases/rowOrderItem',
			type: 'GET',
			dataType: 'json',
			data: {
				csrf_token_name: token,
				order_item_id: order_item_id,
                delivery_id: delivery_id,
			},
		})
		.done(function(data) {
			if (data.order_item) {
				createdRowOrderItem(data.order_item, counter);
				counter++;
			}
		})
		.fail(function() {
			console.log("error");
		});
    	$('#items').val('');
    });

    $(document).on('click', '.ev-all', function(event) {
        event.preventDefault();
        orders_id = $('#reference_orders').val();
        if (orders_id) {
            $.ajax({
                url: site.base_url+'admin/releases/getOrderItem',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    orders_id: orders_id
                },
            })
            .done(function(data) {
                if (data) {
                    $.each(data.items, function(index, el) {
                        createdRowOrderItem(el, counter);
                        counter++;
                    });
                }
            })
            .fail(function() {
                console.log("error");
            });
        }
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
            bootbox.alert('Xin vui lòng chọn khách hàng');
            // $(el).attr('href', link);
        }
    });

    if (edit == 0) {
        $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/releases/refereshReferenceDelivery',
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
    }

    appValidateForm($('#orders'), {
		reference_no: 'required',
        date: 'required',
        address_delivery: 'required',
        person_contact: 'required',
        customers: 'required',
        reference_orders: 'required',
        employees: 'required'
    }, db);

    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', lang_core['check_date_enter']);
            return;
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
        		window.location.href = site.base_url+'admin/releases/deliveries';
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