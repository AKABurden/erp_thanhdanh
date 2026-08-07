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
                <!-- <a class="btn btn-info mright5 test pull-right H_action_button c_modal" href="<?//= admin_url('category_tasks/modal') ?>">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php //echo _l('create_add_new'); ?>
                </a> -->
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/category_tasks/modal_excel') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT EXCEL'); ?>
                </a>
                <div class="line-sp"></div>
                <a href="<?= base_url('admin/category_tasks/modal_import_update') ?>" class="btn btn-info pull-right mright10 H_action_button c_modal">
                    <i class="fa fa-upload" style="display: initial;" aria-hidden="true"></i>
                    <?php echo _l('IMPORT UPDATE QUY TRÌNH'); ?>
                </a>
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
                                    <?php if(!empty($departments)): ?>
                                        <?php foreach ($departments as $key => $value) : ?>
                                            <li role="presentation">
                                                <a href="#tab-<?= $value['departmentid'] ?>" aria-controls="tab-<?= $value['departmentid'] ?>" role="tab" value="<?= $value['departmentid'] ?>" data-toggle="tab"><?= $value['name'] ?></a>
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
                            _l('Mã'),
                            _l('Phòng ban'),
                            _l('Chức vụ cấp 1'),
                            _l('Chức vụ cấp 2'),
                            _l('Định mức (Phút)'),
                            _l('Nội dung'),
                            _l('Quy trình'),
                            _l('Loại CV'),
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