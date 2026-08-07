<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-department-budget th,
    #table-department-budget td {
        white-space: nowrap;
    }

    #table-department-budget tr td:nth-child(1) {
        width: 50px;
        text-align: center;
    }

    #table-department-budget tr td:nth-child(5) {
        width: 150px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" style="margin-bottom: unset">
        <div class="panel-body">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title">KPI Ngân Sách Phòng Ban</span>
                <?php if (has_permission('department_budget', '', 'create')): ?>
                    <div class="pull-right mright5 H_border hide">
                        <a href="<?= base_url('admin/department_budget/detail') ?>"
                            class="tnh-modal btn btn-info H_action_button">
                            <i class="fa fa-plus"></i> Thêm mới
                        </a>
                    </div>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/department_budget/import_excel') ?>"
                            class="tnh-modal btn btn-success H_action_button">
                            <i class="fa fa-upload"></i> Import Excel
                        </a>
                    </div>
                    <div class="pull-right mright5 H_border">
                        <button type="button" id="btn-export-excel" class="btn btn-warning H_action_button">
                            <i class="fa fa-download"></i> Xuất Excel
                        </button>
                    </div>
                <?php endif ?>

                <!-- Filter phòng ban -->

            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-4">
                            <label for="filter-department">Phòng ban</label>
                            <select id="filter-department" class="form-control input-sm selectpicker" data-live-search="true" title="-- Tất cả phòng ban --">
                                <option value="">-- Tất cả phòng ban --</option>
                                <?php foreach ($dtDepartments as $dep): ?>
                                    <option value="<?= $dep['departmentid'] ?>"><?= $dep['code'] ?> - <?= $dep['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="clearfix"></div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <table id="table-department-budget" class="table dt-tnh" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:30px;">
                                            <input type="checkbox" id="checkAll" title="Chọn tất cả">
                                        </th>
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Mã PB</th>
                                        <th class="text-center">Tên phòng ban</th>
                                        <th class="text-center">Mã chi phí</th>
                                        <th class="text-center">Tên loại chi phí</th>
                                        <th class="text-center">Ngân sách được cấp</th>
                                        <th class="text-center">Chi phí thực tế (<?= date('Y') ?>)</th>
                                        <th class="text-center">Chênh lệch</th>
                                        <th class="text-center">Tỷ lệ SD (%)</th>
                                        <th class="text-center">Trạng thái NS</th>
                                        <th class="text-center">Điểm KPI</th>
                                        <th class="text-center">Ghi chú</th>
                                        <th class="text-center">Thao tác</th>
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
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    var oTable = '';
    var fnserverparams = {
        department_id: '#filter-department',
    };

    oTable = tnhInitDataTable('#table-department-budget',
        '<?= site_url('admin/department_budget/get_list') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "scrollX": true,
            "autoWidth": false,
            "ajax": {
                "url": '<?= site_url('admin/department_budget/get_list') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
                "searchable": false,
                "width": "30px",
                "className": "dt-center"
            }, {
                "targets": 5,
                "width": "200px",
                "className": "dt-center"
            }],
        });

    // Check all
    $(document).on('change', '#checkAll', function() {
        $('#table-department-budget tbody input.row-check').prop('checked', this.checked);
    });

    // Filter phòng ban → reload datatable
    $('#filter-department').on('change', function() {
        oTable.draw();
    });

    // Xuất Excel
    $('#btn-export-excel').on('click', function() {
        var checked = $('#table-department-budget tbody input.row-check:checked');
        var form = $('<form method="POST" action="<?= site_url('admin/department_budget/export_excel') ?>"></form>');

        if (typeof csrfData !== 'undefined') {
            form.append($('<input type="hidden">').attr('name', csrfData.token_name).val(csrfData.hash));
        }

        if (checked.length > 0) {
            checked.each(function() {
                var id = $(this).val();
                if (id) {
                    form.append($('<input type="hidden" name="ids[]">').val(id));
                }
            });
        }
        $('body').append(form);
        form.submit();
        form.remove();
    });
</script>