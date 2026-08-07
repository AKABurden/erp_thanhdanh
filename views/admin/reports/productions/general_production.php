<div class="text-center uppercase">
    <h2><?= lang('report_general_production') ?></h2>
</div>
<hr>
<div class="row mbot10">
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
	<table id="tb-general-production" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
				<th><?= lang('tnh_numbers') ?></th>
				<th><?= lang('tnh_orders_and_business_plan') ?></th>
				<th><?= lang('tnh_note_orders') ?></th>
				<th><?= lang('tnh_product_code') ?></th>
				<th><?= lang('tnh_product_name') ?></th>
				<th><?= lang('unit') ?></th>
				<th class="text-center"><?= lang('quantity') ?></th>
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
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	var paramsGeneralProduction = {'products': '#products', 'start_date': '#start_date', 'end_date': '#end_date'};
	$(document).ready(function() {
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        init_datepicker();

		oTableGeneralProduction = tnhDatatable(
            '#tb-general-production',
            {
                'order': [[1, 'asc'], [2, 'asc']],
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
                        title: '<?= lang('report_general_production') ?>',
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
                    //     title: '<?= lang('report_general_production') ?>',
                    //     extend: 'pdfHtml5',
                    //     footer: true,
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_general_production') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports_tnh/getGeneralProduction') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsGeneralProduction) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsGeneralProduction[key]).val()
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
                    {"targets": 1, "name": 'reference_order'},
                    {"targets": 2, "name": 'note_orders'},
                    {"targets": 3, "name": 'item_code'},
                    {"targets": 4, "name": 'item_name'},
                    {"targets": 5, "name": 'unit_name', 'class': 'text-center'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 6, "name": 'quantity', 'class': 'text-center'
                    },
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var quantity = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        quantity+= intVal(aaData[i][6]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[3].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(quantity)+'</div>';
                }
            }
        );

        $('#tb-general-production_wrapper .btn-dt-reload').click(function(event) {
            oTableGeneralProduction.draw();
        });

        $('#products, #productions_orders, #start_date, #end_date').change(function(event) {
            event.preventDefault();
            oTableGeneralProduction.draw();
        });
	});
</script>