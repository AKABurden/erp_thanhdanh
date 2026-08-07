<script>
    $("#branch_id").select2();
    $("#supplier_id").select2();
    ajaxSelectParamsCallback('#client_id', 'admin/clients/searchCustomers/', $('#client_id').val(), false, true);

    ajaxSelectCallBack('#id_quotes', 'admin/request_template/searchQuotes', $("#id_quotes").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/request_template/searchProductByOrders', 0);


    $("#id_quotes").change(function() {
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
                            id_quotes: $("#id_quotes").val(),
                            client_id: $("#client_id").val(),
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
                            id_quotes: $("#id_quotes").val(),
                            client_id: $("#client_id").val(),
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

    function loadItem(item = {}) {
        var _id_quotes = $('#id_quotes').val();
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="quote_items_id[${counter}]" class="quote_items_id" value="${_id_quotes ? item.id : 0}">
         <input type="hidden" name="item_id[${counter}]" class="item_id" value="${item.item_id}">
        ${item.item_code}
        </div>`;
        if (item.images) {
            images = site.base_url+item.images;
        } else {
            images = site.base_url+'assets/images/tnh/no_image.png';
        }
        tdImages = `<div class="td-image">
                    <div class="preview_image" style="width: auto;">
                        <div class="display-block contract-attachment-wrapper img">
                            <div style="width:45px;">
                                <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                    <div class="">
                                        <img src="${images}" style="border-radius: 50%">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
            </div>`;
        tdName = `<div class="name_item">${item.item_name}</div>`;
        tdBrand = `<div class="brand_item">
        ${(item.brand_name ?? '')}
        </div>`;
        // tdCode = `<div class="code_item">${item.item_code}</div>`;
        tdUnit = `<div class="unit_item">${item.unit_name}</div>`;
        tdCategory = `<div class="category_item">${item.category_name}</div>`;
        tdSpecie = `<div class="specie_item">${(item.specie_name ?? '')}</div>`;
        tdHeight = `<div class="height_item">${item.height}</div>`;
        tdWide = `<div class="wide_item">${item.wide}</div>`;
        tdUnit_measure = `<div class="unit_measure_item">${(item.unit_measure ?? '')}</div>`;
        tdPacking = `<div class="packing_item">${item.packing}</div>`;
        tdQuantity_max = `<div class="quantity_max_item text-center">${tnhFormatNumber(item.quantity_max)}</div>`;
        tdTime_inventory = `<div class="time_inventory_item text-center">${tnhFormatNumber(item.time_inventory)}</div>`;
        tdQuota_time_change_one = `<div class="quota_time_change_one_item text-center">${tnhFormatNumber(item.quota_time_change_one)}</div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        var date_run_sample = `<td>
            <input type="text" name="date_run_sample[${counter}]" placeholder="Ngày Chạy Mẫu" class="form-control datepicker" value="">
        </td>`;

        var date_finished = `<td>
            <input type="text" name="date_finished[${counter}]" placeholder="Ngày Hoàn Thành Mẫu" class="form-control datepicker" value="">
        </td>`;

        var date_request_sample = `<td>
            <input type="text" name="date_request_sample[${counter}]" placeholder="Ngày Gửi Mẫu" class="form-control datepicker" value="">
        </td>`;

        var date_approved_sample = `<td>
            <input type="text" name="date_approved_sample[${counter}]" placeholder="Ngày Duyệt Mẫu" class="form-control datepicker" value="">
        </td>`;

        var date_runs_sample = `<td>
            <input type="text" name="date_runs_sample[${counter}]" placeholder="Chạy Hàng Lấy Mẫu" class="form-control datepicker" value="">
        </td>`;

        var date_finished_manufactures = `<td>
            <input type="text" name="date_finished_manufactures[${counter}]" placeholder="Ngày Hoàn Thành Mẫu SX" class="form-control datepicker" value="">
        </td>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCategory}</td>
            <td>${tdSpecie}</td>
            <td>${tdUnit}</td>
            <td>${tdHeight}</td>
            <td>${tdWide}</td>
            <td>${tdUnit_measure}</td>
            <td>${tdCode}</td>
            <td>${tdName}</td>
            <td>${tdBrand}</td>
            <td>${tdPacking}</td>
            <td>${tdQuantity_max}</td>
            <td>${tdTime_inventory}</td>
            <td>${tdQuota_time_change_one}</td>
            <td>${tdImages}</td>
            ${date_run_sample}
            ${date_finished}
            ${date_request_sample}
            ${date_approved_sample}
            ${date_runs_sample}
            ${date_finished_manufactures}
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#tax_id_${counter}`).select2();
        init_datepicker();
        init_selectpicker();
        counter++;
        getTotal();
    }

    function removeRow(el) {
        $(el).closest('tr').remove();
        getTotal();
    }
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

    appValidateForm($('#request_template'), {
        reference_no: 'required',
        date: 'required',
        po_id: 'required',
        branch_id: 'required',
        order_id: 'required',
        supplier_id: 'required',
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
                    window.location.href = site.base_url + 'admin/request_template';
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