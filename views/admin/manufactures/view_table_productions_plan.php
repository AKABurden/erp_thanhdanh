<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<div class="table-responsive" style="margin-bottom: 25px;">
    <table class="table table-bordered table-hover dont-responsive-table">
        <tbody>
            <tr class="success">
                <td style="width: 180px;"><?= lang('tnh_sales_orders') ?></td>
                <td><div style="word-wrap: break-word;"><?= $sales_orders ?></div></td>
            </tr>
            <tr class="danger">
                <td><?= lang('tnh_business_plan') ?></td>
                <td><div style="word-wrap: break-word;"><?= $business_plan ?></div></td>
            </tr>
        </tbody>
    </table>
	<input type="hidden" name="" id="condition_safe_inventory" class="form-control" value="<?= $safe_inventory ?>">
    <input type="hidden" name="" id="condition_options1" class="form-control" value="<?= $options1 ?>">
	<input type="hidden" name="" id="condition_options2" class="form-control" value="<?= $options2 ?>">
    <input type="hidden" name="" id="condition_planning_cycle" class="form-control" value='<?= $planning_cycle ?>'>
    <input type="hidden" name="" id="condition_orders" class="form-control" value='<?= $orders ?>'>
	<input type="hidden" name="" id="condition_business_plans" class="form-control" value='<?= $business_plans ?>'>
	<table id="table-plan" class="dt-tnh table table-bordered table-hover" style="width: 100%;">
		<thead>
			<tr>
				<th><?= lang('tnh_numbers') ?></th>
				<th><?= lang('tnh_product_code') ?></th>
				<th><?= lang('tnh_product_name') ?></th>
				<th><?= lang('unit') ?></th>
				<th><?= lang('tnh_safe_inventory') ?></th>
				<th><?= lang('tnh_quantity_warehouses') ?></th>
				<?= $th ?>
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
	var oTable = '';
	var fnserverparams = {'condition_safe_inventory': '#condition_safe_inventory', 'condition_planning_cycle': '#condition_planning_cycle', 'condition_options1': '#condition_options1', 'condition_options2': '#condition_options2', 'condition_orders': '#condition_orders', 'condition_business_plans': '#condition_business_plans'};
	$(document).ready(function() {
		oTable = tnhDatatable(
            '#table-plan',
            {
                'order': [[2, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                scrollY: "450px",
                scrollX: true,
                fixedColumns:   {
                    leftColumns: 4,
                },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getShowTableProductionsPlan') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    // mainWrapperHeightFix();
                },
                "columnDefs": [
                    {"targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center'},
                    {"targets": 1, "name": 'product_code', 'width': '150px'},
                    {"targets": 2, "name": 'product_name', 'width': '150px'},
                    {"targets": 3, "name": 'unit', 'width': '80px', 'className': 'text-center'},
                    {
                    	"render": function(data) {
                    		return '<div class="text-center">'+data+'</div>'
                    	},
                    	"targets": 4, "name": 'quantity_minimum', 'width': '80px'
                    },
                    {
                    	"render": function(data) {
                    		return '<div class="text-center">'+data+'</div>'
                    	},
                    	"targets": 5, "name": 'quantity_warehouses', 'width': '80px'
                    },
                    <?= $script ?>
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    start = 6;
                    if (!aaData) return;
                    if (!aaData[0]) return;
                    end = aaData[0].length;
                    arr_id = [];
                    arr = [];
                    for (var i = 0; i < aaData.length; i++) {
                        for (j = start; j < end; j++)
                        {
                            if (typeof arr[j] == "undefined")
                            {
                                arr[j] = 0;
                            }
                            if (isNaN(parseFloat(aaData[aiDisplay[i]][j])))
                            {
                                total = 0;
                            } else {
                                total = parseFloat(aaData[aiDisplay[i]][j]);
                            }
                            arr[j] = arr[j] + total;
                        }
                    }
                    for (var i = start; i < arr.length; i++) {
                        if (arr[i] == 0) {
                            if (arr_id.indexOf(i) == -1) {
                                arr_id.push(i);
                            }
                        }
                    }
                    oTable.columns(arr_id).visible(false, false);
                }
            }
        );

        $('#table-plan').on( 'page.dt', function () {
            console.log('page');
            var info = oTable.page.info();
            oTable.columns(arr_id).visible(true, true);
            setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $('#table-plan_filter input').change(function () {
            console.log('search');
            oTable.columns(arr_id).visible(true, true);
            setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $('select[name="table-plan_length"]').change(function(event) {
            console.log('page-length');
            oTable.columns(arr_id).visible(true, true);
            setTimeout(function(){ oTable.columns.adjust().draw(false); }, 2000);
        });

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });
	});
</script>