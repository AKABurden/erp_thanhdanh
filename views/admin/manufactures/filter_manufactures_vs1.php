<style>
    #tb-manufactures_wrapper #tb-manufactures_length, #tb-manufactures_wrapper .dt-buttons{
        /* display: none; */
    }

    #tb-manufactures tr th:nth-child(8), #tb-manufactures tr td:nth-child(8) {
        background: #f1eab5;
    }
</style>

<?php
    $customer_search = $this->input->post('customer_search');
    $start_date_search = to_sql_date($this->input->post('start_date_search'));
    $end_date_search = to_sql_date($this->input->post('end_date_search'));
    $products_search = str_replace('__products', '', $this->input->post('products_search'));
    $category_product_search = !empty($this->input->post('category_product_search')) ? implode(',', $this->input->post('category_product_search')) : '';
    $type_orders_search = !empty($this->input->post('type_orders_search')) ? implode(',', $this->input->post('type_orders_search')) : '';
    $search_date_order = $this->input->post('search_date_order');
    $type_view_search = $this->input->post('type_view_search');
    $products_text_search = $this->input->post('products_text_search');
?>
<table id="tb-manufactures" class="table table-hover table-tb-manufactures" style="min-width: 100%;">
    <thead>
        <tr>
            <th style="width: 30px;" class="text-center uppercase">
                <div class="checkbox mass_select_all_wrap text-center"><input
                            type="checkbox" id="mass_select_all_new" onclick="changeCheckBox(this)"
                            data-to-table="tb-manufactures"><label for="mass_select_all_new"></label>
                </div>
            </th>
            <th class="text-center uppercase" style="width: 50px;"><?= lang('tnh_images') ?></th>
            <th class="text-center uppercase" style="width: 120px;"><?= lang('dt_product_code') ?></th>
            <th class="text-center uppercase" style="width: 12px;"><?= lang('dt_product_name') ?></th>
            <th class="text-center uppercase" style="width: 12px;"><?= lang('Đơn hàng chi tiết') ?></th>
            <th class="text-center uppercase" style="width: 50px;"><?= lang('Giữ kho') ?></th>
            <th class="text-center uppercase" style="width: 80px;"><?= lang('SL đơn hàng') ?></th>
            <th class="text-center uppercase" style="width: 80px;"><?= lang('SL đang sx theo đơn') ?></th>
            <th class="text-center uppercase" style="width: 80px;"><?= lang('SL đang sx dự trù') ?></th>
            <th class="text-center uppercase" style="width: 80px;"><?= lang('SL hàng sẵn trong kho') ?></th>
            <th class="text-center uppercase" style="width: 80px;"><?= lang('SL cần sản xuất') ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr class="bold">
            <td></td>
            <td class="text-center"><?= lang('tnh_grand_total') ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<div>
    <input type="hidden" name="customer_search_manufactures" id="customer_search_manufactures" class="form-control" value="<?= $customer_search ?>">
    <input type="hidden" name="start_date_search_manufactures" id="start_date_search_manufactures" class="form-control" value="<?= $start_date_search ?>">
    <input type="hidden" name="end_date_search_manufactures" id="end_date_search_manufactures" class="form-control" value="<?= $end_date_search ?>">
    <input type="hidden" name="products_search_manufactures" id="products_search_manufactures" class="form-control" value="<?= $products_search ?>">
    <input type="hidden" name="category_product_search_manufactures" id="category_product_search_manufactures" class="form-control" value="<?= $category_product_search ?>">
    <input type="hidden" name="type_orders_search_manufactures" id="type_orders_search_manufactures" class="form-control" value="<?= $type_orders_search ?>">
    <input type="hidden" name="search_date_order_manufactures" id="search_date_order_manufactures" class="form-control" value="<?= $search_date_order ?>">
    <input type="hidden" name="type_view_search_manufactures" id="type_view_search_manufactures" class="form-control" value="<?= $type_view_search ?>">
    <input type="hidden" name="products_text_search_manufactures" id="products_text_search_manufactures" class="form-control" value="<?= $products_text_search ?>">
</div>
<script>

    var fnserverparamsManufactures = {
        customer_search_manufactures: '#customer_search_manufactures',
        start_date_search_manufactures: '#start_date_search_manufactures',
        end_date_search_manufactures: '#end_date_search_manufactures',
        products_search_manufactures: '#products_search_manufactures',
        category_product_search_manufactures: '#category_product_search_manufactures',
        type_orders_search_manufactures: '#type_orders_search_manufactures',
        search_date_order_manufactures: '#search_date_order_manufactures',
        type_view_search_manufactures: '#type_view_search_manufactures',
        products_text_search_manufactures: '#products_text_search_manufactures',
    };

    arrProductId = {};
    function changeProductCb(_this) {
        isCheck = $(_this).prop('checked');
        objectProduct = $(_this).val();
        if (isCheck) {
            arrProductId[objectProduct] = 1;
        } else {
            arrProductId[objectProduct] = 0;
        }
    }

    function changeCheckBox(_this) {
        var to, rows, checked;
        to = $(_this).data('to-table');
        rows = $('.table-' + to).find('tbody tr');
        checked = $(_this).prop('checked');
        $.each(rows, function (k, v) {
            $(v).find('td').eq(0).find('input').prop('checked', checked);
            changeProductCb($(v).find('td').eq(0).find('input'));
        });
    }

    function modalDetailPlan(temp_product_id) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['product_id'] = temp_product_id;
        dataPOST['customer_search_manufactures'] = $('#customer_search_manufactures').val();
        dataPOST['start_date_search_manufactures'] = $('#start_date_search_manufactures').val();
        dataPOST['end_date_search_manufactures'] = $('#end_date_search_manufactures').val();
        dataPOST['products_search_manufactures'] = $('#products_search_manufactures').val();
        dataPOST['category_product_search_manufactures'] = $('#category_product_search_manufactures').val();
        dataPOST['type_view_search_manufactures'] = $('#type_view_search_manufactures').val();
        dataPOST['type_orders_search_manufactures'] = $('#type_orders_search_manufactures').val();
        dataPOST['search_date_order_manufactures'] = $('#search_date_order_manufactures').val();

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures_temp/modalDetailPlan',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.modal-select2').select2('close');
                $('#tnhModal').html(response);
                $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            }
        });
    }

    function keepWarehouseOrders(singe_product_id) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['product_id'] = singe_product_id;
        dataPOST['customer_search_manufactures'] = $('#customer_search_manufactures').val();
        dataPOST['start_date_search_manufactures'] = $('#start_date_search_manufactures').val();
        dataPOST['end_date_search_manufactures'] = $('#end_date_search_manufactures').val();
        dataPOST['products_search_manufactures'] = $('#products_search_manufactures').val();
        dataPOST['category_product_search_manufactures'] = $('#category_product_search_manufactures').val();
        dataPOST['type_view_search_manufactures'] = $('#type_view_search_manufactures').val();
        dataPOST['type_orders_search_manufactures'] = $('#type_orders_search_manufactures').val();
        dataPOST['search_date_order_manufactures'] = $('#search_date_order_manufactures').val();

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufactures_temp/keepWarehouseOrders',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.modal-select2').select2('close');
                $('#tnhModal').html(response);
                $('#tnhModal').modal({backdrop: 'static', keyboard: true});
            }
        });
    }

    $(document).ready(function () {
        oTable = tnhInitDataTable('#tb-manufactures', '', {
            'ordering': false,
            'searching': false,
            // 'fixedHeader': {
            //     header: true,
            // },
            "ajax": {
                "url": '<?= site_url('admin/Manufactures_temp/getManufacturesPlan') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsManufactures) {
                        d[key] = $(fnserverparamsManufactures[key]).val();
                    }
                },

                "dataSrc": function(json) {
                    $('#tb-manufactures tfoot tr td:nth-child(7)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity_orders)+'</div>');
                    $('#tb-manufactures tfoot tr td:nth-child(8)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity_manufactures)+'</div>');
                    $('#tb-manufactures tfoot tr td:nth-child(9)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity_preventive)+'</div>');
                    $('#tb-manufactures tfoot tr td:nth-child(10)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity_warehouses)+'</div>');
                    $('#tb-manufactures tfoot tr td:nth-child(11)').html('<div class="text-center">'+tnhFormatNumber(json.total_quantity_need_manufactures)+'</div>');
                    return json.aaData;
                }
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                cur_product_id = $(nRow).find('input[name="product_id[]"]').val();
                if (typeof arrProductId[cur_product_id] !== 'undefined' && arrProductId[cur_product_id] === 1) {
                    $(nRow).find('input[name="product_id[]"]').prop('checked', true);
                }
                return nRow;
            },
            "columnDefs": [
            ],
        });
    });
</script>