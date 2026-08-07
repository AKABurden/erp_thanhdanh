    <script>
    $("#branch_id").select2();
    $("#staff_plan").select2();
    ajaxSelectParams('#po_id', 'admin/suggest_plan_outsource/searchPoItems', $("#po_id").val(), true, true);
    // ajaxSelectCallBack('#order_id', 'admin/suggest_plan_overtime/searchOrders', $("#order_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), `admin/suggest_plan_outsource/searchProductByProduction/${$("#po_id").val()}`, 0);

    $("#po_id").change(function (){
        $("#order_id").select2("val","");
        $("#tb-purchases").find('tbody').html('');
        ajaxSelectCallBack($('#items_search'), `admin/suggest_plan_outsource/searchProductByProduction/${$("#po_id").val()}`, 0);
        if($('#check_auto').prop('checked') == true) {
            $.get(`${admin_url}suggest_plan_outsource/searchProductByProduction/${$("#po_id").val()}`, function(result) {
                result = JSON.parse(result);
                data = result.results[0].children;
                $.each(data, function(index, dtItems) {
                    if (!arrId.includes(dtItems.id + '_' + dtItems.type_item)) {
                        loadItem(dtItems)
                    }
                })
            })
        }
        getTotal();
    });

    $('#check_auto').change(function() {
        if($('#check_auto').prop('checked') && $("#po_id").val() != "") {
            $.get(`${admin_url}suggest_plan_outsource/searchProductByProduction/${$("#po_id").val()}`, function(result) {
                result = JSON.parse(result);
                data = result.results[0].children;
                $.each(data, function(index, dtItems) {
                    if (!arrId.includes(dtItems.id + '_' + dtItems.type_item)) {
                        loadItem(dtItems)
                    }
                })
            })
        }
    })
    
    
    function ajaxSelectCallBack(element, url, id, types = '')
    {
        if (id != 0)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function (data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            order_id: $("#order_id").val(),
                            po_id: $("#po_id").val(),
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
                //allowClear: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function (term, page) {
                        return {
                            types: types,
                            term: term,
                            order_id: $("#order_id").val(),
                            po_id: $("#po_id").val(),
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


    if (edit == 1){
        for (i = 0; i < counter; i++) {
            $(`#result_id_${i}`).select2();
            $(`#staff_id_${i}`).select2();
            ajaxSelectParams($(`#suppliers_id_${i}`), 'admin/suggest_repalce/searchSuppliers', $(`#suppliers_id_${i}`).val());
        }
    }

    function optionResult(selected_id = 0){
        option = `<option></option>`;
        $.each(dtResult, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= '<option '+selected+' value="'+el.id+'">'+el.name+'</option>';
        });
        return option;
    }

    function optionStaff(selected_id = 0){
        option = `<option></option>`;
        $.each(dtStaff, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= `<option ${selected} value="${el.staffid}">${el.firstname} ${el.lastname}</option>`;
        });
        return option;
    }

    $("#items_search").change(function (){
        dtItems = $(this).select2('data');
        if (arrId.includes(dtItems.id + '_' + dtItems.type_item)){
            alert_float('danger','Mặt hàng đã tồn tại!');
            $(this).select2("val", "");
            return;
        }
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    $('body').on('change', 'input.suppliers_id', function() {
        var tr = $(this).parents('tr');
        var item_id = tr.find('input.item_id').val();
        var type_item = tr.find('input.type_item').val();
        if(typeof type_item == 'undefined') {
            type_item = 'products';
        }
        var id_supplier = $(this).val();
        var quantity = tr.find('.td_quantity').text();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['item_id'] = item_id;
        data['type_item'] = type_item;
        data['id_supplier'] = id_supplier;
        data['quantity'] = quantity;
        $.post(admin_url + 'stage_price_list/get_price', data, function (result) {
            result = JSON.parse(result);
            tr.find('.td-price').find('input').val(tnhFormatNumber(result.price));
            tr.find('.id_stage_price_list_detail').val(result.id);
            getTotal();
        })
    })

    function loadItem(item = {}){
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
         <input type="hidden" class="item_id" name="items_id[${counter}]" value="${item.item_id}">
         <input type="hidden" class="type_item" name="type_items[${counter}]" value="${item.type_item}">
         <input type="hidden" name="id_stage_price_list_detail[${counter}]" class="id_stage_price_list_detail" value="">
        ${item.code_item}
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdMode = `<div class="td_mode">${item.mode}</div>`;
        tdUnit = `<div class="td_unit">${item.unit_name}</div>`;
        tdSupplier = `<div class="supplier">
           <input type="text" name="suppliers_id[${counter}]" id="suppliers_id_${counter}" class="suppliers_id"
                data-placeholder="<?= lang('Nhà gia công') ?>" style="width: 100%;">
        </div>`;
        tdDetail= `<div class="td-detail"><input type="text" name="detail[${counter}]" class="detail form-control" value=""></div>`;
        tdQuantity = `<div class="td_quantity text-center">${tnhFormatNumber(item.total_quantity_item)}</div>`;
        tdPrice = `<div class="td-price"><input type="text" name="price[${counter}]" onchange="getTotal()" class="price form-control number-format" value="0"></div>`;
        tdAmount = `<div class="td-amount"></div>`;
        tdShippingUnitOutsource = `<div class="td-shipping_unit_outsource"><input type="text" name="shipping_unit_outsource[${counter}]" class="shipping_unit_outsource form-control" value=""></div>`;
        tdTransportOutsource = `<div class="td-transport_outsource"><input type="text" name="transport_outsource[${counter}]" class="transport_outsource form-control" value=""></div>`;
        tdDateStartOutsource = `<div class="td-date_start_outsource"><input type="text" name="date_start_outsource[${counter}]" required class="date_start_outsource datepicker form-control" autocomplete="off" value=""></div>`;
        tdDateEndOutsource = `<div class="td-date_end_outsource"><input type="text" name="date_end_outsource[${counter}]" required class="date_end_outsource datepicker form-control" autocomplete="off" value=""></div>`;
        tdStaff = `<div>
            <select class="staff_id" id="staff_id_${counter}" name="staff_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Nhân viên') ?>">
                ${optionStaff()}
            </select>
        </div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td>${tdMode}</td>
            <td>${tdUnit}</td>
            <td style="width: 200px">${tdSupplier}</td>
            <td style="width: 150px">${tdDetail}</td>
            <td>${tdQuantity}</td>
            <td style="width: 150px">${tdPrice}</td>
            <td style="width: 150px;text-align: right">${tdAmount}</td>
            <td style="width: 150px">${tdShippingUnitOutsource}</td>
            <td style="width: 150px">${tdTransportOutsource}</td>
            <td style="width: 150px">${tdDateStartOutsource}</td>
            <td style="width: 150px">${tdDateEndOutsource}</td>
            <td style="width: 150px">${tdStaff}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        ajaxSelectParams($(`#suppliers_id_${counter}`), 'admin/suggest_repalce/searchSuppliers', 0);

        $(`#result_id_${counter}`).select2();
        // $(`#result_id_${counter}`).attr('required','required');
        $(`#staff_id_${counter}`).select2();
        $(`#staff_id_${counter}`).attr('required','required');
        $(`#suppliers_id_${counter}`).attr('required','required');
        init_datepicker();
        counter ++;
        getTotal();
    }

    function removeRow(el)
    {
        $(el).closest('tr').remove();
        getTotal();
    }

    function getTotal(){
        tb = '#tb-purchases tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arrId = [];
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            quantity = intVal($(element).find('.td_quantity').html());
            price = intVal($(element).find('.price').val());
            amount = quantity * price;
            pod_id = $(element).find('.pod_id').val();
            item_id = $(element).find('.item_id').val();
            type_item = $(element).find('.type_item').val();
            var fullKey = item_id + '_' + type_item;
            if (arrId.includes(fullKey) == false){
                arrId.push(fullKey);
            }
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
        }
    }

    appValidateForm($('#suggest_plan_outsource'), {
        reference_no: 'required',
        date: 'required',
        po_id: 'required',
        branch_id: 'required',
        order_id: 'required',
        staff_plan: 'required',
    }, db);

    //save db
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
                console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = site.base_url+'admin/suggest_plan_outsource';
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
</script>