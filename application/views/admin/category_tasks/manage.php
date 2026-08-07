<?php init_head(); ?>
<style>
    .bg-sive {
        background: #a7a7a7;
    }

    .bg-sive td {
        padding-top: 1px !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= !empty($title) ? $title : '' ?></span>
                <div class="line-sp"></div>
                <!-- <a class="btn btn-info mright5 test pull-right H_action_button c_modal" href="<? //= admin_url('category_tasks/modal') 
                                                                                                    ?>">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php //echo _l('create_add_new'); 
                    ?>
                </a> -->
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/category_tasks/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <div class="line-sp"></div>
                <a href="javascript:void(0)" id="btn_export_category_tasks" class="btn btn-success pull-right mright10 H_action_button">
                    <i class="fa fa-download" style="display: initial;" aria-hidden="true"></i>
                    EXPORT EXCEL
                </a>
                <div class="line-sp"></div>
                <!-- <a href="<?= base_url('admin/category_tasks/modal_import_update') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT UPDATE QUY TRÌNH'); ?>
                </a> -->
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-3">
                <?php echo render_select('role_search[]', (!empty($roles) ? $roles : []), ['roleid', 'name'], 'Chức vụ', [], ['multiple' => true, 'data-actions-box' => true]); ?>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="">
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?></a>
                                    </li>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $key => $value) : ?>
                                            <li role="presentation">
                                                <a href="#tab-<?= $value['id'] ?>" aria-controls="tab-<?= $value['id'] ?>" role="tab" value="<?= $value['id'] ?>" data-toggle="tab"><?= $value['name'] ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('STT'),
                            _l('Phòng ban'),
                            _l('Mã'),
                            _l('Tên công việc'),
                            _l('CHỨC VỤ PHÒNG BAN'),
                            _l('MÃ VỊ TRÍ'),
                            _l('Quy trình'),
                            _l('KPI +'),
                            _l('KPI -'),
                            _l('Quy Chuẩn Công Việc'),
                            _l('Mã vị trí duyệt'),
                            _l('Quy Chuẩn Duyệt'),
                            _l('Quy Chuẩn Kiểm Soát Hoàn Thành'),
                            _l('Định mức (Phút)'),
                            _l('Loại CV'),
                            _l('Định Mức Công Việc Phòng Ban'),
                            _l('Ngày Ban Hành'),
                            _l('Sử dụng'),
                            _l('options')
                        ), 'category_tasks'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $('#btn_export_category_tasks').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xuất...');

        // Lấy bộ lọc hiện tại giống DataTable
        var roleSearch = [];
        $('select[name="role_search[]"]').val() && (roleSearch = $('select[name="role_search[]"]').val() || []);

        $.ajax({
            url: admin_url + 'category_tasks/export_excel',
            type: 'POST',
            dataType: 'json',
            data: {
                csrf_token_name: hash,
                status_table: $('#status_table').val(),
                role_search: roleSearch
            },
            success: function(res) {
                if (res.result == 1) {
                    var link = document.createElement('a');
                    link.href = res.file;
                    link.download = res.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    alert_float('success', res.message);
                } else {
                    alert_float('danger', 'Xuất thất bại!');
                }
            },
            error: function() {
                alert_float('danger', 'Có lỗi xảy ra khi xuất Excel!');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-download" style="display:initial;"></i> EXPORT EXCEL');
            }
        });
    });
    var CustomersServerParams = {
        'status_table': '#status_table',
        'role_search': 'select[name="role_search[]"]',
    };

    var tAPI = '';


    tAPI = initDataTable('.table-category_tasks', admin_url + 'category_tasks/table', [], [0, 1, 2, 3, 4, 5], CustomersServerParams, [0, 'desc']);
    $('input[name="exclude_inactive"]').on('change', function() {
        tAPI.ajax.reload();
    });

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        tAPI.draw();
    });

    $(document).on('change', 'select[name="role_search[]"]', function(event) {
        tAPI.draw();
    });

    $('body').on('click', '._delete_row', function() {
        var _href = $(this).attr('href');
        if (confirm('Bạn có chắc chắn muốn xóa?')) {
            $.get(_href, function(result) {
                result = JSON.parse(result);
                alert_float(result.alert_type, result.message);
                if (result.success) {
                    tAPI.ajax.reload();
                }
                return false;
            }).fail(function(error) {
                alert_float('danger', error.responseText);
            });
        }
        return false;
    })
</script>