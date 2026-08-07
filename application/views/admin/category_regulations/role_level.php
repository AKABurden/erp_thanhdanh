<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= base_url('admin/category_regulations/importRoleLevel') ?>" class="tnh-modal pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddRoleLevel): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/category_regulations/detail_role_level') ?>" class="btn btn-info tnh-modal H_action_button">
                            <?php echo _l('add'); ?>
                        </a>
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
                        <div class="col-md-12">
                            <div class="row" style="margin-bottom:5px">
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-role-level" class="table dt-tnh table-role-level" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Tên cấp bậc') ?></th>
                                        <th class="text-center"><?= lang('Bằng cấp tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Tiếng anh tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Tiếng trung tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Số năm kinh nghiệm tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Kỹ năng IT') ?></th>
                                        <th class="text-center"><?= lang('Điểm sàn giá trị cốt lõi') ?></th>
                                        <th class="text-center"><?= lang('Điểm sàn kỹ năng tuân thủ') ?></th>
                                        <th class="text-center"><?= lang('Điểm tổng tối thiếu (Đạt)') ?></th>
                                        <th class="text-center"><?= lang('Trọng số giá trị cốt lõi <br>(GTCL)') ?></th>
                                        <th class="text-center"><?= lang('Trọng số kỹ năng tuân thủ <br>(TC)') ?></th>
                                        <th class="text-center"><?= lang('Trọng số chuyên môn <br>(CM)') ?></th>
                                        <th class="text-center"><?= lang('Trọng số kỹ năng mềm <br>(SK)') ?></th>
                                        <th class="text-center"><?= lang('Trọng số tư duy <br>(TD)') ?></th>
                                        <th class="text-center"><?= lang('CEO duyệt') ?></th>
                                        <th class="text-center"><?= lang('Điểm tối thiểu CEO yêu cầu') ?></th>
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
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $("#room_search").select2({
        placeholder: "<?= lang('Chọn phòng ban') ?>",
        allowClear: true,
    })
    ajaxSelectParams('#role_id_search', 'admin/suggest_task/searchRoles', 0, true, true);
    var oTable = '';

    var fnserverparams = {
        'room_search': '#room_search'
    };
    oTable = tnhInitDataTable('#table-role-level',
        '<?= site_url('admin/category_regulations/getRoleLevel') ?>', {
            'order': [
                [0, 'desc']
            ],
            "ajax": {
                "url": '<?= site_url('admin/category_regulations/getRoleLevel') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "createdRow": function(row, data, index) {
            },
            "columnDefs": [
            ],
        });

    $(document).on('change',
        '#room_search',
        function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    function exportExcel() {
        room_search = $('#room_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/category_regulations/exportExcelRoleLevel',
            data: {
                csrf_token_name: hash,
                room_search: room_search,
                export_excel: 1,
            },
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>