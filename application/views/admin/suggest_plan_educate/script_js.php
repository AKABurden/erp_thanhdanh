<script>
    $("#branch_id").select2();
    $("#staff_id").select2();
    $("#category_plan").select2();
    $("#staff_evaluate").select2();
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_plan_evaluate/searchEvaluates', 0,'educate');

    if (edit == 1){
        for (i = 0; i < counter; i++) {
            $(`#result_id_${i}`).select2();
            $(`#staff_educate_${i}`).select2();
            $(`#tax_id_${i}`).select2();
        }
    }

    function optionTax(selected_id = 0){
        option = `<option></option>`;
        $.each(taxs, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= '<option '+selected+' data-rate="'+el.taxrate+'" value="'+el.id+'">'+el.name+'</option>';
        });
        return option;
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
            option+= '<option '+selected+' value="'+el.staffid+'">'+el.fullname+'</option>';
        });
        return option;
    }

    $("#items_search").change(function (){
        dtItems = $(this).select2('data');
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function loadItem(item = {}){
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="evaluate_id[${counter}]" class="evaluate_id" value="${item.id}">
        ${item.code}
        </div>`;
        tdName = `<div class="name_item">${item.name}</div>`;
        tdPositionEducate = `<div class="td-position_educate"><input type="text" name="position_educate[${counter}]" class="position_educate form-control" value=""></div>`;
        tdDetail = `<div class="td-detail"><input type="text" name="detail[${counter}]" class="detail form-control" value=""></div>`;
        tdQuantity = `<div class="td-quantity"><input type="text" name="quantity[${counter}]" class="quantity number-format form-control" value=""></div>`;
        tdStaff = `<div>
            <select class="staff_educate" id="staff_educate_${counter}" name="staff_educate[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Nhân viên') ?>">
                ${optionStaff()}
            </select>
        </div>`;
        tdUnitEducate = `<div class="td-unit_educate"><input type="text" name="unit_educate[${counter}]" class="unit_educate form-control" value=""></div>`;
        tdCostMoney = `<div class="td-cost_money"><input type="text" name="cost_money[${counter}]" class="cost_money number-format form-control" value=""></div>`;
        tdTax = `<div>
            <select class="tax_id" id="tax_id_${counter}" name="tax_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Thuế') ?>">
                ${optionTax()}
            </select>
        </div>`;
        tdTotal = `<div class="td-total text-right"></div>`;
        tdResult = `<div>
            <select class="result_id" id="result_id_${counter}" name="result_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        tdStandard= `<div class="standard_item" style="width: 100%"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCode}</td>
            <td>${tdName}</td>
            <td>${tdPositionEducate}</td>
            <td style="width: 200px">${tdDetail}</td>
            <td>${tdQuantity}</td>
            <td>${tdStaff}</td>
            <td>${tdUnitEducate}</td>
            <td>${tdCostMoney}</td>
            <td>${tdTax}</td>
            <td>${tdTotal}</td>
            <td>${tdResult}</td>
            <td style="width: 150px">${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#result_id_${counter}`).select2();
        $(`#result_id_${counter}`).attr('required','required');

        $(`#staff_educate_${counter}`).select2();
        $(`#staff_educate_${counter}`).attr('required','required');

        $(`#tax_id_${counter}`).select2();
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
        for (ii = 0; ii < n; ii++)
        {
            stt++;
            element = $(tb)[ii];
            $(element).find('.stt').html(stt);
            tax_rate_item = intVal($(element).find('select.tax_id').select2().find(":selected").data('rate'));
            cost_money = intVal($(element).find('.cost_money').val());
            total = cost_money + (cost_money * tax_rate_item / 100);
            $(element).find('.td-total').html(tnhFormatMoney(total));
        }
    }
    $(document).on('change', '.cost_money,.tax_id', function(event) {
        getTotal();
    });
    appValidateForm($('#suggest_plan_educate'), {
        reference_no: 'required',
        date: 'required',
        staff_id: 'required',
        branch_id: 'required',
        category_plan: 'required',
        staff_evaluate: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_plan_educate';
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