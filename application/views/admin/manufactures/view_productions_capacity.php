<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_view_productions_capacity') ?></h4>
		</div>
		<div class="modal-body">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#info" aria-controls="info" role="tab" data-toggle="tab"><?= lang('info') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#detail" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_detail') ?></a>
                    </li>
                    <li role="presentation">
                        <a href="#statistical" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tnh_statistical_purchases') ?> & Sản xuất</a>
                    </li>
                    <li role="presentation">
                        <a href="#purchases" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('tblpurchases') ?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info">
                        <div class="row">
                            <div class="col-md-12 mbot10">
                                <?php
                                    $convert_purchases = $this->perAddProductionsCapacity ? '<a class="tnh-modal convert-purchase" data-tnh="modal" data-toggle="modal" data-target="#myModal" title="'.lang('tnh_convert_purchases').'" target="_blank" href="'.base_url('admin/manufactures/capacity_convert_purchase/'.$productions_capacity['id']).'"><i class="fa fa-exchange width-icon-actions"></i> '.lang('tnh_convert_purchases').'</a>' : '';

                                    $addPurchase = $this->perAddProductionsCapacity ? '<a class="tnh-modal add-purchase" data-tnh="modal" data-toggle="modal" data-target="#myModal" title="'.lang('tnh_add_purchase').'" href="'.base_url('admin/manufactures/add_purchase/'.$productions_capacity['id']).'"><i class="fa fa-plus width-icon-actions"></i> '.lang('tnh_add_purchase').'</a>' : '';

                                    $delete = $this->perDeleteProductionsCapacity ? '<a type="button" class="po" title="'.lang('delete').'" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                                        <button href=\''.base_url('admin/manufactures/delete_productions_capacity/'.$productions_capacity['id']).'\' class=\'btn btn-danger po-delete-json\'>'.lang('delete').'</button>
                                        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                                    "><i class="fa fa-remove width-icon-actions"></i> '.lang('delete').'</a>' : '';

                                    $print = '<a href="'.base_url('admin/manufactures/print_productions_capacity/'.$productions_capacity['id']).'" target="_blank"><i class="fa fa-print"></i> '.lang('print').'</a>';

                                    $actions = '
                                    <div class="dropdown pull-right">
                                        <button class="btn btn-primary dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                        '.lang('actions').'
                                        <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1">
                                            <li>'.$convert_purchases.'</li>
                                            <li>'.$addPurchase.'</li>
                                            <li>'.$print.'</li>
                                            <li class="not-outside">'.$delete.'</li>
                                        </ul>
                                    </div>';
                                    echo $actions;
                                ?>
                            </div>
                            <div class="col-md-6">
                                <div class="lead-view" id="leadViewWrapper">
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('date') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= _dt($productions_capacity['date']) ?></span>
                                    </div>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_reference_productions_capacity') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= ($productions_capacity['reference_no']) ?></span>
                                    </div>
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('productions_capacity') ?>: </span>
                                        <span class="bold font-medium-xs lead-name" style="word-break: break-word;"><?= $productions_capacity['productions_plan_reference_no'] ?></span>
                                    </div>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_status') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= lang($productions_capacity['status']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="lead-view" id="leadViewWrapper">
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_user_agree') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= $user_status ?></span>
                                    </div>
                                    <div class="wap-content second">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('tnh_date_agree') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= _dt($productions_capacity['date_status']) ?></span>
                                    </div>
                                    <div class="wap-content firt">
                                        <span class="text-muted lead-field-heading no-mtop bold"><?= lang('note') ?>: </span>
                                        <span class="bold font-medium-xs lead-name"><?= $productions_capacity['note'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mtop10">
                                <div class="tabset">
                                    <!-- Tab 1 -->
                                    <input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
                                    <label for="tab1"><?= lang('tnh_items') ?></label>
                                    <!-- Tab 5 -->
                                    <input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
                                    <label for="tab5"><?= lang('activity_log_puchases') ?></label>

                                    <div class="tab-panels">
                                        <section id="view-items" class="tab-panel">
                                            <div class="table-responsive">
                                                <table id="table-view-capacity" class="dt-tnh table tnh-table dataTable table-bordered table-hover dont-responsive-table" style="width: 100%;">
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
                                            </div>
                                        </section>
                                        <section id="view-activity-log" class="tab-panel">
                                            <div class="activity-container tnh-activity-log" style="max-height: 500px;">
                                                <?php
                                                $history = getActivityLogByObjId($productions_capacity['id'], 'productions_capacity');
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
                                        <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                        <div><?= lang('tnh_date_creted') ?>: <?= _dt($productions_capacity['date_created']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="detail">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tb-detail" class="dt-tnh table tnh-table dataTable table-bordered table-hover dont-responsive-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('id') ?></th>
                                            <th><?= lang('tnh_product_code') ?></th>
                                            <th><?= lang('tnh_product_name') ?></th>
                                            <th><?= lang('unit') ?></th>
                                            <th><?= lang('tnh_quantity_productions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="statistical">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="view-statistical" class="dt-tnh table tnh-table dataTable table-bordered table-hover dont-responsive-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('code') ?></th>
                                            <th><?= lang('name') ?></th>
                                            <th><?= lang('type') ?></th>
                                            <th><?= lang('tnh_unit') ?></th>
                                            <th><?= lang('tnh_quantity_warehouses') ?>(<?= lang('tnh_expected') ?>)</th>
                                            <th><?= lang('tnh_quantity_use') ?></th>
                                            <th><?= lang('tnh_quantity_purchase') ?>(<?= lang('tnh_expected') ?>)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="purchases">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tb-purchases" class="dt-tnh table tnh-table dataTable table-bordered table-hover dont-responsive-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;"><?= lang('tnh_numbers') ?></th>
                                            <th><?= lang('id') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('tnh_reference_no') ?></th>
                                            <th><?= lang('ch_name_p') ?></th>
                                            <th><?= lang('ch_note_t') ?></th>
                                            <th><?= lang('tnh_created_by') ?></th>
                                            <th><?= lang('tnh_status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<input type="hidden" name="view_productions_capacity_id" id="view_productions_capacity_id" class="form-control" value="<?= $id ?>">
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	var oTableProductionsCapacity = '';
	var paramsProductionsCapacity = {view_productions_capacity_id: '#view_productions_capacity_id'};
	var ii = 10;
    var ij = 11;
    var arr = [];
    function formatCapacity(d) {
    	versions = '';
        if (typeof d == 'undefined') return '';
        if (typeof d[ii][0] != 'undefined' && d[ii][0]['versions_bom'] != null) {
            versions = '('+d[ii][0]['versions_bom']+')';
        }
	    tr1 = '<tr><td colspan="9" class="text-center bold"><?= lang('BOM') ?> '+versions+'</td></tr>'+
                '<tr class="bold color-dark-green">'+
    	            '<td><?= lang('tnh_numbers') ?></td>'+
    	            '<td><?= lang('code') ?></td>'+
    	            '<td><?= lang('name') ?></td>'+
                    '<td><?= lang('unit') ?></td>'+
                    '<td><?= lang('type') ?></td>'+
                    // '<td><?= lang('tnh_quantity_minimum') ?></td>'+
                    // '<td><?= lang('tnh_quantity_warehouses') ?></td>'+
                    '<td><?= lang('tnh_quantity_use') ?></td>'+
    	            // '<td><?= lang('tnh_quantity_purchase') ?></td>'+
    	        '</tr>';

	    if (d[ii]) {
	    	element = d[ii];
	    	$.each(element, function(i, e) {
	    		if (typeof e.code_sub != 'undefined') {
	        		tr1+= '<tr>'+
			            '<td>'+(++i)+'</td>'+
			            '<td>'+e.code_sub+'</td>'+
                        '<td>'+e.name_sub+'</td>'+
			            '<td>'+e.unit+'</td>'+
	                    '<td>'+lang_core[e.type_sub]+'</td>'+
			            // '<td class="text-center">'+tnhFormatNumber(e.quantity_minimum_sub)+'</td>'+
	                    // '<td class="text-center">'+tnhFormatNumber(e.quantity_warehouse_sub)+'</td>'+
			            '<td class="text-center">'+tnhFormatNumber(e.quantity_plan_sub)+'</td>'+
	                    // '<td class="text-center">'+tnhFormatNumber(e.quantity_purchase_sub)+'</td>'+
			        '</tr>';
	    		}
        	});
        }

        tableBOM = '<table class="dt-table tnh-table " style="width: 92% !important; float: right;">'+
			        tr1+
			    '</table>';

        //stages
        versions = '';
        if (typeof d[ij][0] != 'undefined' && d[ij][0]['versions_stages'] != null) {
            versions = '('+d[ij][0]['versions_stages']+')';
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
            	if (typeof el.stage_name != 'undefined') {
	                tr_stage+= '<tr>'+
	                    '<td>'+(++index)+'</td>'+
	                    '<td>'+el.stage_name+'</td>'+
	                    '<td>'+el.machine_name+'</td>'+
	                    '<td class="text-center">'+tnhFormatNumber(el.number_hours)+'</td>'+
	                '</tr>';
	            }
            });
        }

        tableStages = '<table class="dt-table tnh-table " style="width: 92% !important; float: right;     margin-top: 10px !important;">'+
                    tr_stage+
                '</table>';

        table = tableBOM+''+tableStages;
	    return table;
    }
	$(document).ready(function() {
		oTableProductionsCapacity = tnhDatatable(
            '#table-view-capacity',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: "300px",
                // scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 3,
                // },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getViewProductionsCapacity') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionsCapacity) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionsCapacity[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
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

		$('#table-view-capacity tbody').on('click', 'td.details-control', function () {
	        var tr = $(this).closest('tr');
            var records = tr.find('#records').val();
	        var row = oTableProductionsCapacity.row( tr );

	        if ( row.child.isShown() ) {
	            arr = removeArray(arr, records);
	            row.child.hide();
	            tr.removeClass('shown');
	        }
	        else {
	            if (!arr.includes(records)) {
                    arr.push(records);
                }
	            row.child( formatCapacity(row.data()) ).show();
	            tr.addClass('shown');
	        }
	    } );

	    $('#table-view-capacity').on('draw.dt', function(e, settings) {
            if (arr.length > 0) {
                $.each(arr, function(index, el) {
                    $('input[name="records"][value="'+ el +'"]').closest('td').trigger('click');
                });
            }
        })
	});

    //tab2
    var oTableProductionsCapacityStatistical = '';
    var paramsProductionsCapacityStatistical = {view_productions_capacity_id: '#view_productions_capacity_id'};
    $(document).ready(function() {
        oTableProductionsCapacityStatistical = tnhDatatable(
            '#view-statistical',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                // scrollY: "300px",
                // scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 3,
                // },
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getViewProductionsCapacityStatistical') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionsCapacityStatistical) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionsCapacityStatistical[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "columnDefs": [
                    {
                        "targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center'
                    },
                    {"targets": 1, "name": 'code', 'width': '150px'},
                    {"targets": 2, "name": 'name', 'width': '150px'},
                    {
                        "render": function(data) {
                            return lang_core[data];
                        },
                        "targets": 3, "name": 'type', 'width': '150px'
                    },
                    {"targets": 4, "name": 'unit_name', 'width': '100px', 'className': 'text-center'},
                    // {
                    //     "render": function(data) {
                    //         return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                    //     },
                    //     "targets": 5, "name": 'quantity_minimum', 'width': '80px', 'className': 'text-center'
                    // },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 5, "name": 'quantity_warehouse', 'width': '80px', 'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 6, "name": 'quantity_plan', 'width': '80px', 'className': 'text-center'
                    },
                    {
                        "render": function(data) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 7, "name": 'quantity_purchase', 'width': '80px', 'className': 'text-center'
                    },
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                }
            }
        );
    });

    //tab3
    var oTableProductionsPurchases = '';
    var paramsProductionsPurchases = {view_productions_capacity_id: '#view_productions_capacity_id'};
    $(document).ready(function() {
        oTableProductionsPurchases = tnhDatatable(
            '#tb-purchases',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getPurchasesByProductionsCapacity') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsProductionsPurchases) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsProductionsPurchases[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "columnDefs": [
                    // {
                    //     "targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center', 'sortable': false
                    // },
                    {
                        "render": function(data, type, row) {
                            return '<input type="hidden" name="purchase_id" id="purchase_id" class="form-control purchase_id" value="'+row[1]+'"><div style="padding-left: 30px;">'+data+'</div>';
                        },
                        "targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center details-control'
                    },
                    {"targets": 1, "name": 'id', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date_purchase', 'searchable': false
                    },
                    {"targets": 3, "name": 'reference_purchase'},
                    {"targets": 4, "name": 'name_purchase'},
                    {"targets": 5, "name": 'note_purchase'},
                    {"targets": 6, "name": 'created_by'},
                    {"targets": 7, "name": 'status', 'searchable': false, 'sortable': false},
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                }
            }
        );

        function formatPurchase(purchase_id, row, tr)
        {
            $.ajax({
                url: site.base_url+'admin/manufactures/showInfoRelated',
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    purchase_id: purchase_id
                },
            })
            .done(function(data) {
                row.child(data).show();
                tr.addClass('shown');
            })
            .fail(function() {
                console.log("error");
            });
        }

        $('#tb-purchases tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var purchase_id = tr.find('#purchase_id').val();
            console.log(purchase_id);
            var row = oTableProductionsPurchases.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                formatPurchase(purchase_id, row, tr);
            }
        });
    });

    //tab detail
    var oTableCapacityItems = '';
    var paramsCapacityItems = {view_productions_capacity_id: '#view_productions_capacity_id'};
    $(document).ready(function() {
        oTableCapacityItems = tnhDatatable(
            '#tb-detail',
            {
                'order': [[1, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getCapacityItemsSub') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in paramsCapacityItems) {
                        aoData.push({
                            "name": key,
                            "value": $(paramsCapacityItems[key]).val()
                        });
                    }
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return '<input type="hidden" name="capacity_item_id" id="capacity_item_id" class="form-control capacity_item_id" value="'+row[1]+'"><div style="padding-left: 30px;">'+data+'</div>';
                        },
                        "targets": 0, "name": 'number_records', 'width': '50px', 'className': 'text-center details-control'
                    },
                    {"targets": 1, "name": 'id', 'visible': false},
                    {"targets": 2, "name": 'code'},
                    {"targets": 3, "name": 'name'},
                    {"targets": 4, "name": 'unit'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>'
                        },
                        "targets": 5, "name": 'quantity_productions'
                    },
                ],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                }
            }
        );

        function formatCapacityItemSub(capacity_item_id, row, tr)
        {
            $.ajax({
                url: site.base_url+'admin/manufactures/showBomCapacityItemSub',
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    capacity_item_id: capacity_item_id
                },
            })
            .done(function(data) {
                row.child(data).show();
                tr.addClass('shown');
            })
            .fail(function() {
                console.log("error");
            });
        }

        $('#tb-detail tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var capacity_item_id = tr.find('#capacity_item_id').val();
            var row = oTableCapacityItems.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                formatCapacityItemSub(capacity_item_id, row, tr);
            }
        });
    });
</script>