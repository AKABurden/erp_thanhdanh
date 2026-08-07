<div class="text-center uppercase">
    <h2><?= lang('report_production_detailed') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <b><?= lang('tnh_reference_purchase_products') ?></b>
        <input type="text" name="purchase_products" id="purchase_products" style="width: 100%;" data-placeholder="<?= lang('tnh_reference_purchase_products') ?>" value="">
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
	<table id="tb-production-detailed" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
                <th class="hide"><?= lang('tnh_numbers') ?></th>
                <th><?= lang('tnh_orders_and_business_plan') ?></th>
                <th><?= lang('tnh_note_orders') ?></th>
                <th><?= lang('tnh_reference_purchase_products') ?></th>
				<th><?= lang('tnh_date_enter') ?></th>
				<th><?= lang('tnh_product_code') ?></th>
				<th><?= lang('tnh_product_name') ?></th>
				<th><?= lang('unit') ?></th>
				<th><?= lang('quantity') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td></td>
			</tr>
		</tbody>
        <tfoot>
            <tr class="bold uppercase">
                <th class="hide"></th>
                <th colspan="6" class="text-center"><?= lang('tnh_grand_total') ?></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
	</table>
</div>

<script type="text/javascript">
	var paramsProductionDetailed = {'products': '#products', 'purchase_products': '#purchase_products', 'start_date': '#start_date', 'end_date': '#end_date'};
	$(document).ready(function() {
        ajaxSelectMultipleParams('#products', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams('#purchase_products', 'admin/stock/searchPurchaseProduct', 0, false, true);

        init_datepicker();
		oTableProductionDetailed = tnhDatatable(
            '#tb-production-detailed',
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
                        title: '<?= lang('report_production_detailed') ?>',
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
                    //     title: '<?= lang('report_production_detailed') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_production_detailed') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports_tnh/getProductionDetailed') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionDetailed) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionDetailed[key]).val()
                        });

                        //custom
                        if ($(paramsProductionDetailed[key]).data('select2') && $(paramsProductionDetailed[key]).val()) {
                            var array_data = $(paramsProductionDetailed[key]).select2('data');
                            if (array_data) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key+'_text_'+index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsProductionDetailed[key]).select2('data').text;
                                        aoData.push({
                                            "name": key+'_text',
                                            "value": $(paramsProductionDetailed[key]).select2('data').text
                                        });
                                    }
                                });
                            }
                        } else if ($(paramsProductionDetailed[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsProductionDetailed[key]).find('option:selected').text();
                            if (selectedText) {
                                // aoData[key+'_text'] = selectedText;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": selectedText
                                });
                            }
                        }
                    }
                    // $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': function(response) {
                        $('#tb-production-detailed').attr('title_excel', JSON.stringify(response?.title_excel));
                        fnCallback(response);
                    }});
                },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    typeCustom = aData[6];
                    if (typeCustom == "products") {
                        nRow.className = "bold";
                        nRow.style = "background-color: #80808099";
                    }
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
                	{"targets": 3, "name": 'reference_no'},
                	{
                        "render": function(data, type, row) {
                            if (!data) return '';
                            return '<div>'+fld(data)+'</div>';
                        },
                        "targets": 4, "name": 'date', 'searchable': false
                    },
                    {"targets": 5, "name": 'item_code'},
                    {"targets": 6, "name": 'item_name'},
                    {"targets": 7, "name": 'unit_name', 'class': 'text-center'},
                	{
                        "render": function(data, type, row) {
                            if (data == '') {
                                return '';
                            }
                			return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                		},
                		"targets": 8, "name": 'quantity', 'class': 'text-center'
                	},
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total+= intVal(aaData[i][8]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[2].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(grand_total)+'</div>';
                }
            },
            1
        );

        $('#tb-production-detailed_wrapper .btn-dt-reload').click(function(event) {
            // oTableProductionDetailed.draw();
        });

        $('#products, #end_date, #start_date, #purchase_products').change(function(event) {
            event.preventDefault();
            oTableProductionDetailed.draw();
        });
	});
</script>