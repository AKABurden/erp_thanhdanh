function totalSuggestExporting()
{
    tb = '#tb-items tbody tr:not("[class^=not-tr]")';
    var table = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
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
            number_exchange = intVal($(element).find('.number_exchange').val());
            quantity_exchange = quantity/number_exchange;
            $(element).find('.td-quantity-exchange').html(tnhFormatNumber(quantity_exchange));
            total_quantity+= quantity;
            total_quantity_exchange+= quantity_exchange;
            flag = true;
        }
    }
    $('.th-total-quantity').html(tnhFormatNumber(total_quantity));
    $('.th-total-quantity-exchange').html(tnhFormatNumber(total_quantity_exchange));
}

function getLocations(locations) {
    var option = '<option value=""></option>';
    $.each(locations, function(index, el) {
        option+= '<option value="'+el.localtion+'">'+el.location_name+'</option>';
    });
    return option;
}

function getUnits(units, selected) {
    var option = '<option value=""></option>';
    $.each(units, function(index, el) {
        selected = selected == el.unitid ? 'selected' : '';
        option+= '<option data-number-exchange="'+el.number_exchange+'" '+selected+' value="'+el.unitid+'">'+el.unit+'</option>';
    });
    return option;
}

$(document).ready(function() {
	init_editor('textarea[name="note"]');
    $('#warehouses').select2();
    if (edit == 0) {
        ajaxSelectParams('#productions_orders_detail_id', 'admin/stock/searchProductionsOrdersDetail', 0);
    } else if (edit == 1) {
        $('#warehouses').select2('readonly', true);
        $('select.locations').select2();
    }
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
		tdRef = '<div class="stt text-center"></div>';
		tdItem = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
            <input type="hidden" name="unit_parent_id[]" id="unit_parent_id" class="form-control unit_parent_id" value="">\
            <input type="hidden" name="number_exchange[]" id="number_exchange" class="form-control number_exchange" value="1">\
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
        tdUnit = '<div class="td-unit"><select name="unit_id[]" data-placeholder="'+ lang_core['choose'] +'" id="unit_id" class="unit_id" style="width: 100%;"></select></div>';

        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 100%;"></select></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[]" id="quantity[]" class="form-control quantity" value="1" style="width: 100%;"></div>';
        tdValueExchange = '<div class="text-center td-value-exchange"></div>';
        tdQuantityExchange = '<div class="text-center td-quantity-exchange"></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

		rowNode = dt.row.add( [
            tdRef,
            tdItem,
            tdItemName,
            tdUnit,
            tdQuantity,
            tdValueExchange,
            tdQuantityExchange,
            tdActions
        ] ).draw( false ).node();
        ajaxSelectParams($('#items_'+ counter +''), 'admin/stock/searchMaterialAndSemiProducts', 0);
        $('select.unit_id').select2();
        counter++;
        totalSuggestExporting();
	});

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        paramsData['item_id'] = item_id;
        paramsData['unit'] = true;
        row.find('select.unit_id').val(null).trigger('change');
        row.find('select.unit_id').html('');
        if (item_id) {
            tr = $(sl).closest('tr');
            //ajax
            $.ajax({
                url: site.base_url+'admin/stock/rowMaterialOrSemiProduct',
                type: 'POST',
                dataType: 'json',
                data: paramsData
            })
            .done(function(data) {
                if (data) {
                    item_name = data.item.name;
                    number_exchange = data.number_exchange;
                    unit_parent_id = data.selected;
                    tr.find('.td-item-name').html(item_name);
                    tr.find('.td-value-exchange').html(number_exchange);
                    tr.find('.number_exchange').val(number_exchange);
                    tr.find('.unit_parent_id').val(unit_parent_id);

                    row.find('select.unit_id').html(getUnits(data.units, data.selected));
                    row.find('select.unit_id').val(data.selected).trigger('change');
                    totalSuggestExporting();
                }
            })
            .fail(function() {
                console.log("error");
            })

            lastrow = $('#tb-items tbody tr')[$('#tb-items tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            totalSuggestExporting();
        }
    });

    $(document).on('change', 'select.unit_id', function(event) {
        event.preventDefault();
        unit_id = $(this).val();
        row = $(this).closest('tr');
        if (unit_id) {
            element = row.find("select.unit_id").select2().find(":selected");
            number_exchange = element.data('number-exchange');
            row.find('.td-value-exchange').html(number_exchange);
            row.find('.number_exchange').val(number_exchange);
        }
        totalSuggestExporting();
    });

    $('#productions_orders_detail_id').on('change', function(event) {
        var productions_orders_detail_id = $(this).val();
    });

    $(document).on('change', '.quantity, .quantity_sub', function(event) {
        totalSuggestExporting();
    });


	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
		dt.row( $(this).parents('tr') ).remove().draw();
		totalSuggestExporting();
	});

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.row').remove();
        totalSuggestExporting();
    });


    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        $('.add-row').click();
    });

    if (edit == 0) {
        $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
    		event.preventDefault();
            params = {};
            params['referesh'] = 1;
            params[token] = hash;
    		$.ajax({
    			url: site.base_url+'admin/manufactures/refereshReferenceSuggestExporting',
    			type: 'GET',
    			dataType: 'JSON',
    			// data: {
    			// 	csrf_token_name: hash,
    			// 	'referesh': 1
    			// },
                data: params
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

	appValidateForm($('#add-exporting'), {
		reference_no: 'required',
        date: 'required',
        export_name: 'required',
       	warehouses: 'required',
        productions_orders_detail_id: 'required'
    }, db);

    function db(form) {
        // if (count_errors > 0) {
        //     alert_float('danger', lang_core['check_date_enter']);
        //     return;
        // }
    	$('.add').attr('disabled', 'disabled');
        tinymce.get('note').save();
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
        //
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
        		window.location.href = site.base_url+'admin/manufactures/list_suggest_exporting';
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

