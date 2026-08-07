<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <?php echo render_select('staff_search', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname']], 'Nhân viên đề xuất') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('category_tasks_search', (!empty($category_tasks) ? $category_tasks : []), ['id', 'code', 'content'], 'Mã công việc') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_start', 'Từ ngày') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_date_input('date_end', 'Đến ngày') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('type_plan_propose', (!empty($type_plan_propose) ? $type_plan_propose : []), ['id', 'name'], 'Loại kế hoạch') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php
                                    $this->db->select(
                                        'tbl_recommended_list.id as id, tbl_recommended_list.code as code, tbl_recommended_list.name as name',
                                        false
                                    );
                                    $this->db->from('tbl_recommended_list');
                                    $this->db->where('tbl_recommended_list.type_show', 1);
                                    $this->db->where('tbl_recommended_list.parent_id >', 0);
                                    $dtRecommendedList = $this->db->get()->result_array();
                                    ?>
                                    <?php echo render_select('recommended_list_id_search', (!empty($dtRecommendedList) ? $dtRecommendedList : []), ['id', 'code', 'name'], 'Loại đề xuất') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php
                                    $dtRecommendedListG = $this->recommended_list_model->getRecommendedListParent([0], 1);

                                    ?>
                                    <?php echo render_select('recommended_list_group_id_search', (!empty($dtRecommendedListG) ? $dtRecommendedListG : []), ['id', 'name'], 'Nhóm đề xuất') ?>
                                </div>
                                <div class="col-md-3">
                                    <?php echo render_select('staff_follow_search', (!empty($staff) ? $staff : []), ['staffid', ['firstname', 'lastname']], 'Nhân viên được phân công') ?>
                                    <div id="div_status_follow" class="hide">
                                        <?php echo render_select('status_follow', [
                                            [
                                                'id' => 1,
                                                'name' => 'Chưa Duyệt'
                                            ],
                                            [
                                                'id' => 2,
                                                'name' => 'Không Duyệt'
                                            ],
                                            [
                                                'id' => 3,
                                                'name' => 'Đã Duyệt'
                                            ]
                                        ], ['id', 'name'], 'Trạng thái phân công') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <table id="data-table" class="table-internal_proposal table dt-tnh table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="text-center" style="width: 30px;"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child-all fa fa-caret-right"></a></div>
                                        </th>
                                        <th class="text-center"><?= lang('STT') ?></th>
                                        <th class="text-center"><?= lang('proposals_sort_proposal_date') ?></th>
                                        <th class="text-center"><?= lang('intpro_code') ?></th>
                                        <th class="text-center"><?= lang('Chi nhánh') ?></th>
                                        <th class="text-center"><?= lang('Ngày hoàn thành') ?></th>
                                        <th class="text-center"><?= lang('Nhóm đề xuất') ?></th>
                                        <th class="text-center"><?= lang('Loại đề xuất') ?></th>
                                        <th class="text-center"><?= lang('Chi tiết đề xuất') ?></th>
                                        <th class="text-center"><?= lang('Nhóm nhà cung cấp') ?></th>
                                        <th class="text-center"><?= lang('Nhà cung cấp') ?></th>
                                        <th class="text-center"><?= lang('Loại phiếu yêu cầu') ?></th>
                                        <th class="text-center"><?= lang('Phiếu yêu cầu') ?></th>
                                        <th class="text-center"><?= lang('BCKPH') ?></th>
                                        <th class="text-center"><?= lang('intpro_staff') ?></th>
                                        <th class="text-center"><?= lang('Mã công việc') ?></th>
                                        <th class="text-center"><?= lang('Loại kế hoạch') ?></th>
                                        <th class="text-center"><?= lang('intpro_money') ?></th>
                                        <th class="text-center"><?= lang('intpro_content') ?></th>
                                        <th class="text-center"><?= lang('Trạng thái duyệt') ?></th>
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
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script>
    $(function() {
        var fnserverparams = {
            'filterStatus': '[name="filterStatus"]',
            'staff_search': '[name="staff_search"]',
            'category_tasks': '[name="category_tasks_search"]',
            'date_start': '[name="date_start"]',
            'date_end': '[name="date_end"]',
            'type_plan_propose': '[name="type_plan_propose"]',
            'status_table': '#status_table',
            'recommended_list_id_search': '#recommended_list_id_search',
            'recommended_list_group_id_search': '#recommended_list_group_id_search',
            'staff_follow_search': '#staff_follow_search',
            'status_follow': '#status_follow',
        };

        oTable = tnhInitDataTable('#data-table', '<?= site_url('admin/reports_propose/table') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/reports_propose/table') ?>',
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
            "columnDefs": [{
                "targets": 0,
                'sortable': false,
                'searchable': false,
            }, {
                "targets": 19,
                'sortable': false,
                'searchable': false,
                'visible': false,
            }],
        });
        $('body').on('change', 'input[name="filterStatus"], select[name="staff_search"], select[name="staff_follow_search"], select[name="status_follow"], select[name="category_tasks_search"] , select[name="type_plan_propose"], input[name="date_start"], input[name="date_end"], select[name="recommended_list_id_search"], select[name="recommended_list_group_id_search"]', function() {
            if (oTable) {
                oTable.draw('page');
            }
        })
    });

    $('#data-table').on('draw.dt', function() {
        var expenseReportsTable = $(this).DataTable();
        var total = expenseReportsTable.ajax.json().total;
        var numTotal = 0;
        $.each(total, function(i, v) {
            $('.filter_' + i).html('(' + tnhFormatNumber(v) + ')');
            numTotal += (v * 1)
        })
        $('.filter_all').html('(' + tnhFormatNumber(numTotal) + ')');
        $('.rows-child-all.fa-caret-right').trigger('click');
        $('.rows-child.fa-caret-right').trigger('click');
    });
    $('.table-internal_proposal tbody').on('click', 'td .rows-child', function() {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        if (row.child.isShown()) {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            $(this).removeClass('fa-caret-right');
            $(this).addClass('fa-caret-down');
            row.child(loadItemsTasks(row.data())).show();
            tr.addClass('shown');
        }
    });
    $('.table-internal_proposal thead').on('click', '.rows-child-all', function() {
        if ($(this).hasClass('fa-caret-right')) {
            $(this).addClass('fa-caret-down');
            $(this).removeClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-right');
                $(value).addClass('fa-caret-down');
                row.child(loadItemsTasks(row.data())).show();
                tr.addClass('shown');
            })
        } else {
            $(this).removeClass('fa-caret-down');
            $(this).addClass('fa-caret-right');
            var rows = $('td .rows-child');
            $.each(rows, function(index, value) {
                var tr = $(value).parents('tr');
                var row = oTable.row(tr);
                $(value).removeClass('fa-caret-down');
                $(value).addClass('fa-caret-right');
                row.child.hide();
                tr.removeClass('shown');
            })
        }

    });

    function loadItemsTasks(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        cHtml = cData[19];
        return `<div>${cHtml}</div>`;
    }
</script>