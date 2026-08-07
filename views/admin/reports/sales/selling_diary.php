<div class="text-center uppercase">
    <h2><?= lang('selling_diary') ?></h2>
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
    <table id="tb-selling-diary" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('tnh_orders') ?></th>
                <th class="text-center"><?= lang('date') ?></th>
                <th class="text-center"><?= lang('Số hóa đơn') ?></th>
                <th class="text-center"><?= lang('tnh_item_code') ?></th>
                <th class="text-center"><?= lang('tnh_item_name') ?></th>
                <th class="text-center"><?= lang('unit') ?></th>
                <th class="text-center"><?= lang('quantity') ?></th>
                <th class="text-center"><?= lang('price') ?><div>(Chưa thuế)</div></th>
                <th class="text-center"><?= lang('tnh_subtotal') ?><div>(Chưa thuế)</div></th>
                <th class="text-center"><?= lang('tnh_subtotal') ?><div>(Sau thuế)</div></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
        </tbody>
       <!--  <tfoot>
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
        </tfoot> -->
    </table>
</div>

<script type="text/javascript">
    var paramsSellingDiary = {'start_date': '#start_date', 'end_date': '#end_date', 'customers': '#customers', 'orders': '#orders'};
    $(document).ready(function() {
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        // ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        init_datepicker();

        oTableSellingDiary = tnhDatatable(
            '#tb-selling-diary',
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
                        title: '<?= lang('selling_diary') ?>',
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
                    //     title: '<?= lang('selling_diary') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('selling_diary') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getSellingDiary') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsSellingDiary) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsSellingDiary[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function(aoData, settings) {
                    var api = this.api();
                    var rows = api.rows({ page: 'current' }).nodes();
                    var last = null;
                    groupColumn = 1;
                    totalQuantity = [];
                    totalSubtotal = [];
                    totalSubtotalTax = [];

                    api.column(groupColumn, { page: 'current' })
                    .data()
                    .each(function (group, i) {
                        groupConvert = group.replace(/\//g,"_");
                        group_assoc_quantity = 'quantity_'+groupConvert;
                        if(typeof totalQuantity[group_assoc_quantity] != 'undefined'){
                            totalQuantity[group_assoc_quantity] = totalQuantity[group_assoc_quantity] + intVal(api.column(6).data()[i]);
                        }else{
                            totalQuantity[group_assoc_quantity] = intVal(api.column(6).data()[i]);
                        }

                        group_assoc_subtotal = 'subtotal_'+groupConvert;
                        if(typeof totalSubtotal[group_assoc_subtotal] != 'undefined'){
                            totalSubtotal[group_assoc_subtotal] = totalSubtotal[group_assoc_subtotal] + intVal(api.column(8).data()[i]);
                        }else{
                            totalSubtotal[group_assoc_subtotal] = intVal(api.column(8).data()[i]);
                        }

                        group_assoc_subtotal_tax = 'subtotal_tax_'+groupConvert;
                        if(typeof totalSubtotalTax[group_assoc_subtotal_tax] != 'undefined'){
                            totalSubtotalTax[group_assoc_subtotal_tax] = totalSubtotalTax[group_assoc_subtotal_tax] + intVal(api.column(9).data()[i]);
                        }else{
                            totalSubtotalTax[group_assoc_subtotal_tax] = intVal(api.column(9).data()[i]);
                        }

                        if ( last !== group ) {
                            $(rows).eq(i).before(
                                '<tr class="group group-start">'+
                                    '<td colspan="6">'+group+'</td>'+
                                    '<td class="text-center '+group_assoc_quantity+'"></td>'+
                                    '<td ></td>'+
                                    '<td class="text-right '+group_assoc_subtotal+'"></td>'+
                                    '<td class="text-right '+group_assoc_subtotal_tax+'"></td>'+
                                '</tr>'
                            );
                            last = group;
                        }
                    });

                    for(var key in totalQuantity) {
                        $("."+key).html(tnhFormatNumber(totalQuantity[key]));

                        keySubtotal = key.replace('quantity_', 'subtotal_');
                        $("."+keySubtotal).html(tnhFormatMoney(totalSubtotal[keySubtotal]));

                        keySubtotalTax = key.replace('quantity_', 'subtotal_tax_');
                        $("."+keySubtotalTax).html(tnhFormatMoney(totalSubtotalTax[keySubtotalTax]));
                    }
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
                    {"targets": 0, "name": 'reference_orders', 'width': '150px'},
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 1, "name": 'date', 'width': '80px', 'searchable': false
                    },
                    {"targets": 2, "name": 'bill', 'width': '120px'},
                    {"targets": 3, "name": 'item_code', 'width': '150px'},
                    {"targets": 4, "name": 'item_name', 'width': '150px'},
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
                        "targets": 9, "name": 'subtotalTax'
                    },
                ],
                // rowsGroup: [
                //     0, 1, 2
                // ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    // var quantity = 0, subtotal = 0, cost_price = 0, gross_profit = 0;
                    // for (var i = 0; i < aaData.length; i++) {
                    //     quantity+= intVal(aaData[i][6]);
                    //     subtotal+= intVal(aaData[i][8]);
                    //     cost_price+= intVal(aaData[i][9]);
                    //     gross_profit+= intVal(aaData[i][10]);
                    // }
                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                    // nCells[8].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(subtotal)+'</div>';
                    // nCells[9].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(cost_price)+'</div>';
                    // nCells[10].innerHTML = '<div class="text-right bold">'+tnhFormatMoney(gross_profit)+'</div>';
                }
            }
        );

        $('#tb-selling-diary_wrapper .btn-dt-reload').click(function(event) {
            oTableSellingDiary.draw();
        });

        $('#start_date, #end_date, #customers, #orders').change(function(event) {
            event.preventDefault();
            oTableSellingDiary.draw();
        });
    });
</script>