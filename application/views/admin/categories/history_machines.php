<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_history_machines') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-3">
					<input type="text" name="start_date" id="start_date" class="form-control datepicker" value="" placeholder="<?= lang('start_date') ?>">
				</div>
				<div class="col-md-3">
					<input type="text" name="end_date" id="end_date" class="form-control datepicker" value="" placeholder="<?= lang('start_end') ?>">
				</div>
				<input type="hidden" name="machine_id" id="machine_id" class="form-control" value="<?= $id ?>">
				<div class="col-md-12 mtop10">
					<table id="table-history-machines" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th><?= lang('date') ?></th>
								<th><?= lang('tnh_productions') ?></th>
								<th><?= lang('tnh_products') ?></th>
								<th><?= lang('tnh_machines') ?></th>
								<th><?= lang('tnh_time_used') ?></th>
								<th><?= lang('tnh_time_rest') ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="99"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	var oTable_machine = '';
	var fnserverparams_history = {'machine_id': '#machine_id', "start_date": '#start_date', "end_date": '#end_date'};
	$(document).ready(function() {
		oTable_machine = tnhDatatable(
            '#table-history-machines',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/categories/getHistoryMachines') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams_history) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams_history[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    var $btnReload = $('.btn-dt-reload');
                    $btnReload.attr('data-toggle', 'tooltip');
                    $btnReload.attr('title', app.lang.dt_button_reload);

                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 0,
                        "name": 'date',
                        'searchable': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 1,
                        "name": 'production'
                    },
                    {"targets": 2, "name": 'product_name'},
                    {"targets": 3, "name": 'machine_name'},
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 4, "name": 'time_used'
                    },
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 5, "name": 'time_rest'
                    },
                ]
            }
        );

        $('#start_date, #end_date').change(function(event) {
        	oTable_machine.draw();
        });

        init_datepicker();
		$('#table-machines_wrapper .btn-dt-reload').hide();
		$($('#table-machines_wrapper .btn-dt-reload')[0]).show();
	});
</script>