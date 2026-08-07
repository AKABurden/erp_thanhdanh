<div class="text-center uppercase">
    <h2><?= lang('report_of_material_norms') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-4">
        <b><?= lang('products') ?></b>
        <input type="text" name="products" id="products" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
    </div>
    <div class="col-md-4">
        <b><?= lang('tnh_versions') ?></b>
        <input type="text" name="versions" id="versions" class="form-control" placeholder="<?= lang('tnh_versions') ?>" value="">
    </div>
</div>
<div class="table-responsive">
	<table id="tb-material-norms" class="table table-hover table-bordered table-condensed" style="width: 100%;">
		<thead>
			<tr>
				<th><?= lang('tnh_numbers') ?></th>
				<th><?= lang('Phiên bản BOM') ?></th>
				<th><?= lang('tnh_material_code') ?></th>
				<th><?= lang('tnh_material_name') ?></th>
				<th><?= lang('tnh_type') ?></th>
				<th><?= lang('unit') ?></th>
				<th><?= lang('tnh_landscape_print_size') ?></th>
				<th><?= lang('tnh_number_children_size') ?></th>
				<th><?= lang('tnh_exchange_value') ?></th>
				<th><?= lang('tnh_paper_exchange') ?></th>
				<th><?= lang('tnh_quantity_compensation') ?></th>
				<th><?= lang('tnh_stage') ?></th>
				<!-- <th><?= ''//lang('type_hide') ?></th> -->
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
	var paramsMateralNorms = {"products": "#products", "versions": "#versions"};
	$(document).ready(function() {
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);

		oTableMaterialNorms = tnhDatatable(
            '#tb-material-norms',
            {
                // 'order': [[2, 'asc']],
                'ordering': false,
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
                        title: '<?= lang('report_of_material_norms') ?>',
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
                    //     title: '<?= lang('report_of_material_norms') ?>',
                    //     // extend: 'excelHtml5',
                    //     extend: 'pdf',
                    //     exportOptions: {
                    //         columns: ':visible'
                    //     }
                    // },
                    // {
                    //     text: 'Print',
                    //     title: '<?= lang('report_of_material_norms') ?>',
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
                'sAjaxSource': '<?= site_url('admin/reports/getMaterialNorms') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsMateralNorms) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsMateralNorms[key]).val()
                        });

                        //custom
                        if ($(paramsMateralNorms[key]).data('select2') && $(paramsMateralNorms[key]).val()) {
                            var array_data = $(paramsMateralNorms[key]).select2('data');
                            if (array_data.length) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        // aoData[key+'_text_'+index] = item.text;
                                        aoData.push({
                                            "name": key+'_text_'+index,
                                            "value": item.text
                                        });
                                    } else {
                                        // aoData[key+'_text'] = $(paramsMateralNorms[key]).select2('data').text;
                                        aoData.push({
                                            "name": key+'_text',
                                            "value": $(paramsMateralNorms[key]).select2('data').text
                                        });
                                    }
                                });
                            } else {
                                // aoData[key+'_text'] = $(paramsMateralNorms[key]).select2('data').text;
                                aoData.push({
                                    "name": key+'_text',
                                    "value": $(paramsMateralNorms[key]).select2('data').text
                                });
                            }
                        } else if ($(paramsMateralNorms[key]).hasClass('selectpicker')) {
                            var selectedText = $(paramsMateralNorms[key]).find('option:selected').text();
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
                        $('#tb-material-norms').attr('title_excel', JSON.stringify(response?.title_excel));
                        fnCallback(response);
                    }});
                },
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                    $($('.group-products').closest('tr')).css({"font-weight": "600", "background-color": "rgba(247, 244, 155, 0.6)"});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    // typeCustom = aData[6];
                    // if (typeCustom == "products") {
                    //     nRow.className = "bold";
                    //     nRow.style = "background-color: rgba(247, 244, 155, 0.6);";
                    //     // nRow.style = "background-color: red";
                    // }
                    
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                    $($('.group-products').closest('tr')).css({"font-weight": "600", "background-color": "rgba(247, 244, 155, 0.6)"});
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                	{"targets": 0, "name": 'id', 'width': '50px', 'class': 'text-center', 'sortable': false},
                	{"targets": 1, "name": 'versions', 'sortable': false, 'searchable': false},
                	{"targets": 2, "name": 'code'},
                	{"targets": 3, "name": 'name'},
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
                            }
                			return str;
                		},
                		"targets": 4, "name": 'type', 'class': 'text-center'
                	},
                	{"targets": 5, "name": 'unit_name', 'class': 'text-center'},
                	{
                		"targets": 6, "name": 'quantity'
                	},
                	// {"targets": 6, "name": 'type_hide', 'visible': false},
                ]
            }, 1
        );

		/*$(document).on('click', '#tb-material-norms_wrapper .btn-dt-reload', function(event) {
            oTableMaterialNorms.draw();
        });

        $(document).on('change', '#products, #versions', function(event) {
            event.preventDefault();
            oTableMaterialNorms.draw();
        });*/

        $('#tb-material-norms_wrapper .btn-dt-reload').click(function(event) {
            // oTableMaterialNorms.draw();
        });

        $('#products, #versions').change(function(event) {
            event.preventDefault();
            oTableMaterialNorms.draw();
        });
	});
</script>