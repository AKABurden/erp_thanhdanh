<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-responsive {
        overflow: visible !important;
    }
    .dataTables_wrapper {
        overflow: visible !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : 'Phiếu Yêu Cầu Phát Triển Mẫu (PTM)' ?></span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo render_select('filter_customer', $customers, array('id', 'name'), 'Khách hàng', '', array('data-none-selected-text' => 'Tất cả khách hàng')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo render_select('filter_order', $orders, array('id', 'reference_no'), 'Đơn hàng', '', array('data-none-selected-text' => 'Tất cả đơn hàng')); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo render_select('filter_quote', $quotes, array('id', 'reference_no'), 'Báo giá', '', array('data-none-selected-text' => 'Tất cả báo giá')); ?>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />
                    <?php
                    $table_data = array(
                        'STT',
                        'Mã Phiếu YCPTM',
                        'Ngày tạo',
                        'Mã đơn hàng',
                        'Mã báo giá',
                        'Khách hàng',
                        'Phiếu công việc',
                        'Người tạo',
                        'Tác vụ'
                    );
                    render_datatable($table_data, 'ptm table-bordered');
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
    var filter_data = {
        'filter_customer': '[name="filter_customer"]',
        'filter_order': '[name="filter_order"]',
        'filter_quote': '[name="filter_quote"]'
    };
    initDataTable('.table-ptm', admin_url + 'ptm/get_ptms', [], [], filter_data, [0, 'desc']);
    
    $.each(filter_data, function(key, selector){
        $(selector).on('change', function(){
            $('.table-ptm').DataTable().ajax.reload();
        });
    });
});

function create_ptm_task(ptm_id) {
    if (confirm("Bạn có chắc chắn muốn tạo phiếu phân công cho PTM này?")) {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.post(admin_url + 'ptm/create_task_ajax/' + ptm_id, data, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                alert_float('success', response.message);
                $('.table-ptm').DataTable().ajax.reload();
            } else {
                alert_float('danger', response.message);
            }
        });
    }
}
</script>
