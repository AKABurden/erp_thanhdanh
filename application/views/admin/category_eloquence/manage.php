<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <a href="<?= admin_url('category_eloquence/modal_excel_import') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('c_import_excel'); ?></a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-category-elequence dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center"><?= _l('Mã Khoản Phép') ?></th>
                                        <th class="text-center"><?= _l('Tên Khoản Phép') ?></th>
                                        <th class="text-center"><?= _l('Tiêu Chí') ?></th>
                                        <th class="text-center"><?= _l('Giá Trị') ?></th>
                                        <th class="text-center"><?= _l('Đơn Vị Tính') ?></th>
                                        <th class="text-center"><?= _l('Công Thức Áp Dụng') ?></th>
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
    var filterList = {};
    var oTable;
    $(function(){
        oTable = initDataTable('.table-category-elequence', admin_url + 'category_eloquence/table', [0], [0], filterList, [0, 'desc']);
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
