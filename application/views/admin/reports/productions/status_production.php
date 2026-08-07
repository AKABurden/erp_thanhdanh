<div class="text-center uppercase">
    <h2><?= lang('report_status_production') ?></h2>
</div>
<hr>
<div class="row mbot10">
	<div class="col-md-3">
        <b><?= lang('tnh_reference_productions_orders') ?></b>
        <input type="text" name="productions_orders" id="productions_orders" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" value="">
    </div>
	<div class="col-md-3">
        <b><?= lang('products') ?></b>
        <input type="text" name="products" id="products" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
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
	<table id="tb-status-production" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
				<th><?= lang('tnh_numbers') ?></th>
				<th><?= lang('tnh_reference_productions_orders') ?></th>
				<th><?= lang('date') ?></th>
				<th><?= lang('tnh_product_code') ?></th>
				<th><?= lang('tnh_product_name') ?></th>
				<th><?= lang('unit') ?></th>
				<th><?= lang('tnh_qty_productions') ?></th>
				<th><?= lang('tnh_has_produced') ?></th>
				<th><?= lang('tnh_rest') ?></th>
			</tr>
		</thead>
		<tbody>
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
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	var paramsStatusProduction = {'productions_orders': '#productions_orders', 'products': '#products', 'start_date': '#start_date', 'end_date': '#end_date'};
	$(document).ready(function() {
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams($('#productions_orders'), 'admin/manufactures/searchProductionsOrders', 0, false, true);
        init_datepicker();

		oTableStatusProduction = tnhDatatable(
            '#tb-status-production',
            {
                'order': [[2, 'asc']],
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
                        title: '<?= lang('report_status_production') ?>',
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
                    //     title: '<?= lang('report_status_production') ?>',
                    //     extend: 'pdfHtml5',
                    //     footer: true,
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_status_production') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getStatusProduction') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsStatusProduction) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsStatusProduction[key]).val()
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
                	{"targets": 0, "name": 'id', 'width': '50px', 'class': 'text-center', 'sortable': false, 'visible': false},
                	{"targets": 1, "name": 'reference_no'},
                	{
                		"render": function(data, type, row) {
                			return '<div>'+fld(data)+'</div>';
                		},
                		"targets": 2, "name": 'date', 'searchable': false
                	},
                	{"targets": 3, "name": 'item_code'},
                	{"targets": 4, "name": 'item_name'},
                	{"targets": 5, "name": 'unit_name', 'class': 'text-center'},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 6, "name": 'quantity_production', 'class': 'text-center'
                	},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 7, "name": 'has_produced', 'class': 'text-center'
                	},
                	{
                		"render": function(data, type, row) {
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 8, "name": 'rest', 'class': 'text-center'
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
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var qty_production = 0, has_produced = 0, rest = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        qty_production+= intVal(aaData[i][6]);
                        has_produced+= intVal(aaData[i][7]);
                        rest+= intVal(aaData[i][8]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[5].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(qty_production)+'</div>';
                    nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(has_produced)+'</div>';
                    nCells[7].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(rest)+'</div>';
                }
            }
        );

        $('#tb-status-production_wrapper .btn-dt-reload').click(function(event) {
            oTableStatusProduction.draw();
        });

        $('#products, #productions_orders, #start_date, #end_date').change(function(event) {
            event.preventDefault();
            oTableStatusProduction.draw();
        });
	});
</script>