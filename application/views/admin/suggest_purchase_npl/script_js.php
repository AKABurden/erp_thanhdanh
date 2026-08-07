<script>
    $("#supplier_id").select2();
    ajaxSelectCallBack('#purchase_request_material_id', 'admin/suggest_purchase_npl/searchPurchaseRequestMaterial', $('#purchase_request_material_id').val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_purchase_npl/searchItemsByRequestMaterial', 0);

    $("#purchase_request_material_id").change(function (){
        dataSelect = $(this).select2('data');
        $("#supplier_id").select2("val",dataSelect.supplier_id);
    })

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
                            supplier_id: $("#supplier_id").val(),
                            purchase_request_material_id: $("#purchase_request_material_id").val(),
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
                            supplier_id: $("#supplier_id").val(),
                            purchase_request_material_id: $("#purchase_request_material_id").val(),
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
        }
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
         <input type="hidden" name="purchase_request_material_item_id[${counter}]" class="purchase_request_material_item_id" value="${item.id}">
        ${item.code_item}
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdQuantity = `<div class="td-quantity"><input type="text" name="quantity[${counter}]" onchange="getTotal()" class="quantity form-control" readonly value="${tnhFormatNumber(item.quabtity_purchase)}"></div>`;
        tdQuantityImport = `<div class="td-quantity-import"><input type="text" name="quantity_import[${counter}]" onchange="getTotal()" class="quantity_import form-control number-format"  value="0"></div>`;
        tdDetail = `<div class="td-detail"><textarea cols="2" rows="3" name="detail[${counter}]" class="detail form-control"></textarea>`;
        tdStandard = `<div class="td-standard"><input type="text" name="standard[${counter}]" class="standard form-control"  value=""></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr class='tr_${item.order_id}'>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td style="width: 150px">${tdQuantity}</td>
            <td style="width: 150px">${tdQuantityImport}</td>
            <td style="width: 150px">${tdDetail}</td>
            <td style="width: 150px">${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
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
            purchase_request_material_item_id = $(element).find('.purchase_request_material_item_id').val();
            if (arrId.includes(purchase_request_material_item_id) == false){
                arrId.push(purchase_request_material_item_id);
            }
        }
    }

    appValidateForm($('#suggest_purchase_npl'), {
        reference_no: 'required',
        date: 'required',
        supplier_id: 'required',
        purchase_request_material_id: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_purchase_npl';
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