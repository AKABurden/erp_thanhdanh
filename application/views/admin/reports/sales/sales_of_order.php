<div class="text-center uppercase">
    <h2><?= lang('tnh_sales_of_order') ?></h2>
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
    <div class="col-md-3">
        <b><?= lang('start_date') ?></b>
        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('end_date') ?></b>
        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-sales-order" class="table table-hover table-bordered table-condensed">
        <thead>
            <tr>
                <th rowspan="2" class="text-center"><?= lang('Nhóm khách hàng') ?></th>
                <th rowspan="2" class="text-center"><?= lang('customers') ?></th>
                <th rowspan="2" class="text-center"><?= lang('tnh_orders') ?></th>
                <th rowspan="2" class="text-center"><?= lang('Loại đơn hàng') ?></th>
                <th rowspan="2" class="text-center"><?= lang('date') ?></th>
                <th rowspan="2" class="text-center"><?= lang('tnh_product_code') ?></th>
                <th rowspan="2" class="text-center"><?= lang('tnh_product_name') ?></th>
                <th rowspan="2" class="text-center"><?= lang('unit') ?></th>
                <th rowspan="1" colspan="3" class="text-center"><?= lang('quantity') ?></th>
                <th rowspan="1" colspan="3" class="text-center"><?= lang('Giá trị(Tiền HT)') ?></th>
            </tr>
            <tr>
                <th class="text-center">Đơn hàng</th>
                <th class="text-center">Đã giao</th>
                <th class="text-center">Còn lại</th>
                <th class="text-center">Đơn hàng</th>
                <th class="text-center">Đã giao</th>
                <th class="text-center">Còn lại</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <td></td>
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
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    var paramsSalesOrder = {'start_date': '#start_date', 'end_date': '#end_date', 'customers': '#customers', 'orders': '#orders'};
    $(document).ready(function() {
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        init_datepicker();

        oTableSalesOrder = tnhDatatable(
            '#tb-sales-order',
            {
                'order': [[0, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                'searching': false,
                'ordering': false,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    // {
                    //     text: 'Excel',
                    //     title: '<?= lang('tnh_sales_of_order') ?>',
                    //     // extend: 'excelHtml5',
                    //     // autoFilter: true,
                    //     extend: 'excelHtml5',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     },
                    //     // customize: function ( xlsx ){
                    //     //     var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    //     //     $('row c', sheet).attr( 's', '25' );
                    //     // }
                    // },
                    // {
                    //     text: 'Pdf',
                    //     title: '<?= lang('tnh_sales_of_order') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_sales_of_order') ?>',
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
                //     leftColumns: 3,
                //     rightColumns: 0
                // },
                // stateSave: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports/getSalesOfOrder') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsSalesOrder) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsSalesOrder[key]).val()
                        });

                        //custom
                        if ($(paramsSalesOrder[key]).data('select2') && $(paramsSalesOrder[key]).val()) {
                            var array_data = $(paramsSalesOrder[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key+'_text_'+index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsSalesOrder[key]).select2('data').text;
                                        aoData.push({
                                            "name": key+'_text',
                                            "value": $(paramsSalesOrder[key]).select2('data').text
                                        });
                                    }
                                });
                            } else {
                                // aoData[key+'_text'] = $(paramsSalesOrder[key]).select2('data').text;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": $(paramsSalesOrder[key]).select2('data').text
                                });
                            }
                        } else if ($(paramsSalesOrder[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsSalesOrder[key]).find('option:selected').text();
                            if (selectedText) {
                                // aoData[key+'_text'] = selectedText;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": selectedText
                                });
                            }
                        }
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': function(response) {
                        $('#tb-sales-order').attr('title_excel', JSON.stringify(response?.title_excel));
                        fnCallback(response);
                    }});
                },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'customer_group', 'width': '150px'},
                    {"targets": 1, "name": 'customer', 'width': '150px'},
                    {"targets": 2, "name": 'reference_orders', 'width': '130px'},
                    {"targets": 3, "name": 'type_orders', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 4, "name": 'date_quotes', 'width': '80px', 'searchable': false
                    },
                    {"targets": 5, "name": 'product_code', 'width': '130px'},
                    {"targets": 6, "name": 'product_name', 'width': '130px'},
                    {"targets": 7, "name": 'unit_name', 'width': '50px', 'class': 'text-center'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 8, "name": 'quantity_order'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == '' || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 9, "name": 'quantity_delivery'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 10, "name": 'quantity_end'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 11, "name": 'amount_order'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 12, "name": 'amount_delivery'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 13, "name": 'amount_end'
                    }
                ],
                // rowsGroup: [
                //     0,1, 2, 3, 10, 11, 12
                // ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_order = 0, quantity_delivery = 0, quantity_end = 0, amount_order = 0, amount_delivery = 0, amount_end = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_order+= intVal(aaData[i][8]);
                        quantity_delivery+= intVal(aaData[i][9]);
                        quantity_end+= intVal(aaData[i][10]);
                        amount_order+= intVal(aaData[i][11]);
                        amount_delivery+= intVal(aaData[i][12]);
                        amount_end+= intVal(aaData[i][13]);
                    }
                    var nCells = nRow.getElementsByTagName('td');
                    nCells[8].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_order)+'</div>';
                    nCells[9].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_delivery)+'</div>';
                    nCells[10].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_end)+'</div>';
                    nCells[11].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(amount_order)+'</div>';
                    nCells[12].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(amount_delivery)+'</div>';
                    nCells[13].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(amount_end)+'</div>';
                }
            }, 1
        );

        $('#tb-sales-order_wrapper .btn-dt-reload').click(function(event) {
            // oTableSalesOrder.draw();
        });

        $('#start_date, #end_date, #customers, #orders').change(function(event) {
            event.preventDefault();
            oTableSalesOrder.draw();
        });
    });
</script>