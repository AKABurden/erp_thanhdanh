function totalPurchaseProducts()
{
	tb = '#tb-purchases tbody tr:not("[class^=not-tr]")';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.td-number').html(stt);
        // quantity = intVal($(element).find('.td-quantity').html());
        quantity = intVal($(element).find('.quantity').val());

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

    if (n > 0) {
        $('#warehouses').select2('readonly', true);
    } else {
        $('#warehouses').select2('readonly', false);
    }
}

function removeRow(el)
{
	dt.row( $(el).parents('tr') ).remove().draw();
	totalPurchaseProducts();
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
	$('#warehouses').select2();
	$('#branch_id').select2();
    ajaxSelectParamsCallbackPOManuTotal('#po_id', 'admin/Manufacture/searchProductions', $('#po_id').val(), {type: 1});

    if (edit == 0) {
        $(document).on('click', '.referesh-reference', function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/stock/refereshReferencePurchaseProducts',
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
        $('#warehouses').select2('readonly', true);
        $('select.locations').select2();
        for (i = 0; i < counter; i++)
        {
            locationCurrent = $('input[name="localtion_current['+i+']"]').val();
            ajaxSelectCallBack($('input#items_'+ i +''), 'admin/products/searchProductsSelect2', $('input#items_'+ i +'').val());
            $('select[name="location_id['+i+']"]').val(locationCurrent).trigger('change');
        }
    }

	dt = $('#tb-purchases').DataTable({
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
	    },
	});

    $(document).on('change', '#warehouses', function(event) {
        event.preventDefault();
        warehouse_id = $(this).val();
        if (warehouse_id) {
            $.ajax({
                url: site.base_url+'admin/stock/getLocationWarehouses',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    warehouse_id: warehouse_id,
                },
            })
            .done(function(data) {
                if (data) {
                    locations = data.locations;
                } else {
                    locations = '';
                }
            })
            .fail(function() {
                console.log("error");
            });
        }
    });

	$('.add-row').on('click', function(event) {
		event.preventDefault();
		warehouse_id = $('#warehouses').val();
        if (!warehouse_id) {
            bootbox.alert(lang_purchase['tnh_please_chosen_warehouse']);
            return;
        }

		tdNumber = '<div class="td-number text-center"></div>';
		tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">\
                <input type="text" name="items_id['+counter+']" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value=""></div>'+
                '<div class="type-item"></div>'+
                '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">'+lang_core['delete']+'</a></div></div>';
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
        tdName = '<div class="td-item-name">'+lang_core['product_name']+'</div>';
        tdUnit = '<div class="td-unit"></div>';
        tdPosition = '<div class="td-position"><select data-placeholder="'+lang_core['choose']+'" name="location_id['+counter+']" id="locations" class="locations" style="width: 100%;"><option value=""></option>'+locations+'</select></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity['+counter+']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"><div class="show-exchange text-primary mtop5 hide"></div></div>';
        tdNote = '<div class="td-note"><textarea name="note_items['+counter+']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
		tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';

		rowNode = dt.row.add( [
            tdNumber,
            tdCode,
            tdImage,
            tdName,
            tdUnit,
            tdPosition,
            tdQuantity,
            tdNote,
            tdActions
        ] ).draw( false ).node();
        ajaxSelectCallBack($('input#items_'+ counter +''), 'admin/products/searchProductsSelect2', 0);
        $('select.locations').select2();
        counter++;
        totalPurchaseProducts();
	});

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        if (item_id) {
            tr = $(sl).closest('tr');
            $.ajax({
                url: site.base_url+'admin/stock/getExchangeProduct',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    item_id: item_id,
                    csrf_token_name: hash,
                },
            })
            .done(function(response) {
                subtext = data.item_name;
                // unit_name = data.unit_name;
                unit_name = data.unit_stock;
                unit_id = data.unit_id;
                unit_parent_id = data.unit_parent_id;
                number_exchange = data.number_exchange;

                tr.find('.unit_id').val(unit_id);
                tr.find('.unit_parent_id').val(unit_parent_id);
                tr.find('.td-item-name').html(subtext);
                tr.find('.td-unit').html(unit_name);

                // show-exchange
                tr.find('.show-exchange').html(response.htmlExchange);
                //

                lastrow = $('#tb-purchases tbody tr')[$('#tb-purchases tbody tr').length - 1];
                if ($(lastrow).find('input.items_id').select2('val')) {
                    $('.add-row').click();
                }
                totalPurchaseProducts();

            });
        } else {
            tr.find('.td-item-name').html('');
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            totalPurchaseProducts();
        }
    });

    $(document).on('change', '.quantity', function(event) {
        totalPurchaseProducts();
    });

	appValidateForm($('#purchase_product'), {
		reference_no: 'required',
        date: 'required',
        branch_id: 'required',
        warehouses: 'required'
    }, db);

    function db(form) {
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
        		window.location.href = site.base_url+'admin/stock/purchase_products';
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