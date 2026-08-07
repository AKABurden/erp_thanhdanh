<script>
    ajaxSelectParams('#production_report_id', 'admin/suggest_repalce/searchProductionReports', $("#production_report_id").val(), true, true);
    $("#branch_id").select2();
    $("#staff_suggest").select2();
    $("#staff_agree").select2();
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_rating_system/SearchSystems', 0,1);

    if (edit == 1){
        for (i = 0; i < counter; i++) {
            $(`#result_${i}`).select2();
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

    $("#items_search").change(function (){
        dtItems = $(this).select2('data');
        if (arrId.includes(dtItems.id)){
            alert_float('danger','Mã hệ thống đã tồn tại!');
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
         <input type="hidden" name="system_id[${counter}]" class="system_id" value="${item.id}">
        ${item.code}
        </div>`;
        tdName = `<div class="name_item">${item.name}</div>`;
        tdetail = `<div class="td_detail">
            <textarea class="detail form-control" name="detail[${counter}]" cols="2" rows="2"></textarea>
        </div>`;
        tdResult = `<div>
            <select class="result" id="result_${counter}" name="result[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        tdStandard= `<div class="standard_item"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCode}</td>
            <td>${tdName}</td>
            <td>${tdetail}</td>
            <td>${tdResult}</td>
            <td>${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#result_${counter}`).select2();
        $(`#result_${counter}`).attr('required','required');
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
            system_id = $(element).find('.system_id').val();
            if (arrId.includes(system_id) == false){
                arrId.push(system_id);
            }
        }
    }

    appValidateForm($('#suggest_rating_system'), {
        reference_no: 'required',
        date: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_rating_system';
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