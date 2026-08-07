<script>
    $("#branch_id").select2();
    $("#staff_plan").select2();

    ajaxSelectMultipleParams('#object_id', 'admin/suggest_outsource/searchPoAndOrder', $('#object_id').val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_outsource/searchProductByOrders', 0);


    function ajaxSelectMultipleParams(element, url, id, params = false) {
        if (id) {
            $(element).val(id).select2({
                width: 'resolve',
                //allowClear: true,
                multiple: true,
                initSelection: function(element, callback) {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: site.base_url + url + '/' + $(element).val().replace(/,/g, '_') + '/' + $("input[name=object_type]:checked").val(),
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
                            params: params,
                            term: term,
                            limit: 50,
                            object_type: $("input[name=object_type]:checked").val()
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
                multiple: true,
                ajax: {
                    url: site.base_url + url,
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        return {
                            params: params,
                            term: term,
                            limit: 50,
                            object_type: $("input[name=object_type]:checked").val()
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



    $(document).on('change', 'input[name="object_type"]', function(e) {
        var value = $(this).val();
        $("#object_id").select2("val", "");
        $("#tb-purchases").find('tbody').html('');
        getTotal();
    });


    $("#object_id").change(function() {
        list_object_id = $(this).val().split(',');
        $.each(list_object_id, function(k, v) {
            $('#tb-purchases').find('tbody').find(`tr.tr_${v}`).addClass('findID');
        })
        $('#tb-purchases').find('tbody').find('tr:not(.findID)').remove();
        $('#tb-purchases').find('tbody').find('tr.findID').removeClass('findID');
        getTotal();
    });

    function ajaxSelectCallBack(element, url, id, types = '') {
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
                            order_id: $("#order_id").val(),
                            object_id: $("#object_id").val(),
                            object_type: $("input[name=object_type]:checked").val(),
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
                            order_id: $("#order_id").val(),
                            object_id: $("#object_id").val(),
                            object_type: $("input[name=object_type]:checked").val(),
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


    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            $(`#print_${i}`).select2();
            $(`#material_${i}`).select2();
            $(`#tax_id_${i}`).select2();
            ajaxSelectParams($(`#suppliers_id_${i}`), 'admin/suggest_repalce/searchSuppliers', $(`#suppliers_id_${i}`).val());
            ajaxSelectParams($(`#stage_id_${i}`), 'admin/suggest_outsource/searchStage', $(`#stage_id_${i}`).val(), {
                pod_id: $(`#pod_id_${i}`).val()
            });

        }
    }

    function optionResult(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtResult, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function optionStaff(selected_id = 0) {
        option = `<option></option>`;
        $.each(dtStaff, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += `<option ${selected} value="${el.staffid}">${el.firstname} ${el.lastname}</option>`;
        });
        return option;
    }

    function optionTax(selected_id = 0) {
        option = `<option></option>`;
        $.each(taxs, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' data-rate="' + el.taxrate + '" value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function optionPrint(selected_id = 0) {
        option = `<option></option>`;
        $.each(print, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function optionMaterial(material = [], selected_id = 0) {
        option = `<option></option>`;
        $.each(material, function(index, el) {
            quantity_compensation = el.quantity_compensation;
            landscape_print_size = el.landscape_print_size;
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option data-quantity_compensation=' + quantity_compensation + ' data-landscape_print_size=' + landscape_print_size + ' ' + selected + ' value="' + el.type + '__' + el.item_id + '">' + el.name_items + '</option>';
        });
        return option;
    }
    $("#items_search").change(function() {
        dtItems = $(this).select2('data');
        object_type = $('input[name="object_type"]:checked').val();
        // if (arrId.includes(dtItems.id)){
        //     alert_float('danger','Mặt hàng đã tồn tại!');
        //     $(this).select2("val", "");
        //     return;
        // }
        id = dtItems.id;
        productions_orders_id = dtItems.productions_orders_id;
        items_id = dtItems.item_id;
        plan_id = dtItems.plan_id;
        $.get(admin_url + 'suggest_outsource/getFullDataItems/' + id + '/' + productions_orders_id + '/' + items_id + '/' + plan_id + '/' + object_type, function(data) {
            data = JSON.parse(data);
            loadItem(dtItems, data)
        })
        $(this).select2("val", "");
    })

    function loadItem(item = {}, data) {

        // <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
        var sltin = data.sltin;
        var items = data.items;
        tdStt = `<div class="stt"></div>`;
        tdSupplier = `<div class="supplier">
           <input type="text" name="suppliers_id[${counter}]" id="suppliers_id_${counter}" class="suppliers_id"
                data-placeholder="<?= lang('Nhà gia công') ?>" style="width: 100%;">
        </div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="order_item_id[${counter}]" class="order_item_id" value="${item.order_item_id}">
         <input type="hidden" name="productions_orders_id[${counter}]" class="productions_orders_id" value="${item.productions_orders_id}">
         <input type="hidden" name="plan_id[${counter}]" class="plan_id" value="${item.plan_id}">
         <input type="hidden" name="pod_id[${counter}]" class="pod_id" value="${item.id}">
         <input type="hidden" name="order_id[${counter}]" class="order_id" value="${item.order_id}">
        ${item.code_item}
        <div style="color: green">${item.reference_no_order}</div>
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdimage = `
            <div class="td-image" style="display: flex;justify-content: center;">
                <div class="preview_image" style="width: auto;">
                    <div class="display-block contract-attachment-wrapper img">
                        <div style="width:45px;">
                            <a href="${item.images}" data-lightbox="customer-profile" class="display-block mbot5">
                                <div class="">
                                    <img src="${item.images}" style="border-radius: 50%">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
        </div>`;
        tdStage = `<div class="td-stage">
           <input type="text" name="stage_id[${counter}]" id="stage_id_${counter}" class="stage_id"
                data-placeholder="<?= lang('Công đoạn') ?>" style="width: 100%;">
        </div>`;

        tdMatetial = `<div>
            <select class="material" id="material_${counter}" name="material[${counter}]" style="width: 100%;" data-placeholder="<?= lang('Nguyên vật liệu') ?>">
                ${optionMaterial(items)}
            </select>
        </div>`;
        tdPrint = `<div>
            <select class="print" id="print_${counter}" name="print[${counter}]" style="width: 100%;" data-placeholder="<?= lang('Cách in') ?>">
                ${optionPrint()}
            </select>
        </div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        var styleDiv = ' style="width: 150px" ';
        var styleDivr = ' style="width: 100px" ';

        tdMode = `<div class="mode" ${styleDivr}>${item.mode}</div>`;
        tdUnit = `<div class="unit" ${styleDivr}>${item.unit_name}</div>`;
        tdDetail = `<div class="note_detail"><textarea  style="width: 150px;" name="note_detail[${counter}]" class="note_detail form-control"><?=$value['note_detail'] ?? ''?></textarea></div>`;
        tdshipping_unit_outsource = `<div ${styleDiv}><input type="text" name="shipping_unit_outsource[${counter}]" class="shipping_unit_outsource form-control" value="${item.shipping_unit_outsource ?? ''}"></div>`;
        tdtransport_outsource = `<div ${styleDiv}><input type="text" name="transport_outsource[${counter}]" class="transport_outsource form-control" value="${item.transport_outsource ?? ''}"></div>`;
        tdPrice_transport = `<div ${styleDiv}><input type="text" name="price_transport[${counter}]" class="price_transport form-control number-format" value="${item.amount_transport ?? ''}"></div>`;
        tdPrice = `<div ${styleDiv}><input type="text" name="price[${counter}]" class="price form-control number-format" value="${item.price ?? ''}"></div>`;
        tdAmount = `<div ${styleDivr} class="td-amount"></div>`;
        tdTax = `<div>
                        <select class="tax_id" id="tax_id_${counter}" name="tax_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Thuế') ?>">
                            ${optionTax()}
                        </select>
                        <input type="hidden" class="tax_rate" name="tax_rate[${counter}]" value="0">
                </div>`;

        tdGrandTotal = `<div class="td-grand_total" ${styleDiv}></div>`;
        tdQuantity = `<div class="td-quantity" ${styleDiv}><input type="text" name="quantity[${counter}]" class="quantity form-control number-format" value="${item.total_quantity_item ?? 1}"></div>`;

        trItem = `<tr class='tr_${item.order_id}'>
            <td class="text-center">${tdStt}</td>
            <td style="width: 200px">${tdSupplier}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>


            <td>${tdMode}</td>
            <td style="width: 150px">${tdUnit}</td>
            <td style="width: 150px">${tdDetail}</td>
            <td style="width: 150px">${tdshipping_unit_outsource}</td>
            <td style="width: 150px">${tdtransport_outsource}</td>
            <td style="width: 150px">${tdPrice_transport}</td>
            <td style="width: 150px">${tdPrice}</td>
            <td style="width: 150px">${tdQuantity}</td>
            <td style="width: 150px">${tdAmount}</td>
            <td style="width: 150px">${tdTax}</td>
            <td style="width: 150px">${tdGrandTotal}</td>



            <td  style="width: 150px" class="text-center"><input type="hidden" name="sltin[${counter}]" class="sltin" value="${sltin}">${tnhFormatNumber(sltin)}</td>
            <td  style="width: 150px">${tdMatetial}</td>
            <td  style="width: 150px" class="text-center tdquantity_compensation"></td>
            <td  style="width: 150px"><div class="td-quantity_compensation_more"><input type="text" name="quantity_compensation_more[${counter}]" class="quantity_compensation_more form-control number-format" value="0"></div></td>
            <td  style="width: 150px" class="text-center tdlandscape_print_size"></td>
            <td  style="width: 150px">${tdimage}</td>
            <td style="width: 150px">${tdStage}</td>
            <td  style="width: 150px">${tdPrint}</td>
            <td  style="width: 150px"><div class="td-number_of_printed_sides"><input type="text" name="number_of_printed_sides[${counter}]" class="number_of_printed_sides form-control number-format" value="0"></div></td>
            <td  style="width: 150px"><div class="td-color_number_a"><input type="text" name="color_number_a[${counter}]" class="color_number_a form-control number-format" value="0"></div></td>
            <td  style="width: 150px"><div class="td-color_number_b"><input type="text" name="color_number_b[${counter}]" class="color_number_b form-control number-format" value="0"></div></td>
            <td  style="width: 150px"><div class="td-zinc_number_a"><input type="text" name="zinc_number_a[${counter}]" class="zinc_number_a form-control number-format" value="0"></div></td>
            <td  style="width: 150px"><div class="td-zinc_number_b"><input type="text" name="zinc_number_b[${counter}]" class="zinc_number_b form-control number-format" value="0"></div></td>
            <td  style="width: 150px"><div class="td-grape"><input type="text" name="grape[${counter}]" class="grape form-control number-format" value="0"></div></td>
            <td  style="width: 450px"><input type="file" name="image_mucin[${counter}]" class="form-control image" value="" title=""></td>
            <td  style="width: 450px"><input type="file" name="image_bongmo[${counter}]" class="form-control image" value="" title=""></td>
            <td  style="width: 250px"><textarea style="width: 100%;" class="note_items" name="note_items[${counter}]" value=""></textarea></td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        ajaxSelectParams($(`#suppliers_id_${counter}`), 'admin/suggest_repalce/searchSuppliers', 0);
        ajaxSelectParams($(`#stage_id_${counter}`), 'admin/suggest_outsource/searchStage', 0, {
            pod_id: item.id
        });

        $(`#material_${counter}`).select2();
        $(`#print_${counter}`).select2();
        // $(`#result_id_${counter}`).select2();
        // $(`#result_id_${counter}`).attr('required','required');
        // $(`#staff_id_${counter}`).select2();
        // $(`#staff_id_${counter}`).attr('required','required');
        $(`#suppliers_id_${counter}`).attr('required', 'required');
        $(`#stage_id_${counter}`).attr('required', 'required');
        init_datepicker();
        counter++;
        getTotal();
    }
    $(document).on('change', '.material', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var quantity_compensation = currentQuantityInput.find('option:selected').attr('data-quantity_compensation');
        var landscape_print_size = currentQuantityInput.find('option:selected').attr('data-landscape_print_size');

        tdquantity_compensation = '<input type="hidden" name="quantity_compensation[]" class="quantity_compensation" value="' + quantity_compensation + '">' + tnhFormatNumber(quantity_compensation);
        currentQuantityInput.parents('tr').find('.tdquantity_compensation').html(tdquantity_compensation);

        tdlandscape_print_size = '<input type="hidden" name="landscape_print_size[]" class="landscape_print_size" value="' + landscape_print_size + '">' + (landscape_print_size);
        currentQuantityInput.parents('tr').find('.tdlandscape_print_size').html(tdlandscape_print_size);
    });

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }

    $('body').on('change', '.quantity, .price, .price_transport, .tax_id', function() {
        getTotal();
    });

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
            tax_rate_item = intVal($(element).find('select.tax_id').select2().find(":selected").data('rate'));
            console.log('tax_rate_item', tax_rate_item)
            quantity = intVal($(element).find('.quantity').val());
            console.log('quantity', quantity)
            price = intVal($(element).find('.price').val());
            console.log('price', price)
            price_transport = intVal($(element).find('.price_transport').val());
            console.log('price_transport', price_transport)
            amount = quantity * price;
            amount += price_transport;
            total_tax = (amount * tax_rate_item / 100);
            pod_id = $(element).find('.pod_id').val();
            if (arrId.includes(pod_id) == false) {
                arrId.push(pod_id);
            }
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
            $(element).find('.td-grand_total').html(tnhFormatMoney(amount + total_tax));
            $(element).find('.td-amount-transport').html(tnhFormatMoney(price_transport));
        }
    }

    appValidateForm($('#suggest_outsource'), {
        reference_no: 'required',
        date: 'required',
        po_id: 'required',
        branch_id: 'required',
        staff_plan: 'required',
    }, db);
    var EventCreate = 0;

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
                    if(data.id) {
                        EventCreate = 1;
                        new_task(admin_url + `tasks/task?suggest_id=${data.id}&rel_append_id=1&category_recommended_id=${data.id_category_recomment}`)
                    }
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

    $('#task-modal').on('shown.bs.modal', function () {
        if(EventCreate == 1) {
            window.location.href = site.base_url + 'admin/suggest_outsource';
        }
    });
    $('#_task_modal').on('hide.bs.modal', function () {
        if(EventCreate == 1) {
            window.location.href = site.base_url + 'admin/suggest_outsource';
        }
    });
</script>