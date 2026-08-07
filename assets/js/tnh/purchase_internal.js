function totalPurchaseInternal()
{
    tb = '#tb-items tbody tr:not("[class^=not-tr]")';
    var table = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    var grand_total = 0;
    var total_quantity_exchange = 0;
    count_errors = 0;
    var flag = false;
    for (ii = 0; ii < table; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        item_id_current = $(element).find('input.items_id').val();
        suggest_exporting_items_id = $(element).find('.suggest_exporting_items_id').val();
        if (item_id_current || suggest_exporting_items_id) {
            quantity = intVal($(element).find('.quantity').val());
            price = intVal($(element).find('.price').val());
            amount = quantity*price;
            total_quantity+= quantity;
            grand_total+= amount;
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
            flag = true;
        }
    }
    $('.th-total-quantity').html(tnhFormatNumber(total_quantity));
    $('.th-grand-total').html(tnhFormatNumber(grand_total));
    if (flag) {
        $('#warehouses').select2('readonly', true);
        $('#productions_orders_detail_id').select2('readonly', true);
    } else {
        $('#warehouses').select2('readonly', false);
        $('#productions_orders_detail_id').select2('readonly', false);
    }
}

function getLocations(locations) {
    var option = '<option value=""></option>';
    $.each(locations, function(index, el) {
        option+= '<option value="'+el.localtion+'">'+el.location_name+'</option>';
    });
    return option;
}

function getWarehouseLocations()
{
    warehouse_id = $('#warehouses').val();
    locations = false;
    if (warehouse_id) {
        $.ajax({
            url: site.base_url+'admin/stock/getLocationWarehouses',
            type: 'POST',
            dataType: 'json',
            data: {
                warehouse_id: warehouse_id,
                csrf_token_name: hash,
            },
        })
        .done(function(data) {
            if (data) {
                locations = data.locations;
            }
        })
        .fail(function() {
            console.log("error");
        });
    }
}

$(document).ready(function() {
	// init_editor('textarea[name="note"]');
    $('#warehouses').select2();
    $('#branch_id').select2();
    if (edit == 0) {
        ajaxSelectParams('#productions_orders_detail_id', 'admin/stock/searchProductionsOrdersDetail', 0, false, clearSl2 = true);
    } else if (edit == 1) {
        $('#warehouses').select2('readonly', true);
        $('select.locations').select2();
    }

    //hanlding warehouse locations
    getWarehouseLocations();
    $(document).on('change', '#warehouses', function(event) {
        event.preventDefault();
        getWarehouseLocations();
    });
    //end hanlding warehouse locations

	var dt = $('#tb-items').DataTable({
		"language": lang_datatables,
		'searching': false,
		'ordering': false,
		'paging': false,
        "info": false,
        'fixedHeader': true,
        // scrollY: true,
		// scrollY: '150px',
		// scrollX: true,
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
        },
        "initComplete": function(settings, json) {
            var t = this;
            t.parents('.table-loading').removeClass('table-loading');
            t.removeClass('dt-table-loading');
            mainWrapperHeightFix();
        },
	});

	$('.add-row').on('click', function(event) {
		event.preventDefault();
        productions_orders_detail_id = $('#productions_orders_detail_id').val();
        warehouses = $('#warehouses').val();
        if (!warehouses) {
            bootbox.alert(lang_pi['tnh_please_chosen_warehouse']);
            return;
        }

		tdRef = '<div class="stt text-center"></div>';
		tdItem = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
            <input type="text" name="items_id[]" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="">';
        tdImage = '<div class="td-image">'+
                    '<div class="preview_image" style="width: auto;">'+
                        '<div class="display-block contract-attachment-wrapper img">'+
                            '<div style="width:45px;">'+
                                '<a href="'+site.base_url+'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">'+
                                    '<div class="">'+
                                        '<img src="'+site.base_url+'assets/images/tnh/no_image.png" style="border-radius: 50%">'+
                                    '</div>'+
                                '</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
            '</div>';
        tdItemName = '<div class="td-item-name"></div>';
        tdUnit = '<div class="td-unit"></div>';
        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 100%;;"></select></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" style="width: 100%;" value="1"></div>';
        tdPrice = '<div class="td-price"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price[]" id="price[]" class="form-control price" value="0"></div>';
        tdAmount = '<div class="td-amount text-right"></div>';
        tdNote = '<div class="text-center td-note"><textarea name="note_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

		rowNode = dt.row.add( [
            tdRef,
            tdItem,
            tdItemName,
            tdUnit,
            tdLocation,
            tdQuantity,
            tdPrice,
            tdAmount,
            tdNote,
            tdActions
        ] ).draw( false ).node();
        ajaxSelectParams($('#items_'+ counter +''), 'admin/stock/searchItemsForPurchaseInternal', 0, {productions_orders_detail_id: productions_orders_detail_id});
        $('select.locations').select2();
        $(rowNode).find('select.locations').html(locations);
        counter++;
        totalPurchaseInternal();
	});

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        console.log(data);
        sl = this;
        item_id = $(this).val();
        if (item_id) {

            tr = $(sl).closest('tr');
            name = data.name;
            unit_name = data.unit_name;
            unit_id = data.unit_id;

            tr.find('.unit_id').val(unit_id);
            tr.find('.td-item-name').html(name);
            tr.find('.td-unit').html(unit_name);
            $(row).find('select.locations').html(locations);

            lastrow = $('#tb-items tbody tr')[$('#tb-items tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
        }
        totalPurchaseInternal();
    });

    $('#productions_orders_detail_id').on('change', function(event) {
        var productions_orders_detail_id = $(this).val();
        dt.rows().remove().draw();
        warehouses = $('#warehouses').val();
        if (warehouses) {
            $('.add-row').click();
            totalPurchaseInternal();
        }
    });

    $(document).on('change', '.quantity, .quantity_sub, .price', function(event) {
        totalPurchaseInternal();
    });


	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
		dt.row( $(this).parents('tr') ).remove().draw();
		totalPurchaseInternal();
	});

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.row').remove();
        totalPurchaseInternal();
    });


    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        $('.add-row').click();
    });

    if (edit == 0) {
        // $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
    		event.preventDefault();
    		$.ajax({
    			url: site.base_url+'admin/stock/refereshReferencePurchaseInternal',
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

	appValidateForm($('#add-purchase-internal'), {
		reference_no: 'required',
        date: 'required',
        enter_name: 'required',
       	warehouses: 'required',
        branch_id: 'required',
    }, db);

    function db(form) {
        // if (count_errors > 0) {
        //     alert_float('danger', lang_core['check_date_enter']);
        //     return;
        // }
    	$('.add').attr('disabled', 'disabled');
        // tinymce.get('note').save();
        // var data = $(form).serialize();
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
        		window.location.href = site.base_url+'admin/stock/purchase_internal';
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
});

