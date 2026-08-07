<div class="text-center uppercase">
    <h2><?= lang('tnh_report_order_status') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('customers') ?></b>
        <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('orders') ?></b>
        <input type="text" name="orders" id="orders" style="width: 100%;" data-placeholder="<?= lang('orders') ?>" value="">
    </div>
    <div class="col-md-2">
        <b><?= lang('products') ?></b>
        <input type="text" name="products_search" id="products_search" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
    </div>
    <div class="col-md-2">
        <b><?= lang('start_date_delivery') ?></b>
        <input type="text" name="start_date_delivery" id="start_date_delivery" class="form-control datepicker" placeholder="<?= lang('start_date_delivery') ?>" value="">
    </div>
    <div class="col-md-2">
        <b><?= lang('end_date_delivery') ?></b>
        <input type="text" name="end_date_delivery" id="end_date_delivery" class="form-control datepicker" placeholder="<?= lang('end_date_delivery') ?>" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-order-status" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th rowspan="2" class="text-center"><?= lang('customers') ?></th>
                <th colspan="2" class="text-center"><?= lang('orders') ?></th>
                <th colspan="3" class="text-center"><?= lang('items') ?></th>
                <th colspan="5" class="text-center"><?= lang('quantity') ?></th>
            </tr>
            <tr>
                <th class="text-center"><?= lang('tnh_number') ?></th>
                <th class="text-center"><?= lang('date') ?></th>
                <th class="text-center"><?= lang('tnh_item_code') ?></th>
                <th class="text-center"><?= lang('tnh_item_name') ?></th>
                <th class="text-center"><?= lang('unit') ?></th>
                <th class="text-center"><?= lang('Đặt hàng') ?></th>
                <th class="text-center"><?= lang('Giao kỳ trước') ?></th>
                <th class="text-center"><?= lang('Giao kỳ này') ?></th>
                <th class="text-center"><?= lang('Tổng đã giao') ?></th>
                <th class="text-center"><?= lang('Còn lại') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <th class="text-center"><?= lang('tnh_grand_total') ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var paramsOrderStatus = {
        'customers': '#customers',
        'orders': '#orders',
        'start_date_delivery': '#start_date_delivery',
        'end_date_delivery': '#end_date_delivery',
        'products_search': '#products_search'
    };
    $(document).ready(function() {
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        ajaxSelectParamsCallback('#products_search', 'admin/products/searchProductsSelect2', $('#products_search').val(), false, true);
        // ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        init_datepicker();

        oTableOrderStatus = tnhDatatable(
            '#tb-order-status', {
                'order': [
                    [0, 'asc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                'searching': false,
                'ordering': false,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    {
                        text: 'Excel',
                        title: '<?= lang('tnh_report_order_status') ?>',
                        // extend: 'excelHtml5',
                        // autoFilter: true,
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':visible'
                        },
                        // customize: function ( xlsx ){
                        //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        //     $('row c', sheet).attr( 's', '25' );
                        // }
                    },
                    // {
                    //     text: 'Pdf',
                    //     title: '<?= lang('tnh_report_order_status') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_report_order_status') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'print',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // }
                ],
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                scrollY: true,
                scrollX: true,
                // scrollCollapse: true,
                // fixedColumns: {
                //     leftColumns: 2,
                //     rightColumns: 0
                // },
                // stateSave: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getOrderStatus') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsOrderStatus) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsOrderStatus[key]).val()
                        });
                        
                        //custom
                        if ($(paramsOrderStatus[key]).data('select2') && $(paramsOrderStatus[key]).val()) {
                            var array_data = $(paramsOrderStatus[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key + '_text_' + index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsOrderStatus[key]).select2('data').text;
                                        aoData.push({
                                            "name": key + '_text',
                                            "value": $(paramsOrderStatus[key]).select2('data').text
                                        });
                                    }
                                });
                            } else {
                                // aoData[key+'_text'] = $(paramsOrderStatus[key]).select2('data').text;
                                aoData.push({
                                    "name": key + '_text',
                                    "value": $(paramsOrderStatus[key]).select2('data').text
                                });
                            }
                        } else if ($(paramsOrderStatus[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsOrderStatus[key]).find('option:selected').text();
                            if (selectedText) {
                                // aoData[key+'_text'] = selectedText;
                                aoData.push({
                                    "name": key + '_text',
                                    "value": selectedText
                                });
                            }
                        }
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': function(response) {
                            $('#tb-order-status').attr('title_excel', JSON.stringify(response?.title_excel));
                            fnCallback(response);
                        }
                    });
                },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function(tfoot, data, start, end, display) {},
                "columnDefs": [{
                        "targets": 0,
                        "name": 'customer',
                        'width': '150px'
                    },
                    {
                        "targets": 1,
                        "name": 'reference_orders',
                        'width': '130px'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2,
                        "name": 'date',
                        'width': '80px',
                        'searchable': false
                    },
                    {
                        "targets": 3,
                        "name": 'item_code',
                        'width': '130px'
                    },
                    {
                        "targets": 4,
                        "name": 'item_name',
                        'width': '130px'
                    },
                    {
                        "targets": 5,
                        "name": 'unit_name',
                        'width': '50px',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 6,
                        "name": 'quantity_order'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == '' || data == 0) return '';
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'delivered_last_period'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'delivered_this_period'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 9,
                        "name": 'total_delivery'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 10,
                        "name": 'rest'
                    }
                ],
                // rowsGroup: [
                //     0, 1, 2, 9, 10, 11
                // ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_order = 0,
                        delivered_last_period = 0,
                        delivered_this_period = 0,
                        total_delivery = 0,
                        rest = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_order += intVal(aaData[i][6]);
                        delivered_last_period += intVal(aaData[i][7]);
                        delivered_this_period += intVal(aaData[i][8]);
                        total_delivery += intVal(aaData[i][9]);
                        rest += intVal(aaData[i][10]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[6].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(quantity_order) + '</div>';
                    nCells[7].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(delivered_last_period) + '</div>';
                    nCells[8].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(delivered_this_period) + '</div>';
                    nCells[9].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(total_delivery) + '</div>';
                    nCells[10].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(rest) + '</div>';
                }
            }, 1
        );

        $('#tb-order-status_wrapper .btn-dt-reload').click(function(event) {
            oTableOrderStatus.draw();
        });

        $('#start_date_delivery, #end_date_delivery, #customers, #orders, #products_search').change(function(event) {
            event.preventDefault();
            oTableOrderStatus.draw();
        });
    });
</script>