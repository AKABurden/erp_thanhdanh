function totalProductionsOrders()
{
    tb = '#tb-productions-orders tbody tr:not("[class^=not-tr]")';
    var table = $(tb).length;
    var stt = 0;
    var total_quantity = 0;
    count_errors = 0;
    var flagChonsePL = 0;
    for (ii = 0; ii < table; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.stt').html(stt);
        quantity = intVal($(element).find('.quantity').val());
        cproduction_plan_item_id = $(element).find('.production_plan_item').val();
        if (cproduction_plan_item_id > 0) {
            quantity_produce = intVal($(element).find('.quantity_produce').val());
            if (quantity > quantity_produce) {
                $(element).find('.error-quantity_produce').html('SL <= '+tnhFormatNumber(quantity_produce));
                count_errors++;
            } else {
                $(element).find('.error-quantity_produce').html('');
            }
            flagChonsePL = 1;
        }
        total_quantity+= quantity;
    }
    $('.th-total-quantity').html(tnhFormatNumber(total_quantity));
    if (flagChonsePL == 1) {
        $('#productions_plan').select2('readonly', true);
    } else {
        $('#productions_plan').select2('readonly', false);
    }
}

function addRowShipping(counter, _this)
{
    var div = $(_this).closest('.td-date');

    html = '<div class="row">'+
                '<div class="col-md-7" style="padding: 0px;"><input type="text" name="date_sub['+counter+'][]" id="input" class="form-control datepicker date_sub" placeholder="'+lang_core['date']+'" value="" style="width: 100%;" title=""></div>'+
                '<div class="col-md-4" style="padding: 0px;"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" style="width: 100%;" name="quantity_sub['+counter+'][]" id="input" class="form-control quantity_sub" value="0" title=""></div>'+
                '<div class="col-md-1" style="padding: 0px;"><div style="margin: 50%;"><i class="fa fa-remove remove-sub pointer text-danger"></i></div></div>'+
            '</div>';
    div.find('.sub').append(html);
    totalProductionsOrders();
    init_datepicker();
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

function loadOrdersAndBusinessPlan() {
    cOrders = 0;
    cBusiness = 0;
    if($('#options1').prop('checked')) {
        cOrders = 1;
    }

    if($('#options2').prop('checked')) {
        cBusiness = 1;
    }

    ajaxSelectFormatPOPlanMultipleCallBack('#productions_plan', 'admin/manufactures/searchProductionsPlanForOrders', 0, {cOrders: cOrders, cBusiness: cBusiness});
}

$(document).ready(function() {
	init_editor('textarea[name="note"]');
    // ajaxSelectFormatPOPlanMultipleCallBack('#productions_plan', 'admin/manufactures/searchProductionsPlanForOrders', 0);

    loadOrdersAndBusinessPlan();
    $(document).on('change', '#options1, #options2', function(event) {
        loadOrdersAndBusinessPlan();
    });

	dt = $('#tb-productions-orders').DataTable({
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

    $(document).on('change', '.items_id', function(event) {
        event.preventDefault();
        data = event.added;
        sl = this;
        item_id = $(sl).val();
        if (item_id) {
            tr = $(sl).closest('tr');
            subtext = data.item_name;
            images = data.images;
            if (images) {
                tr.find('.td-image a').attr('href', site.base_url+images);
                tr.find('.td-image img').attr('src', site.base_url+images);
            } else {
                tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
                tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
            }
            tr.find('.td-item-name').html(subtext);

            lastrow = $('#tb-productions-orders tbody tr')[$('#tb-productions-orders tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            tr.find('.td-item-name').html(lang_core['product_name']);
            tr.find('.td-image a').attr('href', site.base_url+'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url+'assets/images/tnh/no_image.png');
        }
    });

    // select2-selecting
    $('#productions_plan').on('change', function(event) {
        // var productions_plan_id = event.object.id;
        var productions_plan_id = $(this).val();
        $('#items').val('');
        ajaxSelectParamsCallback('#items', 'admin/manufactures/searchProductionsPlanItem', 0, {productions_plan_id: productions_plan_id});
        $('.tr-production-plan').find('.remove-row').trigger('click');
        return;
    });

    $(document).on('click', '.ev-all', function(event) {
        event.preventDefault();
        productions_plan_id = $('#productions_plan').val();
        if (productions_plan_id) {
            dt.rows().remove().draw();
            $.ajax({
                url: site.base_url+'admin/manufactures/getProductionsPlanItem',
                type: 'POST',
                dataType: 'json',
                data: {
                    productions_plan_id: productions_plan_id,
                    csrf_token_name: hash,
                },
            })
            .done(function(data) {
                if (data.result) {

                    $.each(data.result, function(index, el) {
                        production_reference_no = el.reference_no;
                        production_plan_item_id = el.production_plan_item_id;
                        items_id = el.product_id;
                        code = el.code;
                        item_name = el.name;
                        images = el.images;
                        quantity = el.total_quantity;
                        if (quantity < 0) {
                            quantity = 0;
                        }

                        dt_production_plan_item_id = el.production_plan_item_id;
                        dt_production_plan_item_id = dt_production_plan_item_id.split('__');
                        strType = '';
                        if (dt_production_plan_item_id[0] == "orders") {
                            strType = `<div class="ntop10"><span class="label label-primary">${lang_productions_orders.tnh_don_hang}</span></div>`;
                        } else if (dt_production_plan_item_id[0] == "business_plan") {
                            strType = `<div class="ntop10"><span class="label label-warning">${lang_productions_orders.tnh_khbtp}</span></div>`;
                        }

                        if (images) {
                            images = site.base_url+images;
                        } else {
                            images = site.base_url+'assets/images/tnh/no_image.png';
                        }
                        
                        td1 = '<div class="stt text-center"></div>';
                        tdProductionPlan = '<div class="td-production-plan">'+production_reference_no+ strType+'</div>';
                        tdName = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
                            <input type="hidden" name="production_plan_item[]" id="production_plan_item" class="form-control production_plan_item" value="'+production_plan_item_id+'">\
                            <input type="hidden" name="items_id[]" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="'+items_id+'">'+item_name+'('+code+')';
                        tdImage = '<div class="td-image">'+
                                    '<div class="preview_image" style="width: auto;">'+
                                        '<div class="display-block contract-attachment-wrapper img">'+
                                            '<div style="width:45px; margin: auto;">'+
                                                '<a href="'+images+'" data-lightbox="customer-profile" class="display-block mbot5">'+
                                                    '<div class="">'+
                                                        '<img src="'+images+'" style="border-radius: 50%">'+
                                                    '</div>'+
                                                '</a>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                            '</div>';
                        td4 = `<div class="td-item-name">
                            <select name="versions[]" class="versions" data-placeholder="BOM" style="width: 100%;">
                                ${el.optionsVersions}
                            </select>
                        </div>`;

                        tdDetail = `<div class="td-detail">
                            <div>SL đơn hàng: ${tnhFormatNumber(el.quantity)}</div>
                            <div>SL đã lên KHNVL: ${tnhFormatNumber(el.quantity_plan)}</div>
                        </div>`;
                        td5 = '<div class="td-quantity"><input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="'+tnhFormatNumber(quantity)+'"></div><input type="hidden" name="quantity_produce" id="quantity_produce" class="form-control quantity_produce" value="'+quantity+'"><div class="text-warning mtop5 text-quantity_produce">SL được sản xuất: '+tnhFormatNumber(quantity)+'</div><div class="error-quantity_produce text-danger"></div>';
                        td6 = '<div class="td-note"><textarea name="note_items[]" id="note_items[]" class="form-control" rows="3"></textarea></div>';
                        td7 = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';
                        rowNode = dt.row.add( [
                            td1,
                            tdProductionPlan,
                            tdImage,
                            tdName,
                            td4,
                            tdDetail,
                            td5,
                            td6,
                            td7
                        ] ).draw( false ).node();

                        $(rowNode).addClass('tr-production-plan');
                        counter++;
                    });

                    $('select.versions').select2();
                    totalProductionsOrders();
                }
            })
            .fail(function() {
                console.log("error");
            });
        }
    });

    $('#items').change(function(event) {
        production_plan_item_id = $(this).val();
        if (production_plan_item_id) {
            $.ajax({
                url: site.base_url+'admin/manufactures/rowProductionsPlanItem',
                type: 'POST',
                dataType: 'json',
                data: {
                    csrf_token_name: hash,
                    production_plan_item_id: production_plan_item_id
                },
            })
            .done(function(data) {
                if (data.item) {

                    production_reference_no = data.item.reference_no;
                    production_plan_item_id = data.item.production_plan_item_id;
                    if($('#tb-productions-orders').find('.production_plan_item[value="'+production_plan_item_id+'"]').length > 0) {
                        rowCur = $('#tb-productions-orders').find('.production_plan_item[value="'+production_plan_item_id+'"]').closest('tr');
                        qtyCur = intVal($(rowCur).find('.quantity').val()) + 1;
                        $(rowCur).find('.quantity').val(tnhFormatNumber(qtyCur));
                        totalProductionsOrders();
                        return;
                    }

                    dt_production_plan_item_id = data.item.production_plan_item_id;
                    dt_production_plan_item_id = dt_production_plan_item_id.split('__');
                    strType = '';
                    if (dt_production_plan_item_id[0] == "orders") {
                        strType = `<div class="ntop10"><span class="label label-primary">${lang_productions_orders.tnh_don_hang}</span></div>`;
                    } else if (dt_production_plan_item_id[0] == "business_plan") {
                        strType = `<div class="ntop10"><span class="label label-warning">${lang_productions_orders.tnh_khbtp}</span></div>`;
                    }

                    items_id = data.item.product_id;
                    code = data.item.code;
                    item_name = data.item.name;
                    images = data.item.images;
                    quantity = data.item.total_quantity;
                    if (quantity < 0) quantity = 0;

                    if (images) {
                        images = site.base_url+images;
                    } else {
                        images = site.base_url+'assets/images/tnh/no_image.png';
                    }
                    td1 = '<div class="stt text-center"></div>';
                    tdProductionPlan = '<div class="td-production-plan">'+production_reference_no+ strType +'</div>';
                    tdName = '<input type="hidden" name="counter[]" id="input" class="form-control" value="'+counter+'">\
                        <input type="hidden" name="production_plan_item[]" id="production_plan_item" class="form-control production_plan_item" value="'+production_plan_item_id+'">\
                        <input type="hidden" name="items_id[]" id="items_'+counter+'" class="items_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="'+items_id+'">'+item_name+'('+code+')';
                    tdImage = '<div class="td-image">'+
                                '<div class="preview_image" style="width: auto;">'+
                                    '<div class="display-block contract-attachment-wrapper img">'+
                                        '<div style="width:45px; margin: auto;">'+
                                            '<a href="'+images+'" data-lightbox="customer-profile" class="display-block mbot5">'+
                                                '<div class="">'+
                                                    '<img src="'+images+'" style="border-radius: 50%">'+
                                                '</div>'+
                                            '</a>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                        '</div>';
                    td4 = `<div class="td-item-name">
                        <select name="versions[]" class="versions" data-placeholder="BOM" style="width: 100%;">
                            ${data.item.optionsVersions}
                        </select>
                    </div>`;

                    tdDetail = `<div class="td-detail">
                        <div>SL đơn hàng: ${tnhFormatNumber(data.item.quantity)}</div>
                        <div>SL đã lên KHNVL: ${tnhFormatNumber(data.item.quantity_plan)}</div>
                    </div>`;

                    td5 = '<div class="td-quantity">'+
                        '<input type="text" onkeyup="formatNumBerKeyUpCus(this)" name="quantity[]" id="quantity[]" class="form-control quantity" value="'+tnhFormatNumber(quantity)+'">'+
                        '<input type="hidden" name="quantity_produce" id="quantity_produce" class="form-control quantity_produce" value="'+quantity+'"><div class="text-warning mtop5 text-quantity_produce">SL được sản xuất: '+tnhFormatNumber(quantity)+'</div><div class="error-quantity_produce text-danger">'
                    '</div>';
                    td6 = '<div class="td-note"><textarea name="note_items[]" id="note_items[]" class="form-control" rows="3"></textarea></div>';
                    td7 = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row"></i></div>';
                    rowNode = dt.row.add( [
                        td1,
                        tdProductionPlan,
                        tdImage,
                        tdName,
                        td4,
                        tdDetail,
                        td5,
                        td6,
                        td7
                    ] ).draw( false ).node();

                    $(rowNode).addClass('tr-production-plan');
                    $('select.versions').select2();
                    counter++;
                    totalProductionsOrders();
                }
            })
            .fail(function() {
                console.log("error");
            });
        }
        $('#items').val('');
    });

    $('#enquiery').on('select2-removed', function (e) {
        // enquiery = e.val;
    });

    $(document).on('change', '.quantity, .quantity_sub', function(event) {
        totalProductionsOrders();
    });


	$(document).on('click', '.remove-row', function(event) {
		event.preventDefault();
		dt.row( $(this).parents('tr') ).remove().draw();
		totalProductionsOrders();
	});

    $(document).on('click', '.remove-sub', function(event) {
        event.preventDefault();
        $(this).closest('.row').remove();
        totalProductionsOrders();
    });


    $(document).on('click', '.add-row-foot', function(event) {
        event.preventDefault();
        $('.add-row').click();
    });

    if (edit == 0) {
        $('.add-row').click();
    	$(document).on('click', '.referesh-reference', function(event) {
    		event.preventDefault();
    		$.ajax({
    			url: site.base_url+'admin/manufactures/refereshReferenceProductionsOrders',
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

	appValidateForm($('#add-productions-orders'), {
		reference_no: 'required',
        date: 'required',
       	location: 'required',
    }, db);

    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', 'Vui lòng kiểm tra lại số lượng cần sản xuất');
            return;
        }
    	$('.add').attr('disabled', 'disabled');
        tinymce.get('note').save();
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
        //
        var url = form.action;
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
        		window.location.href = site.base_url+'admin/manufactures/productions_orders';
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

