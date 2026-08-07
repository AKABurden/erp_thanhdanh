function totalOutsource()
{
    tb = '#tb-deliveries tbody tr.tnh-handling';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;
    var total_quantity_outsource = 0;
    var grand_total = 0;
    for (ii = 0; ii < n; ii++)
    {
        stt++;
        element = $(tb)[ii];
        $(element).find('.td-number').html(stt);
        quantity_outsource = intVal($(element).find('.quantity_outsource').val());
        quantity = intVal($(element).find('.td-quantity').html());
        quantity_had_outsource = intVal($(element).find('.td-quantity-had-outsource').html());
        quantity_max = quantity - quantity_had_outsource;

        // if (quantity_outsource > quantity_max) {
        //     $(element).find('.show-error-item').html(lang_outsource['tnh_quantity_outsource_less']+' '+quantity_max);
        //     count_errors++;
        // } else {
        //     $(element).find('.show-error-item').html('');
        // }


        price = intVal($(element).find('.price_outsource').val());
        subtotal = price * quantity_outsource;
        $(element).find('.td-subtotal').html(tnhFormatMoney(subtotal));
        total_quantity_outsource+= quantity_outsource;
        grand_total+= subtotal;
    }

    $('.th-quantity-processing').html(tnhFormatNumber(total_quantity_outsource));
    $('.th-subtotal').html(tnhFormatMoney(grand_total));

    if (n > 0) {
        $('#id_branch').select2('readonly', true);
    } else {
        $('#id_branch').select2('readonly', false);
    }
}

function totalMaterial()
{
    tbMaterial = '#tb-items-export tbody tr';
    var nMaterial = $(tbMaterial).length;
    var stt = 0;

    for (ii = 0; ii < nMaterial; ii++)
    {
        stt++;
        elementMaterial = $(tbMaterial)[ii];
        $(elementMaterial).find('.td-number').html(stt);

        quantityMaterial = intVal($(elementMaterial).find('.quantity_material').val());
        priceMaterial = intVal($(elementMaterial).find('.price_material').val());
        amountMaterial = quantityMaterial * priceMaterial;

        $(elementMaterial).find('.td-amount-material').html(tnhFormatMoney(amountMaterial));
    }
}

function removeRow(el)
{
    dt.row( $(el).parents('tr') ).remove().draw();
    totalOutsource();
}

function removeRowMaterial(el)
{
    dtMaterial.row( $(el).parents('tr') ).remove().draw();
    totalMaterial();
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
                totalOutsource();
            }
        }
    });
}

function refershItemsTable()
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
                dtMaterial.rows().remove().draw();
            }
        }
    });
}

function loadBom(_this)
{
    bootbox.confirm({
        message: lang_outsource['tnh_do_you_load_bom'],
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
                dtMaterial.rows().remove().draw();
                formDT = $('#outsource').serialize();
                $.ajax({
                    url: site.base_url+'admin/outsource/loadBom',
                    type: 'POST',
                    dataType: 'json',
                    data: formDT
                })
                    .done(function(data) {
                        if (data) {
                            $.each(data.itemsBom, function(index, el) {
                                tdNumber = '<div class="text-center td-number"></div>';
                                tdItem = '<input type="hidden" name="counter_material[]" id="input" class="form-control" value="'+counterMaterial+'">\
                                <input type="text" name="items_material_id[]" id="items_material_id_'+counterMaterial+'" class="items_material_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="'+el.id+'">';
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
                                tdItemName = '<div class="td-item-name-material">'+el.name+'</div>';
                                tdUnit = '<div class="td-unit-material">'+el.unit_name+'</div>';
                                tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 180px;"></select></div>';
                                tdQuantity = '<div class="td-quantity-material"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material" style="width: 100%;" value="'+el.quantity+'"></div>';
                                tdPrice = '<div class="td-price-material"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price_material[]" id="price_material[]" class="form-control price_material" value="0"></div>';
                                tdAmount = '<div class="td-amount-material text-right"></div>';
                                tdNote = '<div class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></div>';
                                // tdActions = '<div class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></div>';
                                tdActions = '';

                                rowNodeMaterial = dtMaterial.row.add( [
                                    tdNumber,
                                    tdItem,
                                    tdItemName,
                                    tdUnit,
                                    // tdLocation,
                                    tdQuantity,
                                    // tdPrice,
                                    // tdAmount,
                                    tdNote,
                                    // tdActions
                                ] ).draw( false ).node();
                                ajaxSelectParams($('#items_material_id_'+ counterMaterial +''), 'admin/outsource/searchItemsOutside', $('#items_material_id_'+ counterMaterial +'').val());
                                // $('#items_material_id_'+ counterMaterial +'').val(el.id).trigger('change');
                                counterMaterial++;
                            });
                            totalMaterial();
                        }
                    })
                    .fail(function() {
                        console.log("error");
                    });
            }
        }
    });
}
function getStage(stages = '',select_id)
{
    var option = '<option value=""></option>';
    $.each(stages, function(index, el) {
        selected = select_id == el.stage_id ? 'selected' : '';
        option+= '<option '+selected+' value="'+el.stage_id+'">'+el.stage_name+'</option>';
    });
    return option;
}

$(document).ready(function() {
    $('#employees').select2();
    $('#warehouses').select2();
    $('#export_form').select2();
    $('#id_branch').select2();
    $(".stage_all").select2();
    $('.ev-all').hide();
    //handling change export form
    $('#export_form').change(function(event) {
        export_form = $(this).val();
        dt.rows().remove().draw();
        $('#reference_orders').val(null);
        $('.show-hide').find('.select2-search-choice').remove();
        if (export_form == 1) {
            $('.show-hide').show();
            $('#reference_orders').select2('readonly', false);
            dt.columns([1, 6, 7]).visible( true, true );
            $('.ev-all').show();
        } else {
            $('.show-hide').hide();
            $('#reference_orders').select2('readonly', true);
            ajaxSelectParamsCallback('#items', 'admin/outsource/searchProductAndSemiProduct', 0);
            dt.columns([1, 6, 7]).visible( false, false );
            $('.ev-all').hide();
        }
    });
    //


    // init_editor('textarea[name="note"]');
    ajaxSelectParamsCallback('#supplies', 'admin/outsource/searchSuppliers', $('#supplies').val(), {type: 0}, true);

    if (edit == 0) {
        ajaxSelectMultipleParams('#reference_orders', 'admin/outsource/getOrdersByOutsource', 0, false);
    } else {
        orders_id = $('.reference_orders').val();
        ajaxSelectParams('#items', 'admin/outsource/getOrdersItemsByOrderId', 0, {'orders_id': orders_id, 'edit': edit, 'outsource_id': outsource_id});
    }

    $(document).on('change', '#reference_orders', function(event) {
        event.preventDefault();
        orders_id = $(this).val();
        ajaxSelectParamsCallback('#items', 'admin/outsource/getOrdersItemsByOrderId', 0, {'orders_id': orders_id, 'edit': edit});
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

    dtMaterial = $('#tb-items-export').DataTable({
        "language": lang_datatables,
        'searching': false,
        'ordering': false,
        'paging': false,
        "info": false,
        'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            $(nRow).addClass('tnh-handling');
        },
    });

    function createdRowOrderItem(order_item, counter)
    {
        elTr = $('.order_item_id[value="'+order_item.id+'"]').closest('tr');
        if(elTr.length > 0) {
            quantity_outsource_current = intVal(elTr.find('.quantity_outsource').val()) + 1;
            elTr.find('.quantity_outsource').val(tnhFormatNumber(quantity_outsource_current));
        } else {
            tdNumber = '<div class="td-number text-center"></div>';
            tdReferenceOrder = '<div class="td-referece-order">'+order_item.reference_no+'</div>';
            tdCode = '<div class="td-code mbot10">'+
                '<input type="hidden" name="order_item_id['+counter+']" id="order_item_id" class="form-control order_item_id" value="'+order_item.id+'">'+
                '<input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">'+
                order_item.item_code+
                '<div class="type-item"></div>'+
                '<div><div class=""><a href="javascript:void(0)" onclick="removeRow(this)" class="text-danger delete-remind remove-row">'+lang_core['delete']+'</a></div></div>';
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
            tdQuantityHadOutsource = '<div class="td-quantity-had-outsource text-center">'+tnhFormatNumber(order_item.quantity_outsource)+'</div>';
            quantityOutsource = intVal(order_item.quantity) - intVal(order_item.quantity_outsource);
            if (quantityOutsource < 0) quantityOutsource = 0;
            tdquantityOutsource = '<div class="td-quantity-outsource"><input type="text" name="quantity_outsource['+counter+']" id="quantity_outsource[]" onchange="totalOutsource()" class="form-control quantity_outsource number-format" value="'+tnhFormatNumber(quantityOutsource)+'"><div class="show-error-item text-danger"></div></div>';
            tdPrice = '<div class="td-price-outsource"><input type="text" onchange="totalOutsource()" name="price_outsource['+counter+']" id="price_outsource[]" class="form-control price_outsource money-format" value="'+tnhFormatMoney(order_item.processing)+'"></div>';
            tdSubtotal = '<div class="td-subtotal text-right"></div>';
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
                tdQuantityHadOutsource,
                tdquantityOutsource,
                tdPrice,
                tdSubtotal,
                tdNote,
                tdActions
            ] ).draw( false ).node();
        }
        totalOutsource();
    }

    function createdRowItem(data, counter)
    {
        console.log(data);
        td_detail = '';
        elTr = $('.check_item[value="' + data.id + '__' + data.pod_id + '"]').closest('tr');
        title = '';
        reference_no = '';
        if(data.object_type == "business_plan"){
            title = ' KHKD';
            reference_no = data.reference_no_plan;
        } else if(data.object_type == "orders"){
            title = 'Đơn hàng';
            reference_no = data.reference_no;
        }
        td_detail = '' +
            '<div class="bold" style="font-size: 12px;"></div>' +
            '<div>Lệnh SXCT: ' + data.reference_no_production_detail + ' - '+title+': ' +reference_no + '</div>' +
            '';
        if(elTr.length > 0) {
            alert_float('warning', 'Mặt hàng đã tồn tại !');
            return;
        } else {
            qty = 0;
            if (data.total_qty) {
                qty = data.total_qty - data.qty_outsource;
            }
            if (!data.images) {
                data.images = site.base_url + 'assets/images/tnh/no_image.png';
            } else {
                data.images = site.base_url + 'uploads/products/' + data.images;
            }
            plan_id = 0;
            order_id= 0;
            if(data.plan_id == null) {
                plan_id = 0;
            } else {
                plan_id = data.plan_id;
            }
          
            if(data.idd == null) {
                order_id = 0;
            } else {
                order_id = data.idd;
            }
            tdNumber = '<div class="td-number text-center"></div>';
            tdCode = '<div class="td-code mbot10">'+
                '<input type="hidden" name="item_id['+counter+']" id="item_id" class="form-control item_id" value="'+data.id+'">'+
                '<input type="hidden" name="counter['+counter+']" id="counter" class="form-control counter" value="'+counter+'">'+
                '<input type="hidden" class="form-control check_item" value="' + data.id + '__' + data.pod_id + '">' +
                '<input type="hidden" name="pod_id[' + counter + ']" id="pod_id" class="form-control pod_id" value="' + data.pod_id + '">' +
                '<input type="hidden" name="order_id[' + counter + ']" id="order_id" class="form-control order_id" value="' + order_id + '">' +
                '<input type="hidden" name="object_type[' + counter + ']" id="object_type" class="form-control object_type" value="' + data.object_type + '">' +
                '<input type="hidden" name="plan_id[' + counter + ']" id="plan_id" class="form-control plan_id" value="' + plan_id + '">' +
                '<input type="hidden" name="outsource_item_id[' + counter + ']" id="outsource_item_id" class="form-control outsource_item_id" value="0">' +
                '<input type="hidden" class="stage_default" value="'+data.stage_default+'">' +
                data.text+
                '<div style="font-style: italic;font-size: 11px">' + td_detail + '</div>'+
                '<div class="type-item"></div>'+
                '<div><div class=""><a href="javascript:void(0)" onclick="removeRow(this)" class="text-danger delete-remind remove-row">'+lang_core['delete']+'</a></div></div>';
            '</div>';


            tdImage = '<div class="td-image">'+
                '<div class="preview_image" style="width: auto;">'+
                '<div class="display-block contract-attachment-wrapper img">'+
                '<div style="width:45px;">'+
                '<a href="'+data.images+'" data-lightbox="customer-profile" class="display-block mbot5">'+
                '<div class="">'+
                '<img src="'+data.images+'" style="border-radius: 50%">'+
                '</div>'+
                '</a>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>';
            tdStage = '<div style="width:200px" class="td-stage"><select required class="id_stage" style="width: 100%; height: 30px" id="id_stage_'+counter+'" name="id_stage['+counter+']">'+getStage(data.stages,data.stage_default)+'</select></div>';
            tdUnit = '<div class="td-unit">'+data.unit_name+'</div>';
            tdQuantity = '<div class="td-quantity text-center">'+ data.total_qty +'</div>';
            tdQuantityHadOutsource = '<div class="td-quantity-had-outsource text-center">'+ data.qty_outsource +'</div>';

            tdquantityOutsource = '<div class="td-quantity-outsource"><input style="width:100%" type="text" name="quantity_outsource['+counter+']" id="quantity_outsource[]" onchange="totalOutsource()" class="form-control quantity_outsource number-format" value="'+tnhFormatNumber(data.total_qty)+'"><div class="show-error-item text-danger"></div></div>';
            tdPrice = '<div class="td-price-outsource"><input style="width:100%" type="text" onchange="totalOutsource()" name="price_outsource['+counter+']" id="price_outsource[]" class="form-control price_outsource money-format" value="'+tnhFormatMoney(0)+'"></div>';
            tdSubtotal = '<div class="td-subtotal text-right"></div>';
            tdNote = '<div class="td-note">'+
                '<textarea name="note_item['+counter+']" id="note_item[]" class="form-control" rows="3"></textarea>'+
                '</div>';
            tdActions = '<div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row btn btn-danger"></i></div>';

            rowNode = dt.row.add( [
                tdNumber,
                tdCode,
                tdImage,
                tdUnit,
                tdStage,
                // tdQuantity,
                // tdQuantityHadOutsource,
                tdquantityOutsource,
                tdPrice,
                tdSubtotal,
                tdNote,
                tdActions
            ] ).draw( false ).node();
        }
        $("#id_stage_"+counter).select2();
        totalOutsource();
    }

    // $(document).on('change', '.id_stage', function (event) {
    //     event.preventDefault();
    //     row = $(this).closest('tr');
    //     data = event.added;
    //     sl = this;
    //     id_stage = $(this).val();
    //     if (id_stage) {
    //         tr = $(sl).closest('tr');
    //         pod_id = tr.find('.pod_id').val();
    //         outsource_item_id = tr.find('.outsource_item_id').val();
    //         $.ajax({
    //             url: site.base_url + 'admin/outsource/getQuantityOutsource',
    //             type: 'GET',
    //             dataType: 'JSON',
    //             data: {
    //                 id_stage: id_stage,
    //                 pod_id: pod_id,
    //                 outsource_item_id:outsource_item_id,
    //                 csrf_token_name: hash,
    //             },
    //         })
    //             .done(function (response) {
    //                 if(response.result.quantity - response.result.qty_outsource == 0){
    //                     alert_float('warning', 'Công đoạn này đã hết số lượng gia công!');
    //                     tr.find(".id_stage").select2("val", "");
    //                     return;
    //                 } else {
    //                     tr.find('.td-quantity').html(tnhFormatNumber(response.result.quantity));
    //                     tr.find('.td-quantity-had-outsource').html(tnhFormatNumber(response.result.qty_outsource));
    //                 }
    //                 totalOutsource();
    
    //             });
    //     } else {
    //         totalOutsource();
    //     }
    // });

    $(document).on('change', '#items', function(event) {

        data = $("#items").select2('data');
        createdRowItem(data, counter);
        counter++;
        $("#items").select2("val", "");
    });

    $(document).on('change', '#productions', function (event) {
        productions = $('#productions').val();
        data = $("#productions").select2('data');
        production_id = data.id;
        if(production_id){
            link = site.base_url + 'admin/outsource/searchProductbyProductions';
            $.ajax({
                url: link,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    production_id: production_id,
                    csrf_token_name: hash
                },
            })
                .done(function (data) {
                    console.log(data);
                    if(data.results.length > 0){
                        $.each(data.results,function(key,value){
                            createdRowItem(value, counter);
                            counter++;
                        });
                    }
                })
                .fail(function () {
                    console.log("error");
                });
        }
    
        $("#productions").select2("val", "");
    });

    $(document).on('click', '.ev-all', function(event) {
        event.preventDefault();
        orders_id = $('#reference_orders').val();
        if (orders_id) {
            $.ajax({
                url: site.base_url+'admin/outsource/getOrderItem',
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

    $('.add-row').on('click', function(event) {
        event.preventDefault();
        // warehouses = $('#warehouses').val();
        // if (!warehouses) {
        //     bootbox.alert(lang_pi['tnh_please_chosen_warehouse']);
        //     return;
        // }

        tdNumber = '<div class="text-center td-number"></div>';
        tdItem = '<input type="hidden" name="counter_material[]" id="input" class="form-control" value="'+counterMaterial+'">\
            <input type="text" name="items_material_id[]" id="items_material_id_'+counterMaterial+'" class="items_material_id" style="width: 100%;" data-placeholder="'+ lang_core['choose'] +'" value="">';
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
        tdItemName = '<div class="td-item-name-material"></div>';
        tdUnit = '<div class="td-unit-material"></div>';
        tdLocation = '<div class="td-location"><select name="locations[]" data-placeholder="'+ lang_core['choose'] +'" id="locations" class="locations" style="width: 180px;"></select></div>';
        tdQuantity = '<div class="td-quantity-material"><input type="text" onchange="formatNumBerKeyUpCus(this)" name="quantity_material[]" id="quantity_material[]" class="form-control quantity_material" style="width: 100%;" value="1"></div>';
        tdPrice = '<div class="td-price-material"><input type="text" style="width: 100%;" onchange="formatNumBerKeyUpCus(this)" name="price_material[]" id="price_material[]" class="form-control price_material" value="0"></div>';
        tdAmount = '<div class="td-amount-material text-right"></div>';
        tdNote = '<div class="text-center td-note"><textarea name="note_material_item[]" id="note_item" style="width: 100%;" class="form-control note_item" rows="3"></textarea></div>';
        tdActions = '<div class="text-center"><i onclick="removeRowMaterial(this)" class="fa fa-remove btn btn-danger remove-row-material"></i></div>';

        rowNodeMaterial = dtMaterial.row.add( [
            tdNumber,
            tdItem,
            tdItemName,
            tdUnit,
            // tdLocation,
            tdQuantity,
            // tdPrice,
            // tdAmount,
            tdNote,
            tdActions
        ] ).draw( false ).node();
        ajaxSelectParams($('#items_material_id_'+ counterMaterial +''), 'admin/outsource/searchItemsOutside', 0);
        counterMaterial++;
        totalMaterial();
    });

    $(document).on('change', '.items_material_id', function(event) {
        event.preventDefault();
        rowMaterial = $(this).closest('tr');
        dataMaterial = event.added;
        slMaterial = this;
        item_material_id = $(this).val();
        if (item_material_id) {

            trMaterial = $(slMaterial).closest('tr');
            nameMaterial = dataMaterial.name;
            unitName = dataMaterial.unit_name;

            trMaterial.find('.td-item-name-material').html(nameMaterial);
            trMaterial.find('.td-unit-material').html(unitName);

            lastrow = $('#tb-items-export tbody tr')[$('#tb-items-export tbody tr').length - 1];
            if ($(lastrow).find('input.items_id').select2('val')) {
                $('.add-row').click();
            }
        } else {
            trMaterial.find('.td-item-name-material').html('');
        }
        totalMaterial();
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

    $(document).on('change', '.price_material, .quantity_material', function(event) {
        event.preventDefault();
        totalMaterial();
    });

    if (edit == 0) {
        $('.add-row').click();
        $(document).on('click', '.referesh-reference', function(event) {
            event.preventDefault();
            $.ajax({
                url: site.base_url+'admin/outsource/refereshReferenceOutsource',
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
        ajaxSelectParamsCallbackOrder('#items', 'admin/outsource/searchProductByProduction', 0);
        ajaxSelectParamsCallbackProduction('#productions', 'admin/outsource/searchProduction', 0);
    } else if (edit == 1) {
        for (i = 0; i < counterMaterial; i++) {
            ajaxSelectParams($('#items_material_id_'+ i +''), 'admin/outsource/searchItemsOutside', $('#items_material_id_'+ i +'').val());
        }
        for (i = 0; i < counter; i++) {
            $("#id_stage_"+i).select2();
        }
        ajaxSelectParamsCallbackOrder('#items', 'admin/outsource/searchProductByProduction', 0,{outsourceId:pod_id});
        ajaxSelectParamsCallbackProduction('#productions', 'admin/outsource/searchProduction', 0);
    }

    appValidateForm($('#outsource'), {
        // reference_no: 'required',
        date: 'required',
        supplies: 'required',
        warehouses: 'required',
        id_branch: 'required',
    }, db);

    function db(form) {
        if (count_errors > 0) {
            alert_float('danger', 'Kiểm tra lại số lượng');
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
                    window.location.href = site.base_url+'admin/outsource';
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

function ajaxSelectParamsCallbackProduction(
    element,
    url,
    id,
    params = false,
    clearSl2 = false
) {
    if (id != 0) {
        $(element)
            .val(id)
            .select2({
                // minimumInputLength: 1,
                width: "resolve",
                allowClear: clearSl2,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + "/" + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.row);
                        },
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: "json",
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            customer_id: $('#customer_id').val(),
                            id_branch:$("#id_branch").val(),
                            params: params,
                            term: term,
                            limit: 50,
                        };
                    },
                    results: function (data, page) {
                        if (data.results != null) {
                            return {results: data.results};
                        } else {
                            return {
                                results: [{id: "", text: "No Match Found"}],
                            };
                        }
                    },
                },
                formatResult: repoFormatSelectionProduction,
            });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: "resolve",
            allowClear: clearSl2,
            ajax: {
                url: site.base_url + url,
                dataType: "json",
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        customer_id: $('#customer_id').val(),
                        id_branch:$("#id_branch").val(),
                        params: params,
                        term: term,
                        limit: 50,
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        console.log(data.results);
                        return {results: data.results};
                    } else {
                        return {
                            results: [{id: "", text: "No Match Found"}],
                        };
                    }
                },
            },
            formatResult: repoFormatSelectionProduction,
        });
    }
}

function repoFormatSelectionProduction(state) {
    if (!state.id) return state.text;
    if (state.img) {
        var img = '<img class="img_option" src="' + site.base_url + state.img + '"/> ';
    } else {
        var img = '<img class="img_option" src="' + site.base_url + 'download/preview_image"/> ';
    }
    reference_no_business_plan_text = '';
    reference_no_orders_text = '';
    if(state.reference_no_business_plan !=null){
        reference_no_business_plan = state.reference_no_business_plan.split('|||');
        $.each(reference_no_business_plan,function(k,v){
            if((reference_no_business_plan.length - 1) == k){
                reference_no_business_plan_text += `${v}`;
            } else {
                reference_no_business_plan_text += `${v}`+', ';
            }
            
        });
    }
    if(state.reference_no_orders !=null){
        reference_no_orders = state.reference_no_orders.split('|||');
        $.each(reference_no_orders,function(k,v){
            if((reference_no_orders.length - 1) == k){
                reference_no_orders_text += `${v}`;
            } else {
                reference_no_orders_text += `${v}`+', ';
            }
        });
    }
    html = '';
    if(reference_no_business_plan_text != ''){
        html += `<div>Kế hoạch BTP: ${reference_no_business_plan_text}</div>`;
    }
    if(reference_no_orders_text != ''){
        html += `<div></div>Đơn hàng: ${reference_no_orders_text}<div>`;
    }
    var tr = '' +
            '<div class="bold" style="font-size: 14px;">' + state.text + '</div>' +
            html
            '';
    tableSelect = tr;
    return tableSelect;
}

function repoFormatSelectionOrder(state) {
    if (!state.id) return state.text;
    if (state.img) {
        var img = '<img class="img_option" src="' + site.base_url + state.img + '"/> ';
    } else {
        var img = '<img class="img_option" src="' + site.base_url + 'download/preview_image"/> ';
    }
    if (state.color == null) {
        state.color = '';
    }
    if (!state.reference_no) {
        state.reference_no = '';
    }
    if (state.type == 'nvl') {
        var tr = '' +
            '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
            '<div>Loại: ' + state.new_type + ' - Quy cách: ' + state.specification + '</div>' +
            '<div>Khổ: ' + state.suffering + ' - Màu sắc: ' + state.color + '</div>' +
            '';
        tableSelect = tr;
        return tableSelect;
    } else {
        title = '';
        reference_no = '';
        if(state.object_type == "business_plan"){
            title = ' KHKD';
            reference_no = state.reference_no_plan;
        } else if(state.object_type == "orders"){
            title = 'Đơn hàng';
            reference_no = state.reference_no;
        }
        var tr = '' +
            '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
            '<div>Số lượng: ' + (state.total_qty - state.qty_outsource) + ' - Đơn vị: ' + state.unit_name + '</div>' +
            '<div>Lệnh SXCT: ' + state.reference_no_production_detail + ' - '+title+': ' + reference_no + '</div>' +
            '';
        tableSelect = tr;
        return tableSelect;
    }
}
function ajaxSelectParamsCallbackOrder(element, url, id, params = false, clearSl2 = false, txtJson = false)
{
    if (id != 0)
    {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
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
                        id_branch:$("#id_branch").val(),
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
            },
            formatResult: repoFormatSelectionOrder,
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: clearSl2,
            ajax: {
                url: site.base_url + url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        params: params,
                        id_branch:$("#id_branch").val(),
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
            },
            formatResult: repoFormatSelectionOrder,
        });
    }
}
