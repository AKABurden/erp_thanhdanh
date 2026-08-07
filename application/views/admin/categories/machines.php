<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }

    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 200px !important;
    }

    .dt-buttons>.btn-default:nth-child(1) {
        /* display: none!important; */
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
            <div class="line-sp"></div>
            <a href="<?= base_url('admin/categories/add_machines/'.(!empty($is_type) ? $is_type : '')) ?>" class="btn btn-info pull-right H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <a href="<?= base_url('admin/categories/modal_excel_machines') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('IMPORT EXCEL'); ?>
            </a>
            <a href="<?= base_url('admin/categories/export_machines') ?>" class="btn btn-info pull-right mright10 H_action_button">
                <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                <?php echo _l('EXPORT EXCEL'); ?>
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
                        <div class="clearfix"></div>
                        <div class="btn-group" style="max-width: 100%;">
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left disabled" style="display: block;"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right" style="display: block;"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li class="active">
                                            <a class="H_filter" data-id="">
                                                <?= _l('cong_all') ?> <b class="filter_all"></b>
                                            </a>
                                        </li>
                                        <?php if (!empty($category)) { ?>
                                            <?php foreach ($category as $key => $value) { ?>
                                                <li>
                                                    <a class="H_filter" data-id="<?= $value['id'] ?>">
                                                        <?= $value['name'] ?> (<?= $value['code'] ?>)
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="filterCategory" id="filterCategory" value="">
                        <input type="hidden" name="filterCategoryAll" id="filterCategoryAll" value="<?=(!empty($is_type) ? $is_type : '')?>">
                        <div class="table-responsive">
                            <table id="table-machines" class="table table-hover table-bordered table-condensed dataTable table-machines" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="machines"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('dt_category_machines') ?></th>
                                        <th><?= lang('Mã Thiết Bị/Công Việc') ?></th>
                                        <th><?= lang('Tên Thiết Bị/Công Việc') ?></th>
                                        <th><?= lang('tnh_product_in_month') ?></th>
                                        <th><?= lang('Thời gian sử dụng') ?></th>
                                        <th><?= lang('Trạng Thái') ?></th>
                                        <th><?= lang('tnh_specifications') ?></th>
                                        <th><?= lang('Nhóm công đoạn') ?></th>
                                        <th><?= lang('Nhà cung cấp') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('Phân loại') ?></th>
                                        <th><?= lang('actions') ?></th>
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
<?php $this->load->view('loader') ?>
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var lang_machines = <?= json_encode(status_machine_new()) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        'filterCategory': '#filterCategory',
        'filterCategoryAll': '#filterCategoryAll',
    };
    var oTable = '';
    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterCategory"]').val($(this).attr('data-id'));
        oTable.draw('page');
    });
    $(document).ready(function() {
        oTable = tnhDatatable(
            '#table-machines', {
                'order': [1, 'desc'],
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
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/categories/getMachines') ?>',
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
                "drawCallback": function(aoData, settings) {
                    $('.stage_id').selectpicker();
                    $('.supplier_id').selectpicker();
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    var $btnReload = $('.btn-dt-reload');
                    $btnReload.attr('data-toggle', 'tooltip');
                    $btnReload.attr('title', app.lang.dt_button_reload);
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" id="check-item" class="id" value="' + data + '"><label for="check-item"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '50px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + (data != null ? data : '') + '</div>';
                        },
                        "targets": 1,
                        "name": 'name_category'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data + '</div>';
                        },
                        "targets": 2,
                        "name": 'code'
                    },
                    {
                        "targets": 3,
                        "name": 'name'
                    },
                    {
                        "render": function(data, type, row) {
                            return formatNumberTnh(data);
                        },
                        "targets": 4,
                        "name": 'product_in_month'
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div class="text-center">${data != null ? formatNumberTnh(data) : 0}</div>`;
                        },
                        "targets": 5,
                        "name": 'used_time'
                    },
                    {
                        "render": function(data, type, row) {
                            // if (data == "not_produced") {
                            //     return '<span class="label label-success">'+lang_machines[data]+'</span>';
                            // } else
                            if (data == "producing") {
                                return '<span class="label label-primary">' + lang_machines[data] + '</span>';
                            } else if (data == "maintenance") {
                                return '<span class="label label-warning">' + lang_machines[data] + '</span>';
                            } else if (data == "damaged") {
                                return '<span class="label label-danger">' + lang_machines[data] + '</span>';
                            } else {
                                return '';
                            }
                        },
                        "targets": 6,
                        "name": 'status'
                    },
                    {
                        "targets": 7,
                        "name": 'specifications'
                    },
                    {
                        "targets": 8,
                        "name": 'stage',
                        'width': '200px'
                    },
                    {
                        "targets": 9,
                        "name": 'supplier_id',
                        'width': '200px'
                    },
                    {
                        "targets": 10,
                        "name": 'note'
                    },
                    {
                        "targets": 11,
                        'visible': false,
                        "name": 'is_type'
                    },
                    {
                        "targets": 12,
                        "name": 'actions',
                        'orderable': false,
                        'searchable': false,
                        'width': '100px'
                    }
                ],
                buttons: get_datatable_buttons('#table-machines'),
            }
        );

        $('#table-machines').on('draw.dt', function(e, settings) {
            $('.tip').tooltip();
        });

        $(document).on('click', '#table-machines_wrapper .btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('click', '#table-history-machines_wrapper .btn-dt-reload', function(event) {
            oTable_machine.draw();
        });


    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>

<script>
    function changeStage(_this) {
        stage_id = $(_this).val();
        id = $(_this).closest('tr').find('.id').val();
        dataString = {
            stage_id: stage_id,
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>categories/add_category_stage",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                if (response.result) {
                    alert_float('success', response.message);
                    oTable.draw(false);
                } else {
                    alert_float('danger', response.message);
                    oTable.draw(false);
                }
            }
        });
    };

    function changeSupplier(_this) {
        supplier_id = $(_this).val();
        id = $(_this).closest('tr').find('.id').val();
        dataString = {
            supplier_id: supplier_id,
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>categories/add_supplier_machines",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                if (response.result) {
                    alert_float('success', response.message);
                    oTable.draw(false);
                } else {
                    alert_float('danger', response.message);
                    oTable.draw(false);
                }
            }
        });
    };

    function totalProcess() {
        tbProcess = '#tb-process tbody tr';
        var nProcess = $(tbProcess).length;
        var sttProcess = 0;
        for (iProcess = 0; iProcess < nProcess; iProcess++) {
            sttProcess++;
            elProcess = $(tbProcess)[iProcess];
            $(elProcess).find('.td-numbers').html(sttProcess);
        }
    }

    function removeProcess(_this) {
        $(_this).closest('tr').remove();
        totalProcess();
    }
    var countItems = 0;

    function addProcess() {
        tdNumbers = `<td class="text-center td-numbers"></td>`;
        tdProcess = `<td>
            <input type="text" name="process[${countItems}]" class="form-control process" value="" placeholder="<?= lang('tnh_process') ?>">
        </td>`;

        tdFile = `<td>
            <input type="file" name="file[${countItems}]" class="form-control file" multiple value="" placeholder="<?= lang('File') ?>">
        </td>`;

        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeProcess(this)"></i></td>`;

        trProcess = `<tr>
            ${tdNumbers}
            ${tdProcess}
            ${tdFile}
            ${tdActions}
        </tr>`;

        $('#tb-process tbody').append(trProcess);
        countItems++;
        totalProcess();
    }


    var countItemsMain = 0;

    function
    addMaintenance() {
        tdNumbers = `<td class="text-center td-numbers-maintenance"></td>`;
        tdProcess = `<td>
            <input type="text" name="maintenance[${countItemsMain}]" required class="form-control process" value="" placeholder="<?= lang('Bộ phận') ?>">
        </td>`;

        tdMonth = `<td>
            <input type="text" name="month[${countItemsMain}]" class="form-control" value="" placeholder="<?= lang('Số ngày cần bảo trì') ?>">
        </td>`;

        tdFile = `<td>
                    <textarea type="text" name="note_main[${countItemsMain}]" class="form-control note mbot10" placeholder="Ghi chú cách thức bảo trì" aria-invalid="false"></textarea>
                    <input type="file" name="file_main[${countItemsMain}][]" class="form-control file_main" multiple value="" placeholder="<?= lang('File') ?>">
                </td>`;

        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeMaintenance(this)"></i></td>`;

        trProcess = `<tr>
            ${tdNumbers}
            ${tdProcess}
            ${tdMonth}
            ${tdFile}
            ${tdActions}
        </tr>`;

        $('#tb-maintenance tbody').append(trProcess);
        countItemsMain++;
        stt_maintenance();
    }

    function stt_maintenance() {
        var tr = $('#tb-maintenance tbody tr');
        $.each(tr, function(index, value) {
            $(value).find('.td-numbers-maintenance').text((index + 1));
        })
    }

    function removeMaintenance(_this) {
        $(_this).closest('tr').remove();
        stt_maintenance();
    }

    function removeFileProcess(id, _this) {
        if (confirm('Bạn có muốn xóa file này?')) {
            $.get(admin_url + 'categories/remove_file_machines/' + id, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.success) {
                    $(_this).parents('.url_file').remove();
                }
            })
        }
    }

    function removeFileMain(id, _this) {
        if (confirm('Bạn có muốn xóa file này?')) {
            $.get(admin_url + 'categories/remove_file_main/' + id, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.success) {
                    $(_this).parents('.url_file').remove();
                }
            })
        }
    }

    function printQRItems() {
        var ids = '';
        var rows = $('#table-machines').find('tbody tr');
        var grand_total_seller = 0;
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });

        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn Thiết bị cần in QR');
            return;
        }

        window.open(site.base_url + 'admin/categories/print_qr_machine?ids=' + ids, "_blank");
    }

    function addCleaning_5s() {
        tdNumbers = `<td class="stt text-center"></td>`;
        td5S = `<td>
                        <input type="text" name="items_5s[${countItems5S}][name]" class="form-control form-control" value="">
                    </td>`;
        tdFile = `<td>
                    <textarea name="items_5s[${countItems5S}][note]" class="form-control"></textarea>
                    <input type="file" name="file_5s[${countItems5S}]" class="form-control file_5s" value="" placeholder="<?= lang('File') ?>">
                </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="remove5S(this)"></i></td>`;
        tr5s = `<tr>
                    ${tdNumbers}
                    ${td5S}
                    ${tdFile}
                    ${tdActions}
                </tr>`;
        $(`#tb-cleaning_5s`).find('tbody').append(tr5s);
        countItems5S++;
        orderStt($(`#tb-cleaning_5s`));
    }

    function addPCCC() {
        tdNumbers = `<td class="stt text-center"></td>`;
        td5S = `<td>
                        <input type="text" name="items_pccc[${countItems5S}][name]" class="form-control form-control" value="">
                    </td>`;
        tdFile = `<td>
                    <textarea name="items_pccc[${countItems5S}][note]" class="form-control"></textarea>
                    <input type="file" name="file_pccc[${countItems5S}]" class="form-control file_pccc" value="" placeholder="<?= lang('File') ?>">
                </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removepccc(this)"></i></td>`;
        tr5s = `<tr>
                    ${tdNumbers}
                    ${td5S}
                    ${tdFile}
                    ${tdActions}
                </tr>`;
        $(`#tb-pccc`).find('tbody').append(tr5s);
        countItems5S++;
        orderStt($(`#tb-pccc`));
    }

    function addAccreditation() {
        tdNumbers = `<td class="stt text-center"></td>`;
        td5S = `<td>
                        <input type="text" name="items_accreditation[${countItems5S}][name]" class="form-control form-control" value="">
                    </td>`;
        tdFile = `<td>
                    <textarea name="items_accreditation[${countItems5S}][note]" class="form-control"></textarea>
                    <input type="file" name="file_accreditation[${countItems5S}]" class="form-control file_accreditation" value="" placeholder="<?= lang('File') ?>">
                </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeaccreditation(this)"></i></td>`;
        tr5s = `<tr>
                    ${tdNumbers}
                    ${td5S}
                    ${tdFile}
                    ${tdActions}
                </tr>`;
        $(`#tb-accreditation`).find('tbody').append(tr5s);
        countItems5S++;
        orderStt($(`#tb-accreditation`));
    }

    function orderStt(table) {
        var list_stt = $(table).find('tr').find('.stt');
        $.each(list_stt, function(index, value) {
            $(value).html((index + 1));
        })
    }

    function remove5S(_this) {
        $(_this).closest('tr').remove();
        orderStt($(`#tb-cleaning_5s`));
    }

    function removepccc(_this) {
        $(_this).closest('tr').remove();
        orderStt($(`#tb-pccc`));
    }

    function removeaccreditation(_this) {
        $(_this).closest('tr').remove();
        orderStt($(`#tb-accreditation`));
    }


    var countItemsMainh = 0;

    function
    addMaintenanceh() {
        tdNumbers = `<td class="text-center td-numbers-maintenanceh"></td>`;
        tdProcess = `<td>
            <input type="text" name="refrigeration[${countItemsMainh}][name]" required class="form-control process" value="" placeholder="<?= lang('Tên bộ phận') ?>">
        </td>`;
        tdActions = `<td class="text-center text-danger"><i class="fa fa-remove tnh-icon-remove pointer" onclick="removeMaintenance(this)"></i></td>`;

        trProcess = `<tr>
            ${tdNumbers}
            ${tdProcess}
            ${tdActions}
        </tr>`;

        $('#tb-maintenanceh tbody').append(trProcess);
        countItemsMainh++;
        stt_maintenanceh();
    }

    function stt_maintenanceh() {
        var tr = $('#tb-maintenanceh tbody tr');
        $.each(tr, function(index, value) {
            $(value).find('.td-numbers-maintenanceh').text((index + 1));
        })
    }

    function removeMaintenanceh(_this) {
        $(_this).closest('tr').remove();
        stt_maintenanceh();
    }
</script>