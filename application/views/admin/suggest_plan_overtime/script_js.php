<script>
    $("#branch_id").select2();
    $("#staff_plan").select2();
    ajaxSelectParams('#po_id', 'admin/suggest_plan_overtime/searchPo', $("#po_id").val(), true, true);
    ajaxSelectCallBack('#order_id', 'admin/suggest_plan_overtime/searchOrders', $("#order_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_plan_overtime/searchProductByOrders', 0);

    $("#po_id").change(function (){
        $("#order_id").select2("val","");
        $("#tb-purchases").find('tbody').html('');
    });

    $("#order_id").change(function (){
        $("#tb-purchases").find('tbody').html('');
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
         <input type="hidden" name="pod_id[${counter}]" class="pod_id" value="${item.id}">
         <input type="hidden" name="quantity[${counter}]" class="quantity" value="${item.total_quantity_item}">
        ${item.code_item}
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdMode = `<div class="td_mode">${item.mode}</div>`;
        tdUnit = `<div class="td_unit">${item.unit_name}</div>`;
        tdQuantity = `<div class="td_quantity text-center">${tnhFormatNumber(item.total_quantity_item)}</div>`;
        tdStage = `<div>
            <select class="stage_id" id="stage_id_${counter}" name="stage_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Nhóm công đoạn') ?>">
                ${optionStage()}
            </select>
        </div>`;
        tdCapacityLevel = `<div class="td-capacity_level"><input type="text" name="capacity_level[${counter}]" class="capacity_level form-control" value=""></div>`;
        tdCategoryOvertime = `<div class="td-category_overtime"><input type="text" name="category_overtime[${counter}]" class="category_overtime form-control" value=""></div>`;
        tdDetail = `<div class="td-detail"><input type="text" name="detail[${counter}]" class="detail form-control" value=""></div>`;
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
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td>${tdMode}</td>
            <td>${tdUnit}</td>
            <td>${tdQuantity}</td>
            <td style="width: 150px">${tdStage}</td>
            <td style="width: 150px">${tdCapacityLevel}</td>
            <td style="width: 150px">${tdCategoryOvertime}</td>
            <td style="width: 150px">${tdDetail}</td>
            <td style="width: 150px">${tdStaff}</td>
            <td style="width: 150px">${tdDateOvertime}</td>
            <td>${tdHourStart}</td>
            <td>${tdHourEnd}</td>
            <td style="width: 120px">${tdResult}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        // $(`#result_id_${counter}`).attr('required','required');
        $(`#stage_id_${counter}`).select2();
        $(`#stage_id_${counter}`).attr('required','required');
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

    appValidateForm($('#suggest_plan_overtime'), {
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
                    window.location.href = site.base_url+'admin/suggest_plan_overtime';
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