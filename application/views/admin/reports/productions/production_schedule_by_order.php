<div class="text-center uppercase">
    <h2><?= lang('tnh_production_schedule_by_order') ?></h2>
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
        <input type="text" name="products" id="products" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
    </div>
    <div class="col-md-2">
        <b><?= lang('start_date') ?></b>
        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="<?= lang('start_date') ?>" value="">
    </div>
    <div class="col-md-2">
        <b><?= lang('end_date') ?></b>
        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="<?= lang('end_date') ?>" value="">
    </div>
</div>
<div class="table-responsive">
	<table id="tb-production-schedule-order" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
				<th><?= lang('customers') ?></th>
				<th><?= lang('tnh_orders') ?></th>
                <th><?= lang('tnh_product_code') ?></th>
				<th><?= lang('tnh_product_name') ?></th>
				<th><?= lang('unit') ?></th>
                <th class="text-center"><?= lang('quantity') ?></th>
                <th class="text-center"><?= lang('tnh_has_join_produced') ?></th>
                <th class="text-center"><?= lang('tnh_has_warehoused') ?></th>
				<th class="text-center"><?= lang('tnh_rest') ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
		<!-- <tfoot>
			<tr class="bold uppercase">
				<th></th>
				<th class="text-center"><?= lang('tnh_grand_total') ?></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot> -->
	</table>
</div>
<script type="text/javascript">
	var paramsProductionScheduleOrder = {'products': '#products', 'customers': '#customers', 'start_date': '#start_date', 'end_date': '#end_date', 'orders': '#orders'};

	$(document).ready(function() {
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        init_datepicker();

		oTableProductionScheduleOrder = tnhDatatable(
            '#tb-production-schedule-order',
            {
                'order': [[1, 'asc'], [2, 'asc']],
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
                        title: '<?= lang('tnh_production_schedule_by_order') ?>',
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
                    //     title: '<?= lang('tnh_production_schedule_by_order') ?>',
                    //     extend: 'pdfHtml5',
                    //     footer: true,
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_production_schedule_by_order') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getProductionScheduleOrder') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionScheduleOrder) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionScheduleOrder[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
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
                    {"targets": 0, "name": 'customer_name', 'width': '150px'},
                    {"targets": 1, "name": 'reference_order', 'width': '130px'},
                    {"targets": 2, "name": 'product_code', 'width': '150px'},
                    {"targets": 3, "name": 'product_name', 'width': '150px'},
                    {"targets": 4, "name": 'unit_name', 'class': 'text-center', 'width': '50px'},
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 5, "name": 'quantity', 'class': 'text-center', 'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 6, "name": 'has_produced', 'class': 'text-center', 'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 7, "name": 'has_warehoused', 'class': 'text-center', 'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data || data == 0) return '';
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 8, "name": 'rest', 'class': 'text-center', 'width': '100px'
                    }
                ],
                rowsGroup: [
                    0, 1
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    // var quantity = 0;
                    // for (var i = 0; i < aaData.length; i++) {
                    //     quantity+= intVal(aaData[i][4]);
                    // }
                    // var nCells = nRow.getElementsByTagName('th');
                    // nCells[3].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                }
            }
        );

        $('#tb-production-schedule-order_wrapper .btn-dt-reload').click(function(event) {
            oTableProductionScheduleOrder.draw();
        });

        $('#products, #customers, #start_date, #end_date, #orders').change(function(event) {
            event.preventDefault();
            oTableProductionScheduleOrder.draw();
        });
	});
</script>