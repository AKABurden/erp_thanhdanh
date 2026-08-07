<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perAddListSuggestExporting): ?>
            <a href="<?= base_url('admin/manufactures/add_suggest_exporting') ?>" class="btn btn-info pull-right H_action_button">
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
                                <li role="presentation">
                                    <a href="#tnh_un_approved" aria-controls="tnh_un_approved" role="tab" value="un_approved" data-toggle="tab"><?= lang('tnh_un_approved') ?>(<span><?= $un_approved ?></span>)</a>
                                </li>
                                <li role="presentation">
                                    <a href="#tnh_approved" aria-controls="tnh_approved" role="tab" value="approved" data-toggle="tab"><?= lang('tnh_approved') ?>(<span><?= $approved ?></span>)</a>
                                </li>
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value="" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="">
                        </div>
                        <div class="">
                            <table id="table-suggest-exporting" class="table dt-tnh table-hover table-condensed table-suggest-exporting dont-responsive-table dataTable" style="min-width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?= lang('tnh_numbers') ?></th>
                                        <th><?= lang('id') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_no_suggest') ?></th>
                                        <th><?= lang('tnh_reference_productions_orders_details') ?></th>
                                        <th><?= lang('tnh_products') ?></th>
                                        <th><?= lang('tnh_export_name') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
                                        <th><?= lang('tnh_status_convert_stock') ?></th>
                                        <th><?= lang('tnh_type') ?></th>
                                        <th><?= lang('actions') ?></th>
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

<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
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
            '#table-suggest-exporting',
            {
                'order': [[2, 'desc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                scrollY: height_body,
                scrollX: true,
                fixedColumns: {
                    leftColumns: 4,
                    rightColumns: 1
                },
                // scrollY: true,
                // scrollX: true,
                // responsive: true,
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/manufactures/getSuggestExporting') ?>',
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
                    st = aData[9];
                    cv = aData[11];
                    if (st == "un_approved" || cv == "completed")
                    {
                        $(nRow).find('.tnh-stock').addClass('tnh-disabled');
                    }
                    type = aData[12];
                    if (type != 3 || st == "approved") {
                        // $(nRow).find('.tnh-delete').addClass('tnh-disabled');
                        $(nRow).find('.tnh-edit').addClass('tnh-disabled');
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
                    {"targets": 0, "name": 'number_records', 'width': '45px', 'className': 'text-center', 'sortable': false},
                    {
                        "targets": 1, "name": 'id', 'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 2, "name": 'date', 'searchable': false, 'width': '60px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<a class="tnh-modal" title="<?= lang('view') ?>" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/manufactures/view_suggest_exporting/') ?>'+row[1]+'">'+data+'</a>';
                        },
                        "targets": 3, "name": 'reference_no', 'width': '120px'
                    },
                    {
                        "render": function(data, type, row) {
                            type = row[12];
                            label = 'success';
                            typeText = '<?= lang('tnh_tbom') ?>';
                            if (type == 3) {
                                label = 'primary';
                                typeText = '<?= lang('tnh_additional') ?>';
                            }
                            return '<div class="mbot5">'+data+'</div><div class="label label-'+label+'">'+typeText+'</div>';
                        },
                        "targets": 4, "name": 'reference_production_detail', 'width': '120px'
                    },
                    {"targets": 5, "name": 'product', 'width': '100px'},
                    {"targets": 6, "name": 'export_name', 'width': '100px'},
                    {"targets": 7, "name": 'note', 'width': '100px'},
                    {"targets": 8, "name": 'created_by', 'width': '100px'},
                    {
                        "render": function(data, type, row) {
                            suggest_exporting_id = row[1];
                            if (data == "approved") {
                                user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: '+row[10]+'</div>';
                            } else {
                                user_status = '';
                            }
                            if (data == "un_approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>" data-content="<p><a id=\'agree\' suggest_exporting_id=\''+suggest_exporting_id+'\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>'+user_status;
                            } else if (data == "approved") {
                                return  '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_status') ?>\" data-content="<p><a id=\'agree\' suggest_exporting_id=\''+suggest_exporting_id+'\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>'+user_status;
                            }
                            return '';
                        },
                        "targets": 9, "name": 'status', 'width': '120px'
                    },
                    {"targets": 10, "name": 'user_status', 'visible': false},
                    {
                        "render": function(data, type, row) {
                            if (data == "completed") {
                                return '<span class="label label-success"><?= lang('tnh_converted') ?></span>';
                            } else  if (data == "apart") {
                                return '<span class="label label-info"><?= lang('apart') ?></span>';
                            }
                            return '<span class="label label-danger"><?= lang('tnh_not_convert') ?></span>';
                        },
                        "targets": 11, "name": 'status_stock', 'width': '160px'
                    },
                    {"targets": 12, "name": 'type', 'visible': false},
                    {"targets": 13, "name": 'actions', 'searchable': false, 'sortable': false, 'width': '155px'},
                ]
            }
        );

        $(document).on('click', '#table-suggest-exporting_wrapper .btn-dt-reload', function(event) {
            // oTable.draw('page');
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
                    url: site.base_url+'admin/manufactures/agreeSuggestExporting',
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
            return;
            url = $(this).attr('href');
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
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
