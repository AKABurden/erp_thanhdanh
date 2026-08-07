<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    #table-kpi-equipment-stage th,
    #table-kpi-equipment-stage td {
        white-space: nowrap;
    }

    #table-kpi-equipment-stage tr td:nth-child(1) {
        width: 50px;
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" style="margin-bottom: unset">
        <div class="panel-body">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title">KPI Thiết Bị Công Đoạn</span>
                <?php if (has_permission('kpi_equipment_stage', '', 'create')): ?>
                    <!-- <div class="pull-right mright5 H_border">
                        <a href="<? //= base_url('admin/kpi_equipment_stage/detail') 
                                    ?>"
                            class="tnh-modal btn btn-info H_action_button">
                            <?php //echo _l('add'); 
                            ?>
                        </a>
                    </div> -->
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/kpi_equipment_stage/import_excel') ?>"
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
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 table-responsive">
                            <table id="table-kpi-equipment-stage" class="table dt-tnh" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:30px;">
                                            <input type="checkbox" id="checkAll" title="Chọn tất cả">
                                        </th>
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Nhóm công đoạn</th>
                                        <th class="text-center">Mã công đoạn</th>
                                        <th class="text-center">Tên công đoạn</th>
                                        <th class="text-center">Mã thiết bị</th>
                                        <th class="text-center">Tên thiết bị</th>
                                        <th class="text-center">Trạng thái TB</th>
                                        <th class="text-center">TG ngừng (phút)</th>
                                        <th class="text-center">Nguyên nhân ngừng</th>
                                        <th class="text-center">Số lần SC</th>
                                        <th class="text-center">TG sửa chữa (phút)</th>
                                        <th class="text-center">Bảo trì định kỳ</th>
                                        <th class="text-center">BT gần nhất</th>
                                        <th class="text-center">Hiệu chuẩn</th>
                                        <th class="text-center">HC gần nhất</th>
                                        <th class="text-center">NPL CB (%)</th>
                                        <th class="text-center">Số lỗi</th>
                                        <th class="text-center">Tỷ lệ lỗi (%)</th>
                                        <th class="text-center">NS định mức</th>
                                        <th class="text-center">SL thực tế</th>
                                        <th class="text-center">TL đạt KH (%)</th>
                                        <th class="text-center">Ngân sách TB</th>
                                        <th class="text-center">CP sửa chữa</th>
                                        <th class="text-center">CP bảo trì</th>
                                        <th class="text-center">Tổng CP</th>
                                        <th class="text-center">TT cảnh báo</th>
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
    var fnserverparams = {};

    oTable = tnhInitDataTable('#table-kpi-equipment-stage',
        '<?= site_url('admin/kpi_equipment_stage/get_list') ?>', {
            'order': [
                [1, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/kpi_equipment_stage/get_list') ?>',
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
            "createdRow": function(row, data, index) {

            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false,
                "searchable": false,
                "width": "30px",
                "className": "dt-center"
            }],
        });

    // Check all
    $(document).on('change', '#checkAll', function() {
        $('#table-kpi-equipment-stage tbody input.row-check').prop('checked', this.checked);
    });

    // Xuất Excel
    $('#btn-export-excel').on('click', function() {
        var checked = $('#table-kpi-equipment-stage tbody input.row-check:checked');
        var form = $('<form method="POST" action="<?= site_url('admin/kpi_equipment_stage/export_excel') ?>"></form>');

        if (typeof csrfData !== 'undefined') {
            form.append($('<input type="hidden">').attr('name', csrfData.token_name).val(csrfData.hash));
        }

        if (checked.length > 0) {
            // Xuất các dòng đã chọn: lấy id từ attribute data-id trên checkbox
            checked.each(function() {
                var id = $(this).val();
                if (id) {
                    form.append($('<input type="hidden" name="ids[]">').val(id));
                }
            });
        }

        // Nếu không có ids[] → server xuất tất cả
        $('body').append(form);
        form.submit();
        form.remove();
    });
</script>