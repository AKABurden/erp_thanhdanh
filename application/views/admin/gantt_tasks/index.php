<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" id="jquery-gantt-css" href="<?= base_url('assets/plugins/gantt/css/style.css?v=2.3.3') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('gantt.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .fn-gantt .ganttRed {
        background-color: #f44336;
    }

    .fn-gantt .ganttOrange {
        background-color: #ff9800;
    }

    .fn-gantt .ganttGreen {
        background-color: #4CAF50;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="row mbot10">
                            <div class="col-md-12">
                                <div class="pull-right">
                                    <table class="tnh-table table-hover table-bordered fn-gantt" cellpadding="3">
                                        <tbody>
                                            <tr>
                                                <td class="ganttGreen" style="width: 30px;"></td>
                                                <td><?= lang('tnh_ht') ?></td>
                                                <td class="ganttRed" style="width: 30px;"></td>
                                                <td><?= lang('tnh_trh') ?></td>
                                                <td class="ganttOrange" style="width: 30px;"></td>
                                                <td><?= lang('tnh_th') ?></td>
                                                <td class="ganttYellow" style="width: 30px;"></td>
                                                <td><?= lang('tnh_sth') ?></td>
                                                <td class="ganttPrimary" style="width: 30px;"></td>
                                                <td><?= lang('Chưa tới hạn') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row mbot10">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="customer" <?= !empty($rel_type) && $rel_type == 'customer' ? 'selected' : '' ?>>
                                            <?php echo _l('client'); ?>
                                        </option>
                                        <option value="order_production_details" <?= !empty($rel_type) && ($rel_type == 'order_production_details' || $rel_type == 'pod') ? 'selected' : '' ?>>
                                            <?php echo _l('order_production_details') ?>
                                        </option>
                                        <option value="orders" <?= !empty($rel_type) && $rel_type == 'orders' ? 'selected' : '' ?>>
                                            <?php echo _l('Đơn đặt hàng bán'); ?>
                                        </option>

                                        <option value="internal_proposal" <?= !empty($rel_type) && $rel_type == 'internal_proposal' ? 'selected' : '' ?>>
                                            <?php echo _l('Phiếu đề xuất nội bộ'); ?>
                                        </option>

                                        <option value="maintenance_ticket" <?= !empty($rel_type) && $rel_type == 'maintenance_ticket' ? 'selected' : '' ?>>
                                            <?php echo _l('Phiếu bảo trì'); ?>
                                        </option>

                                        <option value="materials" <?= !empty($rel_type) && $rel_type == 'materials' ? 'selected' : '' ?>>
                                            <?php echo _l('materials'); ?>
                                        </option>

                                        <option value="plan_propose" <?= !empty($rel_type) && $rel_type == 'plan_propose' ? 'selected' : '' ?>>
                                            <?php echo _l('Phiếu kế hoạch'); ?>
                                        </option>

                                        <option value="production_report" <?= !empty($rel_type) && $rel_type == 'production_report' ? 'selected' : '' ?>>
                                            <?php echo _l('Phiếu báo cáo không phụ hợp'); ?>
                                        </option>

                                        <option value="products" <?= !empty($rel_type) && $rel_type == 'products' ? 'selected' : '' ?>>
                                            <?php echo _l('products'); ?>
                                        </option>

                                        <option value="suggest_check" <?= !empty($rel_type) && $rel_type == 'suggest_check' ? 'selected' : '' ?>>
                                            <?php echo _l('Phiếu yêu cầu kiểm tra vệ sinh ATLĐ-5S'); ?>
                                        </option>

                                        <option value="supplier" <?= !empty($rel_type) && $rel_type == 'supplier' ? 'selected' : '' ?>>
                                            <?php echo _l('Nhà cung cấp'); ?>
                                        </option>

                                        <option value="work_plan_task" <?= !empty($rel_type) && $rel_type == 'work_plan_task' ? 'selected' : '' ?>>
                                            <?php echo _l('Kế hoạch công việc'); ?>
                                        </option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" id="rel_id_wrapper">
                                    <label for="rel_id" class="control-label"><span class="rel_id_label">-</span></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id" class="ajax-sesarch selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if (!empty($rel_id) && !empty($rel_type)) {
                                                $rel_data = get_relation_data($rel_type, $rel_id);
                                                $rel_val = get_relation_values($rel_data, $rel_type);
                                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 hide">
                                <div class="form-group">
                                    <label for="department_search" class="control-label department_search">Phiếu liên quan đến</label>
                                    <select name="department_search" id="department_search" data-none-selected-text="Phiếu liên quan đến" class="form-control department_search selectpicker">
                                        <option value=""></option>
                                        <?php
                                            $room = get_table_where('tbl_room');
                                        ?>
                                        <?php if($room): ?>
                                            <?php foreach($room as $key => $value): ?>
                                                <option <?= !empty($department_search) && $department_search == $value['id'] ? 'selected' : '' ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status_search" class="control-label status_search">Trạng thái</label>
                                    <select name="status_search" id="status_search" data-none-selected-text="Trạng thái" class="form-control status_search selectpicker">
                                        <option value=""></option>
                                        <option <?= !empty($status_search) && $status_search == 1 ? 'selected' : '' ?> value="1">Chưa tới hạn</option>
                                        <option <?= !empty($status_search) && $status_search == 2 ? 'selected' : '' ?> value="2">Sắp tới hạn</option>
                                        <option <?= !empty($status_search) && $status_search == 3 ? 'selected' : '' ?> value="3">Tới hạn</option>
                                        <option <?= !empty($status_search) && $status_search == 4 ? 'selected' : '' ?> value="4">Trễ hạn</option>
                                        <option <?= !empty($status_search) && $status_search == 5 ? 'selected' : '' ?> value="5">Hoàn thành</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mtop30">
                                <button type="submit" name="search" value="search" class="btn btn-info"><?= lang('search') ?></button>
                                <button type="submit" name="search" value="unsearch" class="btn btn-danger"><?= lang('tnh_un_search') ?></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="gantt"></div>
                        <div class="text-center pagination-wrapper">
                            <ul class="pagination" id="pagination">
                                <?php
                                // $page = 1;
                                // for ($i = 0; $i < $numPages; $i++) {
                                //     $active = $page == $pageCurrent ? 'active' : '';
                                //     echo '<li class="' . $active . '"><a href="' . base_url('admin/gantt?page=' . $page) . '">' . $page . '</a></li>';
                                //     $page++;
                                // }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<style>
    .fn-gantt .leftPanel {
        width: <?= !empty($widthTitle) ? $widthTitle : 0 ?>px;
        font-size: 12px;
    }

    .fn-gantt .leftPanel .fn-label {
        width: 100%;
        font-size: 12px;
    }

    .fn-gantt .leftPanel .name {
        width: calc(100% - 150px);
        font-size: 12px;
    }

    .spanLeft {
        width: <?= (100 / $num_row) + 10 ?>% !important;
        float: left;
        text-align: left;
        font-size: 12px;
    }

    .spanRight {
        /*margin-left: 5px;*/
        width: <?= (100 / $num_row) - (10 / ($num_row - 1)) ?>% !important;
        float: left;
        text-align: center;
        font-size: 12px;
    }

    .spanRightMax {
        /*margin-left: 5px;*/
        width: <?= 100 - ((100 / $num_row) + 10) ?>% !important;
        float: left;
        font-size: 12px;
    }
</style>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" id="jquery-gantt-js" src="<?= base_url('assets/plugins/gantt/js/jquery.fn.gantt.min.js?v=2.3.3') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
    };
    var oTable = '';
</script>
<script type="text/javascript">
    var gantt_data = {};
    var widthTitle = <?= !empty($widthTitle) ? $widthTitle : 600 ?>;
    var nameGroupTitle = <?= json_encode($nameGroupTitle) ?>;
    var nunRow = <?= $num_row ?>;
</script>
<script>
    $(function() {
        ajaxSelectParamsCallback('#productions_orders', 'admin/gantt/searchwork_list', $('#productions_orders').val(), false);
        <?php if (!empty($gantt_data)): ?>
            gantt_data = <?= json_encode($gantt_data) ?>;
        <?php endif ?>
        $(function() {
            $("#gantt").gantt({
                source: gantt_data,
                itemsPerPage: 10000000000000,
                months: app.months_json,
                navigate: 'scroll',
                onRender: function(ev) {
                    var descLabelLength = $('.leftPanel .desc .fn-label').length;
                    for (var i = 0; i < descLabelLength; i++) {
                        el = $('.leftPanel .desc .fn-label')[i];
                        descLabel = $(el).html();

                        if (descLabel == "productions_orders") {
                            var divRow = $(el).parent('div');
                            divRow.addClass('spanRightMax');
                            divRow.prev('div').addClass('spanLeft');

                            var widthRow = divRow.prev('div').width();
                            widthRow = (widthTitle - widthRow) / (nunRow - 1);
                            $(el).html('');
                            // console.log(nameGroupTitle[i])
                            // $.each(nameGroupTitle[i], function(index, value) {
                            $.each(nameGroupTitle[0], function(index, value) {
                                $(el).append(`<span style="width:${widthRow}px;float: left;font-weight: 600;text-align: center;">${value}</span>`);
                            });
                        }
                    }
                    $('#gantt .leftPanel .name .fn-label:empty').parents('.name').css({
                        'background': 'initial',
                        'width': '0px',
                    });
                    var emptyLength = $('#gantt .leftPanel .name .fn-label:empty').length;
                    for (var i = 0; i < emptyLength; i++) {
                        el = $('#gantt .leftPanel .name .fn-label:empty')[i];
                        // console.log($(el));
                        descNext = $(el).closest('div').next('.desc');
                        descNext.css({
                            'width': widthTitle + 'px',
                        });
                    }
                },
                onItemClick: function(data) {
                    if (typeof(data.production_order_detail_id) != 'undefined') {
                        init_task_modal(data.production_order_detail_id);
                    }

                    if (typeof(data.id_detail) !== 'undefined') {
                        init_task_modal(data.id_detail);
                    }
                },
            });
        });
    });
</script>

<script>
    var _rel_id = $('#rel_id'),
        _rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper'),
        data = {};

    _rel_type.on('change', function() {
        var clonedSelect = _rel_id.html('').clone();
        _rel_id.selectpicker('destroy').remove();
        _rel_id = clonedSelect;
        $('#rel_id_select').append(clonedSelect);
        $('.rel_id_label').html(_rel_type.find('option:selected').text());

        task_rel_select();
        if ($(this).val() != '') {
            _rel_id_wrapper.removeClass('hide');
        } else {
            _rel_id_wrapper.addClass('hide');
        }

        rel_type_custom = _rel_type.val();
        if (rel_type_custom == 'order_production_details') {
            $('.custom-pod').css('display', 'block');
        } else {
            $('.custom-pod').css('display', 'none');
        }

        init_project_details(_rel_type.val());
    });

    function task_rel_select() {
        var serverData = {};
        serverData.rel_id = _rel_id.val();
        data.type = _rel_type.val();
        init_ajax_search(_rel_type.val(), _rel_id, serverData);
    }

    function init_project_details(type, tasks_visible_to_customer) {
        var wrap = $('.non-project-details');
        var wrap_task_hours = $('.task-hours');
        if (type == 'project') {
            if (wrap_task_hours.hasClass('project-task-hours') == true) {
                wrap_task_hours.removeClass('hide');
            } else {
                wrap_task_hours.addClass('hide');
            }
            wrap.addClass('hide');
            $('.project-details').removeClass('hide');
        } else {
            wrap_task_hours.removeClass('hide');
            wrap.removeClass('hide');
            $('.project-details').addClass('hide');
            $('.task-visible-to-customer').addClass('hide').prop('checked', false);
        }
        if (typeof(tasks_visible_to_customer) != 'undefined') {
            if (tasks_visible_to_customer == 1) {
                $('.task-visible-to-customer').removeClass('hide');
                $('.task-visible-to-customer input').prop('checked', true);
            } else {
                $('.task-visible-to-customer').addClass('hide')
                $('.task-visible-to-customer input').prop('checked', false);
            }
        }
    }

    $(document).ready(function () {
        task_rel_select();
    });
</script>
<script>
    const totalPages = <?= $numPages ?? 0 ?>; // Tổng số trang
    const maxVisiblePages = 5; // Số trang hiển thị đồng thời
    const pagination = $('#pagination');

    function renderPagination(currentPage) {
        pagination.empty(); // Xóa phân trang cũ

        // Nút "Previous"
        var page = Math.max(1, currentPage - 1);
        pagination.append(`
            <li class="${currentPage === 1 ? 'disabled' : ''}">
                <a href="${site.base_url+'admin/gantt?page='+page}" data-page="${page}">&laquo;</a>
            </li>
        `);

        // Tính toán nhóm trang
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // Hiển thị dấu "..." nếu cần
        if (startPage > 1) {
            pagination.append(`<li><a data-page="1">1</a></li>`);
            if (startPage > 2) {
                pagination.append(`<li class="disabled"><span>...</span></li>`);
            }
        }

        // Hiển thị các trang trong nhóm
        for (let i = startPage; i <= endPage; i++) {
            pagination.append(`
          <li class="${i === currentPage ? 'active' : ''}">
            <a href="${site.base_url+'admin/gantt?page='+i}" data-page="${i}">${i}</a>
          </li>
        `);
        }

        // Hiển thị dấu "..." nếu cần
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pagination.append(`<li class="disabled"><span>...</span></li>`);
            }
            pagination.append(`<li><a href="${site.base_url+'admin/gantt?page='+totalPages}" data-page="${totalPages}">${totalPages}</a></li>`);
        }

        // Nút "Next"
        page = Math.min(totalPages, currentPage + 1);
        pagination.append(`
        <li class="${currentPage === totalPages ? 'disabled' : ''}">
          <a href="${site.base_url+'admin/gantt?page='+page}" data-page="${page}">&raquo;</a>
        </li>
      `);
    }

    function handlePaginationClick(e) {
        // e.preventDefault();
        const page = parseInt($(this).data('page'), 10);

        if (!isNaN(page) && page >= 1 && page <= totalPages) {
            $('#current-page').text(page); // Cập nhật số trang hiện tại
            renderPagination(page); // Cập nhật giao diện phân trang
        }
    }

    // Khởi tạo
    $(document).ready(() => {
        const initialPage = <?= $pageCurrent ?? 1 ?>; // Trang mặc định
        $('#current-page').text(initialPage);
        renderPagination(initialPage);

        // Xử lý sự kiện click
        pagination.on('click', 'a', handlePaginationClick);
    });
</script>