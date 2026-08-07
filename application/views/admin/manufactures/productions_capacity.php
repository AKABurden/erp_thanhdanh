<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddProductionsCapacity): ?>
                <a href="<?= base_url('admin/manufactures/add_productions_capacity') ?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div role="tabpanel">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <?php foreach (status_productions_capacity() as $key => $value): ?>
                                    <li role="presentation">
                                        <a href="#<?= $key ?>" aria-controls="plan" role="<?= $key ?>" value="<?= $key ?>" data-toggle="tab"><?= $value ?>(<span><?= $status[$key] ?></span>)</a>
                                    </li>
                                <?php endforeach ?>
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value="" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="">
                        </div>
                        <div class="">
                            <table id="table-productions-capacity" class="table dt-tnh table-hover table-condensed table-productions-capacity">
                                <thead>
                                    <tr>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_productions_capacity') ?></th>
                                        <th><?= lang('KHSX/HĐSX') ?></th>
                                        <th><?= lang('un_number_want_purchases') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('YCMH') ?></th>
                                        <th><?= lang('tnh_status_material') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
	// var site = <?= json_encode(array('base_url' => base_url())) ?>;
	var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
	var fnserverparams = {status_table: '#status_table'};
	var oTable = '';
</script>
<script type="text/javascript">
	$(document).ready(function() {
		oTable = tnhDatatable(
            '#table-productions-capacity',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                // stateSave: true,
                // autoWidth: true,
                fixedColumns:   {
                    leftColumns: 4,
                    rightColumns: 1
                },
                "cache": false,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getProductionsCapacity') ?>',
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
                    number_purchases = intVal(aData[5]);
                    if (!number_purchases) {
                        $(nRow).find('.convert-purchase').addClass('tnh-disabled');
                    }
                    purchases = aData[10];
                    if (purchases) {
                        $(nRow).find('.convert-purchase').addClass('tnh-disabled');
                        // purchases = purchases.split('__');
                        // if (purchases[0] == "purchases") {
                        //     $(nRow).find('.convert-purchase').addClass('tnh-disabled');
                        // }
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                    {"targets": 0, "name": 'id', 'visible': false},
                    {"targets": 1, "name": 'number_records', 'width': '45px', 'className': 'text-center'},
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            data = data.split('___');
                            str = '';
                            if (data[1]) {
                                str = '<div class="mbot5"><span class="label btn-danger"><?= lang('tnh_reference_transfer') ?>: '+data[1]+'</span></div>'
                            }
                            return '<a class="tnh-modal" data-tnh="modal" href="'+site.base_url+'admin/manufactures/view_productions_capacity/'+row[0]+'" data-toggle="modal" data-target="#myModal">'+data[0]+'</a>'+str;
                        },
                        "targets": 3, "name": 'reference_no', 'width': '150px'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            if (data) {
                                str = data.replace(/,/g, '</br>');
                            }
                            return str;
                        },
                        "targets": 4, "name": 'productions_plan_reference_no', 'width': '140px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">'+data+'</div>';
                        },
                        "targets": 5, "name": 'number_purchases', 'width': '130px'
                    },
                    {"targets": 6, "name": 'note', 'width': '100px'},
                    {"targets": 7, "name": 'created_by', 'width': '130px'},
                    {
                        "render": function(data, type, row) {
                            productions_capacity_id = row[0];
                            if (data == "approved") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[9]+'</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' productions_capacity_id=\''+productions_capacity_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' productions_capacity_id=\''+productions_capacity_id+'\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            }
                            return '';
                        },
                        "targets": 8, "name": 'status', "width": '120px'
                    },
                    {"targets": 9, "name": 'user_status', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            return data;
                        },
                        "targets": 10, "name": 'status_purchases', 'visible': true, "width": '200px'
                    },
                    {
                        "render": function(data, type, row) {
                            var str = "";
                            var countInComplete = row[5];
                            var statusPurchases = row[10];
                            if (data > 0 || (countInComplete > 0 && statusPurchases == null)) {
                                str = '<span class="label label-danger"><?= lang('tnh_not_enough_material') ?></span>';
                            } else {
                                str = '<span class="label label-primary"><?= lang('tnh_had_enough_material') ?></span>';
                            }
                            return '<div class="text-center">'+str+'</div>';
                        },
                        "targets": 11, "name": 'status_material', 'width': '100px', 'visible': false
                    },
                    {"targets": 12, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '150px'}
                ]
            }
        );

        $(document).on('click', '#table-productions-capacity_wrapper .btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('click', '#table-view-capacity_wrapper .btn-dt-reload', function(event) {
            oTableProductionsCapacity.draw();
        });

        $(document).on('click', '#view-statistical_wrapper .btn-dt-reload', function(event) {
            oTableProductionsCapacityStatistical.draw();
        });

        $(document).on('click', '#tb-purchases_wrapper .btn-dt-reload', function(event) {
            oTableProductionsPurchases.draw();
        });

        $('#table-productions-capacity').on('draw.dt', function(e, settings) {
        })

        $(document).on('click', '.export-excel', function(event) {
            event.preventDefault();
            return;
            productions_capacity_id = $(this).attr('value');
            bootbox.confirm({
                message: '<?= lang('tnh_you_want_to_export_excel') ?>',
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
                        if (productions_capacity_id) {
                            $.ajax({
                                url: site.base_url+'admin/manufactures/export_excel_production_capacity',
                                type: 'POST',
                                dataType: 'JSON',
                                data: {
                                    productions_capacity_id: productions_capacity_id,
                                    export_excel: 1,
                                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                                },
                            })
                            .done(function(data) {
                                if (data.result) {
                                    alert_float('success', data.message);
                                    download(data.filename, data.file);
                                } else {
                                    alert_float('danger', data.message);
                                }
                            })
                            .fail(function() {
                                alert_float('danger', 'errors');
                            });
                        }
                    }
                }
            });
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            productions_capacity_id = $(this).attr('productions_capacity_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_capacity_id) {
                $.ajax({
                    url: site.base_url+'admin/manufactures/agreeProductionsCapacity',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        productions_capacity_id: productions_capacity_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw();
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw();
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });

        $(document).on('click', '.add-semi', function(event) {
            event.preventDefault();
            productionsCapacityId = $(this).attr('value');
            bootbox.confirm({
                message: '<?= lang('tnh_you_can_created_hdbtp') ?>',
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
                            url: site.base_url+'admin/manufactures/addProductionCapacitySemi',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                productionsCapacityId: productionsCapacityId,
                                "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>"
                            },
                        })
                        .done(function(data) {
                            if (data.result) {
                                alert_float('success', data.message);
                                oTable.draw();
                            } else {
                                alert_float('danger', data.message);
                                oTable.draw();
                            }
                        })
                        .fail(function() {
                            console.log("error");
                        });
                    }
                }
            });
        });

	});
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
