<script>
    var taxes_dropdown_template = <?= json_encode($taxes) ?>;
    $("#branch_id").select2();
    $("#supplier_id").select2();
    ajaxSelectParams('#po_id', 'admin/request_graft_size/searchPo', $("#po_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/request_graft_size/searchMachines', 0);

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
    if (edit == 1) {
        for (i = 0; i < counter; i++) {
            ajaxSelectParams($(`#operating_equipment_${i}`), 'admin/request_graft_size/searchOperating_equipment', $(`#operating_equipment_${i}`).val());
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
        tdName = `<div class="name_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="machines[${counter}]" class="machines" value="${item.id}">
         <input type="hidden" name="id_products[${counter}]" class="id_products" value="${item.id_products}">
        <input type="hidden" name="id_items_stages[${counter}]" class="id_items_stages" value="${item.id_items_stages}">
        <input type="hidden" name="id_stages[${counter}]" class="id_stages" value="${item.id_stages}">
         
        ${item.name}
        </div>`;
        tdUnit = `<div class="td_unit">${item.unit_name}</div>`;
        tdHeight = `<div class="height_item">${item.height}</div>`;
        tdWide = `<div class="wide_item">${item.wide}</div>`;
        tdchildsheet = `<div class="td-childsheet"><input type="text" name="childsheet[${counter}]" class="childsheet form-control number-format" value="${item.quantity_child_sheet}"></div>`;
        printcolor1 = '';
        quantity_zinc1 = '';
        number_operations1 = '';
        printcolor2 = '';
        quantity_zinc2 = '';
        number_operations2 = '';
        readonly1 = 'readonly="readonly"';
        readonly2 = 'readonly="readonly"';
        total_zinc = 0;
        if (item.face == 1) {
            readonly1 = '';
            printcolor1 = intVal(item.printcolor);
            quantity_zinc1 = intVal(item.quantity_zinc);
            number_operations1 = intVal(item.number_operations);
            total_zinc += intVal(quantity_zinc1);
        }
        if (item.face_after == 2) {
            readonly2 = '';
            printcolor2 = intVal(item.printcolor);
            quantity_zinc2 = intVal(item.quantity_zinc);
            number_operations2 = intVal(item.number_operations);
            total_zinc += intVal(quantity_zinc2);
        }

        tdColumnsSheets1 = `<div class="td-ColumnsSheets1"><input ${readonly1} type="text" name="columnssheets1[${counter}]" class="columnssheets1 form-control number-format" value="0"></div>`;
        tdRowsSheets1 = `<div class="td-rowssheets1"><input ${readonly1} type="text" name="rowssheets1[${counter}]" class="rowssheets1 form-control number-format" value="0"></div>`;
        tdPrintColor1 = `<div class="td-quantity_print_color1"><input readonly="readonly" type="text" name="quantity_print_color1[${counter}]" class="quantity_print_color1 form-control number-format" value="${printcolor1}"></div>`;
        tdZinc1 = `<div class="td-quantity_zinc1"><input readonly="readonly" type="text" name="quantity_zinc1[${counter}]" class="quantity_zinc1 form-control number-format" value="${quantity_zinc1}"></div>`;
        tdNumberOperations1 = `<div class="td-number_operations1"><input readonly="readonly" type="text" name="number_operations1[${counter}]" class="number_operations1 form-control number-format" value="${number_operations1}"></div>`;

        tdColumnsSheets2 = `<div class="td-ColumnsSheets2"><input ${readonly2} type="text" name="columnssheets2[${counter}]" class="columnssheets2 form-control number-format" value="0"></div>`;
        tdRowsSheets2 = `<div class="td-rowssheets2"><input ${readonly2} type="text" name="rowssheets2[${counter}]" class="rowssheets2 form-control number-format" value="0"></div>`;
        tdPrintColor2 = `<div class="td-quantity_print_color2"><input readonly="readonly" type="text" name="quantity_print_color2[${counter}]" class="quantity_print_color2 form-control number-format" value="${printcolor2}"></div>`;
        tdZinc2 = `<div class="td-quantity_zinc2"><input readonly="readonly" type="text" name="quantity_zinc2[${counter}]" class="quantity_zinc2 form-control number-format" value="${quantity_zinc2}"></div>`;
        tdNumberOperations2 = `<div class="td-number_operations2"><input readonly="readonly" type="text" name="number_operations2[${counter}]" class="number_operations2 form-control number-format" value="${number_operations2}"></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;
        tdZinc = `<div class="td-quantity_total_zinc"><input readonly="readonly" type="text" name="quantity_total_zinc[${counter}]" class="quantity_total_zinc form-control number-format" value="${total_zinc}"></div>`;
        tdSizegraft = `<div class="td-sizegraft"><input type="text" name="sizegraft[${counter}]" class="sizegraft form-control number-format" value=""></div>`;
        tdTotalSize = `<div class="td-totalsize"><input type="text" name="totalsize[${counter}]" class="totalsize form-control number-format" value=""></div>`;
        tdLayout = `<div class="td-layout"><input type="text" name="layout[${counter}]" class="layout form-control number-format" value=""></div>`;
        tdTimeQuota = `<div class="td-timequota"><input type="text" name="timequota[${counter}]" class="number-format timequota form-control" value=""></div>`;

        if (item.images) {
            images = site.base_url + item.images;
        } else {
            images = site.base_url + 'assets/images/tnh/no_image.png';
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
        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdName}</td>
            <td style="width: 150px">${tdHeight}</td>
            <td style="width: 150px">${tdWide}</td>
            <td style="width: 100px">${tdchildsheet}</td>
            <td style="width: 100px">${tdColumnsSheets1}</td>
            <td style="width: 100px">${tdRowsSheets1}</td>
            <td style="width: 100px">${tdPrintColor1}</td>
            <td style="width: 100px">${tdZinc1}</td>
            <td style="width: 100px">${tdNumberOperations1}</td>
            <td style="width: 100px">${tdColumnsSheets2}</td>
            <td style="width: 100px">${tdRowsSheets2}</td>
            <td style="width: 100px">${tdPrintColor2}</td>
            <td style="width: 100px">${tdZinc2}</td>
            <td style="width: 100px">${tdNumberOperations2}</td>
            <td style="width: 100px">${tdZinc}</td>
            <td style="width: 100px">${tdSizegraft}</td>
            <td style="width: 100px">${tdTotalSize}</td>
            <td style="width: 100px">${tdLayout}</td>
            <td style="width: 100px">${tdTimeQuota}</td>
            <td >${tdImages}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#tax_id_${counter}`).select2();
        ajaxSelectParams($(`#operating_equipment_${counter}`), 'admin/request_graft_size/searchOperating_equipment', 0);

        init_datepicker();
        init_selectpicker();
        counter++;
        getTotal();
    }

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
            pod_id = $(element).find('.pod_id').val();
            quantity_zinc1 = intVal($(element).find('.quantity_zinc1').val());
            quantity_zinc2 = intVal($(element).find('.quantity_zinc2').val());
            total_zinc = intVal($(element).find('.quantity_total_zinc').val(quantity_zinc1 + quantity_zinc2));
            if (arrId.includes(pod_id) == false) {
                arrId.push(pod_id);
            }
        }
    }

    appValidateForm($('#request_graft_size'), {
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
                    window.location.href = site.base_url + 'admin/request_graft_size';
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