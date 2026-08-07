<script>
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    $("#branch_id").select2();
    $("#supplier_id").select2();
    ajaxSelectParams('#po_id', 'admin/request_place_the_tank_mold/searchPo', $("#po_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/request_place_the_tank_mold/searchProductByOrders', 0);

    $("#po_id").change(function() {
        $("#order_id").select2("val", "");
        $("#tb-purchases").find('tbody').html('');
    });

    $("#order_id").change(function() {
        $("#tb-purchases").find('tbody').html('');
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
                            po_id: $("#po_id").val(),
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
                            po_id: $("#po_id").val(),
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
    if (edit == 1){
        for (i = 0; i < counter; i++) {
            ajaxSelectParams($(`#operating_equipment_${i}`), 'admin/request_place_the_tank_mold/searchOperating_equipment', $(`#operating_equipment_${i}`).val());
        }
    }
    $("#items_search").change(function() {
        dtItems = $(this).select2('data');
        if (arrId.includes(dtItems.id)) {
            alert_float('danger', 'Mặt hàng đã tồn tại!');
            $(this).select2("val", "");
            return;
        }
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function optionTax(selected_id = 0) {
        option = `<option></option>`;
        $.each(taxs, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option += '<option ' + selected + ' data-rate="' + el.taxrate + '" value="' + el.id + '">' + el.name + '</option>';
        });
        return option;
    }

    function loadItem(item = {}) {
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="order_item_id[${counter}]" class="order_item_id" value="${item.order_item_id}">
         <input type="hidden" name="pod_id[${counter}]" class="pod_id" value="${item.id}">
         <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
        ${item.code_item}
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdUnit = `<div class="td_unit">${item.unit_name}</div>`;
        tdQuantityTotal = `<div class="td-quabtity_total"><input type="text" name="quabtity_total[${counter}]" class="quabtity_total form-control number-format" value="0"></div>`;
        tdHeight = `<div class="height_item">${item.height}</div>`;
        tdWide = `<div class="wide_item">${item.wide}</div>`;

        // tdOperatingEquipment = `<div class="td-operating_equipment"><input type="text" name="operating_equipment[${counter}]" class="operating_equipment form-control" value=""></div>`;
        tdOperatingEquipment = `<div class="supplier">
           <input type="text" name="operating_equipment[${counter}]" id="operating_equipment_${counter}" class="operating_equipment"
                data-placeholder="<?= lang('Mã Thiết Bị Vận Hành') ?>" style="width: 100%;">
        </div>`;
        tdProductivityNorms = `<div class="td-productivity_norms"><input type="text" name="productivity_norms[${counter}]" class="productivity_norms form-control format-number" value=""></div>`;
        tdTotal = `<div class="td_total">0</div>`;

        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td style="width: 150px">${tdHeight}</td>
            <td style="width: 150px">${tdWide}</td>
            <td style="width: 100px">${tdUnit}</td>
            <td style="width: 150px">${tdQuantityTotal}</td>
            <td style="width: 150px">${tdOperatingEquipment}</td>
            <td style="width: 150px">${tdProductivityNorms}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#tax_id_${counter}`).select2();
        ajaxSelectParams($(`#operating_equipment_${counter}`), 'admin/request_place_the_tank_mold/searchOperating_equipment', 0);

        init_datepicker();
        init_selectpicker();
        counter++;
        getTotal();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }
    $(document).on('change', 'input.operating_equipment', function(e) {
        var quota_productivity = $(this).select2('data').quota_productivity;
        $(this).parents('tr').find('input.productivity_norms').val(tnhFormatNumber(quota_productivity));
        getTotal();
    });
    $(document).on('change', 'select.tax_id', function(e) {
        var tax_id = $(this).val();
        var tax_rate = parseInt($(this).find('option:selected').attr('data-rate'));
        var current_row = $(this).parents('tr');
        if (isNaN(tax_rate)) tax_rate = 0;
        $(this).parents('tr').find('input.tax_rate').val(tax_rate);
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
            pod_id = $(element).find('.pod_id').val();
            price = intVal($(element).find('.price').val());
            console.log(price)
            quabtity_purchase = intVal($(element).find('.quabtity_purchase').val());
            let tax = $(element).find('.tax_rate').val();

            var total = price * quabtity_purchase * (1 + tax / 100);
            $(element).find('.td_total').html(tnhFormatMoney(total));
            if (arrId.includes(pod_id) == false) {
                arrId.push(pod_id);
            }
        }
    }

    appValidateForm($('#request_place_the_tank_mold'), {
        reference_no: 'required',
        date: 'required',
        po_id: 'required',
        branch_id: 'required',
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
                    window.location.href = site.base_url + 'admin/request_place_the_tank_mold';
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
    getTotal();
</script>