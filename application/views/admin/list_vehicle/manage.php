<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=!empty($title) ? $title : ''?></span>
            <?php if($this->HasCreate) {?>
                <a href="<?= admin_url('list_vehicle/detail') ?>" class="btn btn-info mright5 test pull-right H_action_button c_modal"><?php echo _l('Thêm mới'); ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_input('search_transporters', 'Mã Vận Chuyển')?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('search_code_vehicle', 'Mã Phương Tiện')?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_input('search_type_vehicle', 'Loại Phương Tiện')?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table class="table table-list_vehicle dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center"><?= _l('Nhà Vận Chuyển') ?></th>
                                        <th class="text-center"><?= _l('Hình Ảnh') ?></th>
                                        <th class="text-center"><?= _l('Mã Vận Chuyển') ?></th>
                                        <th class="text-center"><?= _l('Loại Phương Tiện') ?></th>
                                        <th class="text-center"><?= _l('Đơn Vị Tính') ?></th>
                                        <th class="text-center"><?= _l('Điểm Đi') ?></th>
                                        <th class="text-center"><?= _l('Điểm Đến') ?></th>
                                        <th class="text-center"><?= _l('Số KM') ?></th>
                                        <th class="text-center"><?= _l('Đơn Giá') ?></th>
                                        <th class="text-center"><?= _l('Đơn Vị Tiền Tệ') ?></th>
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
    var oTable;
    var filterList = {
        'search_transporters' : '[name="search_transporters"]',
        'search_code_vehicle' : '[name="search_code_vehicle"]',
        'search_type_vehicle' : '[name="search_type_vehicle"]',
    };
  
    $(function(){
        oTable = initDataTable('.table-list_vehicle', admin_url + 'list_vehicle/table', [0], [0], filterList, [0, 'desc']);
        $.each(filterList, function(i, filter){
            $(filter).on('change', function(e){
                oTable.draw("page");
            })
        })
    });
    
    
    $('body').on('click', '.deleteItems', function() {
        if(confirm('Dữ liệu xóa không thể khôi phục?')) {
            var href = $(this).attr('data-href');
            if(href) {
                var data = {};
                if (typeof (csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['is_delete'] = 1;
                $.post(href, data, function(data) {
                    data = JSON.parse(data);
                    alert_float(data.alert_type, data.message);
                    if(data.success) {
                        oTable.draw("page");
                    }
                }).error(function (response) {
                    alert_float('danger', response.responseText);
                })
            }
        }
    })

</script>
</body>
</html>
