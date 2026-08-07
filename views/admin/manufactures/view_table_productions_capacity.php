<table id="table-plan" class="dt-tnh table table-hover dont-responsive-table" style="width: 100%;">
	<thead>
		<tr>
			<th><?= lang('tnh_numbers') ?></th>
			<th><?= lang('tnh_product_code') ?></th>
			<th><?= lang('tnh_product_name') ?></th>
			<th><?= lang('unit') ?></th>
			<th><?= lang('tnh_safe_inventory') ?></th>
			<th><?= lang('tnh_quantity_warehouses') ?></th>
            <th><?= lang('tnh_quantity_plan') ?></th>
            <th><?= lang('tnh_quantity_reserve') ?></th>
            <th><?= lang('tnh_quantity_productions') ?></th>
			<th><?= lang('tnh_number_labor_minimum') ?></th>
            <th><?= lang('sub') ?></th>
			<th><?= lang('st') ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	var oTable = '';
    var ii = 10;
    var ij = 11;
    var arr = [];
	function format ( d ) {
        versions = '';
        if (typeof d[ii][0] != 'undefined') {
            versions = '('+d[ii][0]['versions']+')';
        }
	    tr1 = '<tr><td colspan="9" class="text-center bold"><?= lang('BOM') ?> '+versions+'</td></tr>'+
                '<tr class="bold color-dark-green">'+
    	            '<td><?= lang('tnh_numbers') ?></td>'+
    	            '<td><?= lang('code') ?></td>'+
    	            '<td><?= lang('name') ?></td>'+
    	            '<td><?= lang('unit') ?></td>'+
    	            '<td><?= lang('type') ?></td>'+
                    '<td><?= lang('tnh_quantity_use') ?></td>'+
    	        '</tr>';

	    tr = '';
	    if (d[ii]) {
	    	element = d[ii];
	    	$.each(element, function(index, el) {
	    		
		        if (el['sub_items']) {
		        	$.each(el['sub_items'], function(i, e) {
		        		tr1+= '<tr>'+
				            '<td>'+e.number_records+'</td>'+
				            '<td>'+e.code+'</td>'+
				            '<td>'+e.name+'</td>'+
				            '<td>'+e.unit+'</td>'+
                            '<td>'+e.type+'</td>'+
				            '<td class="text-center">'+tnhFormatNumber(e.quantity)+'</td>'+
				        '</tr>';

                        if (typeof e.replace != 'undefined')
                        {
                            $.each(e.replace, function(ir, re) {
                                tr1+= '<tr style="background: #ddd;">'+
                                    '<td>'+re.number_records+'</td>'+
                                    '<td>'+re.code+'</td>'+
                                    '<td>'+re.name+'</td>'+
                                    '<td>'+re.unit+'</td>'+
                                    '<td>'+re.type+'</td>'+
                                    '<td class="text-left"><?= lang('tnh_material_replace') ?></td>'+
                                '</tr>';
                            });
                        }
		        	});
		        }
	    	});
	    }
	    tableBOM = '<table class="dt-table tnh-table table-bordered" style="width: 92% !important; float: right;">'+
			        tr1+
			    '</table>';

        //stages
        versions = '';
        if (typeof d[ij][0] != 'undefined') {
            versions = '('+d[ij][0]['versions']+')';
        }
        tr_stage = '<tr><td colspan="9" class="text-center bold"><?= lang('stages') ?> '+versions+'</td></tr>'+
                '<tr class="bold color-dark-green">'+
                    '<td style="width: 80px;"><?= lang('tnh_numbers') ?></td>'+
                    '<td><?= lang('tnh_stage_name') ?></td>'+
                    '<td><?= lang('tnh_machines') ?></td>'+
                    '<td><?= lang('tnh_number_hours') ?></td>'+
                '</tr>';

        if (d[ij]) {
            element = d[ij];
            $.each(element, function(index, el) {
                tr_stage+= '<tr>'+
                    '<td>'+el.number_records+'</td>'+
                    '<td>'+el.stage_name+'</td>'+
                    '<td>'+el.machine_name+'</td>'+
                    '<td class="text-center">'+tnhFormatNumber(el.number_hours)+'</td>'+
                '</tr>';
            });
        }

        tableStages = '<table class="dt-table tnh-table table-bordered" style="width: 92% !important; float: right;     margin-top: 10px !important;">'+
                    tr_stage+
                '</table>';

        table = tableBOM+''+tableStages;
	    return table;
	}

    function statistical(data)
    {
    }

	$(document).ready(function() {
		oTable = tnhDatatable(
            '#table-plan',
            {
                'order': [[2, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: "350px",
                scrollY: true,
                scrollX: true,
                // 'searching': false,
                'ordering': false,
                // 'paging': false,
                "info": false,
                // fixedColumns:   {
                //     leftColumns: 4,
                // },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getTableProductionsCapacity') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    aoData.push({
                        "name": "arr_productions_plan_id",
                        "value": <?= json_encode($arr_productions_plan_id) ?>
                    });
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': function(data) {
                            fnCallback(data);
                            statistical(data);
                        }});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    // mainWrapperHeightFix();
                },
                "columnDefs": [
                    {
                    	"render": function(data) {
                    		return '<input type="hidden" name="records" id="records" class="form-control records" value="'+data+'"><div style="padding-left: 30px;">'+data+'</div>';
                    	},
                    	"targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center details-control'
                    },
                    {"targets": 1, "name": 'code', 'width': '150px'},
                    {"targets": 2, "name": 'name', 'width': '150px'},
                    {"targets": 3, "name": 'unit', 'width': '80px', 'className': 'text-center'},
                    {
                    	"render": function(data) {
                    		return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                    	},
                    	"targets": 4, "name": 'quantity_minimum', 'width': '80px', 'className': 'text-center'
                    },
                    {
                    	"render": function(data) {
                    		return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                    	},
                    	"targets": 5, "name": 'quantity_warehouses', 'width': '80px', 'className': 'text-center'
                    },
                    {
                    	"render": function(data) {
                    		return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                    	},
                    	"targets": 6, "name": 'quantity_use', 'width': '80px', 'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 7, "name": 'quantity_reserve', 'width': '80px', 'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 8, "name": 'quantity_productions', 'width': '80px', 'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+data+'</div>'
                        },
                        "targets": 9, "name": 'number_labor', 'width': '80px', 'className': 'text-center'
                    },
                    {"targets": ii, "name": 'sub', 'width': '150px', 'visible': false, 'searchable': false},
                    {"targets": ij, "name": 'st', 'width': '150px', 'visible': false, 'searchable': false},
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                }
            }
        );

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw('page');
        });

        $('#table-plan').on('draw.dt', function(e, settings) {
            if (arr.length > 0) {
                $.each(arr, function(index, el) {
                    $('input[name="records"][value="'+ el +'"]').closest('td').trigger('click');
                });
            }
            // reloadDataTableFullScreen();
        })

        $('#table-plan tbody').on('click', 'td.details-control', function () {
	        var tr = $(this).closest('tr');
            var records = tr.find('#records').val();
	        var row = oTable.row( tr );

	        if ( row.child.isShown() ) {
	            arr = removeArray(arr, records);
	            row.child.hide();
	            tr.removeClass('shown');
	        }
	        else {
	            if (!arr.includes(records)) {
                    arr.push(records);
                }
	            row.child( format(row.data()) ).show();
	            tr.addClass('shown');
	        }
	    } );
	});
</script>