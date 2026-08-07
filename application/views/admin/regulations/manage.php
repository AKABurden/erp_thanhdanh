<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
	.H_action_button i {
      display: contents!important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <a href="<?= admin_url('regulations/modal_excel_import/' . (!empty($type) ? $type : 1)) ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><i class="fa fa-upload"></i> <?php echo _l('c_import_excel'); ?></a>
            <a href="<?= admin_url('regulations/export_excel/' . (!empty($type) ? $type : 1)) ?>" target="_blank" class="btn btn-info mright5 test pull-right H_action_button"><i class="fa fa-download"></i> <?php echo _l('Xuất Excel'); ?></a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-regulations dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center"><?= _l('Mã Danh Mục') ?></th>
                                        <th class="text-center"><?= _l('Tên Danh Mục') ?></th>
                                        <th class="text-center"><?= _l('Mã ' . (!empty($name) ? $name : '')) ?></th>
                                        <th class="text-center"><?= _l('Tên ' . (!empty($name) ? $name : '')) ?></th>
                                        <th class="text-center"><?= _l('Loại '  . (!empty($name) ? $name : '')) ?></th>
                                        <th class="text-center"><?= _l('Nội Dung') ?></th>
                                        <th class="text-left"><?= _l('Hướng Dẫn-Tiêu Chí') ?></th>
                                        <th class="text-left"><?= _l('Lần Vi Phạm') ?></th>
                                        <th class="text-left"><?= _l('Hình Thức Xử Lý') ?></th>
                                        <th class="text-left"><?= _l('Ngày Ban Hành') ?></th>
                                        <th class="text-left"><?= _l('Thời gian sử dụng') ?></th>
                                        <th class="text-left"><?= _l('Đề Xuất Nội Bộ') ?></th>
                                        <th class="text-center" style="width: 50px;"><?= _l('options') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var filterList = {
        'datestart' : '[name="date_start"]',
        'dateend' : '[name="date_end"]',
    };
    var oTable;
    $(function(){
        oTable = initDataTable('.table-regulations', admin_url + 'regulations/table/<?=!empty($type) ? $type : 1?>', [0], [0], filterList, [0, 'desc']);
    })
    
    $('body').on('click', '.deleteItems', function() {
        if(confirm('Dữ liệu xóa không thể khôi phục?')) {
            var href = $(this).attr('data-href');
            if(href) {
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                $.post(href, data, function(data) {
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success) {
                        oTable.draw("page");
                    }
                })
            }
        }
    })
</script>
