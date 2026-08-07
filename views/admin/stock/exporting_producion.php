<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddExportingProducion): ?>
                <a href="<?= base_url('admin/stock/add_exporting_production') ?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                    <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                </div>
                <div class="col-md-3">
                    <?= lang('productions_plan', 'productions_plan_search') ?>
                    <input type="text" name="productions_plan_search" data-placeholder="<?= lang('productions_plan') ?>" id="productions_plan_search" class="productions_plan_search" style="width: 100%;" value="">
                </div>
                <div class="col-md-3">
                    <?= lang('Chi nhánh', 'branch_search') ?>
                    <select name="branch_search" id="branch_search" class="branch_search"  data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                        <option value=""></option>
                        <?php if (!empty($branch)) { ?>
                            <?php foreach ($branch as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-stock" class="table dt-tnh table-hover table-condensed table-stock dont-responsive-table dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_stock') ?></th>
                                        <th><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th><?= lang('tnh_reference_productions_orders_details') ?></th>
                                        <th><?= lang('tnh_export_name') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('type') ?></th>
                                        <th><?= lang('ch_warehoues_app') ?></th>
                                        <th><?= lang('purchase_internal') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog modal-lm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_export_quantity_missing');?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
	var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {status_table: '#status_table', productions_orders_search: '#productions_orders_search', productions_plan_search: '#productions_plan_search',branch_search : '#branch_search'};
	var oTable = '';
</script>
<script type="text/javascript">
	$(document).ready(function() {
        $("#branch_search").select2({
            'allowClear': true
        })
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParamsCallback('#productions_plan_search', 'admin/manufactures/searchProductionsPlanNew', 0, false, true);

		oTable = tnhDatatable(
            '#table-stock',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                // fixedColumns:   {
                //     leftColumns: 4,
                //     rightColumns: 1
                // },
                // scrollY: true,
                // scrollX: true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/stock/getExportingProductions') ?>',
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
                "drawCallback": function(settings, nRow) {
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                    type = aData[12];
                    status = aData[10];
                    if (type != 2) {
                        $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                        $(nRow).addClass('danger');
                    }
                    if (status != "un_approved_stock") {
                        $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                        $(nRow).find('.tnh-delete').addClass('tnh-disabled');
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'number_records', 'width': '45px', 'className': 'text-center', 'sortable': false, 'width': '50px'},
                    {
                        "targets": 1, "name": 'id', 'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'searchable': false, 'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            data = data.split('__');
                            branch_name = data[1];
                            var strBranch = '';
                            var str = '';
                            if (row[14]) {
                                str = '<div>Thu hồi: '+row[14]+'</div>';
                            }
                            if (branch_name){
                                strBranch = `<div style="font-style: italic">${branch_name}</div>`;
                            }
                            return '<a class="tnh-modal" title="<?= lang('view') ?>" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/stock/view_exporting_production/') ?>'+row[1]+'">'+data[0]+'</a>'+str+strBranch;
                        },
                        "targets": 3, "name": 'reference_stock', 'width': '150px'
                    },
                    {"targets": 4, "name": 'reference_no', 'width': '150px', 'visible': true},
                    {
                        "render": function(data, type, row) {
                            type = row[11];
                            if (!data) return '';
                            // label = 'success';
                            // typeText = '<?= lang('tnh_tbom') ?>';
                            // if (type == 2) {
                            //     label = 'primary';
                            //     typeText = '<?= lang('tnh_additional') ?>';
                            // }
                            // return '<div class="mbot5">'+data+'</div><div class="label label-'+label+'">'+typeText+'</div>';
                            return '<div class="mbot5">'+data+'</div>';
                        },
                        "targets": 5, "name": 'reference_production_detail', 'width': '150px'
                    },
                    {"targets": 6, "name": 'export_name', 'width': '100px'},
                    {"targets": 7, "name": 'warehouse_name', 'width': '100px', 'visible': false},
                    {"targets": 8, "name": 'note', 'width': '100px'},
                    {"targets": 9, "name": 'created_by', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            suggest_exporting_id = row[1];
                            if (data == "approved") {
                                // user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[9]+'</div>';
                                user_status = '';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved_stock") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' suggest_exporting_id=\''+suggest_exporting_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved_stock') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' suggest_exporting_id=\''+suggest_exporting_id+'\' value=\'un_approved_stock\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            }
                            return '';
                        },
                        "targets": 10, "name": 'status', 'width': '80px'
                    },
                    {"targets": 11, "name": 'user_status', 'visible': false},
                    {"targets": 12, "name": 'type', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            if (row[10] == "approved") {
                                if(data == 0){
                                return '<a href="javascript:void(0)" onclick="confirm_warehous('+row[1]+')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                                }else
                                {
                                return '<a href="javascript:void(0)" onclick="confirm_warehous('+row[1]+','+data+')" class=" btn btn-info btn-icon "  data-loading-text="Xin vui lòng đợi..." data-toggle="tooltip"  data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i>Đã duyệt kho</a>';
                                }
                            }else
                            {
                                return '';
                            }
                        },
                        "targets": 13, "name": 'status_warehouse'
                    },
                    {"targets": 14, "name": 'purchase_internal', 'visible': false},
                    {"targets": 15, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '160px'},
                ]
            }
        );

        $(document).on('click', '#table-stock_wrapper .btn-dt-reload', function(event) {
            // oTable.draw('page');
        });

        $(document).on('change', '#productions_orders_search, #productions_plan_search, #branch_search', function(event) {
            oTable.draw();
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            suggest_exporting_id = $(this).attr('suggest_exporting_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (suggest_exporting_id) {
                $.ajax({
                    url: site.base_url+'admin/stock/agreeStock',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        suggest_exporting_id: suggest_exporting_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('page');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('page');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });

        $(document).on('click', '.tnh-stock', function(event) {
            event.preventDefault();
            url = $(this).attr('href');
            return;
            bootbox.confirm({
                message: '<?= lang('tnh_do_you_want_convert_stock') ?>',
                buttons: {
                    confirm: {
                        label: '<?= lang('yes') ?>',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '<?= lang('no') ?>',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                convert: 1,
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                            },
                        })
                        .done(function(data) {
                            if (data.result) {
                                alert_float('success', data.message);
                                oTable.draw('page');
                            } else {
                                alert_float('danger', data.message);
                                oTable.draw('page');
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                    }
                }
            });
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
	});
    //hau
    function confirm_warehous(id,warehouseman_id) {
        {
            dataString={id:id,warehouseman_id:warehouseman_id,[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({
                type: "post",
                url:"<?=admin_url()?>stock/confirm_warehous",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    oTable.draw('page');
                    if(response.success == false)
                    {
                    alert_float(response.message.alert_type, response.message.message);
                    var html ='<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                                <thead>\
                                    <tr>\
                                        <th class="text-center"><?= lang('tnh_items') ?></th>\
                                        <th class="text-center"><?= lang('custom_field_add_edit_type') ?></th>\
                                        <th class="text-center"><?= lang('ch_quantity_missing') ?></th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                        $.each(response.item, function(key,value){
                        html +=     '<tr>\
                                        <th>'+value.item_name+'('+value.item_code+')'+value.thongso+'</th>\
                                        <th class="text-center">'+value.type+'</th>\
                                        <th class="text-center">'+tnhFormatNumber(value.quantity_net)+'</th>\
                                    </tr>';
                        });
                        html +=  '</tbody>\
                            </table>';
                            $('#confirm_warehous').modal('show');
                            $('#table_html').html(html);
                    }else
                    {
                    alert_float(response.alert_type, response.message);
                    }
                }
            });
            return false;
        }
    }
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
