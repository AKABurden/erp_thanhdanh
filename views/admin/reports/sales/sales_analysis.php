<div class="text-center uppercase">
    <h2><?= lang('sales_analysis') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('customers') ?></b>
        <input type="text" name="customers" id="customers" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
    </div>
    <div class="col-md-3">
        <b><?= lang('orders') ?></b>
        <input type="text" name="orders" id="orders" style="width: 100%;" data-placeholder="<?= lang('orders') ?>" value="<?= $orders ?>">
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
    <table id="tb-sales-analysic" class="table table-hover table-bordered table-condensed">
        <thead>
            <tr>
                <th class="text-center"><?= lang('customers') ?></th>
                <th class="text-center"><?= lang('tnh_orders') ?></th>
                <th class="text-center"><?= lang('date') ?></th>
                <th class="text-center"><?= lang('tnh_item_code') ?></th>
                <th class="text-center"><?= lang('tnh_item_name') ?></th>
                <th class="text-center"><?= lang('unit') ?></th>
                <th class="text-center"><?= lang('quantity') ?></th>
                <th class="text-center"><?= lang('price') ?><div>(Chưa thuế)</div></th>
                <th class="text-center"><?= lang('tnh_subtotal') ?><div>(Chưa thuế)</div></th>
                <th class="text-center"><?= lang('Giá vốn') ?></th>
                <th class="text-center"><?= lang('Lại gộp') ?></th>
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
    var paramsSalesAnalysis = {'start_date': '#start_date', 'end_date': '#end_date', 'customers': '#customers', 'orders': '#orders'};
    $(document).ready(function() {
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        init_datepicker();

        oTableSalesAnalysis = tnhDatatable(
            '#tb-sales-analysic',
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
                    {
                        text: 'Excel',
                        title: '<?= lang('sales_analysis') ?>',
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
                    //     title: '<?= lang('sales_analysis') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('sales_analysis') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getSalesAnalysis') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsSalesAnalysis) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsSalesAnalysis[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function(aoData, settings) {
                    // var api = this.api();
                    // var rows = api.rows({ page: 'current' }).nodes();
                    // var last = null;
                    // groupColumn = 2;
                    // api.column(groupColumn, { page: 'current' })
                    //     .data()
                    //     .each(function (group, i) {
                    //         $(rows).eq(i).before(
                    //             '<tr class="group"><td colspan="11">test</td></tr>'
                    //         );
                    //     });
                    // console.log(rows);
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
                    {"targets": 0, "name": 'customer', 'width': '150px'},
                    {"targets": 1, "name": 'reference_orders', 'width': '130px'},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'width': '80px', 'searchable': false
                    },
                    {"targets": 3, "name": 'item_code', 'width': '130px'},
                    {"targets": 4, "name": 'item_name', 'width': '130px'},
                    {"targets": 5, "name": 'unit_name', 'width': '50px', 'class': 'text-center'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 6, "name": 'quantity'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == '' || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 7, "name": 'price'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 8, "name": 'subtotal'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 9, "name": 'cost_price'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-right">'+tnhFormatMoney(data)+'</div>';
                        },
                        "targets": 10, "name": 'gross_profit'
                    }
                ],
                rowsGroup: [
                    0, 1, 2
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity = 0, subtotal = 0, cost_price = 0, gross_profit = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity+= intVal(aaData[i][6]);
                        subtotal+= intVal(aaData[i][8]);
                        cost_price+= intVal(aaData[i][9]);
                        gross_profit+= intVal(aaData[i][10]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                    nCells[8].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(subtotal)+'</div>';
                    nCells[9].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(cost_price)+'</div>';
                    nCells[10].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(gross_profit)+'</div>';
                }
            }
        );

        $('#tb-sales-analysic_wrapper .btn-dt-reload').click(function(event) {
            oTableSalesAnalysis.draw();
        });

        $('#start_date, #end_date, #customers, #orders').change(function(event) {
            event.preventDefault();
            oTableSalesAnalysis.draw();
        });
    });
</script>