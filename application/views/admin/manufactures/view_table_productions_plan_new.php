<style>
    #table-plan tr th:nth-child(10),
    #table-plan tr td:nth-child(10) {
        display: none;
    }

    #table-plan tr th:nth-child(7),
    #table-plan tr td:nth-child(7) {
        display: none;
    }
</style>

<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="table-responsive" style="margin-bottom: 25px;">
    <table class="table table-bordered table-hover dont-responsive-table">
        <tbody>
            <tr class="success">
                <td style="width: 180px;"><?= lang('tnh_sales_orders') ?></td>
                <td>
                    <div style="word-wrap: break-word;"><?= $sales_orders ?></div>
                </td>
            </tr>
            <tr class="danger">
                <td><?= lang('tnh_business_plan') ?></td>
                <td>
                    <div style="word-wrap: break-word;"><?= $business_plan ?></div>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="" id="condition_p_id_post" class="form-control" value="<?= $p_id_post ?>">
    <input type="hidden" name="" id="condition_arrObjecOrderstId_post" class="form-control" value="<?= $arrObjecOrderstId_post ?>">
    <input type="hidden" name="" id="condition_arrObjecBusinesstId_post" class="form-control" value="<?= $arrObjecBusinesstId_post ?>">
    <input type="hidden" name="" id="condition_safe_inventory" class="form-control" value="<?= $safe_inventory ?>">
    <input type="hidden" name="" id="condition_options1" class="form-control" value="<?= $options1 ?>">
    <input type="hidden" name="" id="condition_options2" class="form-control" value="<?= $options2 ?>">
    <input type="hidden" name="" id="condition_planning_cycle" class="form-control" value='<?= $planning_cycle ?>'>
    <input type="hidden" name="" id="condition_orders" class="form-control" value='<?= $orders ?>'>
    <input type="hidden" name="" id="condition_business_plans" class="form-control" value='<?= $business_plans ?>'>
    <input type="hidden" name="" id="condition_orders_items" class="form-control" value='<?= $orders_items ?>'>
    <input type="hidden" name="" id="condition_business_plans_items" class="form-control" value='<?= $business_plans_items ?>'>
    <input type="hidden" name="" id="condition_remove_item" class="form-control condition_remove_item" value="">


    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#home-orders" aria-controls="home-orders" role="tab" data-toggle="tab"><?= lang('Thành phẩm đơn hàng') ?></a>
            </li>
            <li role="presentation">
                <a href="#tab-preventive" aria-controls="tab-preventive" role="tab" data-toggle="tab"><?= lang('Thành phẩm dự phòng') ?></a>
            </li>
            <li role="presentation" class="">
                <a href="#tab-bom" aria-controls="tab-bom" role="tab" data-toggle="tab"><?= lang('Tổng hợp NPL') ?></a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home-orders">
                <table id="table-plan" class="dt-tnh table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center"><?= lang('tnh_numbers') ?></th>
                            <th class="text-center"><?= lang('tnh_sales_orders') ?>/<?= lang('tnh_business_plan') ?></th>
                            <th class="text-center"><?= lang('tnh_products') ?></th>
                            <th class="text-center"><?= lang('BOM') ?></th>
                            <th class="text-center"><?= lang('stages') ?></th>
                            <th class="text-center"><?= lang('tnh_conversion_unit') ?></th>
                            <th class="text-center"><?= lang('tnh_safe_inventory') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_warehouses') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_need') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_reserve') ?></th>
                            <th class="text-center"><?= lang('tnh_type_object') ?></th>
                            <th class="text-center"><?= lang('tnh_item_object_id') ?></th>
                            <th class="text-center"><?= lang('tnh_expected_delivery') ?></th>
                            <th class="text-center"><?= lang('actions') ?></th>
                            <?= '' //$th 
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-preventive">
                <table id="table-plan-preventive" class="dt-tnh table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                            <th class="text-center"><?= lang('tnh_products') ?></th>
                            <th class="text-center"><?= lang('BOM') ?></th>
                            <th class="text-center"><?= lang('stages') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('tnh_conversion_unit') ?></th>
                            <th class="text-center" style="width: 150px;"><?= lang('tnh_quantity_reserve') ?></th>
                            <th class="text-center" style="width: 150px;"><?= lang('Số lượng tối đa') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-bom">
                <table id="tb-bom" class="table table-hover table-bordered dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 150px;"><?= lang('tnh_materials_code') ?></th>
                            <th class="text-center" style="width: 150px;"><?= lang('tnh_materials_name') ?></th>
                            <th class="text-center" style="width: 100px;"><?= lang('type') ?></th>
                            <th class="text-center"><?= lang('unit_bom') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_use') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_compensation') ?></th>
                            <th class="text-center"><?= lang('Tổng số lượng (ĐV kho)') ?></th>
                            <th class="text-center"><?= lang('tnh_quantity_inventory') ?></th>
                            <th class="text-center"><?= lang('status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable = '';
    var arrIdReserve = [];
    var arrInfoReserve = [];
    var arrIdQuantity = [];
    var arrInfoQuantity = [];
    var cTr = '';
    var tempProductId = '';

    var arrIdPreventive = [];
    var arrInfoPreventive= [];

    var fnserverparams = {
        'condition_safe_inventory': '#condition_safe_inventory',
        'condition_planning_cycle': '#condition_planning_cycle',
        'condition_options1': '#condition_options1',
        'condition_options2': '#condition_options2',
        'condition_orders': '#condition_orders',
        'condition_business_plans': '#condition_business_plans',
        'condition_orders_items': '#condition_orders_items',
        'condition_business_plans_items': '#condition_business_plans_items',
        'condition_remove_item': '#condition_remove_item',
        'condition_p_id_post': '#condition_p_id_post',
        'condition_arrObjecOrderstId_post': '#condition_arrObjecOrderstId_post',
        'condition_arrObjecBusinesstId_post': '#condition_arrObjecBusinesstId_post',
    };
    var arr = [];

    function format(d) {
        versions = '';
        if (typeof d[11] != 'undefined') {
            return d[11];
        }
        return '';
    }

    function totalProductionsPlanItems() {
        tbPlanItems = '#table-plan tbody tr:not("[class^=not-tr]")';
        var nPlanItems = $(tbPlanItems).length;
        for (ii = 0; ii < nPlanItems; ii++) {
            element = $(tbPlanItems)[ii];
            type_object = $(element).find('.type_object').val();
            item_object_id = $(element).find('.item_object_id').val();
            str_object = type_object + '___' + item_object_id;

            if (typeof arrIdReserve[str_object] !== 'undefined') {
                $(element).find('.quantity_reserve').val(tnhFormatNumber(arrIdReserve[str_object]));
            }

            if (typeof arrIdQuantity[str_object] !== 'undefined') {
                $(element).find('.quantity').val(tnhFormatNumber(arrIdQuantity[str_object]));
            }
        }
    }

    function changeReserve(_this) {
        trItems = $(_this).closest('tr');
        type_object = trItems.find('.type_object').val();
        item_object_id = trItems.find('.item_object_id').val();
        str_object = type_object + '___' + item_object_id;

        quantity_reserve = intVal(trItems.find('.quantity_reserve').val());
        arrIdReserve[str_object] = quantity_reserve;
    }

    function changeQuantity(_this) {
        trItems = $(_this).closest('tr');
        type_object = trItems.find('.type_object').val();
        item_object_id = trItems.find('.item_object_id').val();
        str_object = type_object + '___' + item_object_id;

        quantity = intVal(trItems.find('.quantity').val());
        arrIdQuantity[str_object] = quantity;
        totalBOM();
    }

    function removeProductionsPlan(_this) {
        // oTable.row( $(this).parents('tr') ).remove().draw();
        trItems = $(_this).closest('tr');
        condition_remove_item = $('#condition_remove_item').val();
        type_object = trItems.find('.type_object').val();
        item_object_id = trItems.find('.item_object_id').val();
        str_object = type_object + '___' + item_object_id;
        if (condition_remove_item) {
            condition_remove_item = condition_remove_item + '|||' + str_object;
        } else {
            condition_remove_item = str_object;
        }
        $('#condition_remove_item').val(condition_remove_item);
        oTable.draw();
        // totalProductionsPlanItems();
    }

    function changeQuantityPreventive(_this) {
        trItems = $(_this).closest('tr');
        product_id_preventive = trItems.find('.product_id_preventive').val();
        str_product_id_preventive = 'products__'+product_id_preventive;
        quantity_preventive = intVal($(_this).val());
        arrIdPreventive[str_product_id_preventive] = quantity_preventive;

        var quantity_max = trItems.find('.quantity_max').val();
        var txtErrorQuantity = '';
        if (quantity_preventive > quantity_max) {
            txtErrorQuantity = 'Vượt quá SL tối đa';
        }
        trItems.find('.show-errors').html(txtErrorQuantity);

        totalBOM();
    }

    function clickBom(_this, temp_product_id) {
        cTr = $(_this).closest('tr');
        versions = cTr.find('select.versions').val();

        dataPOST = {};
        dataPOST["<?= $this->security->get_csrf_token_name() ?>"] = "<?= $this->security->get_csrf_hash() ?>";
        dataPOST['product_id'] = temp_product_id;
        dataPOST['versions'] = versions;
        tempProductId = temp_product_id;

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures/clickBom',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                $.ajax({
                        url: response.link,
                        type: 'GET',
                        dataType: 'html',
                    })
                    .done(function(data) {
                        $('.modal-select2').select2('close');
                        $('#tnhModal').html(data);
                    })
                    .fail(function() {
                        console.log("error");
                    });
                $('#tnhModal').modal({
                    backdrop: 'static',
                    keyboard: true
                });
            }
        });
    }

    function loadProductsPreventive() {
        var aoDataPOST = {};
        aoDataPOST["<?= $this->security->get_csrf_token_name() ?>"] = "<?= $this->security->get_csrf_hash() ?>";

        for (var key in fnserverparams) {
            aoDataPOST[key] = $(fnserverparams[key]).val();
        }

        cs_product_id = [];
        $.each($('#table-plan tbody tr'), function (index, value) { 
            cur_product_id = $(value).find('.cs_product_id').val();
            cs_product_id.push(cur_product_id);
        });

        aoDataPOST['cs_product_id'] = cs_product_id;

        $.ajax({
            'dataType': 'json',
            'type': 'POST',
            'url': site.base_url+'admin/manufactures/loadProductsPreventive',
            'data': aoDataPOST,
            success: function (response) {
                trHtml = '';
                if (typeof response.arrProducts !== 'undefined' && response.arrProducts.length) {
                    var stt = 0;
                    $.each(response.arrProducts, function (index, value) { 
                        stt++;
                        tdNumber = `<td class="text-center td-numbers">${stt}</td>`;
                        tdProduct = `<td>
                            <input type="hidden" name="product_id_preventive[]" class="form-control product_id_preventive" value="${value.product_id}">
                            ${value.item_name}(${value.item_code})
                        </td>`;
                        tdBOM = `<td>
                            <select name="versions_perventive[]" onchange="totalBOM()" data-placeholder="BOM" class="versions_perventive" style="width: 100%;">
                                ${value.optionsVersions}
                            </select>
                        </td>`;
                        tdStages = `<td>
                            <select name="versions_stages_perventive[]" data-placeholder="Công đoạn" class="versions_stages_perventive" style="width: 100%;">
                                ${value.optionsVersionsStages}
                            </select>
                        </td>`;
                        tdUnits = `<td class="text-center">${value.unit_name}</td>`;
                        quantity_preventive = 0;
                        if (typeof arrIdPreventive['products__'+value.product_id] !== 'undefined') {
                            quantity_preventive = intVal(arrIdPreventive['products__'+value.product_id]);
                        }

                        var readonly = '';
                        if(value.is_no_stock == 1){
                            readonly = 'readonly';
                        }

                        var txtErrorQuantity = '';
                        quantity_max = value.quantity_max;
                        if (quantity_preventive > quantity_max) {
                            txtErrorQuantity = 'Vượt quá SL tối đa';
                        }

                        tdQuantity = `<td class="">
                            <input type="hidden" name="is_no_stock[]" class="form-control is_no_stock" value="${value.is_no_stock}">
                            <input type="text" ${readonly} name="quantity_preventive[]" onchange="changeQuantityPreventive(this)" class="form-control quantity_preventive number-format" value="${tnhFormatNumber(quantity_preventive)}">
                            <div class="show-errors text-danger">${txtErrorQuantity}</div>
                        </td>`;

                        tdQuantityMax = `<td class="text-center">
                            <input type="hidden" class="form-control quantity_max" value="${quantity_max}">
                            ${tnhFormatNumber(quantity_max)}
                        </td>`;

                        trHtml+= `<tr>
                            ${tdNumber}
                            ${tdProduct}
                            ${tdBOM}
                            ${tdStages}
                            ${tdUnits}
                            ${tdQuantity}
                            ${tdQuantityMax}
                        </tr>`;
                    });
                }
                $('#table-plan-preventive tbody').html(trHtml);
                $('.versions_perventive').select2();
                $('.versions_stages_perventive').select2();
            }
        });
    }

    function isStatusW() {
        tb = '#tb-bom tbody tr:not("[class^=not-tr]")';
        var n = $(tb).length;
        is_errors = 0;
        for (ii = 0; ii < n; ii++)
        {
            element = $(tb)[ii];

            b_standard_unit = intVal($(element).find('.standard_unit'));
            b_exchange_standard_unit = intVal($(element).find('.exchange_standard_unit').val());
            b_quantity_exchange = intVal($(element).find('.quantity_exchange').val());
            b_exchange_unit = intVal($(element).find('.exchange_unit').val());
            b_quantity = intVal($(element).find('.quantity').val());
            b_quantity_warehouse = intVal($(element).find('.quantity_warehouse').val());
            b_quantity_compensation = intVal($(element).find('.quantity_compensation').val());
            b_conversion_quantity_unit = intVal($(element).find('.conversion_quantity_unit').val());
            item_type = ($(element).find('.item_type').val());

            b_quantity_need = b_quantity + b_quantity_compensation;
            b_quantity_primary = b_quantity_need * b_quantity_exchange / b_exchange_unit;
            if (item_type == "materials") {
                b_quantity_convert_warehouse = tnhToFixedNumber(b_quantity_primary / b_exchange_standard_unit * b_exchange_unit, 0);
            } else {
                b_quantity_convert_warehouse = tnhToFixedNumber(b_quantity_primary * b_conversion_quantity_unit, 0);
            }


            b_strStatus = '';
            if (b_quantity_convert_warehouse > b_quantity_warehouse) {
                b_strStatus = '<span class="label label-danger"><?= lang('Chưa đủ kho') ?></span>';
                is_errors++;
            } else {
                b_strStatus = '<span class="label label-success"><?= lang('Đã đủ kho') ?></span>';
            }

            $(element).find('.quantity_convert_warehouse').html(tnhFormatNumber(b_quantity_convert_warehouse));
            $(element).find('.td-status').html(b_strStatus);
        }

        // if (is_errors) {
        //     $('a[aria-controls="tab-bom"]').css('color', 'red');
        // } else {
        //     $('a[aria-controls="tab-bom"]').css('color', 'unset');
        // }

        if (is_errors) {
            $('a[aria-controls="tab-bom"]').closest('li').css('background', '#ff00003b');
        } else {
            $('a[aria-controls="tab-bom"]').closest('li').css('background', 'unset');
            // $('a[aria-controls="tab-bom"]').css('color', 'unset');
        }
    }

    function totalBOM() {
        var form = $('#add-productions-plan'), formData = new FormData(), formParams = form.serializeArray();
        $.each(form.find('input[type="file"]'), function(i, tag) {
            $.each($(tag)[0].files, function(i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function(i, val) {
            formData.append(val.name, val.value);
        });

        var url = form.action;
        $.ajax({
            url : site.base_url+'admin/manufactures_temp/loadBOMPP',
            type : 'POST',
            dataType: 'JSON',
            cache : false,
            contentType : false,
            processData : false,
            data: formData,
        })
        .done(function(data) {
            $('#tb-bom tbody').html(data.trItems);
            isStatusW();
        })
        .fail(function() {
            // alert_float('danger', 'error');
        });
        return false;
    }

    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-plan', {
                'order': [
                    [2, 'asc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                'searching': false,
                'ordering': false,
                'paging': false,
                "info": false,
                // scrollY: true,
                // scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 4,
                // },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getShowTableProductionsPlanNew') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    // mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "targets": 0,
                        "name": 'number_records',
                        'width': '50px',
                        'className': 'text-center'
                    },
                    {
                        "targets": 1,
                        "name": 'reference_no',
                        'width': '120px'
                    },
                    {
                        "targets": 2,
                        "name": 'product_code',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            temp_str_object = row[10] + '___' + row[11];
                            return `
                                <select name="versions[${temp_str_object}]" onchange="totalBOM()" data-placeholder="BOM" class="versions" style="width: 100%;">
                                    ${data}
                                </select>
                                <div class="mtop5 text-danger show-erros-versions"></div>
                            `;
                        },
                        "targets": 3,
                        "name": 'versions',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            temp_str_object = row[10] + '___' + row[11];
                            return `
                                <select name="versions_stage[${temp_str_object}]" data-placeholder="Công đoạn" class="stages" style="width: 100%;">
                                    ${data}
                                </select>
                                <div class="mtop5 text-danger show-erros-stages"></div>
                            `;
                        },
                        "targets": 4,
                        "name": 'stages',
                        'width': '120px'
                    },
                    {
                        "targets": 5,
                        "name": 'unit',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 6,
                        "name": 'quantity_minimum',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>'
                        },
                        "targets": 7,
                        "name": 'quantity_warehouses',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            temp_str_object = row[10] + '___' + row[11];
                            tempQuantity = data;
                            if (typeof arrIdQuantity[temp_str_object] !== "undefined") {
                                tempQuantity = arrIdQuantity[temp_str_object];
                            }
                            return `
                                <input type="text" name="quantity[${temp_str_object}]" onkeyup="changeQuantity(this)" class="form-control quantity number-format" style="width: 100%;" value="${tnhFormatNumber(tempQuantity)}">
                            `;
                        },
                        "targets": 8,
                        "name": 'quantity',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            temp_str_object = row[10] + '___' + row[11];
                            tempQuantity = 0;
                            if (typeof arrIdReserve[temp_str_object] !== "undefined") {
                                tempQuantity = arrIdReserve[temp_str_object];
                            }

                            return `
                                <input type="hidden" name="type_object[${temp_str_object}]" class="form-control type_object" value="${row[10]}">
                                <input type="hidden" name="item_object_id[${temp_str_object}]" class="form-control item_object_id" value="${row[11]}">
                                <input type="text" name="quantity_reserve[${temp_str_object}]" onchange="changeReserve(this)" class="form-control quantity_reserve number-format" style="width: 100%;" value="${tnhFormatNumber(tempQuantity)}">
                            `;
                        },
                        "targets": 9,
                        "name": 'quantity_reserve',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return data;
                        },
                        "targets": 10,
                        "name": 'type_object',
                        'width': '80px',
                        'className': 'text-center',
                        'visible': false
                    },
                    {
                        "render": function(data) {
                            return data;
                        },
                        "targets": 11,
                        "name": 'item_object_id',
                        'width': '80px',
                        'className': 'text-center',
                        'visible': false
                    },
                    {
                        "render": function(data) {
                            return data;
                        },
                        "targets": 12,
                        "name": 'expected',
                        'width': '120px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return data;
                            // return `<div class="text-center">
                            //     <a href="javascript:void(0)" class="fa fa-pencil text-primary" title="Chỉnh sửa BOM" onclick="clickBom(this, ${row[0]})"></a>
                            //     <a href="javascript:void(0)" onclick="removeProductionsPlan(this)" class="fa fa-remove text-danger"></a>
                            // </div>`;
                        },
                        "targets": 13,
                        "name": 'actions',
                        'width': '50px',
                        'className': 'text-center'
                    },
                    <?= '' //$script 
                    ?>
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    // start = 6;
                    // if (!aaData) return;
                    // if (!aaData[0]) return;
                    // end = aaData[0].length;
                    // arr_id = [];
                    // arr = [];
                    // for (var i = 0; i < aaData.length; i++) {
                    //     for (j = start; j < end; j++)
                    //     {
                    //         if (typeof arr[j] == "undefined")
                    //         {
                    //             arr[j] = 0;
                    //         }
                    //         if (isNaN(parseFloat(aaData[aiDisplay[i]][j])))
                    //         {
                    //             total = 0;
                    //         } else {
                    //             total = parseFloat(aaData[aiDisplay[i]][j]);
                    //         }
                    //         arr[j] = arr[j] + total;
                    //     }
                    // }
                    // for (var i = start; i < arr.length; i++) {
                    //     if (arr[i] == 0) {
                    //         if (arr_id.indexOf(i) == -1) {
                    //             arr_id.push(i);
                    //         }
                    //     }
                    // }
                    // oTable.columns(arr_id).visible(false, false);
                }
            }
        );

        $('#table-plan').on('page.dt', function() {
            // console.log('page');
            // var info = oTable.page.info();
            // oTable.columns(arr_id).visible(true, true);
            // setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $('#table-plan_filter input').change(function() {
            // console.log('search');
            // oTable.columns(arr_id).visible(true, true);
            // setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $('select[name="table-plan_length"]').change(function(event) {
            // console.log('page-length');
            // oTable.columns(arr_id).visible(true, true);
            // setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $('.btn-dt-reload').click(function(event) {
            // oTable.draw();
        });

        $('#table-plan').on('draw.dt', function(e, settings) {
            // totalProductionsPlanItems();
            $('select.versions').select2();
            $('select.stages').select2();
            loadProductsPreventive();
            totalBOM();
        });

        $('#table-plan tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var records = tr.find('#records').val();
            var row = oTable.row(tr);

            if (row.child.isShown()) {
                arr = removeArray(arr, records);
                row.child.hide();
                tr.removeClass('shown');
            } else {
                if (!arr.includes(records)) {
                    arr.push(records);
                }
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });
    });
</script>