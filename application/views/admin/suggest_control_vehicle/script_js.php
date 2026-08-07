<script>
    $("#branch_id").select2();
    ajaxSelectParams('#customer_id', 'admin/clients/searchOnlyCustomers', $("#customer_id").val(), true, true);
    ajaxSelectCallBack($('#items_search'), 'admin/suggest_control_vehicle/searchItemDeliveryByCustomer', 0);

    $("#customer_id").change(function (){
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
                            customer_id: $("#customer_id").val(),
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
                            customer_id: $("#customer_id").val(),
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
            $("#transport_unit_id_"+i).select2();
        }
    }

    function optionTransport(selected_id = 0){
        option = `<option></option>`;
        $.each(dtTrans, function(index, el) {
            selected = selected_id == el.id ? 'selected' : '';
            option+= '<option '+selected+' value="'+el.id+'">'+el.company+'</option>';
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
         <input type="hidden" name="delivery_id[${counter}]" class="delivery_id" value="${item.delivery_id}">
         <input type="hidden" name="delivery_item_id[${counter}]" class="delivery_item_id" value="${item.id}">
         <input type="hidden" name="item_id[${counter}]" class="item_id" value="${item.item_id}">
        ${item.code_item}
        <div style="color: green">${item.reference_no}</div>
        </div>`;
        tdName = `<div class="name_item">${item.name_item}</div>`;
        tdConKien = `<div class="">${item.quantity_sheet_bale}</div>`;
        tdKyKien = `<div class="td_ky_kien">
            <input type="text" name="number_ky[${counter}]" class="number_ky form-control number-format" value="">
        </div>`;
        tdTotalKien = `<div class="td_total_kienn">
            <input type="text" name="total_kien[${counter}]" class="total_kien form-control number-format" value="">
        </div>`;
        tdTotalKy = `<div class="td_total_ky">
            <input type="text" name="total_ky[${counter}]" class="total_ky form-control number-format" value="">
        </div>`;
        tdQuateVehicle = `<div class="td_quota_vehicle">
            <input type="text" name="quota_vehicle[${counter}]" class="quota_vehicle form-control number-format" value="">
        </div>`;
        tdVehicle = `<div class="td_vehicle">
            <input type="text" name="vehicle[${counter}]" class="vehicle form-control" value="">
        </div>`;
        tdRoute = `<div class="td_route">
            <input type="text" name="route[${counter}]" class="route form-control" value="">
        </div>`;
        tdTransportUnit = `<div>
            <select class="transport_unit_id" id="transport_unit_id_${counter}" name="transport_unit_id[${counter}]" style="width: 100%;"  data-placeholder="<?= lang('Phương tiện vận chuyển') ?>">
                ${optionTransport()}
            </select>
        </div>`;
        tdPrice = `<div class="td-price"><input type="text" onchange="getTotal()" name="price[${counter}]" class="price form-control number-format" value=""></div>`;
        tdAmount = `<div class="td-amount text-right"></div>`;
        tdStandard = `<div class="td-standard"><input type="text" name="standard[${counter}]" class="standard form-control" value=""></div>`;
        tdActions = `<a onclick="removeRow(this)" href="javascript:void(0)" class="fa fa-remove remove-row"></a>`;

        trItem = `<tr>
            <td class="text-center">${tdStt}</td>
            <td  style="width: 150px">${tdCode}</td>
            <td  style="width: 150px">${tdName}</td>
            <td class="text-center">${tdConKien}</td>
            <td style="width: 100px">${tdKyKien}</td>
            <td style="width: 100px">${tdTotalKien}</td>
            <td style="width: 150px">${tdTotalKy}</td>
            <td style="width: 150px">${tdQuateVehicle}</td>
            <td style="width: 150px">${tdVehicle}</td>
            <td style="width: 150px">${tdRoute}</td>
            <td style="width: 150px">${tdTransportUnit}</td>
            <td style="width: 150px">${tdPrice}</td>
            <td>${tdAmount}</td>
            <td>${tdStandard}</td>
            <td class="td-actions text-center">${tdActions}</td>
        </tr>`;

        $("#tb-purchases").find('tbody').append(trItem);
        $(`#transport_unit_id_${counter}`).select2();
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
            price = intVal($(element).find('.price').val());
            delivery_item_id = $(element).find('.delivery_item_id').val();
            if (arrId.includes(delivery_item_id) == false){
                arrId.push(delivery_item_id);
            }
            amount = price;
            $(element).find('.td-amount').html(tnhFormatMoney(amount));
        }
    }

    appValidateForm($('#suggest_control_vehicle'), {
        reference_no: 'required',
        date: 'required',
        customer_id: 'required',
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
                    window.location.href = site.base_url+'admin/suggest_control_vehicle';
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