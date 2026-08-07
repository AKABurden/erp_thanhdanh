<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="pull-right">
                    <?php if ($this->preAdd): ?>
                        <div class="pull-right mright5 H_border">
                            <a data-tnh="modal" href="<?= admin_url('entrance_ticket/detail') ?>" class="btn btn-info tnh-modal H_action_button" data-toggle="modal" data-target="#myModal">
                                <?php echo _l('add'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if (is_admin()): ?>
                        <a href="<?= admin_url('entrance_ticket/locations') ?>" class="btn btn-info H_action_button pull-right mright5">
                            <i class="fa fa-cog"></i> <?= lang('Quản lý vị trí') ?>
                        </a>
                    <?php endif; ?>
                    <a href="javascript:void(0)" onclick="exportEntranceExcel()" class="btn btn-info H_action_button pull-right mright5">
                        <?= lang('Xuất Excel') ?>
                    </a>
                    <button class="btn btn-default H_action_button pull-right mright5 hide" data-toggle="collapse" data-target="#search-tnh">
                        <i class="fa fa-filter"></i> <?= lang('Bộ lọc') ?>
                    </button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- ===== Bộ lọc ===== -->
                        <div id="search-tnh" class="collapse in" style="margin-bottom:10px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <?= lang('Từ ngày', 'start_date_search') ?>
                                    <input type="text" id="start_date_search" name="start_date_search"
                                        class="form-control datepicker" placeholder="DD/MM/YYYY" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <?= lang('Đến ngày', 'end_date_search') ?>
                                    <input type="text" id="end_date_search" name="end_date_search"
                                        class="form-control datepicker" placeholder="DD/MM/YYYY" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <?= lang('Trạng thái', 'status_filter') ?>
                                    <select id="status_filter" class="form-control">
                                        <option value="">-- Tất cả --</option>
                                        <option value="-1">Bị từ chối</option>
                                        <option value="0">Chờ duyệt</option>
                                        <option value="1">QA duyệt đi</option>
                                        <option value="2">BV xác nhận ra</option>
                                        <option value="3">BV xác nhận về</option>
                                        <option value="4">Hoàn tất</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button class="btn btn-primary" id="btn-search">
                                            <i class="fa fa-search"></i> <?= lang('search') ?>
                                        </button>
                                        <button class="btn btn-default hide" id="btn-reset">
                                            <i class="fa fa-refresh"></i> <?= lang('Reset') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===== DataTable ===== -->
                        <div class="clearfix"></div>
                        <table id="dt-entrance-ticket" class="table dt-tnh table-bordered table-hover" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:40px">STT</th>
                                    <th class="text-center"><?= lang('Mã Phiếu') ?></th>
                                    <th class="text-center"><?= lang('Ngày Lập') ?></th>
                                    <th class="text-center"><?= lang('Mã NV') ?></th>
                                    <th class="text-center"><?= lang('Họ Tên') ?></th>
                                    <th class="text-center"><?= lang('Vị Trí') ?></th>
                                    <th class="text-center"><?= lang('Phòng Ban') ?></th>
                                    <th class="text-center"><?= lang('Lý Do') ?></th>
                                    <th class="text-center"><?= lang('Điểm Đến') ?></th>
                                    <th class="text-center"><?= lang('Lộ Trình') ?></th>
                                    <th class="text-center"><?= lang('Chi Phí') ?></th>
                                    <th class="text-center"><?= lang('Số Xe') ?></th>
                                    <th class="text-center" style="width:100px"><?= lang('Số MH') ?></th>
                                    <th class="text-center" style="width:150px"><?= lang('Trạng Thái') ?></th>
                                    <th class="text-center" style="width:80px"><?= lang('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="15"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="id_filter" value="<?= $this->input->get("id") ?>">
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript">
    var oTable;

    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        status_filter: '#status_filter',
        id: '#id_filter',
    };

    $(function() {
        init_datepicker();

        oTable = tnhInitDataTable('#dt-entrance-ticket',
            '<?= site_url('admin/entrance_ticket/getTable') ?>', {
                'order': [
                    [0, 'desc']
                ],
                'ajax': {
                    'url': '<?= site_url('admin/entrance_ticket/getTable') ?>',
                    'type': 'POST',
                    'data': function(d) {
                        if (typeof csrfData !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparams) {
                            d[key] = $(fnserverparams[key]).val();
                        }
                    },
                    'dataSrc': function(json) {
                        return json.aaData;
                    }
                },
                'columnDefs': [],
                'createdRow': function(row, data, index) {},
                'initComplete': function(settings, json) {
                    var id_filter = '<?= $this->input->get("id") ?>';
                    if (id_filter) {
                        // Tìm link trong table và click
                        var $link = $('#dt-entrance-ticket tbody tr:first-child a.tnh-modal');
                        if ($link.length) {
                            $link[0].click();
                        }
                        
                        // // Xóa tham số trên URL để tránh refresh bị lại
                        $('#id_filter').val('');
                        var url = window.location.href.split('?')[0];
                        window.history.replaceState({}, document.title, url);
                    }
                }
            });

        // Tìm kiếm
        $('#btn-search').on('click', function() {
            oTable.draw();
        });

        // Reset
        $('#btn-reset').on('click', function() {
            $('#start_date_search, #end_date_search').val('');
            $('#status_filter').val('');
            oTable.draw();
        });
    });

    // Xuất Excel
    function exportEntranceExcel() {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['export_excel'] = 1;
        dataPOST['start_date_search'] = $('#start_date_search').val();
        dataPOST['end_date_search'] = $('#end_date_search').val();
        $.ajax({
            url: '<?= site_url('admin/entrance_ticket/exportExcel') ?>',
            type: 'POST',
            data: dataPOST,
            dataType: 'JSON',
            success: function(res) {
                if (res.result) {
                    download(res.filename, res.file);
                } else {
                    alert_float('danger', res.message || 'Xuất thất bại!');
                }
            }
        });
    }
</script>