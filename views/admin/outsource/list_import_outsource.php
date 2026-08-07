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
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="div-import-outsource">
                            <table id="table-import-outsource"
                                class="table dt-tnh table-hover table-bordered table-condensed table-import-outsource-new">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="import-outsource-new"><label
                                                    for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_import_outsource') ?></th>
                                        <th><?= lang('tnh_reference_outsource') ?></th>
                                        <th><?= lang('tnh_reference_transfer') ?></th>
                                        <th><?= lang('Xuất kho khác') ?></th>
                                        <th><?= lang('tnh_enter_name') ?></th>
                                        <th><?= lang('tnh_warehouse_from') ?></th>
                                        <th><?= lang('tnh_warehouse_to') ?></th>
                                        <th><?= lang('tnh_employees') ?></th>
                                        <th><?= lang('Nhà cung cấp') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('Duyệt kho') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_user_agree') ?></th>
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
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {};
var oTable = '';

$(document).ready(function() {
    oTable = tnhDatatable(
        '#table-import-outsource', {
            'order': [
                [1, 'desc'],
                [2, 'desc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            fixedColumns: {
                // leftColumns: 3,
                // rightColumns: 1
            },
            scrollY: height_body,
            // scrollX: true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/outsource/getImportOutsource') ?>',
            'fnServerData': function(sSource, aoData, fnCallback) {
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
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "rowCallback": function(row, data) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" name="import_outsource_id[]" id="check-item' +
                            data + '" value="' + data + '"><label for="check-item' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "render": function(data, type, row) {
                        return fld(data);
                    },
                    "targets": 1,
                    "name": 'date',
                    'width': '100px',
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        data = data.split('__');
                        branch = data[1];
                        return '<div style="min-width: 150px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url +
                            'admin/outsource/view_import_outsource/' + row[0] +
                            '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a>\
                            <div style="font-style: italic;">' + branch + '</div>\
                            </div><div class="td-reference-no"></div>';
                    },
                    "targets": 2,
                    "name": 'reference_no',
                    'width': '80px'
                },
                {
                    "targets": 3,
                    "name": 'reference_outsource',
                    'width': '120px'
                },
                {
                    "targets": 4,
                    "name": 'reference_transfer',
                    'width': '150px',
                    'visible': false
                },
                {
                    "targets": 5,
                    "name": 'reference_exportDiff',
                    'width': '150px',
                    'visible': false
                },
                {
                    "targets": 6,
                    "name": 'enter_name',
                    'width': '100px',
                    "visible": false
                },
                {
                    "targets": 7,
                    "name": 'warehouse_from',
                    'width': '150px',
                    'visible': false
                },
                {
                    "targets": 8,
                    "name": 'warehouse_to',
                    'width': '150px',
                    'visible': false
                },
                {
                    "targets": 9,
                    "name": 'employee',
                    'width': '120px',
                    "visible": false
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + (data) + '</div>';
                    },
                    "targets": 10,
                    "name": 'company',
                    'width': '200px'
                },
                {
                    "targets": 11,
                    "name": 'created_by',
                    'width': '100px',
                    "visible": false
                },
                {
                    "render": function(data, type, row) {
                        str = '';
                        import_outsource_id = row[0];
                        if (data == "approved") {
                            user_status = '<div class="mtop10"><?= lang('tnh_user_agree') ?>: ' +
                                row[15] + '</div>';
                        } else {
                            user_status = '';
                        }
                        if (data == "un_approved") {
                            str =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>" data-content="<p><a id=\'agree\' import_outsource_id=\'' +
                                import_outsource_id +
                                '\' value=\'approved\' class=\'btn btn-success\'><?= lang('tnh_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-danger po"><?= lang('tnh_un_approved') ?></span></div>' +
                                user_status;
                        } else if (data == "approved") {
                            str =
                                '<div class="text-left"><span data-html="true" data-toggle="popover" data-container="body" data-placement="left" style="cursor: pointer;" title="<?= lang('tnh_agree') ?>\" data-content="<p><a id=\'agree\' import_outsource_id=\'' +
                                import_outsource_id +
                                '\' value=\'un_approved\' class=\'btn btn-danger\'><?= lang('tnh_un_agree') ?></a><button class=\'btn po-close\'><?= lang('close') ?></button></p>" class="label label-success po"><?= lang('tnh_approved') ?></span></div>' +
                                user_status;
                        }

                        return str;
                    },
                    "targets": 12,
                    "name": 'status',
                    'width': '100px',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        if (row[12] == 'approved') {
                            if (data == 0) {
                                return '<a href="javascript:void(0)" onclick="confirm_warehous(' +
                                    row[0] +
                                    ')" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                            }
                            return '<a href="javascript:void(0)" onclick="confirm_warehous(' + row[
                                    0] + ', ' + data +
                                ')" class=" btn btn-danger btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i> Đã duyệt kho</a>';
                        } else {
                            return '';
                        }
                    },
                    "targets": 13,
                    "name": 'status_warehouse',
                    'width': '100px'
                },
                {
                    "targets": 14,
                    "name": 'note',
                    'width': '100px'
                },
                {
                    "targets": 15,
                    "name": 'user_status',
                    'visible': false
                },
                {
                    "targets": 16,
                    "name": 'actions',
                    'sortable': false,
                    'searchable': false,
                    'width': '100px'
                },
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                // var grand_total = 0;
                // for (var i = 0; i < aaData.length; i++) {
                //     grand_total += intVal(aaData[i][11]);
                // }
                // var nCells = nRow.getElementsByTagName('th');
                // nCells[8].innerHTML = '<div class="text-right bold">' + tnhFormatMoney(grand_total) +
                //     '</div>';
            }
        }
    );

    $('#table-tools_supplies').on('draw.dt', function() {})

    $(document).on('click', '.btn-dt-reload', function(event) {
        oTable.draw('false');
    });

    $(document).on('click', '#agree', function(event) {
        event.preventDefault();
        index = this;
        import_outsource_id = $(this).attr('import_outsource_id');
        status = $(this).attr('value');
        $(index).attr('disabled', 'disabled');
        $('.po').popover('hide');
        if (import_outsource_id) {
            $.ajax({
                    url: site.base_url + 'admin/outsource/agreeImportOutsource',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        import_outsource_id: import_outsource_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                        oTable.draw('false');
                    } else {
                        alert_float('danger', data.message);
                        oTable.draw('false');
                    }
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
        }
    });
});

function confirm_warehous(id, warehouseman_id) {
    dataString = {
        id: id,
        warehouseman_id: warehouseman_id,
        [csrfData['token_name']]: csrfData['hash']
    };
    jQuery.ajax({
        type: "post",
        url: "<?=admin_url()?>outsource/confirm_warehous",
        data: dataString,
        cache: false,
        success: function(response) {
            response = JSON.parse(response);
            oTable.draw('page');
            if (response.success == false) {
                alert_float(response.message.alert_type, response.message.message);
            } else {
                alert_float(response.alert_type, response.message);
            }
        }
    });
    return false;
}
</script>