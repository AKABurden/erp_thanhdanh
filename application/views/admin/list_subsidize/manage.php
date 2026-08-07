<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= admin_url('list_subsidize/modal_excel_import') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('c_import_excel'); ?></a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-list-subsidize dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center"><?= _l('Tên Trợ Cấp') ?></th>
                                        <th class="text-center"><?= _l('Mã Vị Trí') ?></th>
                                        <th class="text-center"><?= _l('Tên Nhân Viên') ?></th>
                                        <th class="text-center"><?= _l('Thời Gian Bắt Đầu') ?></th>
                                        <th class="text-center"><?= _l('Thời Gian Kết Thúc') ?></th>
                                        <th class="text-center"><?= _l('Số Lượng Con Nhỏ') ?></th>
                                        <th class="text-center"><?= _l('Giá Trị') ?></th>
                                        <th class="text-center"><?= _l('Đơn Vị Tính') ?></th>
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
        oTable = initDataTable('.table-list-subsidize', admin_url + 'list_subsidize/table', [0], [0], filterList, [0, 'desc']);
    });
    
    
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
</body>
</html>
