<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
    .table-condensed tbody tr td:nth-child(12) {
      white-space: inherit;
      min-width: 160px;
    }
    .table-condensed tbody .dropdown {
      text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= base_url('admin/stock/add_purchase_internal') ?>" class="btn btn-info pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-purchase-internal" class="table dt-tnh table-hover table-condensed table-purchase-internal dont-responsive-table dataTable">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_purchase_internal') ?></th>
                                        <th><?= lang('tnh_reference_productions_orders_details') ?></th>
                                        <th><?= lang('tnh_enter_name') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('quantity') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('ch_warehoues_app') ?></th>
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
	var fnserverparams = {status_table: '#status_table'};
	var oTable = '';
</script>
<script type="text/javascript">
	$(document).ready(function() {
		oTable = tnhDatatable(
            '#table-purchase-internal',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                fixedColumns:   {
                    leftColumns: 4,
                    rightColumns: 1
                },
                // scrollY: true,
                // scrollX: true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/stock/getPurchaseInternal') ?>',
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
                    // type = aData[12];
                    // status = aData[10];
                    // if (type != 2) {
                    //     $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                    //     $(nRow).addClass('danger');
                    // }
                    // if (status != "un_approved_stock") {
                    //     $(nRow).find('.tnh-edit').addClass('tnh-disabled');
                    //     $(nRow).find('.tnh-delete').addClass('tnh-disabled');
                    // }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                    var total_quantity = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        total_quantity+= intVal(aaData[i][7]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[6].innerHTML = '<div class="text-center bold">'+tnhFormatNumber(total_quantity)+'</div>';
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
                            strBranch = '';
                            if (branch_name){
                                strBranch = `<div style="font-style: italic">${branch_name}</div>`;
                            }
                            return '<a class="tnh-modal" title="<?= lang('view') ?>" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/stock/view_purchase_internal/') ?>'+row[1]+'">'+data[0]+'</a>'+strBranch;
                        },
                        "targets": 3, "name": 'reference_no', 'width': '150px'
                    },
                    {
                        "targets": 4, "name": 'reference_production_detail', 'width': '150px'
                    },
                    {"targets": 5, "name": 'enter_name', 'width': '100px'},
                    {"targets": 6, "name": 'warehouse_name', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+tnhFormatNumber(data)+'</div>';
                        },
                        "targets": 7, "name": 'total_quantity', 'width': '100px'
                    },
                    {"targets": 8, "name": 'note', 'width': '100px'},
                    {"targets": 9, "name": 'created_by', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            purchase_internal_id = row[1];
                            if (data == "approved") {
                                // user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[9]+'</div>';
                                user_status = '';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' purchase_internal_id=\''+purchase_internal_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' purchase_internal_id=\''+purchase_internal_id+'\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            }
                            return '';
                        },
                        "targets": 10, "name": 'status', 'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            if (row[10] == "approved") {
                                if (data == 0) {
                                    return '<a href="javascript:void(0)" onclick="confirm_warehous('+row[1]+')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                                } else {
                                    return '<a href="javascript:void(0)" onclick="confirm_warehous('+row[1]+','+data+')" class=" btn btn-info btn-icon "  data-loading-text="Xin vui lòng đợi..." data-toggle="tooltip"  data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i>Đã duyệt kho</a>';
                                }
                            } else {
                                return '';
                            }
                        },
                        "targets": 11, "name": 'status_warehouse', 'width': '100px'
                    },
                    {"targets": 12, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '50px'},
                ]
            }
        );

        $(document).on('click', '#table-purchase-internal_wrapper .btn-dt-reload', function(event) {
            oTable.draw('page');
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            purchase_internal_id = $(this).attr('purchase_internal_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (purchase_internal_id) {
                $.ajax({
                    url: site.base_url+'admin/stock/agreePurchaseInternal',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        purchase_internal_id: purchase_internal_id,
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
            return;
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
                                        <th>'+value.item_name+'('+value.item_code+')</th>\
                                        <th class="text-center">'+value.type+'</th>\
                                        <th class="text-center">'+formatNumber(value.quantity_net)+'</th>\
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
    //hau
    function confirm_warehous(id,warehouseman_id) {
        {
            dataString={id:id,warehouseman_id:warehouseman_id,[csrfData['token_name']] : csrfData['hash']};
            jQuery.ajax({   
                type: "post",
                url:"<?=admin_url()?>stock/confirm_warehous_purchase_internal",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    oTable.draw('page');
                    alert_float(response.alert_type, response.message);    
                }
            });
            return false;
        }
    }
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
