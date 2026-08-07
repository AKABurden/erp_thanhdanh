<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.fixedHeader-floating {
    position: fixed !important;
}

.pannel-custom:before {
    /* content: "Đầu ra"; */
    /* position: absolute; */
    /* top: -8px; */
    /* background: w    hite; */
}

.pannel-custom {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 0px 10px;
}
</style>
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= base_url('admin/products/add_stage') ?>"
                class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal"
                data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <a href="<?= base_url('admin/products/modal_excel_stages') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('IMPORT EXCEL'); ?>
            </a>
            <a onclick="printQRItems()" href="#" class="btn btn-info pull-right mright5 H_action_button">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                <?php echo lang('IN QR'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-3 row">
                            <?php echo render_select('category_stages[]', !empty($category_stages) ? $category_stages : [], ['id', 'name', 'code'], 'Nhóm công đoạn', '', ['multiple' => true, 'data-actions-box' => true])?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-stage"
                                class="table table-hover table-bordered table-condensed dataTable table-stage"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all" data-to-table="stage"><label
                                                    for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('') ?></th>
                                        <th class="text-center"><?= lang('tnh_code_category_stage') ?></th>
                                        <th class="text-center"><?= lang('tnh_name_category_stage') ?></th>
                                        <th class="text-center"><?= lang('tnh_stage_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_stage_name') ?></th>
                                        <th class="text-center"><?= lang('tnh_status_qc') ?></th>
                                        <th class="text-center"><?= lang('type') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th><?= lang('sub') ?></th>
                                        <th class="hide"><?= lang('outsource') ?></th>
                                        <th class="hide"><?= lang('import_outsource') ?></th>
                                        <th class="text-center"><?= lang('Công thức m2') ?></th>
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
<?php init_tail(); ?>
<?php $this->load->view('loader')?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
var site = <?= json_encode(array('base_url' => base_url())) ?>;
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {
    "category_stages" : "select[name='category_stages[]']"
};
var oTable = '';
var arr = [];

$('body').on('change', "select[name='category_stages[]']", function() {
    oTable.draw();
})

function format(d) {
    tr1 = '<tr class="bold">' +
        '<td class="text-center" style="width: 30px;"><?= lang('tnh_numbers') ?></td>' +
        '<td class="text-center" style="width: 40px;"><?= lang('tnh_sequence') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('code') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('name') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('departments') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('tnh_number_hours') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('tnh_status_qc') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('note') ?></td>' +
        '<td class="text-center" style="width: 100px;"><?= lang('actions') ?></td>' +
        '</tr>';

    tr = '';
    if (d[9] != '' && d[9] != null && d[9] != 'null') {
        // data = d[5].split('____');
        data = d[9];
        $.each(data, function(index, el) {
            // info = el.split('||');
            info = el;

            sEdit =
                '<a class="tnh-modal btn btn-success btn-icon" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="<?= base_url('admin/products/edit_stage_sub') ?>/' +
                info['id'] + '"><i class="fa fa-pencil"></i></a>';

            sDelete = '<button type="button" class="btn btn-danger po btn-icon" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="\
                    <button href=\'<?= base_url('admin/products/delete_stage/') ?>/' + info['id'] + '\' class=\'btn btn-danger po-delete-json\'><?= lang('delete') ?></button>\
                    <button class=\'btn btn-default po-close\'><?= lang('close') ?></button>\
                "><i class="fa fa-remove"></i></button>';

            var strDetail = '';
            if (info['status_qc'] == 1) {
                strDetail = '<span class="fa system-checkmark-checkbox"></span>';
            } else {
                strDetail = '<span class="fa system-checkmark-delete"></span>';
            }

            tr += '<tr>' +
                '<td class="text-center">' + (++index) + '</td>' +
                '<td class="text-center">' + info['sequence'] + '</td>' +
                '<td class="text-center">' + info['code'] + '</td>' +
                '<td class="text-center">' + info['name'] + '</td>' +
                '<td class="text-center">' + info['departments_name'] + '</td>' +
                '<td class="text-center">' + info['number_hours'] + '</td>' +
                '<td class="text-center">' + strDetail + '</td>' +
                '<td>' + info['note'] + '</td>' +
                '<td class="text-center">' + sEdit + '' + sDelete + '</td>' +
                '</tr>';
        });
    }

    tb = '<table class="dt-table tnh-table table-bordered" style="width: 95% !important; float: right;">' +
        '<tbody>' +
        tr1 +
        tr +
        '</tbody>' +
        '</table>'
    return tb;
}

$(document).ready(function() {
    oTable = tnhDatatable(
        '#table-stage', {
            'order': [
                [2, 'asc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // "processing": true,
            // 'fixedHeader': {
            //     header: true,
            //     footer: true
            // },
            // scrollY: height_body,
            // scrollX: true,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/products/getStages') ?>',
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
            "rowCallback": function(row, data) {
                stage_id = data[0];
                if (stage_id == <?= STAGE_PRINT_BARCODE ?>) {
                    $(row).find('.btn-delete').hide();
                }
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" id="check-item" value="' +
                            data + '"><label for="check-item"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '50px'
                },
                {
                    "render": function(data, type, row) {
                        return '<input type="hidden" name="stage" id="stage" class="form-control" value="' +
                            data + '">';
                    },
                    "targets": 1,
                    "name": 'records',
                    "className": 'details-control',
                    'visible': false,
                    'width': '30px'
                },
                {
                    "targets": 2,
                    "name": 'code_category'
                },
                {
                    "targets": 3,
                    "name": 'name_category'
                },
                {
                    "render": function(data, type, row) {
                        outsource = row[10];
                        import_outsource = row[11];
                        string_outsource = '';
                        string_import_outsource = '';
                        if (outsource == 1) {
                            string_outsource = '<p class="text-danger">Mặc định xuất gia công</p>';
                        }
                        if (import_outsource == 1) {
                            string_import_outsource =
                                '<p class="text-danger">Mặc định nhập gia công</p>';
                        }
                        return '<div>' + data + string_outsource + string_import_outsource +
                            '</div>';
                    },
                    "targets": 4,
                    "name": 'code'
                },
                {
                    "targets": 5,
                    "name": 'name'
                },
                {
                    "render": function(data, type, row) {
                        var str = '';
                        if (data == 1) {
                            str = '<span class="fa system-checkmark-checkbox"></span>';
                        } else {
                            str = '<span class="fa system-checkmark-delete"></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 6,
                    "name": 'status_qc'
                },
                {
                    "render": function(data, type, row) {
                        var str = '';
                        if (data == 1) {
                            str =
                                '<span class="label label-success"><?= lang('tnh_semi_finished_product') ?></span>';
                        } else if (data == 2) {
                            str =
                                '<span class="label label-primary"><?= lang('tnh_unfinished_product') ?></span>';
                        } else if (data == 6) {
                            str =
                                '<span class="label label-warning"><?= lang('tnh_prepare_materials') ?></span>';
                        } else if (data == 7) {
                            str =
                                '<span class="label label-danger"><?= lang('tnh_commune') ?></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 7,
                    "name": 'type',
                    'width': '100px',
                    'visible': false,
                },
                {
                    "targets": 8,
                    "name": 'note'
                },
                {
                    "targets": 9,
                    "name": 'sub',
                    'visible': false
                },
                {
                    "targets": 10,
                    "name": 'outsource',
                    'visible': false
                },
                {
                    "targets": 11,
                    "name": 'import_outsource',
                    'visible': false
                },
                {
                    "render": function(data, type, row) {
                        return `<div class="onoffswitch">
                            <input type="checkbox" data-switch-url="${site.base_url+'admin/products/formula_m2'}" name="onoffswitch" class="onoffswitch-checkbox" id="c_single_use_${row[0]}" data-id="${row[0]}" ${data == 1 ? 'checked' : ''}>
                            <label class="onoffswitch-label" for="c_single_use_${row[0]}"></label>
                        </div>`;
                    },
                    "targets": 12,
                    "name": 'formula_m2'
                },
                {
                    "targets": 13,
                    "name": 'actions',
                    'orderable': false,
                    'searchable': false,
                    'width': '100px'
                }
            ]
        }
    );

    $('#table-stage').on('draw.dt', function(e, settings) {
        if (arr.length > 0) {
            $.each(arr, function(index, el) {
                $('input[name="stage"][value="' + el + '"]').closest('td').trigger('click');
            });
        }
    })

    $('#table-stage tbody').on('click', 'td.details-control', function() {
        var tr = $(this).closest('tr');
        var stage_id = tr.find('#stage').val();
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            arr = removeArray(arr, stage_id);
            row.child.hide();
            tr.removeClass('shown');
        } else {
            if (!arr.includes(stage_id)) {
                arr.push(stage_id);
            }
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
        console.log(arr);
    });
});
</script>
<!-- <script type="text/javascript" src="<?= js('core.js?vs=1.1') ?>"></script> -->
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script>
$(document).ready(function() {
    $(document).on('change', '#semi_finished_product', function(event) {
        if ($('#semi_finished_product').prop('checked')) {
            $('#unfinished_product').prop('checked', false);
        }
    });

    $(document).on('change', '#unfinished_product', function(event) {
        if ($('#unfinished_product').prop('checked')) {
            $('#semi_finished_product').prop('checked', false);
        }
    });
});


$(document).on('click', '.stage_import_outsource', function(event) {
    checked = $(this).is(':checked');
    id = $(".id").val();
    if (checked) {
        $.ajax({
                url: site.base_url + 'admin/products/checkStageImportOusource',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token_name: hash,
                    id: id
                },
            })
            .done(function(data) {
                if (data.result) {
                    $(".stage_import_outsource").prop('checked', false);
                    $(".error_import").html('Đã có công đoạn check mặc định rồi');
                } else {
                    $(".error_import").html('');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
            });
    }
})
$(document).on('click', '.status_default_outsource', function(event) {
    checked = $(this).is(':checked');
    id = $(".id").val();
    if (checked) {
        $.ajax({
                url: site.base_url + 'admin/products/checkStageOusource',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    csrf_token_name: hash,
                    id: id
                },
            })
            .done(function(data) {
                if (data.result) {
                    $(".status_default_outsource").prop('checked', false);
                    $(".error_outsource").html('Đã có công đoạn check mặc định rồi');
                } else {
                    $(".error_outsource").html('');
                }
            })
            .fail(function() {
                alert_float('danger', 'error');
            });
    }
})
</script>

<script>
    function totalCriteria()
    {
        tbCriteria = '#tb-criteria tbody tr';
        var nCriteria = $(tbCriteria).length;
        var sttCriteria = 0;
        for (iCriteria = 0; iCriteria < nCriteria; iCriteria++)
        {
            sttCriteria++;
            elCriteria = $(tbCriteria)[iCriteria];
            $(elCriteria).find('.td-numbers').html(sttCriteria);
        }
    }

    function removeCriteria(_this) {
        $(_this).closest('tr').remove();
        totalCriteria();
    }

    function addCriteria()
    {
        tdNumbers = `<td class="text-center td-numbers"></td>`;
        tdWithdrawCheck = `<td>
            <input type="text" name="withdraw_check[]" class="form-control withdraw_check" value="" placeholder="<?= lang('tnh_withdraw_check') ?>">
        </td>`

        tdTestStandards = `<td>
            <input type="text" name="test_standards[]" class="form-control test_standards" value="" placeholder="<?= lang('tnh_test_standards') ?>">
        </td>`
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeCriteria(this)"></i></td>`;

        trCriteria = `<tr>
            ${tdNumbers}
            ${tdWithdrawCheck}
            ${tdTestStandards}
            ${tdActions}
        </tr>`;

        $('#tb-criteria tbody').append(trCriteria);
        totalCriteria();
    }
</script>

<script>
    function totalGroupCustomer()
    {
        tbGroupCustomer = '#tb-group-customer tbody tr';
        var nGroupCustomer = $(tbGroupCustomer).length;
        var sttGroupCustomer = 0;
        for (iGroupCustomer = 0; iGroupCustomer < nGroupCustomer; iGroupCustomer++)
        {
            sttGroupCustomer++;
            elGroupCustomer = $(tbGroupCustomer)[iGroupCustomer];
            $(elGroupCustomer).find('.td-numbers').html(sttGroupCustomer);
        }
    }

    function removeGroupCustomer(_this) {
        $(_this).closest('tr').remove();
        totalGroupCustomer();
    }

    $(document).on('change', '#group_customer', function(event) {

        customers_groups_id = $(this).val();
        dataG = event.added;
        customers_groups_name = dataG.text;

        if ($('.customers_groups_id[value="'+customers_groups_id+'"]').length > 0) {
            alert_float('danger', 'Nhóm này đã được chọn');
            return;
        }

        tdNumbers = `<td class="text-center td-numbers"></td>`;
        tdGroupCustomer = `<td class="text-center">
            <input type="hidden" name="customers_groups_id[]" class="form-control customers_groups_id" value="${customers_groups_id}">
            ${customers_groups_name}
        </td>`

        tdPriceGroupCustomer = `<td>
            <input type="text" name="price_group_customer[]" class="form-control price_group_customer number-format" value="0" placeholder="">
        </td>`;

        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeGroupCustomer(this)"></i></td>`;

        trGroupCustomer = `<tr>
            ${tdGroupCustomer}
            ${tdPriceGroupCustomer}
            ${tdActions}
        </tr>`;

        $('#tb-group-customer tbody').append(trGroupCustomer);
        totalGroupCustomer();
    });

    function printQRItems()
    {
        var ids = '';
        var rows = $('#table-stage').find('tbody tr');
        var grand_total_seller = 0;
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });

        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn Công đoạn cần in QR');
            return;
        }

        window.open(site.base_url+'admin/products/print_qr_stage?ids='+ids, "_blank");
    }
</script>