<div class="text-center uppercase">
    <h2><?= lang('tnh_report_orders_of_quotes') ?></h2>
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
	<table id="tb-orders-quotes" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
				<th class="text-center"><?= lang('customers') ?></th>
				<th class="text-center"><?= lang('quotes') ?></th>
				<th class="text-center"><?= lang('date') ?></th>
				<th class="text-center"><?= lang('tnh_orders') ?></th>
				<th class="text-center"><?= lang('tnh_product_code') ?></th>
				<th class="text-center"><?= lang('tnh_product_name') ?></th>
				<th class="text-center"><?= lang('unit') ?></th>
				<th class="text-center"><?= lang('tnh_quantity_quotes') ?></th>
				<th class="text-center"><?= lang('tnh_quantity_orders') ?></th>
				<th class="text-center"><?= lang('tnh_quantity_delivery') ?></th>
				<th class="text-center"><?= lang('tnh_end_delivery') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
			</tr>
		</tbody>
		<tfoot>
			<tr class="bold uppercase">
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
			</tr>
		</tfoot>
	</table>
</div>

<script type="text/javascript">
	var paramsOrdersQuotes = {'start_date': '#start_date', 'end_date': '#end_date', 'customers': '#customers', 'orders': '#orders'};
	$(document).ready(function() {
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#customers', 'admin/clients/searchCustomers', $('#customers').val(), false, true);
        ajaxSelectParams('#orders', 'admin/orders/searchOrders', $('#orders').val(), false, true);
        init_datepicker();

		oTableOrdersQuotes = tnhDatatable(
            '#tb-orders-quotes',
            {
                'order': [[0, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    {
                        text: 'Excel',
                        title: '<?= lang('tnh_report_orders_of_quotes') ?>',
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
                    //     title: '<?= lang('tnh_report_orders_of_quotes') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('tnh_report_orders_of_quotes') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getOrdersQuotes') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsOrdersQuotes) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsOrdersQuotes[key]).val()
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
                	{"targets": 0, "name": 'customer', 'width': '150px'},
                	{"targets": 1, "name": 'reference_quotes', 'width': '130px'},
                	{
                		"render": function(data, type, row) {
                			return fld(data);
                		},
                		"targets": 2, "name": 'date_quotes', 'width': '100px', 'searchable': false
                	},
                	{"targets": 3, "name": 'reference_orders', 'width': '130px'},
                	{"targets": 4, "name": 'product_code', 'width': '130px'},
                	{"targets": 5, "name": 'product_name', 'width': '130px'},
                	{"targets": 6, "name": 'unit_name', 'width': '50px', 'class': 'text-center'},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 7, "name": 'quantity_quotes'
                	},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 8, "name": 'quantity_orders'
                	},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 9, "name": 'quantity_delivery'
                	},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 10, "name": 'end_delivery'
                	}
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity_quotes = 0, quantity_orders = 0, quantity_delivery = 0, end_delivery = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity_quotes+= intVal(aaData[i][7]);
                        quantity_orders+= intVal(aaData[i][8]);
                        quantity_delivery+= intVal(aaData[i][9]);
                        end_delivery+= intVal(aaData[i][10]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[7].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_quotes)+'</div>';
                    nCells[8].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_orders)+'</div>';
                    nCells[9].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity_delivery)+'</div>';
                    nCells[10].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(end_delivery)+'</div>';
                }
            }
        );

        $('#tb-orders-quotes_wrapper .btn-dt-reload').click(function(event) {
            oTableOrdersQuotes.draw();
        });

        $('#start_date, #end_date, #customers, #orders').change(function(event) {
            event.preventDefault();
            oTableOrdersQuotes.draw();
        });
	});
</script>