<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('view') ?></h4>
		</div>
		<div class="modal-body">
            <div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                        $print = '<a href="'.base_url('admin/releases/print_export_warehouse/'.$export_warehouses['id']).'" target="_blank"><i class="fa fa-print"></i> '.lang('print').' '.lang('tnh_export_warehouses').'</a>';

                        $delete = '<a type="button" class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\''.base_url('admin/releases/deleteExportWarehouses/'.$export_warehouses['id']).'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
                        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').' '.lang('deliveries').'</a>';

                        $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            '.lang('actions').'
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>'.$print.'<li>
                                <li class="not-outside">'.$delete.'</li>
                            </ul>
                        </div>';
                        if ($this->input->get('view') != 'seen')
                        {
                            echo $actions;
                        }
                    ?>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($export_warehouses['date']) ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_export_warehouses') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= ($export_warehouses['reference_no']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_deliveries') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $delivery['reference_no'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('customers') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $delivery['customer_name'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('status') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $export_warehouses['status'] == "un_approved" ? lang('tnh_un_approved_ws_stock') : lang('tnh_approved_ws_stock') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <?php if ($this->input->post('view') != 'seen'): ?>
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><?= lang('activity_log_puchases') ?></label>
                        <?php endif ?>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                                <table id="table-items" class="table table-bordered table-hover dont-responsive-table" style="max-height: 400px !important; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></th>
                                            <th style="width: 70px;"><?= lang('tnh_images') ?></th>
                                            <th style="width: 50px;"><?= lang('tnh_type') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_product_code') ?></th>
                                            <th style="width: 100px;"><?= lang('tnh_product_name') ?></th>
                                            <th style="width: 60px;"><?= lang('tnh_warehouses') ?></th>
                                            <th style="width: 60px;"><?= lang('tnh_location_warehouse') ?></th>
                                            <th><?= lang('unit') ?></th>
                                            <th class="text-center"><?= lang('quantity') ?></th>
                                            <th><?= lang('note') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $bodyItems ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-center bold" style="text-transform: uppercase;"><?= lang('tnh_grand_total') ?></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </section>
                            <?php if ($this->input->get('view') != 'seen'): ?>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($export_warehouses['id'], 'export_warehouses');
                                    ?>
                                    <?php if (!empty($history)): ?>
                                        <?php foreach ($history as $key => $value): ?>
                                            <?php
                                                echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        '.staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small').''.$value['staff_name'].'
                                                    </div>
                                                    <div class="activity-time">
                                                        '.time_ago($value['date']).'<span class="activity-module">'._l($value['type_parent_obj']).'</span>
                                                    </div>
                                                    <div>
                                                        '.$value['content'].'
                                                    </div>
                                                </div>';
                                            ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                            </section>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($export_warehouses['date_created']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var dtItems = $('#table-items').DataTable({
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            scrollY: true,
            scrollX: true,
            // fixedColumns:   {
            //     leftColumns: 3,
            //     rightColumns: 0
            // },
            // 'searching': false,
            // 'ordering': false,
            // 'paging': false,
            // "info": false,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                pageTotalQuantity = api
                    .column( 8, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                $( api.column( 8 ).footer() ).html('<div class="text-center bold">'+tnhFormatNumber(pageTotalQuantity)+'</div>');
            }
        });
    });
</script>