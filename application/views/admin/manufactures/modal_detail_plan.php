<div class="modal-dialog modal-lg" style="width: 85%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Tạo lập kế hoạch NVL') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <div class="mbot10"><span class="label btn-success"><?= lang('orders') ?></span></div>
                        <table id="table-view-items" class="table dataTable table-view-items" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <div class="mass_select_all_wrap text-center">
                                            <input type="checkbox" id="mass_select_all_orders" onclick="tickAllDetailOrders(this)">
                                            <label for="mass_select_all_orders"></label>
                                        </div>
                                    </th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_code') ?></th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_name') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_sample_cover_code') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('dt_date_delivery') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('Ngày đơn hàng') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_type_orders') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('customers') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_orders') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_quantity_manufactures') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <div class="mbot10"><span class="label btn-primary"><?= lang('business_plan') ?></span></div>
                        <table id="table-view-items-business" class="table dataTable table-view-items-business" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <div class="mass_select_all_wrap text-center">
                                            <input type="checkbox" id="mass_select_all_plan" onclick="tickAllDetailBusinessPlan(this)">
                                            <label for="mass_select_all_plan"></label>
                                        </div>
                                    </th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_code') ?></th>
                                    <th class="text-center" style="width: 120px;"><?= lang('dt_product_name') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('tnh_sample_cover_code') ?></th>
                                    <th class="text-center" style="width: 100px;"><?= lang('dt_date_delivery') ?></th>
                                    <th class="text-center"><?= lang('Ngày kế hoạch TP') ?></th>
                                    <th class="text-center"><?= lang('tnh_business_plan') ?></th>
                                    <th class="text-center"><?= lang('tnh_plan_name') ?></th>
                                    <th class="text-center"><?= lang('quantity') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="bold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center"><?= lang('tnh_grand_total') ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="convertManufacturesDetail()" class="btn btn-default btn-warning"><span class="fa fa-exchange"></span> <?= lang('Lập kế hoạch NVL') ?></button>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTableModal = '';
    var fnserverparamsModal = {
        start_date_search: "<?= $this->input->post('start_date_search_manufactures') ?>",
        end_date_search: "<?= $this->input->post('end_date_search_manufactures') ?>",
        customer_search: "<?= $this->input->post('customer_search_manufactures') ?>",
        type_orders_search: "<?= $this->input->post('type_orders_search_manufactures') ?>",
        search_date_order: "<?= $this->input->post('search_date_order_manufactures') ?>",
        product_id: "<?= implode(',', $this->input->post('product_id')) ?>",
    };

    var arrObjectDetail = {};
    function changeCheckboxObject(_this) {
        isCheck = $(_this).prop('checked');
        objectVal = $(_this).val();
        if (isCheck) {
            arrObjectDetail[objectVal] = 1;
        } else {
            arrObjectDetail[objectVal] = 0;
        }
    }

    function tickAllDetailOrders(_this) {
        isCheck = $(_this).prop('checked');
        $.each($('#table-view-items tbody tr'), function (index, value) { 
            elC = $(value).find('.object_id');
            elC.prop('checked', isCheck);
            changeCheckboxObject(elC);
        });
    }

    function tickAllDetailBusinessPlan(_this) {
        isCheck = $(_this).prop('checked');
        $.each($('#table-view-items-business tbody tr'), function (index, value) { 
            elC = $(value).find('.object_id');
            elC.prop('checked', isCheck);
            changeCheckboxObject(elC);
        });
    }

    function convertManufacturesDetail() {
        // console.log(arrObjectDetail);
        arrObjecOrderstId = '';
        arrObjecBusinesstId = '';
        $.each(arrObjectDetail, function (index, value) { 
            if (value == 1) {
                arrObject = index.split('__');
                if (arrObject[0] == "orders") {
                    arrObjecOrderstId += arrObject[1] + ',';
                } else if (arrObject[0] == "business_plan") {
                    arrObjecBusinesstId += arrObject[1] + ',';
                }
            }
        });

        if (arrObjecOrderstId) {
            arrObjecOrderstId = arrObjecOrderstId.substring(0, arrObjecOrderstId.length - 1);
        }

        if (arrObjecBusinesstId) {
            arrObjecBusinesstId = arrObjecBusinesstId.substring(0, arrObjecBusinesstId.length - 1);
        }

        if (!arrObjecOrderstId && !arrObjecBusinesstId) {
            alert_float('danger', 'Vui lòng chọn mặt hàng để chuyển qua kế hoạch');
            return;
        }
         
        var url = site.base_url + 'admin/manufactures/add_productions_plan';
        var inputs = '';
        inputs += `<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">`;
        inputs += `<input type="hidden" name="p_id" value="<?= implode(',', $this->input->post('product_id')) ?>">`;
        inputs += `<input type="hidden" name="arrObjecOrderstId" value="${arrObjecOrderstId}">`;
        inputs += `<input type="hidden" name="arrObjecBusinesstId" value="${arrObjecBusinesstId}">`;
        inputs += `<input type="hidden" name="cs_id" value="${customer_search}">`;
        inputs += `<input type="hidden" name="start_date" value="${start_date_search}">`;
        inputs += `<input type="hidden" name="end_date" value="${end_date_search}">`;
        $("#show-form-detail-1").html('<form target="_blank" action="' + url + '" method="post" id="poster-detail-1">' + inputs + '</form>');
        $("#poster-detail-1").submit();
    }

    $(document).ready(function() {
        oTableModal = tnhInitDataTable('#table-view-items', '', {
            'order': [
                [1, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getShowDetailManufactures') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsModal) {
                        d[key] = fnserverparamsModal[key];
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-items tfoot tr td:nth-child(10)').html('<div class="text-center">'+tnhFormatNumber(json.totalQuantity)+'</div>');
                    return json.aaData;
                }
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                cur_object_id = $(nRow).find('input[name="object_id[]"]').val();
                if (typeof arrObjectDetail[cur_object_id] !== 'undefined' && arrObjectDetail[cur_object_id] === 1) {
                    $(nRow).find('input[name="object_id[]"]').prop('checked', true);
                }
                return nRow;
            },
        });

        oTableModalBusinessPlan = tnhInitDataTable('#table-view-items-business', '', {
            'order': [
                [1, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/manufactures_temp/getShowDetailManufacturesBusinessPlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsModal) {
                        d[key] = fnserverparamsModal[key];
                    }
                },
                "dataSrc": function(json) {
                    $('#table-view-items-business tfoot tr td:nth-child(9)').html('<div class="text-center">'+tnhFormatNumber(json.totalQuantity)+'</div>');
                    return json.aaData;
                }
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                cur_object_id = $(nRow).find('input[name="object_id[]"]').val();
                if (typeof arrObjectDetail[cur_object_id] !== 'undefined' && arrObjectDetail[cur_object_id] === 1) {
                    $(nRow).find('input[name="object_id[]"]').prop('checked', true);
                }
                return nRow;
            },
        });
    });
</script>