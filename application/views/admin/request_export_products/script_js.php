<script>
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    $("#branch_id").select2();
    $("#supplier_id").select2();
    ajaxSelectParams('#po_id', 'admin/request_export_products/searchPo', $("#po_id").val(), true, true);
    ajaxSelectCallBack('#order_id', 'admin/request_export_products/searchOrders', $("#order_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/request_export_products/searchProductByOrders', 0);

    $("#po_id").change(function() {
        $("#order_id").select2("val", "");
        $("#tb-purchases").find('tbody').html('');
    });

    $("#order_id").change(function() {
        $("#tb-purchases").find('tbody').html('');
    });
    $(document).on('change', '#supplier_id', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var id = $(currentQuantityInput).val();
        if (id == '') {} else {
            $.post(admin_url + 'suppliers/get_debt/' + id, {
                [csrfData['token_name']]: csrfData['hash']
            }, function(data) {
                data = JSON.parse(data);
                table_price = data.table_price;
                <?php if (empty($dtItems)) { ?>
                    var items = $('#tb-purchases tbody').find('tr');
                    $.each(items, (index, value) => {
                        $(value).find('input.price ').val(0).change();
                    });
                    $.each(items, (index, value) => {
                        if (table_price.length > 0) {
                            $.each(table_price, (i, v) => {
                                var product_id = $(value).find(
                                    'input.item_id').val();
                                if (v.product_id == product_id && v.product_type == 'nvl') {
                                    $(value).find('input.price ').val(tnhFormatNumber(v.price)).change();
                                }
                            });
                        }
                    });
                <?php } ?>
            });
        }
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
        tdStt = `<div class="stt"></div>`;
        tdBrand_name = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="order_item_id[${counter}]" class="order_item_id" value="${item.order_item_id}">
         <input type="hidden" name="pod_id[${counter}]" class="pod_id" value="${item.id}">
         <input type="hidden" class="item_id" value="${item.item_id}">
         <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
        ${item.brand_name}
        </div>`;
        tdCode = `<div class="name_item">${item.code_item}</div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        if (item.name_species == null) {
            item.name_species = '';
        }
        tdName_category = `<div class="td_name_category">${item.category_name}</div>`;
        tdName_species = `<div class="td_name_species">${item.name_species}</div>`;
        tdQuantity = `<div class="td_quantity text-center">${tnhFormatNumber(item.total_quantity_item)}</div>`;
        tdQuantityWarehouse = `<div class="td_quantity text-center">${tnhFormatNumber(item.product_quantity)}</div>`;
        tdQuantityManufactures = `<div class="td-quabtity_manufactures"><input type="text" name="quabtity_manufactures[${counter}]" class="quabtity_manufactures form-control number-format" value=""></div>`;
        tdQuantityAllow = `<div class="td-quabtity_allow"><input type="text" name="quabtity_allow[${counter}]" class="number-format quabtity_allow form-control" value=""></div>`;
        tdQuantityPurchase = `<div class="td-quabtity_purchase"><input type="text" name="quabtity_purchase[${counter}]" class="number-format quabtity_purchase form-control" value=""></div>`;

        tdHeight = `<div class="height_item">${item.height}</div>`;
        tdWide = `<div class="wide_item">${item.wide}</div>`;
        tdLongs = `<div class="longs_item">${item.longs}</div>`;
        tdtotalCon = `<div class="td-totalcon"><input type="text"   name="totalcon[${counter}]" class="number-format totalcon form-control" value=""></div>`;
        tdtotalkien = `<div class="td-totalkien"><input type="text"   name="totalkien[${counter}]" class="number-format totalkien form-control" value=""></div>`;
        tdtotalkg = `<div class="td-totalkg"><input type="text"   name="totalkg[${counter}]" class="number-format totalkg form-control" value=""></div>`;
        tdtotalallkien = `<div class="td-totalallkien"><input type="text"   name="totalallkien[${counter}]" class="number-format totalallkien form-control" value=""></div>`;
        tdTimeQuota = `<div class="td-timequota"><input type="text" name="timequota[${counter}]" class="number-format timequota form-control" value=""></div>`;
        tdTimeRegulations = `<div class="td-timeregulations"><input type="text" name="timeregulations[${counter}]" class="number-format timeregulations form-control" value=""></div>`;
       
        tdTotal = `<div class="td_total">0</div>`;

        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdBrand_name}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td>${tdName_category}</td>
            <td>${tdName_species}</td>
            <td>${tdQuantity}</td>
            <td>${tdQuantityWarehouse}</td>
            <td style="width: 150px">${tdQuantityManufactures}</td>
            <td style="width: 150px">${tdQuantityAllow}</td>
            <td style="width: 150px">${tdQuantityPurchase}</td>
            <td style="width: 150px">${tdHeight}</td>
            <td style="width: 150px">${tdWide}</td>
            <td style="width: 150px">${tdtotalCon}</td>
            <td style="width: 150px">${tdtotalkien}</td>
            <td style="width: 150px">${tdtotalkg}</td>
            <td style="width: 150px">${tdtotalallkien}</td>
            <td style="width: 150px">${tdTimeQuota}</td>
            <td style="width: 150px">${tdTimeRegulations}</td>
            <td>${tdImages}</td>
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

    appValidateForm($('#request_export_products'), {
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
                    window.location.href = site.base_url + 'admin/request_export_products';
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