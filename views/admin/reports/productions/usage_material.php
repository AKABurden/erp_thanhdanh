<div class="text-center uppercase">
    <h2><?= lang('report_the_usage_material') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <?= lang('materials', 'materials_search') ?>
        <input type="text" name="materials_search" autocomplete="off" placeholder="<?= lang('materials') ?>" id="materials_search" class="materials_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('start_date', 'start_date_search') ?>
        <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('end_date', 'end_date_search') ?>
        <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
</div>
<div class="table-responsive">
	<table id="tb-usage-material" class="tnh-table table table-hover table-condensed" style="width: 100%;">
		<thead>
			<tr>
                <th rowspan="2"><?= lang('tnh_numbers') ?></th>
                <th rowspan="2"><?= lang('tnh_orders_and_business_plan') ?></th>
                <th rowspan="2"><?= lang('tnh_note_orders') ?></th>
				<th rowspan="2"><?= lang('tnh_reference_productions_orders_details') ?></th>
				<th rowspan="2"><?= lang('date') ?></th>
                <th rowspan="2"><?= lang('tnh_material_code') ?></th>
				<th rowspan="2"><?= lang('tnh_material_name') ?></th>
				<th rowspan="2"><?= lang('tnh_type') ?></th>
				<th rowspan="2"><?= lang('unit') ?></th>
				<th colspan="3" rowspan="1" class="text-center"><?= lang('quantity') ?></th>
			</tr>
            <tr>
                <th><?= lang('tnh_output') ?></th>
                <th><?= lang('tnh_reenter') ?></th>
                <th><?= lang('tnh_used') ?></th>
            </tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var paramsUsageMateral = {"materials_search": "#materials_search", "start_date_search": "#start_date_search", "end_date_search": "#end_date_search"};
	$(document).ready(function() {
        ajaxSelectMultipleParams($('#materials_search'), 'admin/reports_tnh/searchItemsCustom', 0, false, true);
        init_datepicker();
		oTableUsageMaterial = tnhDatatable(
            '#tb-usage-material',
            {
                'order': [[1, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                'sort': false,
                // "dom": 'Blpfrti',
                'dom': "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i><'#colvis'><'.dt-page-jump'>p>",
                buttons: [
                    // 'copy', 'excel', 'csv', 'pdf',
                    {
                        text: 'Excel',
                        title: '<?= lang('report_the_usage_material') ?>',
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
                    //     title: '<?= lang('report_the_usage_material') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_the_usage_material') ?>',
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
                rowsGroup: [
                    0, 1, 2, 3, 4
                ],
                // 'rowsGroup': [0],
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
                'sAjaxSource': '<?= site_url('admin/reports_tnh/getUsageMaterial') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsUsageMateral) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsUsageMateral[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    // typeCustom = aData[6];
                    // if (typeCustom == "products") {
                    //     nRow.className = "bold";
                    //     nRow.style = "background-color: #80808099";
                    // }
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
                	{"targets": 0, "name": 'id', 'width': '50px', 'class': 'text-center', 'sortable': false},
                	{"targets": 1, "name": 'reference_order', 'width': '100px', 'sortable': false},
                	{"targets": 2, "name": 'note_orders', 'width': '100px', 'sortable': false},
                	{"targets": 3, "name": 'reference_no', 'width': '120px', 'sortable': false},
                	{
                        "render": function(data, type, row) {
                            if (data) {
                                return fld(data);
                            }
                            return '';
                        },
                        "targets": 4, "name": 'date', 'width': '80px', 'searchable': false, 'sortable': false
                    },
                    {"targets": 5, "name": 'item_code', 'width': '100px', 'sortable': false},
                    {"targets": 6, "name": 'item_name', 'width': '100px', 'sortable': false},
                    {
                        "render": function(data, type, row) {
                            var str = '';
                            if (data == 'semi_products') {
                                str = '<?= lang('semi_products') ?>'
                            } else if (data == 'products') {
                                str = '<?= lang('products') ?>'
                            } else if (data == "element") {
                                str = '<?= lang('tnh_element') ?>';
                            } else if (data == "materials") {
                                str = '<?= lang('materials') ?>';
                            } else if (data == "semi_products_outside") {
                                str = '<?= lang('semi_products_outside') ?>';
                            } else if (data == "tools_supplies") {
                                str = '<?= lang('tnh_tools_supplies') ?>';
                            }
                            return str;
                        },
                        "targets": 7, "name": 'type_item', 'sortable': false
                    },
                    {"targets": 8, "name": 'unit_name'},
                	{
                		"render": function(data, type, row) {
                            // if (data == '') {
                            //     return '';
                            // }
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                		"targets": 9, "name": 'quantity', 'sortable': false
                	},
                    {
                        "render": function(data, type, row) {
                            // if (data == '') {
                            //     return '';
                            // }
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 10, "name": 'quantity', 'sortable': false
                    },
                    {
                        "render": function(data, type, row) {
                            // if (data == '') {
                            //     return '';
                            // }
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 11, "name": 'quantity', 'sortable': false
                    },
                	// {"targets": 6, "name": 'type_hide', 'visible': false},
                ]
            }
        );

        $('#tb-usage-material_wrapper .btn-dt-reload').click(function(event) {
            oTableUsageMaterial.draw();
        });

        $('#materials_search, #start_date_search, #end_date_search').change(function(event) {
            event.preventDefault();
            oTableUsageMaterial.draw();
        });
	});
</script>