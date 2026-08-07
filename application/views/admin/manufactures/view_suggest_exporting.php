<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('view') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
                <div class="col-md-12 mbot10">
                    <?php
                        $stock = $this->perAddListSuggestExporting ? '<a class="tnh-modal tnh-stock" title="'.lang('view').'" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="'.base_url('admin/manufactures/convert_stock/'.$suggest_exporting['id']).'"><i class="fa fa-exchange width-icon-actions"></i> '.lang('tnh_convert_to_export_stock').'</a>' : '';

                        $edit = $this->perEditListSuggestExporting ? '<a class="tnh-edit" title="'.lang('edit').'" href="'.base_url('admin/manufactures/edit_exporting_production/'.$suggest_exporting['id']).'"><i class="fa fa-edit width-icon-actions"></i> '.lang('edit').'</a>' : '';

                        $delete = $this->perDeleteListSuggestExporting ? '<a type="button" class="po tnh-delete" title="'.lang('delete').'" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                            <button href=\''.base_url('admin/manufactures/delete_suggest_exporting/'.$suggest_exporting['id'].'/3').'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
                            <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                        "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').'</a>' : '';

                        $print = '<a href="'.base_url('admin/manufactures/print_suggest_exporting/'.$suggest_exporting['id']).'" target="_blank"><i class="fa fa-print"></i> '.lang('print').'</a>';

                        $actions = '
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                            '.lang('actions').'
                            <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                <li>'.$edit.'</li>
                                <li class="">'.$stock.'</li>
                                <li class="">'.$print.'</li>
                            </ul>
                        </div>';
                        echo $actions;
                    ?>
                </div>
				<div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                    	<div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= _dt($suggest_exporting['date']) ?></span>
                        </div>
                        <div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_no_suggest') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $suggest_exporting['reference_no'] ?></span>
                        </div>
                        <div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_export_name') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $suggest_exporting['export_name'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="lead-view" id="leadViewWrapper">
                		<div class="wap-content firt">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_products') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $pod_order_item['items_code'] ?> (<?= $pod_order_item['items_name'] ?>)</span>
                        </div>
                	</div>
                	<div class="lead-view" id="leadViewWrapper">
                		<div class="wap-content second">
                            <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                            <span class="bold font-medium-xs lead-name"><?= $suggest_exporting['note'] ?></span>
                        </div>
                	</div>
                </div>
                <div class="col-md-12 mtop10">
                    <div class="tabset">
                        <!-- Tab 1 -->
                        <input type="radio" name="tabset" id="tab-item-1" aria-controls="view-items" checked>
                        <label for="tab-item-1"><?= lang('tnh_items') ?></label>
                        <!-- Tab 5 -->
                        <input type="radio" name="tabset" id="tab-item-5" aria-controls="view-activity-log">
                        <label for="tab-item-5"><?= lang('activity_log_puchases') ?></label>

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
            									<th class="text-center"><?= lang('tnh_quantity_export') ?></th>
            									<th class="text-center"><?= lang('tnh_value_exchange') ?></th>
            									<th class="text-center"><?= lang('tnh_quantity_exchange') ?></th>
                            				</tr>
                            			</thead>
                            			<tbody>
                            				<?php foreach ($items as $key => $value): ?>
                            					<tr>
                            						<td class="text-center"><?= ++$key ?></td>
                            						<td>
                                                        <?= $value['item_code'] ?>
                                                        <?php if ($value['type_item'] == 'semi_products_outside'): ?>
                                                            <div style="margin-bottom: 5px;"></div>
                                                            <div class="label label-danger" style="margin-top: 5px;"><?= lang('semi_products_outside') ?></div>
                                                        <?php endif ?>
                                                        <?php if ($value['type_item'] == 'semi_products'): ?>
                                                            <div style="margin-bottom: 5px;"></div>
                                                            <div class="label label-warning" style="margin-top: 5px;"><?= lang('semi_products') ?></div>
                                                        <?php endif ?>
                                                    </td>
                            						<td><?= $value['item_name'] ?></td>
                            						<td class="text-center"><?= $value['unit_name'] ?></td>
                            						<td class="text-center"><?= formatNumber($value['quantity_export']) ?></td>
                            						<td class="text-center"><?= formatNumber($value['number_exchange']) ?></td>
                            						<td class="text-center"><?= formatNumber($value['quantity_exchange']) ?> <?= $value['unit_name_parent'] ?></td>
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
                            				</tr>
                            			</tfoot>
                            		</table>
                            	</div>
                            </section>
                            <section id="view-activity-log" class="tab-panel">
                                <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                    <?php
                                        $history = getActivityLogByObjId($suggest_exporting['id'], 'suggest_exporting');
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
                <div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($suggest_exporting['date_created']) ?></div>
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
            "pageLength": intVal(app.options.tables_pagination_limit),
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
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
                    .column( 4, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                pageQuantityExchange = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                $( api.column( 4 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageQuantityExport)+'</div>');
                // $( api.column( 6 ).footer() ).html('<div class="text-center">'+tnhFormatNumber(pageQuantityExchange)+'</div>');
            }
		});
	});
</script>