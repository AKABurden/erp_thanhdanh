<script>
    $("#branch_id").select2();
    $("#staff_plan").select2();
    $("#staff_agree").select2();
    ajaxSelectMultipleParamsC('#po_id', `admin/request_overtime/search_poid_orders`, $("#po_id").val(), {type_object : $('#type_object').val()}, true);
    ajaxSelectCallBack($('#items_search'), `admin/request_overtime/searchProductByOrdersPOID?type_object=${$('#type_object').val()}`, 0);

    $("#po_id").change(function (){
        var list_po_id = $("#po_id").val().split(',');
        $.each(list_po_id, function(index, value) {
            $('#tb-purchases').find('tbody').find(`tr.tr_${value}`).addClass('findID');
        })
        $('#tb-purchases').find('tbody').find('tr:not(.findID)').remove();
        $('#tb-purchases').find('tbody').find('tr.findID').removeClass('findID');
        getTotal();
    });
    
    $("#type_object").change(function () {
        if(confirm('Thay đổi loại phiếu sẽ xóa hết dữ liệu trong bảng mặt hàng bạn có muốn thay đổi?')) {
            $("#tb-purchases").find('tbody').html('');
            $('#po_id').val('');
            ajaxSelectMultipleParamsC('#po_id', `admin/request_overtime/search_poid_orders`, 0, {type_object : $('#type_object').val()}, true);
            ajaxSelectCallBack($('#items_search'), `admin/request_overtime/searchProductByOrdersPOID?type_object=${$('#type_object').val()}`, 0);
        }
        else {
            if($("#type_object").val() == 'productions_orders') {
                $("#type_object").val('orders').select2();
            }
            else {
                $("#type_object").val('productions_orders').select2();
            }
        }
        
        if($("#type_object").val() == 'orders') {
            $('span.name_type').text('Đơn hàng bán');
        }
        else {
            $('span.name_type').text('Lệnh sản xuất');
        }
    });
    
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

    function ajaxSelectMultipleParamsC(element, url, id, data_get = {}, params = false)
    {
        if (id)
        {
            $(element).val(id).select2({
                // minimumInputLength: 1,
                width: 'resolve',
                //allowClear: true,
                multiple: true,
                initSelection: function (element, callback) {
                    $.ajax({
                        type: "get", async: false,
                        url: site.base_url + url + '/' + $(element).val().replace(/,/g, '_'),
                        dataType: "json",
                        data: data_get,
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
                    results: function (data, page) {
                        if(data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{id: '', text: 'No Match Found'}]};
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
                    data: function (term, page) {
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
            $(`#stage_id_${i}`).select2();
            $(`#staff_id_${i}`).select2();
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

    function optionStage(selected_id = 0){
        option = `<option></option>`;
        $.each(dtCategoryStage, function(index, el) {
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
        if (arrId.includes(dtItems.id)){
            alert_float('danger','Mặt hàng đã tồn tại!');
            $(this).select2("val", "");
            return;
        }
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function loadItem(item = {}){
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="order_item_id[${counter}]" class="order_item_id" value="${item.order_item_id}">
         <input type="hidden" name="order_id[${counter}]" class="order_id" value="${item.order_id}">
         <input type="hidden" name="pod_id[${counter}]" class="pod_id" value="${item.id}">
         <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
        ${item.code_item}
        <div style="color: green">${item.reference_no_order}</div>
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdQuantity = `<div class="td_quantity text-center">${tnhFormatNumber(item.total_quantity_item)}</div>`;
        tdCapacityLevel = `<div class="td-capacity_level"><input type="text" name="quota_productivity[${counter}]" class="quota_productivity number-format form-control" value=""></div>`;
        tdCategoryOvertime = `<div class="td-category_overtime"><input type="text" name="category_overtime[${counter}]" class="category_overtime form-control" value=""></div>`;
        tdQuantityOvertime = `<div class="td-quantity_overtime"><input type="text" name="quantity_overtime[${counter}]" class="quantity_overtime number-format form-control" value=""></div>`;
        tdCoefficient = `<div class="td-coefficient"><input type="text" name="coefficient[${counter}]" class="coefficient number-format form-control" value=""></div>`;
        tdSalary = `<div class="td-salary"><input type="text" name="salary[${counter}]" class="salary number-format form-control" value=""></div>`;
        tdStaff = `<div>
            <select class="staff_id" id="staff_id_${counter}" name="staff_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Nhân viên') ?>">
                ${optionStaff()}
            </select>
        </div>`;
        tdDateOvertime = `<div class="td-date_overtime"><input type="text" name="date_overtime[${counter}]" required class="date_overtime datepicker form-control" autocomplete="off" value=""></div>`;
        tdHourStart = `<div class="td-hour_start"><input type="time" name="hour_start[${counter}]" required class="hour_start form-control" autocomplete="off" value=""></div>`;
        tdHourEnd = `<div class="td-hour_end"><input type="time" name="hour_end[${counter}]" required class="hour_end form-control" autocomplete="off" value=""></div>`;
        tdResult = `<div>
            <select class="result_id" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        tdStandard = `<div class="td-standard"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr class="tr_${item.order_id}">
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td>${tdQuantity}</td>
            <td style="width: 150px">${tdCapacityLevel}</td>
            <td style="width: 150px">${tdCategoryOvertime}</td>
            <td style="width: 150px">${tdQuantityOvertime}</td>
            <td style="width: 150px">${tdCoefficient}</td>
            <td style="width: 150px">${tdSalary}</td>
            <td style="width: 150px">${tdStaff}</td>
            <td style="width: 120px">${tdResult}</td>
            <td style="width: 120px">${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        $(`#result_id_${counter}`).attr('required','required');
        $(`#staff_id_${counter}`).select2();
        $(`#staff_id_${counter}`).attr('required','required');
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
            pod_id = $(element).find('.pod_id').val();
            if (arrId.includes(pod_id) == false){
                arrId.push(pod_id);
            }
        }
    }

    appValidateForm($('#request_overtime'), {
        reference_no: 'required',
        date: 'required',
        po_id: 'required',
        branch_id: 'required',
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
                    window.location.href = site.base_url+'admin/request_overtime';
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