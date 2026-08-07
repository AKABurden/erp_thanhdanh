function totalCheckQuality() {
    tb = '#tb-check-quality tbody tr.chonse-tr';
    var n = $(tb).length;
    var stt = 0;
    count_errors = 0;

    for (ii = 0; ii < n; ii++) {
        stt++;
        element = $(tb)[ii];
        $(element).find('.td-number').html(stt);

        quantityhad = intVal($(element).find('.qty_qc').html());

        quantity = intVal($(element).find('.quantity_qc').val());

        quantity_che = intVal($(element).find('.quantity_che').val());
        quantity_phe = intVal($(element).find('.quantity_phe').val());

        quantity_json_che = intVal($(element).find('.qty_json_taiche').val());
        quantity_json_phe = intVal($(element).find('.qty_json_phe').val());
        result = $(element).find('.result:checked').val();
       

        if (quantity_che > 0) {
            $(element).find(".tai_che").removeClass('hide');
        } else {
            $(element).find(".tai_che").addClass('hide');
            $(element).find('input.data_json_taiche').val('');
            $(element).find('input.qty_json_taiche').val(0);
        }

        if (quantity_phe > 0) {
            $(element).find(".phe").removeClass('hide');
        } else {
            $(element).find(".phe").addClass('hide');
            $(element).find('input.data_json_phe').val('');
            $(element).find('input.qty_json_phe').val(0);
        }

        quantity_check = quantity_che + quantity_phe;

        tileKDat = (quantity_check * 100) / quantity;
        tileDat = ((quantity - quantity_check) * 100) / quantity;


        if (quantity_check > quantity) {
            $(element).find('.show-error-item').html('Số lượng lỗi nhỏ hơn' + ' ' + tnhFormatNumber(quantity));
            count_errors++;
        } else {
            $(element).find('.show-error-item').html('');
        }
        $(element).find('.quantity_che').val(quantity_che);
        $(element).find('.quantity_phe').val(quantity_phe);

        $(element).find(".td-khong-dat").html(tnhFormatNumber(tileKDat));
        $(element).find(".td-dat").html(tnhFormatNumber(tileDat));
        $(element).find(".td-qty-dat").html(tnhFormatNumber(quantity - quantity_check));

        if(quantity_json_phe > quantity_phe){
            $(element).find('.show-error-phe').html('Số lượng chi tiết phế phải nhỏ hơn' + ' ' + tnhFormatNumber(quantity_phe));
            count_errors++;
        } else {
            $(element).find('.show-error-phe').html('');
        }
        if(result == 2){
            if(quantity_che == 0){
                $(element).find('.show-error-tai-che').html('Vui lòng nhập số lượng lỗi');
                count_errors++;
            } else{
                if(quantity_json_che > quantity_che){
                    // $(element).find('.show-error-tai-che').html('Số lượng chi tiết tái chế phải nhỏ hơn' + ' ' + tnhFormatNumber(quantity_che));
                    // count_errors++;
                } else {
                    $(element).find('.show-error-tai-che').html('');
                }
           
            }
        } else {
            if(quantity_json_che > quantity_che){
                // $(element).find('.show-error-tai-che').html('Số lượng chi tiết tái chế phải nhỏ hơn' + ' ' + tnhFormatNumber(quantity_che));
                // count_errors++;
            } else {
                $(element).find('.show-error-tai-che').html('');
            }
        }
        // if(quantity_json_che > quantity_che){
        //     $(element).find('.show-error-tai-che').html('Số lượng chi tiết tái chế phải nhỏ hơn' + ' ' + tnhFormatNumber(quantity_che));
        //     count_errors++;
        // } else {
        //     $(element).find('.show-error-tai-che').html('');
        // }
        // if (quantity > quantityhad) {
        //     $(element).find('.show-error-item-qc').html('Số lượng qc phải nhỏ hơn' + ' ' + tnhFormatNumber(quantityhad));
        //     count_errors++;
        // } else {
        //     $(element).find('.show-error-item-qc').html('');
        // }

    }

    if (n > 0) {
        $('#customer_id').select2('readonly', true);
        $('#order_production_details').select2('readonly', true);
    } else {
        $('#customer_id').select2('readonly', false);
        $('#order_production_details').select2('readonly', false);
    }
}


function removeRow(el) {
    dt.row($(el).parents('tr')).remove().draw();
    totalCheckQuality();
}

function refershTable() {
    dt.rows().remove().draw();
    totalCheckQuality();
}

//reason
function addListReason(_this, type = '') {
    cTr = $(_this).closest('tr');
    cTrChonse = cTr;
    cItemsId = cTr.find('input.item_id').val();

    if (!cItemsId) {
        bootbox.alert('Vui lòng chọn mặt hàng.');
        return;
    }
    if (type == 1) {
        cQuantity = intVal(cTr.find('input.quantity_che').val());
        data_json = cTr.find('input.data_json_taiche').val();
    } else if (type == 2) {
        cQuantity = intVal(cTr.find('input.quantity_phe').val());
        data_json = cTr.find('input.data_json_phe').val();
    }

    check_quality_item = cTr.find('.check_quality_item').val();
    cCounter = cTr.find('.counter').val();

    link = site.base_url + 'admin/quality_control/calReasonQC';
    $.ajax({
        url: link,
        type: 'POST',
        dataType: 'html',
        data: {
            cQuantity: cQuantity,
            cItemsId: cItemsId,
            type: type,
            check_quality_item: check_quality_item,
            data_json: data_json,
            csrf_token_name: hash
        },
    })
        .done(function (data) {
            $('.modal-select2').select2('close');
            $('#tnhModal2').html(data);
        })
        .fail(function () {
            console.log("error");
        });
    $('#tnhModal2').modal({backdrop: 'static', keyboard: false});
}

//end reason

function getStage(stages = '',select_id)
{
    var option = '<option value=""></option>';
    $.each(stages, function(index, el) {
        disabled = '';
        if(el.active == 0){
            disabled = 'disabled';
        }
        selected = select_id == el.stage_id ? 'selected' : '';
        option+= '<option '+selected+' '+disabled+' value="'+el.stage_id+'">'+el.stage_name+'</option>';
    });
    return option;
}

function getStageAgain(stages = '',select_id)
{
    var option = '<option value=""></option>';
    $.each(stages, function(index, el) {
        selected = select_id == el.stage_id ? 'selected' : '';
        option+= '<option  value="'+el.stage_id+'">'+el.stage_name+'</option>';
    });
    return option;
}

$(document).on('change', '#items', function (event) {
    items = $('#items').val();
    if (empty(items)) {
        bootbox.alert('Vui lòng chọn mặt hàng ');
        return;
    }
    data = $("#items").select2('data');
    createdRowItem(data, counter);
    counter++;

    $("#items").select2("val", "");
});

$(document).on('change', '#productions', function (event) {
    productions = $('#productions').val();
    if (empty(productions)) {
        bootbox.alert('Vui lòng chọn lệnh tổng ');
        return;
    }
    data = $("#productions").select2('data');
    production_id = data.id;
    if(production_id){
        link = site.base_url + 'admin/quality_control/searchProductbyProductions';
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
                if(data.results.length > 0){
                    $.each(data.results,function(key,value){
                        console.log(value);
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


$(document).on('change', '.stage_all', function (event) {
    tb = '#tb-check-quality tbody tr.chonse-tr';
    var n = $(tb).length;
    if(n == 0){
        bootbox.alert('Không có mặt hàng');
        $(".stage_all").select2("val", "");
        return;
    }
    id_stage = $(this).val();
    for (ii = 0; ii < n; ii++) {
        element = $(tb)[ii];
        data_stage = $(element).find('.data_stage').val();
        data_stage = JSON.parse(data_stage);
        $(element).find('.id_stage').select2("val","");
        $.each(data_stage,function(key, value){
            if(id_stage == value.stage_id){
                if(value.active == 1){
                    $(element).find('.id_stage').select2("val",id_stage);
                } else {
                    $(element).find('.id_stage').select2("val","");
                }
            } 
        });
    }
    $(".stage_all").select2("val", "");

});

function addItemQc() {
    items = $('#items').val();
    if (empty(items)) {
        bootbox.alert('Vui lòng chọn mặt hàng ');
        return;
    }
    data = $("#items").select2('data');
    createdRowItem(data, counter);
    counter++;
}

function createdRowItem(data, counter) {
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
    if (elTr.length > 0) {
        alert_float('warning', 'Mặt hàng đã tồn tại !');
        return;
    } else {
        qty = 0;
        if (data.total_qty) {
            qty = data.total_qty - data.qty_qc;
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

        cqis_id = 0;
        if (typeof data.cqis_id != "undefined") {
            cqis_id = data.cqis_id;
        }

        tdNumber = '<div class="td-number text-center"></div>';
        tdImage = '<div class="td-image">' +
            '<div class="preview_image" style="width: auto;">' +
            '<div class="display-block contract-attachment-wrapper img">' +
            '<div style="width:45px;">' +
            '<a href="' + data.images + '" data-lightbox="customer-profile" class="display-block mbot5">' +
            '<div class="">' +
            '<img src="' + data.images + '" style="border-radius: 50%">' +
            '</div>' +
            '</a>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        tdCode = '<div class="td-code mbot10">' +
            '<input type="hidden" name="cqis_id[' + counter + ']" class="form-control cqis_id" value="' + cqis_id + '">' +
            '<input type="hidden" name="item_id[' + counter + ']" id="item_id" class="form-control item_id" value="' + data.id + '">' +
            '<input type="hidden" class="form-control check_item" value="' + data.id + '__' + data.pod_id + '">' +
            '<input type="hidden" name="counter[' + counter + ']" id="counter" class="form-control counter" value="' + counter + '">' +
            '<input type="hidden" name="sum_qty[' + counter + ']" id="sum_qty" class="form-control sum_qty" value="' + qty + '">' +
            '<input type="hidden" name="pod_id[' + counter + ']" id="pod_id" class="form-control pod_id" value="' + data.pod_id + '">' +
            '<input type="hidden" name="order_id[' + counter + ']" id="order_id" class="form-control order_id" value="' + order_id + '">' +
            '<input type="hidden" name="object_type[' + counter + ']" id="object_type" class="form-control object_type" value="' + data.object_type + '">' +
            '<input type="hidden" name="plan_id[' + counter + ']" id="plan_id" class="form-control plan_id" value="' + plan_id + '">' +
            '<input type="hidden" name="check_quality_item_id['+ counter + ']" id="check_quality_item_id" class="check_quality_item_id" style="width: 100%;"  value="0">'+
            `<input type="hidden" class="data_stage" value='${JSON.stringify(data.stages)}''>`+
            data.name + '(' + data.code + ')' +
            '<div class="hide" style="color:red;text-transform:uppercase"> SL có thể QC : <span class="qty_qc">'+ tnhFormatNumber(qty) + '</span></div>' +
            '<div class="type-item"></div>' +
            '<div><div class=""><a href="javascript:void(0)" onclick="removeRow(this)" class="text-danger delete-remind remove-row">' + lang_core['delete'] + '</a></div></div>';
        '</div>';
        color = data.name_color != null ? data.name_color : '';
        tdColor = '<div class="td-item-name "><div style="font-style: italic;font-size: 12px">' + td_detail + '</div></div>';
        tdUnit = '<div class="td-unit text-center">' + data.unit_name + '</div>';
        tdStage = '<div style="width:200px" class="td-stage"><select  data-placeholder="công đoạn" required class="id_stage" style="width: 100%; height: 30px" id="id_stage_'+counter+'" name="id_stage['+counter+']">'+getStage(data.stages,data.stages_default)+'</select></div>';
        tdSumQty = '<div class="td-sum-qty text-center"><input style="width: 100%;" type="text" name="quantity_qc[' + counter + ']" id="quantity_qc[]" onchange="totalCheckQuality()" class="form-control quantity_qc number-format" value="'+data.total_qty+'"><div class="show-error-item-qc text-danger"></div><div class="show-error-item text-danger"></div></div>';
        tdQtyDat = '<div class="td-qty-dat text-center">0</div>';
        tdquantityChe = '<div style="width:100px" class="td-quantity-che"><input style="width: 100%;" type="text" name="quantity_che[' + counter + ']" id="quantity_che[]" onchange="totalCheckQuality()" class="form-control quantity_che number-format" value="0"><div class="mtop5"><i onclick="addListReason(this,1)" class="btn btn-primary hide tai_che">Chi tiết lỗi</i></div><div class="show-error-tai-che text-danger"></div>' +
            ' <input type="hidden" name="data_json_taiche[' + counter + ']" class="form-control data_json_taiche" value="">'+
            ' <input type="hidden" class="form-control qty_json_taiche" value=""></div>';
        tdquantityPhe = '<div style="width:100px" class="td-quantity-phe"><input style="width: 100%;" type="text" name="quantity_phe[' + counter + ']" id="quantity_phe[]" onchange="totalCheckQuality()" class="form-control quantity_phe number-format" value="0"><div class="mtop5"><i onclick="addListReason(this,2)" class="btn btn-primary hide phe">Chi tiết lỗi</i></div><div class="show-error-phe text-danger"></div>' +
            '<input type="hidden" name="data_json_phe[' + counter + ']" class="form-control data_json_phe" value="">'+
            '<input type="hidden" class="form-control qty_json_phe" value=""></div>';

        tdResult = `<div style="width:200px"  class="td-result">
            <div class="radio radio-primary">
                <input type="radio" value="1" id="result1${counter}" class="result" checked name="result[${counter}]"><label for="result1${counter}">Đạt</label>
            </div>
        
            <div class="radio radio-primary">
                <input type="radio" value="2" id="result2${counter}" class="result" name="result[${counter}]"><label for="result2${counter}">Không Đạt</label>
            </div>
            <div>
            <select class="id_stage_again hide" data-placeholder="công đoạn" style="width: 100%; height: 30px" id="id_stage_again_${counter}" name="id_stage_again[${counter}]">${getStageAgain(data.stages_again)}</select>
            </div>
        </div>`;    
        tdKhongDat = '<div class="td-khong-dat text-center"></div>';
        tdDat = '<div class="td-dat text-center"></div>';
        tdActions = '<div class="td-actions text-center"><a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row btn btn-danger"></i></div>';
        rowNode = dt.row.add([
            tdNumber,
            tdImage,
            tdCode,
            tdColor,
            tdStage,
            tdSumQty,
            tdquantityChe,
            // tdquantityPhe,
            tdQtyDat,
            tdResult,
            tdKhongDat,
            tdDat,
            tdActions
        ]).draw(false).node();
    }
    $(rowNode).addClass('chonse-tr');
    $("#id_stage_"+counter).select2({
        formatResult: formatStage,
    });
    $("#id_stage_again_"+counter).select2({
        formatResult: formatStageAgain,
    });
    totalCheckQuality();
}
$(document).on('change', '.id_stage', function (e) {
    var value = $(this).val();
    cTr = $(this).closest('tr');
    count = cTr.find('.counter').val();
    result = cTr.find('.result:checked').val();
    cTr.find('select.id_stage').select2({
        formatResult: formatStage,
    })
    if(result == 2){
        link = site.base_url + 'admin/quality_control/getStageAgain';
        $.ajax({
            url: link,
            type: 'POST',
            dataType: 'JSON',
            data: {
                id_stage: value,
                csrf_token_name: hash
            },
        })
            .done(function (data) {
                selected = data.selected;
                if(selected){
                    $("#id_stage_again_"+count).select2('val', selected);
                } else {
                    $("#id_stage_again_"+count).select2('val', "");
                }
            })
            .fail(function () {
                console.log("error");
            });

    }
    
});
$(document).on('change', '.result', function (e) {
    var value = $(this).val();
    cTr = $(this).closest('tr');
    count = cTr.find('.counter').val();
    id_stage = cTr.find('select.id_stage').select2().find(":selected").val();
    cTr.find('select.id_stage').select2({
        formatResult: formatStage,
    })
    if(value == 2){
        link = site.base_url + 'admin/quality_control/getStageAgain';
        $.ajax({
            url: link,
            type: 'POST',
            dataType: 'JSON',
            data: {
                id_stage: id_stage,
                csrf_token_name: hash
            },
        })
            .done(function (data) {
                console.log(data);
                selected = data.selected;
                if(selected){
                    $("#id_stage_again_"+count).select2('val', selected);
                }
            })
            .fail(function () {
                console.log("error");
            });

        // $("#id_stage_again_"+count).removeClass('hide');
        $("#id_stage_again_"+count).addClass('hide');
        $("#id_stage_again_"+count).attr('required', false);
    } else {
        $("#id_stage_again_"+count).select2('val', "");
        $("#id_stage_again_"+count).addClass('hide');
        $("#id_stage_again_"+count).attr('required', false);
    }
    totalCheckQuality();
});

// $(document).on('change', '.id_stage', function (event) {
//     event.preventDefault();
//     row = $(this).closest('tr');
//     data = event.added;
//     sl = this;
//     id_stage = $(this).val();
//     if (id_stage) {
//         tr = $(sl).closest('tr');
//         pod_id = tr.find('.pod_id').val();
//         check_quality_item_id = tr.find('.check_quality_item_id').val();
//         $.ajax({
//             url: site.base_url + 'admin/quality_control/getQuantityQc',
//             type: 'GET',
//             dataType: 'JSON',
//             data: {
//                 id_stage: id_stage,
//                 pod_id: pod_id,
//                 check_quality_item_id:check_quality_item_id,
//                 csrf_token_name: hash,
//             },
//         })
//             .done(function (response) {
//                 if(response.result.quantity - response.result.qty_qc == 0){
//                     alert_float('warning', 'Công đoạn này đã hết số lượng QC!');
//                     tr.find(".id_stage").select2("val", "");
//                     return;
//                 } else {
//                     tr.find('.qty_qc').html(tnhFormatNumber(response.result.quantity - response.result.qty_qc));
//                 }
//                 totalCheckQuality();

//             });
//     } else {
//         totalCheckQuality();
//     }
// });

$(document).ready(function () {
    
    $("#id_branch").select2();
    $(".stage_all").select2();

    if (edit == 0) {
        ajaxSelectCustomerFormatTableCallBack('#customer_id', 'admin/clients/searchCustomers', $('#customer_id').val());
        ajaxSelectCallBack_ProductionDetail('#order_production_details', 'admin/quality_control/searchProductionsOrdersDetail', 0, 0);
        
        ajaxSelectParamsCallbackItems('#items', 'admin/quality_control/searchProductByProduction', 0);
        ajaxSelectParamsCallbackProduction('#productions', 'admin/quality_control/searchProduction', 0);
    } else if (edit == 1) {
        ajaxSelectParamsCallbackItems('#items', 'admin/quality_control/searchProductByProduction', 0,{checkQualityId:pod_id,stage_id:stage_text});
        ajaxSelectParamsCallbackProduction('#productions', 'admin/quality_control/searchProduction', 0);
        for (i = 0; i < counter; i++) {
            $("#id_stage_"+i).select2({
                formatResult: formatStage,
            });
            $("#id_stage_again_"+i).select2({
                formatResult: formatStageAgain,
            });
        }
    }

    dt = $('#tb-check-quality').DataTable({
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

    $('.add-row').on('click', function (event) {
        event.preventDefault();
        warehouse_id = $('#warehouses').val();
        order_production_details = $('#order_production_details').val();
        type_enter = $('#type_enter').val();
        if (!warehouse_id || !order_production_details || !type_enter) {
            // bootbox.alert(lang_purchase['tnh_please_chosen_warehouse']);
            bootbox.alert('Xin vùi lòng chọn kho , LSX, loại nhập');
            return;
        }

        tdNumber = '<div class="td-number text-center"></div>';
        tdCode = '<div class="td-code mbot10"><input type="hidden" name="counter[' + counter + ']" id="counter" class="form-control counter" value="' + counter + '">\
                <input type="text" name="items_id[' + counter + ']" id="items_' + counter + '" class="items_id" style="width: 100%;" data-placeholder="' + lang_core['choose'] + '" value=""></div>' +
            '<div class="type-item"></div>' +
            '<div><div class="row-options"><a href="javascript:void(0)"class="text-danger delete-remind remove-row" onclick="removeRow(this)">' + lang_core['delete'] + '</a></div></div>';
        tdImage = '<div class="td-image">' +
            '<div class="preview_image" style="width: auto;">' +
            '<div class="display-block contract-attachment-wrapper img">' +
            '<div style="width:45px;">' +
            '<a href="' + site.base_url + 'assets/images/tnh/no_image.png" data-lightbox="customer-profile" class="display-block mbot5">' +
            '<div class="">' +
            '<img src="' + site.base_url + 'assets/images/tnh/no_image.png" style="border-radius: 50%">' +
            '</div>' +
            '</a>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        tdName = '<div class="td-item-name">' + lang_core['product_name'] + '</div>';
        tdUnit = '<div class="td-unit"></div>';
        tdSize = '<div class="td-position"><select data-placeholder="' + lang_core['choose'] + '" name="size_id[' + counter + ']" id="sizes" class="sizes" style="width: 100%;"><option value=""></option></select></div>';
        tdPosition = '<div class="td-position"><select data-placeholder="' + lang_core['choose'] + '" name="location_id[' + counter + ']" id="locations" class="locations" style="width: 100%;"><option value=""></option>' + locations + '</select></div>';
        tdQuantity = '<div class="td-quantity"><input type="text" name="quantity[' + counter + ']" id="quantity[]" class="form-control quantity number-format" style="width: 100%;" value="0"><div class="show-exchange text-primary mtop5"></div></div>';
        tdNote = '<div class="td-note"><textarea name="note_items[' + counter + ']" id="note_items[]" class="form-control" rows="3"></textarea></div>';
        tdActions = '<div class="text-center"><i class="fa fa-remove btn btn-danger remove-row" onclick="removeRow(this)"></i></div>';

        rowNode = dt.row.add([
            tdNumber,
            tdCode,
            tdImage,
            tdName,
            tdUnit,
            tdSize,
            tdPosition,
            tdQuantity,
            tdNote,
            tdActions
        ]).draw(false).node();

        // ajaxSelectCallBack($('input#items_' + counter + ''), 'admin/products/searchProductsSelect2', 0);

        $('select.locations').select2();
        $('select.sizes').select2();
        counter++;
        totalPurchaseProducts();
    });

    $(document).on('change', '.items_id', function (event) {
        event.preventDefault();
        row = $(this).closest('tr');
        data = event.added;
        sl = this;
        item_id = $(this).val();
        if (item_id) {
            tr = $(sl).closest('tr');
            $.ajax({
                url: site.base_url + 'admin/stock/getExchangeProduct',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    item_id: item_id,
                    csrf_token_name: hash,
                },
            })
                .done(function (response) {
                    subtext = data.item_name;
                    unit_name = data.unit_name;
                    unit_id = data.unit_id;
                    unit_parent_id = data.unit_parent_id;
                    number_exchange = data.number_exchange;
                    var array_size = data.name_size_ch.split("FF");
                    var html_size = '<option></option>';
                    if (array_size[0] != "") {
                        $.each(array_size, function (i, v) {
                            var value = v.split("|||");
                            html_size += '<option value=' + value[0] + '>' + value[1] + '</option>';
                        });
                    }
                    tr.find('select.sizes').find('option:gt(0)').remove();
                    tr.find('.unit_id').val(unit_id);
                    tr.find('.unit_parent_id').val(unit_parent_id);
                    tr.find('.td-item-name').html(subtext);
                    tr.find('.td-unit').html(unit_name);
                    tr.find('select.sizes').html(html_size);

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
            tr.find('.td-image a').attr('href', site.base_url + 'assets/images/tnh/no_image.png');
            tr.find('.td-image img').attr('src', site.base_url + 'assets/images/tnh/no_image.png');
            totalPurchaseProducts();
        }
    });
    $(document).on('change', '.quantity_che', function (event) {
        totalCheckQuality();
    });
    $(document).on('change', '.quantity_phe', function (event) {
        totalCheckQuality();
    });

    $(document).on('change', '.quantity', function (event) {
        totalPurchaseProducts();
    });
    $(document).on('change', '.order_production_details', function (event) {
        $("#items").select2("val", "");
        ajaxSelectParamsCallbackItems('#items', 'admin/quality_control/searchProductByProduction', 0);
    });
    $(document).on('change', '.customer_id', function (event) {
        $("#order_production_details").select2("val", "");
        // $("#items").select2("val", "");
        ajaxSelectParamsCallbackItems('#items', 'admin/quality_control/searchProductByProduction', 0);
    });

    appValidateForm($('#check_quality'), {
        reference_no: 'required',
        date: 'required',
        id_branch: 'required',
        // order_production_details: 'required',
        // customer_id: 'required',
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

        $.each(form.find('input[type="file"]'), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });
        $.each(formParams, function (i, val) {
            formData.append(val.name, val.value);
        });

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
        })
            .done(function (data) {
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url + 'admin/quality_control/check_quality';
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function () {
                alert_float('danger', lang_core['errors']);
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }
});

function formatStage(state) {
    if (!state.id) return state.text;
    title = '';
    if(state.disabled == true) {
        title = 'Chưa hoàn thành';
    }
    var tr = '' +
        '<div class="bold" style="font-size: 14px;">'+ state.text + '</div>' +
        '<div style="color:red">'+title+'</div>' +
        '';
    tableSelect = tr;
    return tableSelect;
}
function formatStageAgain(state) {
    if (!state.id) return state.text;
    title = '';
    var tr = '' +
        '<div class="bold" style="font-size: 14px;">'+ state.text + '</div>' +
        '';
    tableSelect = tr;
    return tableSelect;
}

function ajaxSelectCallBack_ProductionDetail(element, url, id, customer_id) {
    if (id > 0) {
        $(element).val(id).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: false,
            multiple: true,
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: site.base_url + url + '/' + id,
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: url,
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        customer_id: $('#customer_id').val(),
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {
                            results: data.results
                        };
                    } else {
                        return {
                            results: [{
                                id: '',
                                text: 'No Match Found'
                            }]
                        };
                    }
                }
            },
            formatResult: repoFormatSelection_order,
            formatSelection: repoFormatSelection_order,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) {
                return m;
            }
        });
    } else {
        $(element).select2({
            // minimumInputLength: 1,
            width: 'resolve',
            allowClear: false,
            multiple: true,
            ajax: {
                url: site.base_url + url + '/' + $(element).val(),
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        customer_id: $('#customer_id').val(),
                        term: term,
                        limit: 50
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {
                            results: data.results
                        };
                    } else {
                        return {
                            results: [{
                                code_client: '',
                                id: '',
                                text: 'No Match Found'
                            }]
                        };
                    }
                }
            },
            formatResult: repoFormatSelection_order,
            formatSelection: repoFormatSelection_order,
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) {
                return m;
            }
        });
    }
}

function repoFormatSelection_order(result) {
    if (!result.id) return result.text; // optgroup
    txtPod = '<div class="bold">' + result.text + "</div>";
    txtPod +=
        '<div class="italic" style="font-size: 12px;">' +
        result.reference_orders +
        " - " +
        result.company +
        "</div>";
    return txtPod;

}

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


function ajaxSelectParamsCallbackItems(
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
                formatResult: repoFormatSelectionOrder,
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
            formatResult: repoFormatSelectionOrder,
        });
    }
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
        qty_qc_text = '';
        if(state.name_stages != ''){
            name_stages = state.name_stages.split('FF');
            total = 0;
            check_stage = '';
            $.each(name_stages,function(k,v){
                stage = v.split('__');
                if(stage[0] != check_stage){
                    stage[1] = Number(stage[1]);
                    qty_qc_text +=`<div>Số lượng QC:<span style="color:red">Công đoạn ${stage[0]} đã QC ${stage[1]}</span></div>`;
                    check_stage = stage[0];
                }
              
            });
        }
        var tr = '' +
            '<div class="bold" style="font-size: 14px;">' + img + state.text + '</div>' +
            '<div>Số lượng: ' + (state.total_qty - state.qty_qc) + ' - Đơn vị: ' + state.unit_name + '</div>' +
            // ''+qty_qc_text+
            '<div>Lệnh SXCT: ' + state.reference_no_production_detail + ' - '+title+': ' + reference_no + '</div>' +
            '<div>'+state.note_order+'</div>'+
            '';
        tableSelect = tr;
        return tableSelect;
    }
}