function totalExportingProductions()
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

    if (flag) {
        $('#warehouses').select2('readonly', true);
        $('#productions_orders_detail_id').select2('readonly', true);
    } else {
        $('#warehouses').select2('readonly', false);
        $('#productions_orders_detail_id').select2('readonly', false);
    }
}

function getLocations(locations, selected_id = 0) {
    var option = '<option value=""></option>';
    $.each(locations, function(index, el) {
        selected = '';
        // if (selected_id == 0 && index == 0) {
        //     selected = "selected";
        // }
        option+= '<option '+selected+' value="'+el.warehouse_id+'__'+el.localtion+'">'+el.location_name+'</option>';
        // option+= '<option '+selected+' value="'+el.localtion+'">'+el.location_name+'</option>';
    });
    return option;
}

function getLocationsNew(locationsW, selected_id = 0) {
    var option = '<option value=""></option>';
    $.each(locationsW, function (index, value) { 
        option+= '<optgroup label="'+value.text+'">';
        if (value.children) {
            $.each(value.children, function (i, v) { 
                option+= '<option value="'+v.id+'">'+v.text+'</option>';
            });
        }
        option+= '</optgroup>';
    });

    return option;
}

function ajaxSelectParamsNew(element, url, id, params = false, clearSl2 = false)
{
    console.log(clearSl2);
    if (id)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            formatResult: formatProductions,
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
                    if(data.results != null) {
                        return { results: data.results };
                    } else {
                        return { results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            formatResult: formatProductions,
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
function formatProductions(state)
{
    if (!state.id) return state.text;
    reference_no_orders = state.reference_no_order;
    reference_no_productions = state.reference_no_production;
    customer_name = state.customer_name;
   
    html = '';
    if(reference_no_orders != null){
        html += `<div>Đơn hàng: ${reference_no_orders}<div>`;
    }
    if(customer_name != null){
        html += `<div>Khách hàng: ${customer_name}<div>`;
    }
    if(reference_no_productions != null){
        html += `<div>LSX: ${reference_no_productions}<div>`;
    }
    var tr = '' +
            '<div class="bold" style="font-size: 14px;">' + state.text + '</div>' +
            html
            '';
    tableSelect = tr;
    return tableSelect;
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

$(document).ready(function() {
	// init_editor('textarea[name="note"]');
    $('#warehouses').select2();
    $('#branch_id').select2();


	dt = $('#tb-items').DataTable({
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


    ajaxSelectParamsCallbackPOManuTotal('#po_id', 'admin/Manufacture/searchProductions', 0, {type: 1});
    if (edit == 0) {
        ajaxSelectParamsNew('#productions_orders_detail_id', 'admin/stock/searchProductionsOrdersDetail', id_pod);
    } else if (edit == 1) {
        $('#warehouses').select2('readonly', true);
        $('select.locations').select2();
    }

	$('.add-row').on('click', function(event) {
		event.preventDefault();
        productions_orders_detail_id = $('#productions_orders_detail_id').val();
        warehouses = $('#warehouses').val();
        if (!productions_orders_detail_id) {
            // bootbox.alert(lang_ex['tnh_please_chosen_pod']);
            // return;
        }

        // <input type="hidden" name="unit_id[]" id="unit_id" class="form-control unit_id" value="">\

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
        // tdUnit = '<div class="td-unit"></div>';
        tdUnit = '<div class="td-unit"><select name="unit_id[]" data-placeholder="'+ lang_core['choose'] +'" id="unit_id" class="unit_id" style="width: 100%;"></select></div>';
        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 100%;"></select></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="1"></div>';
        tdValueExchange = '<div class="text-center td-value-exchange"></div>';
        tdQuantityExchange = '<div class="text-center td-quantity-exchange"></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

		rowNode = dt.row.add( [
            tdRef,
            tdItem,
            tdItemName,
            tdUnit,
            tdLocation,
            tdQuantity,
            tdValueExchange,
            tdQuantityExchange,
            tdActions
        ] ).draw( false ).node();

        if (!productions_orders_detail_id) {
            ajaxSelectParams($('#items_'+ counter +''), 'admin/stock/searchMaterialAndSemiProducts', 0);
        } else {
            ajaxSelectParams($('#items_'+ counter +''), 'admin/stock/searchItemsByProductionDetail', 0, {productions_orders_detail_id: productions_orders_detail_id});
        }
        $('select.locations').select2();
        $('select.unit_id').select2();
        counter++;
        totalExportingProductions();
	});

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        paramsData['warehouse_id'] = $('#warehouses').val();
        paramsData['item_id'] = item_id;
        row.find('select.locations').val(null).trigger('change');
        row.find('select.locations').html('');
        if (item_id) {
            tr = $(sl).closest('tr');
            subtext = data.item_name;
            unit_name = data.unit_name;
            unit_id = data.unit_id;
            unit_parent_id = data.unit_parent_id;
            number_exchange = data.number_exchange;
            number_exchange = 1;
            

            // tr.find('.unit_id').val(unit_id);
            optionUnit = '<option value="0"></option>';
            if (unit_parent_id) {
                optionUnit+= '<option data-number-exchange="1" selected value="'+unit_parent_id+'__1">'+data.unit_parent_name+'</option>';
            }

            if (unit_id+'__'+data.number_exchange != unit_parent_id+'__'+1) {
                optionUnit+= '<option data-number-exchange="'+data.number_exchange+'" value="'+unit_id+'__'+data.number_exchange+'">'+unit_name+'</option>';
            }

            row.find('select.unit_id').html(optionUnit);

            tr.find('.unit_parent_id').val(unit_parent_id);
            tr.find('.number_exchange').val(number_exchange);

            tr.find('.td-item-name').html(subtext);
            // tr.find('.td-unit').html(unit_name);
            tr.find('.td-value-exchange').html(number_exchange);

            lastrow = $('#tb-items tbody tr')[$('#tb-items tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }

            //ajax
            $.ajax({
                // url: site.base_url+'admin/stock/rowItem',
                url: site.base_url+'admin/stock/rowLocationWarehouseNew_warehouse',
                type: 'POST',
                dataType: 'json',
                data: paramsData
            })
            .done(function(data) {
                if (data) {

                    row.find('select.locations').html(getLocationsNew(data.results));
                    // row.find('select.locations').val(data.valSelected).trigger('change');
                }
            })
            .fail(function() {
                console.log("error");
            })
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
        }
        totalExportingProductions();
    });

    $('#productions_orders_detail_id').on('change', function(event) {
        var productions_orders_detail_id = $(this).val();
        dt.rows().remove().draw();
        warehouses = $('#warehouses').val();
        if (warehouses) {
            $('.add-row').click();
            totalExportingProductions();
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
        totalExportingProductions();
    });

    $(document).on('change', '.quantity, .quantity_sub', function(event) {
        totalExportingProductions();
    });


	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
		dt.row( $(this).parents('tr') ).remove().draw();
		totalExportingProductions();
	});

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.row').remove();
        totalExportingProductions();
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
    			url: site.base_url+'admin/stock/refereshReferenceProductionsOrders',
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

	appValidateForm($('#add-exporting'), {
		reference_no: 'required',
        date: 'required',
        branch_id: 'required',
        // export_name: 'required',
       	// warehouses: 'required',
        // productions_orders_detail_id: 'required'
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
        		window.location.href = site.base_url+'admin/stock/exporting_producion';
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

if (edit == 0)
{
    if (id_pod) {
        function loadData() {
            warehouse_id = $('#warehouses').val();
            dt.rows().remove().draw();
            $.ajax({
                url: site.base_url+'admin/stock/getProductionsOrdersDetailAll',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token_name: hash,
                    id_pod: id_pod,
                    warehouse_id: warehouse_id
                },
            })
            .done(function(data) {
                if (data)
                {
                    $.each(data.items, function(index, el) {
                        item_id = el.id;
                        item_code = el.text;
                        item_name = el.item_name;
                        unit_name = el.unit_name;
                        unit_id = el.unit_id;
                        unit_parent_id = el.unit_parent_id;
                        number_exchange = el.number_exchange;
                        quantityExport = el.quantity;

                        tdRef = '<div class="stt text-center"></div>';
                        tdItem = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
                            <input type="hidden" name="unit_id[]" id="unit_id" class="form-control unit_id" value="'+unit_id+'">\
                            <input type="hidden" name="unit_parent_id[]" id="unit_parent_id" class="form-control unit_parent_id" value="'+unit_parent_id+'">\
                            <input type="hidden" name="number_exchange[]" id="number_exchange" class="form-control number_exchange" value="'+number_exchange+'">\
                            <input type="hidden" name="items_id[]" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="'+item_id+'">'+item_code;
                        
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
                        tdItemName = '<div class="td-item-name">'+item_name+'</div>';
                        tdUnit = '<div class="td-unit">'+unit_name+'</div>';
                        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 180px;">'+getLocations(el.warehouses, 0)+'</select></div>';
                        tdQuantity = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="'+tnhFormatNumber(quantityExport)+'"></div>';
                        tdValueExchange = '<div class="text-center td-value-exchange">'+number_exchange+'</div>';
                        tdQuantityExchange = '<div class="text-center td-quantity-exchange"></div>';
                        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';

                        rowNode = dt.row.add( [
                            tdRef,
                            tdItem,
                            tdItemName,
                            tdUnit,
                            tdLocation,
                            tdQuantity,
                            tdValueExchange,
                            tdQuantityExchange,
                            tdActions
                        ] ).draw( false ).node();
                        
                        $('select.locations').select2();

                        counter++;
                    });
                    totalExportingProductions();
                }
            })
            .fail(function() {
                console.log("error");
            });
        }
    }
}