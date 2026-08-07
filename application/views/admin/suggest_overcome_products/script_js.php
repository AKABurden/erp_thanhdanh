<script>
    ajaxSelectParams('#pod_id', 'admin/suggest_overcome_products/searchPod', $("#pod_id").val(), true, true);
    $("#branch_id").select2();
    $("#employee_id").select2();
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_overcome_products/searchProductsSelect2', 0);

    $("#pod_id").change(function (){
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
                            pod_id: $("#pod_id").val(),
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
                            pod_id: $("#pod_id").val(),
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

    $("#items_search").change(function (){
        dtItems = $(this).select2('data');
        loadItem(dtItems)
        $(this).select2("val", "");
    })

    function loadItem(item = {}){
        tdStt = `<div class="stt"></div>`;
        tdCode = `<div class="code_item">
         <input type="hidden" name="counter[]" class="counter" value="${counter}">
         <input type="hidden" name="item_id[${counter}]" class="item_id" value="${item.id}">
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
        tdUnit = `<div class="unit_item">${item.unit_name}</div>`;
        tdQuantity = `<div class="td-quantity"><input type="text" name="quantity[${counter}]" class="quantity form-control number-format" value="1"></div>`;
        tdQuantityKien = `<div class="td-quantity-kien"><input type="text" name="quantity_kien[${counter}]" class="quantity_kien number-format form-control" value="1"></div>`;
        tdQuantityKg = `<div class="td-quantity-kg"><input type="text" name="quantity_kg[${counter}]" class="quantity_kg form-control number-format" value="1"></div>`;
        tdStandard= `<div class="standard_item"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td>${tdCode}</td>
            <td>${tdImages}</td>
            <td>${tdName}</td>
            <td>${tdUnit}</td>
            <td>${tdQuantity}</td>
            <td>${tdQuantityKien}</td>
            <td>${tdQuantityKg}</td>
            <td>${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
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
        }
    }

    appValidateForm($('#suggest_overcome_product'), {
        reference_no: 'required',
        date: 'required',
        branch_id: 'required',
        date_import: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_overcome_products';
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