<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .tree-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tree-table td {
        padding: 10px;
        position: relative;
        /* Giữ nội dung chữ ở giữa ô để line khớp với chữ */
        vertical-align: middle;
    }

    .tree.level-1 { padding-left: 20px; }
    .tree.level-2 { padding-left: 50px; }

    /* ĐƯỜNG DỌC: Nối liền mạch không đứt đoạn */
    .tree::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 130px;
        width: 2px;
        background: #d6dde5;
        z-index: 1;
    }

    /* ĐƯỜNG NGANG + DẤU TÍCH Ở CUỐI */
    .tree::after {
        content: "✔";
        position: absolute;
        /* Thay đổi quan trọng: dùng 50% và transform để chống bể */
        top: 56%;
        left: 130px;
        width: 105px; /* Độ dài line cũ của bạn */
        height: 12px;
        border-left: 2px solid #d6dde5;
        border-bottom: 2px solid #d6dde5;
        border-radius: 0 0 0 8px;
        transform: translateY(-100%); /* Đưa line lên khớp tâm chữ */

        color: #28a745;
        font-weight: bold;

        /* Khoảng cách dấu tích tách ra khỏi line */
        padding-right: -10px;
        margin-right: -25px;
        white-space: nowrap;
        text-indent: 105px;
        z-index: 2;
    }

    /* ĐIỀU CHỈNH ĐỂ KHÔNG BỊ DƯ LINE */
    /* Hàng đầu tiên của nhóm: line dọc bắt đầu từ giữa ô */
    .row-parent + .row-child td.tree::before {
        top: 0%;
    }

    /* Hàng cuối cùng của nhóm: line dọc kết thúc ở giữa ô */
    .tree.last::before {
        bottom: 50%;
        height: auto; /* Reset cái 50% cũ của bạn */
    }

    /* Không vẽ line cho level 1 */
    .tree.level-1::before,
    .tree.level-1::after {
        display: none;
    }

    /* CSS cho border bảng giữ nguyên */
    .table tbody tr td{
        border-right: 0 !important;
        border-left: 0 !important;
    }
    .table tbody tr td:last-child{
        border-right: 1px solid #cedae6 !important;
    }
    .table tbody tr td:first-child{
        border-left: 1px solid #cedae6 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                <a href="<?= base_url('admin/roadmap_advancement/import') ?>" class=" tnh-modal pull-right mright5 btn btn-info H_action_button">
                    <?php echo _l('Import Excel'); ?>
                </a>
                <?php if ($this->preAddAdvancement): ?>
                    <div class="pull-right mright5 H_border">
                        <a href="<?= base_url('admin/roadmap_advancement/detail') ?>" class="btn btn-info H_action_button">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <?= lang('Phòng ban', 'room_search') ?>
                                        <select type="text" name="room_search" id="room_search" class="room_search modal-select2"
                                             style="width: 100%;">
                                            <option value="">Tất cả</option>
                                            <?php if (!empty($dtRoom)){ ?>
                                                <?php foreach ($dtRoom as  $key => $value){?>
                                                     <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="">
                                <table id="table-advancement" class="table dt-tnh table-advancement tree-table" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('Mã lộ trình') ?></th>
                                        <th class="text-center"><?= lang('Phòng ban') ?></th>
                                        <th class="text-center"><?= lang('Từ vị trí') ?></th>
                                        <th class="text-center"><?= lang('Lên vị trí') ?></th>
                                        <th class="text-center"><?= lang('Vị trí từ') ?></th>
                                        <th class="text-center"><?= lang('Vị trí đến') ?></th>
                                        <th class="text-center"><?= lang('Thời gian tối thiểu') ?></th>
                                        <th class="text-center"><?= lang('Điều kiện năng lực') ?></th>
                                        <th class="text-center"><?= lang('Điều kiện KPI') ?></th>
                                        <th class="text-center"><?= lang('Link đào tạo') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
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
    oTable = tnhInitDataTable('#table-advancement',
        '<?= site_url('admin/roadmap_advancement/getAdvancement') ?>', {
            'order': [
                [0, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/roadmap_advancement/getAdvancement') ?>',
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
    $('#table-advancement').on('draw.dt', function () {
        var table = $(this).DataTable();
        row_header();
    });
    function row_header() {
        var class_tr_parent = $('.row-parent');
        var class_tr = $('.row-child');

        $.each(class_tr_parent, function (index, value) {
            $(value).find('td:eq(1)').addClass('tree level-1');
        });

        $.each(class_tr, function (index, value) {
            // Gộp cột Phòng ban
            $(value).find('td:eq(2)').remove();
            $(value).find('td:eq(1)').attr('colspan', 2).addClass('tree level-2');
        });

        // TÌM HÀNG CUỐI CÙNG: Để cắt đường kẻ dọc đúng chỗ
        $('.row-child').removeClass('last');
        $('.row-child').each(function() {
            var nextRow = $(this).next();
            // Nếu dòng sau là dòng cha, hoặc là hết bảng, hoặc là dòng rỗng của DataTable
            if (nextRow.hasClass('row-parent') || nextRow.length === 0 || nextRow.find('td[colspan="99"]').length > 0) {
                $(this).find('td.tree').addClass('last');
            }
        });
    }
    function exportExcel() {
        room_search = $('#room_search').val();

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/roadmap_advancement/exportExcel',
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