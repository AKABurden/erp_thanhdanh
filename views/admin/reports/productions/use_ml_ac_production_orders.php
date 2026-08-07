<div class="text-center uppercase">
    <h2><?= lang('report_use_material_according_production_orders') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <div class="form-group">
            <?= lang('tnh_orders_and_business_plan', 'orders_and_business_plan') ?>
            <input type="text" name="orders_and_business_plan" id="orders_and_business_plan" style="width: 100%;" data-placeholder="<?= lang('tnh_orders_and_business_plan') ?>" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('tnh_reference_productions_orders', 'tnh_reference_productions_orders') ?></b>
            <input type="text" name="productions_orders" id="productions_orders" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('start_date', 'start_date') ?></b>
            <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <b><?= lang('end_date', 'end_date') ?></b>
            <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="tb-material-production-orders" class="table table-hover table-bordered table-condensed" style="width: 100%;">
        <thead>
            <tr>
                <th><?= lang('tnh_numbers') ?></th>
                <th><?= lang('tnh_orders_and_business_plan') ?></th>
                <th><?= lang('tnh_reference_productions_orders') ?></th>
                <th><?= lang('date') ?></th>
                <th><?= lang('tnh_material_code') ?></th>
                <th><?= lang('tnh_material_name') ?></th>
                <th><?= lang('unit') ?></th>
                <th class="text-center"><?= lang('tnh_quota') ?><div>(1)</div>
                </th>
                <th class="text-center"><?= lang('tnh_exported') ?><div>(2)</div>
                </th>
                <th class="text-center"><?= lang('%') ?><div>(3)=(2)/(1)</div>
                </th>
                <th class="text-center"><?= lang('tnh_rest') ?><div>(4)=(1)-(2)</div>
                </th>
                <th class="text-center"><?= lang('tnh_used') ?><div>(5)</div>
                </th>
                <th class="text-center"><?= lang('tnh_redundant/missing') ?><div>(6)=(2)-(5)</div>
                </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr class="bold uppercase">
                <th></th>
                <th></th>
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
    var paramsMaterialProductionOrders = {
        'productions_orders': '#productions_orders',
        'start_date': '#start_date',
        'end_date': '#end_date',
        'orders_and_business_plan': '#orders_and_business_plan',
    };
    $(document).ready(function() {
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams($('#productions_orders'), 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        init_datepicker();

        oTableMaterialProductionOrders = tnhDatatable(
            '#tb-material-production-orders', {
                'order': [
                    [1, 'asc'],
                    [2, 'asc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    {
                        text: 'Excel',
                        title: '<?= lang('report_use_material_according_production_orders') ?>',
                        // extend: 'excelHtml5',
                        // autoFilter: true,
                        extend: 'excelHtml5',
                        footer: true,
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
                    //     title: '<?= lang('report_use_material_according_production_orders') ?>',
                    //     extend: 'pdfHtml5',
                    //     footer: true,
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_use_material_according_production_orders') ?>',
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
                // scrollY: height_body,
                // scrollX: true,
                // scrollCollapse: true,
                // fixedColumns:   {
                //     leftColumns: 5,
                //     rightColumns: 0
                // },
                // stateSave: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/reports_tnh/getMaterialProductionOrders') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsMaterialProductionOrders) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsMaterialProductionOrders[key]).val()
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
                        "name": 'id',
                        'width': '50px',
                        'class': 'text-center',
                        'sortable': false,
                        'visible': false
                    },
                    {
                        "targets": 1,
                        "name": 'reference_orders',
                        'width': '120px'
                    },
                    {
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + fld(data) + '</div>';
                        },
                        "targets": 3,
                        "name": 'date',
                        'searchable': false,
                        'width': '80px'
                    },
                    {
                        "targets": 4,
                        "name": 'item_code',
                        'width': '120px'
                    },
                    {
                        "targets": 5,
                        "name": 'item_name',
                        'width': '150px'
                    },
                    {
                        "targets": 6,
                        "name": 'unit_name',
                        'class': 'text-center',
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'quantity_quota',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'quantity_exported',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            if (data == null) data = 0;
                            return '<div class="text-center">' + data + '</div>';
                        },
                        "targets": 9,
                        "name": 'percent',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 10,
                        "name": 'quantity_end',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 11,
                        "name": 'quantity_used',
                        'class': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 12,
                        "name": 'missing',
                        'class': 'text-center'
                    },
                ],
                // "orderFixed": {
                //     "pre": [ 1, 'asc' ],
                //     "post": [ 2, 'asc' ]
                // },
                // rowsGroup: [
                //     1
                // ],
                // rowGroup: {
                //     dataSrc: 1
                // },
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_quota = 0, quantity_exported = 0, percent = 0, quantity_end = 0, quantity_used = 0, missing = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_quota+= intVal(aaData[i][7]);
                        quantity_exported+= intVal(aaData[i][8]);
                        percent+= intVal(aaData[i][9]);
                        quantity_end+= intVal(aaData[i][10]);
                        quantity_used+= intVal(aaData[i][11]);
                        missing+= intVal(aaData[i][12]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_quota)+'</div>';
                    nCells[7].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_exported)+'</div>';
                    nCells[9].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_end)+'</div>';
                    nCells[10].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_used)+'</div>';
                    nCells[11].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(missing)+'</div>';
                }
            }
        );

        $('#tb-material-production-orders_wrapper .btn-dt-reload').click(function(event) {
            oTableMaterialProductionOrders.draw();
        });

        $('#products, #productions_orders, #start_date, #end_date, #orders_and_business_plan').change(function(event) {
            event.preventDefault();
            oTableMaterialProductionOrders.draw();
        });
    });
</script>