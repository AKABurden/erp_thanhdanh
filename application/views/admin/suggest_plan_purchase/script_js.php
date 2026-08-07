<script>
    $("#branch_id").select2();
    $("#staff_id").select2();
    $("#category_plan").select2();
    ajaxSelectMultipleParamsC('#plan_id', `admin/suggest_plan_purchase/searchPlans`, $("#plan_id").val(), {}, true);
    if (type == 3) {
        ajaxSelectParams('#items_search', 'admin/suggest_plan_purchase/searchMachinesItems', $("#items_search").val(), true, true);
    } else if (type == 1) {
        ajaxSelectParams('#items_search', 'admin/stock/searchMaterialAndSemiProducts', $("#items_search").val(), true, true);

    } else {
        ajaxSelectCallBack($('#items_search'), 'admin/stock/searchMaterialAndSemiProducts', 0);
    }

    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            ajaxSelectParams($(`#suppliers_id_${i}`), 'admin/suggest_repalce/searchSuppliers', $(`#suppliers_id_${i}`).val());
            $(`#result_${i}`).select2();
        }
    }

    $("#plan_id").change(function() {
        var checklan = 0;
        var list_po_id = $("#plan_id").val().split(',');
        $.each(list_po_id, function(index, value) {
            $('#tb-purchases').find('tbody').find(`tr.tr_${value}`).addClass('findID');
            if (value != '') {
                checklan++;
            }

        })
        $('#tb-purchases').find('tbody').find('tr:not(.findID)').remove();
        $('#tb-purchases').find('tbody').find('tr.findID').removeClass('findID');
        getTotal();
        console.log(list_po_id);
        if (checklan == 0) {
            ajaxSelectParams('#items_search', 'admin/stock/searchMaterialAndSemiProducts', $("#items_search").val(), true, true);
        } else {
            ajaxSelectCallBackNew('#items_search', 'admin/suggest_plan_purchase/searchMaterialByPlan', $("#items_search").val(), true, true);
        }
    });

    $("#items_search").change(function() {
        dtItems = $(this).select2('data');
        checkExist = dtItems.plan_id + '__' + dtItems.id;
        if (arrId.includes(checkExist)) {
            alert_float('danger', 'Mặt hàng đã tồn tại!');
            $(this).select2("val", "");
            return;
        }
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function loadItem(item = {}) {
        if (type == 3) {
            tdStt = `<div class="stt"></div>`;
            tdCode = `<div class="code_item">
             <input type="hidden" name="counter[]" class="counter" value="${counter}">
             <input type="hidden" name="machines_id[${counter}]" class="machines_id" value="${item.id}">
            ${item.code}
            </div>`;
            tdName = `<div class="name_item">${item.name}</div>`;
            tdSupplier = `<div class="supplier">
               <input type="text" name="suppliers_id[${counter}]" id="suppliers_id_${counter}" class="suppliers_id"
                    data-placeholder="<?= lang('Nhà cung cấp') ?>" style="width: 100%;">
            </div>`;
            tdQuantity = `<div class="td-quantity"><input type="text" name="quantity[${counter}]" class="quantity form-control number-format" value="1"></div>`;
            tdPrice = `<div class="td-price"><input type="text" name="price[${counter}]" class="price form-control number-format" value="0"></div>`;
            tdAmount = `<div class="td-amount text-right"></div>`;
            tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

            trItem = `<tr>
                <td class="text-center">${tdStt}</td>
                <td>${tdCode}</td>
                <td>${tdName}</td>
                <td>${tdSupplier}</td>
                <td>${tdQuantity}</td>
                <td>${tdPrice}</td>
                <td>${tdAmount}</td>
                <td class="td-actions text-center">${tdActions}</td>
            </tr>`;
        } else {
            tdStt = `<div class="stt"></div>`;
            tdCode = `<div class="code_item">
             <input type="hidden" name="counter[]" class="counter" value="${counter}">
             <input type="hidden" name="item_id[${counter}]" class="item_id" value="${item.id}">
             <input type="hidden" name="plan_id[${counter}]" class="plan_id" value="${item.plan_id}">
            ${item.item_code}
            <div style="color: green">${item.reference_no}</div>
            </div>`;
            tdName = `<div class="name_item">${item.item_name}</div>`;
            tdSupplier = `<div class="supplier">
               <input type="text" name="suppliers_id[${counter}]" id="suppliers_id_${counter}" class="suppliers_id"
                    data-placeholder="<?= lang('Nhà cung cấp') ?>" style="width: 100%;">
            </div>`;
            tdUnit = `<div class="unit_item">${item.unit_name}</div>`;
            tdQuantity = `<div class="td-quantity"><input type="text" name="quantity[${counter}]" class="quantity form-control number-format" value="${tnhFormatNumber(item.quantity_primary)}"></div>`;
            tdPrice = `<div class="td-price"><input type="text" name="price[${counter}]" class="price form-control number-format" value="0"></div>`;
            tdAmount = `<div class="td-amount text-right"></div>`;
            tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

            trItem = `<tr class="tr_${item.plan_id}">
                <td class="text-center">${tdStt}</td>
                <td>${tdCode}</td>
                <td>${tdName}</td>
                <td>${tdSupplier}</td>
                <td>${tdUnit}</td>
                <td>${tdQuantity}</td>
                <td>${tdPrice}</td>
                <td>${tdAmount}</td>
                <td class="td-actions text-center">${tdActions}</td>
            </tr>`;
        }

        $("#tb-purchases").find('tbody').append(trItem);
        ajaxSelectParams($(`#suppliers_id_${counter}`), 'admin/suggest_repalce/searchSuppliers', 0);
        $(`#suppliers_id_${counter}`).attr('required', 'required');
        counter++;
        getTotal();
    }

    $(document).on('change', '.quantity, .price', function(event) {
        getTotal();
    });
    getTotal();

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }

    function getTotal() {
        tb = '#tb-purchases tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        var stt = 0;
        count_errors = 0;
        arrId = [];
        for (ii = 0; ii < n; ii++) {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            quantity = intVal($(element).find('.quantity').val());
            price = intVal($(element).find('.price').val());
            amount = quantity * price;
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
            item_id = $(element).find('.item_id').val();
            plan_id = $(element).find('.plan_id').val();
            checkExist = plan_id + '__' + item_id;
            if (arrId.includes(checkExist) == false) {
                arrId.push(checkExist);
            }
        }
    }

    appValidateForm($('#suggest_plan_purchase'), {
        reference_no: 'required',
        date: 'required',
        staff_id: 'required',
        branch_id: 'required',
        category_plan: 'required',
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
                url: url,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
            })
            .done(function(data) {
                console.log(data);
                if (data.result) {
                    alert_float('success', data.message);
                    window.location.href = data.link;
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
    $(document).on('change', 'input.suppliers_id', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var id = $(currentQuantityInput).val();
        var item_id = $(currentQuantityInput).parents('tr').find('.item_id').val();
        var machines_id = $(currentQuantityInput).parents('tr').find('.machines_id').val();
        $.post(admin_url + 'suggest_plan_purchase/get_price', {
            [csrfData['token_name']]: csrfData['hash'],
            suppliers_id: id,
            machines_id: machines_id,
            item_id: item_id
        }, function(data) {
            $(currentQuantityInput).parents('tr').find('.price ').val(tnhFormatMoney(data)).change();
        });
    });

    function ajaxSelectMultipleParamsC(element, url, id, data_get = {}, params = false) {
        if (id) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                multiple: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val().replace(/,/g, '_'),
                        dataType: "json",
                        data: data_get,
                        success: function(data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        var data_next_get = {
                            params: params,
                            term: term,
                            limit: 50
                        };
                        $.each(data_get, function(i, v) {
                            data_next_get[i] = v;
                        })
                        return data_next_get;
                    },
                    results: function(data, page) {
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
                }
            });
        } else {
            $(element).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                multiple: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        var data_next_get = {
                            params: params,
                            term: term,
                            limit: 50
                        };
                        $.each(data_get, function(i, v) {
                            data_next_get[i] = v;
                        })
                        return data_next_get;
                    },
                    results: function(data, page) {
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
                }
            });
        }
    }

    function ajaxSelectCallBackNew(element, url, id, types = '') {
        if (id != 0) {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val(),
                        dataType: "json",
                        success: function(data) {
                            callback(data.row);
                        }
                    });
                },
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            plan_id: $("#plan_id").val(),
                            limit: 50
                        };
                    },
                    results: function(data, page) {
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
                    data: function(term, page) {
                        return {
                            types: types,
                            term: term,
                            plan_id: $("#plan_id").val(),
                            limit: 50
                        };
                    },
                    results: function(data, page) {
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
                }
            });
        }
    }
</script>