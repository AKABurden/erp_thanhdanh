<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('exporting_producion') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                        $print = '<a href="'.base_url('admin/stock/print_exporting_production/'.$suggest_exporting['id']).'" target="_blank"><i class="fa fa-print"></i> '.lang('print').'</a>';
                        $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            '.lang('actions').'
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>'.$print.'</li>
                            </ul>
                        </div>';
                        echo $actions;
                    ?>
                </div>
				<div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('date') ?>: </div>
                            <div class="ml-at t-bold"><?= _dt($suggest_exporting['date_convert_stock']) ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_stock') ?>: </div>
                            <div class="ml-at t-bold"><?= $suggest_exporting['reference_stock'] ?></div>
                        </div>
                        <!-- <div class="row-contro">
                            <div><?= ''//lang('tnh_reference_no_suggest') ?>: </div>
                            <div class="ml-at t-bold"><?= ''//$sug['reference_no'] ?></div>
                        </div> -->
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_productions_orders_details') ?>: </div>
                            <div class="ml-at t-bold"><?= (!empty($pod) ? $pod['reference_no'] : '') ?></div>
                        </div>
                        <div class="row-contro">
                            <div><?= lang('tnh_reference_productions_orders') ?>: </div>
                            <div class="ml-at t-bold"><?= (!empty($po) ? $po['reference_no'] : '') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                	<div class="lead-view" id="leadViewWrapper">
                        <div class="row-contro">
                            <div><?= lang('tnh_export_name') ?>: </div>
                            <div class="ml-at t-bold"><?= $suggest_exporting['export_name'] ?></div>
                        </div>
                        <div class="row-contro hide">
                            <div><?= lang('tnh_warehouses') ?>: </div>
                            <div class="ml-at t-bold"><?= $warehouse['name'] ?></div>
                        </div>
                		<div class="row-contro">
                            <div><?= lang('note') ?>: </div>
                            <div class="ml-at t-bold"><?= $suggest_exporting['note'] ?></div>
                        </div>
                	</div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                        <label for="tab1"><i class="icon-foso fal fa-info-circle"></i><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                        <label for="tab5"><i class="icon-foso fal fa-history"></i><?= lang('activity_log_puchases') ?></label>

                        <div class="tab-panels">
                            <section id="view-items" class="tab-panel">
                            	<div class="table-responsive">
                            		<table id="table-items" class="table dt-tnh table-hover table-condensed">
                            			<thead>
                            				<tr>
                            					<th class="text-center"><?= lang('tnh_numbers') ?></th>
                            					<th><?= lang('tnh_material_code') ?></th>
            									<th><?= lang('tnh_material_name') ?></th>
                                                <th class="text-center"><?= lang('tnh_unit') ?></th>
            									<th class="" style="width: 150px;"><?= lang('tnh_location_warehouse') ?></th>
            									<th class="text-center"><?= lang('tnh_quantity_export') ?></th>
            									<!-- <th class="text-center"><?= lang('tnh_value_exchange') ?></th> -->
                                                <th class="text-center"><?= lang('tnh_quantity_exchange') ?>(DV chuẩn)</th>
                                                <th class="text-center"><?= lang('tnh_quantity_exchange') ?>(DV kho)</th>
            									<th class="text-center"><?= lang('tnh_total_amount') ?></th>
                            				</tr>
                            			</thead>
                            			<tbody>
                            				<?php foreach ($items as $key => $value): ?>
                                                <?php 
                                                    $location = str_replace('->', '<i class="fa fa-caret-right text-danger" aria-hidden="true"></i>', recursiveLocations($value['location_id']));
                                                    $warehouse = $this->stock_model->rowWarehouse($value['warehouse_item_id']);
                                                    if ($value['type_item'] == 'materials') {
                                                        $info = $this->items_model->rowMaterial($value['item_id']);
                                                    } else {
                                                        $info = $this->products_model->rowProduct($value['item_id']);
                                                    }
                                                ?>
                            					<tr>
                            						<td class="text-center"><?= ++$key ?></td>
                            						<td><?= $info['code'] ?></td>
                            						<td><?= $info['name'] ?></td>
                            						<td class="text-center"><?= $value['unit_name'] ?></td>
                                                    <td class="">
                                                        <?= $warehouse['name'] ?> -> <?= $location ?>
                                                        <?php
                                                            $lot_code = $value['lot_code'] ? ' <div>- Lot: '.$value['lot_code'].'</div>' : '';
                                                            $date_sx = $value['date_sx'] ? ' <div>- Ngày SX: '._d($value['date_sx']).'</div>' : '';
                                                            $date_sd = $value['date_sd'] ? ' <div>- Ngày SD: '._d($value['date_sd']).'</div>' : '';

                                                            echo $lot_code.$date_sx.$date_sd;
                                                        ?>
                                                    </td>
                            						<td class="text-center"><?= formatNumber($value['quantity_export']) ?></td>
                            						<!-- <td class="text-center"><?= formatNumber($value['number_exchange']) ?></td> -->
                                                    <td class="text-center"><?= formatNumber($value['quantity_exchange']) ?> <?= $value['unit_name_parent'] ?></td>
                                                    <td class="text-center"><?= formatNumber($value['quantity_warehouse']) ?> <?= $value['unit_name_warehouse'] ?></td>
                            						<td class="text-center"><?= formatNumber($value['amount']) ?></td>
                            					</tr>
                            				<?php endforeach ?>
                            			</tbody>
                            			<tfoot>
                            				<tr>
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
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($suggest_exporting['id'], 'exporting_producion');
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
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($suggest_exporting['date_convert_stock']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($suggest_exporting['date_updated']) ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
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
            // "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            // scrollY: true,
            // scrollX: true,
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                pageQuantityExport = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageQuantityExchange = api
                    .column( 7, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageTotalAmount = api
                    .column( 8, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                $( api.column( 5 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageQuantityExport)+'</div>');
                $( api.column( 8 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageTotalAmount)+'</div>');
            }
		});
	});
</script>