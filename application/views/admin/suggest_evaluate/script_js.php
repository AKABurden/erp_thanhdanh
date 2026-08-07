<script>
    $("#branch_id").select2();
    $("#staff_id").select2();
    $("#type_evaluate_id").select2();
    $("#category_plan").select2();
    $("#staff_evaluate").select2();
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_plan_evaluate/searchEvaluates', 0,'evaluate');
    $("#type_evaluate_id").change(function (){
        ajaxSelectParamsCallback('#object_id', 'admin/suggest_evaluate/searchObject', $('#object_id').val(), {'type_evaluate_id': $(this).val()}, true);

        $.ajax({
            url: site.base_url+'admin/suggest_evaluate/changeEvaluate',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token_name: hash,
                type_evaluate_id: $(this).val(),
            },
        })
            .done(function(data) {
                $("#tb-purchases").find('tbody').html(data.html);
            })
            .fail(function() {
                console.log("error");
            });
    })

    if (edit == 1){
        ajaxSelectParamsCallback('#object_id', 'admin/suggest_evaluate/searchObject', $('#object_id').val(), {'type_evaluate_id': $("#type_evaluate_id").val()}, true);
        for (i = 0; i < counter; i++) {
            $(`#result_${i}`).select2();
        }
    } else {
        <?php if (!empty($type_evaluate_id)) {?>
        $("#type_evaluate_id").trigger('change');
        <?php } ?>
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
        tdContent = `<div class="td-content" style="width: 200px"><textarea name="content[${counter}]" class="content form-control" cols="2" rows="3"></textarea></div>`;
        tdActualSituation = `<div class="td-actual_situation"><input type="text" name="actual_situation[${counter}]" class="actual_situation form-control" value=""></div></div>`;
        tdResult = `<div>
            <select class="result" id="result_${counter}" name="result[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Kết quả') ?>">
                ${optionResult()}
            </select>
        </div>`;
        tdStandard= `<div class="standard_item" style="width: 100%"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCode}</td>
            <td>${tdName}</td>
            <td style="width: 200px">${tdContent}</td>
            <td>${tdActualSituation}</td>
            <td style="width: 150px">${tdStandard}</td>
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
        id = $(el).closest('tr').attr('data-id');
        $(el).closest('tr').remove();
        $(`.child_${id}`).remove();
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
        }
    }

    appValidateForm($('#suggest_evaluate'), {
        reference_no: 'required',
        date: 'required',
        branch_id: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_evaluate?type=<?= $this->type ?>';
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