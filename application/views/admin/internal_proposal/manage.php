<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= base_url('assets/treegrid/') ?>css/jquery.treegrid.css">
<style>
    .progressbar li:not(.initli) {
        width: 110px !important;
    }

    .select2-chosen {
        word-wrap: break-word !important;
        text-overflow: inherit !important;
        white-space: normal !important;
    }

    .select2-choice {
        height: auto;
    }

    #data-table img {
        height: 18px;
        width: 18px;
    }

    .btn-dt-reload {
        min-height: 32px;
    }

    .max-400 table {
        width: 100% !important;
    }


    #data-table table tbody tr td {
        border-bottom: 1px solid #cedae6;
        border-left: 1px solid #cedae6;
    }

    #data-table table tbody tr td {
        border-bottom: 1px solid #cedae6;
        border-left: 1px solid #cedae6;
        border-right: 1px solid #cedae6;
    }

    #data-table table>tbody>tr>td,
    #data-table table>tfoot>tr>td {
        padding: 10px 10px 5px 10px;
    }

    #data-table table>tbody>tr>td,
    #data-table table>tbody>tr>th,
    #data-table table>tfoot>tr>td,
    #data-table table>tfoot>tr>th,
    #data-table table>thead>tr>td,
    #data-table table>thead>tr>th {
        padding: 3px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }

    #data-table tr td:nth-child(4) {
        min-width: 80px;
        white-space: unset;
    }

    #data-table tr td:nth-child(5) {
        min-width: 80px;
        white-space: unset;
    }

    #data-table tr td:nth-child(6) {
        min-width: 80px;
        white-space: unset;
    }

    #data-table tr td:nth-child(7) {
        min-width: 80px;
        text-align: center;
        white-space: unset;
    }

    #data-table tr td:nth-child(8) {
        min-width: 80px;
        white-space: unset;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.4') ?>">
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
                <div class="line-sp"></div>
                <a href="javascript:void(0)" onclick="exportExcel()" class="btn btn-info H_action_button pull-right"><?= lang('Xuất excel') ?></a>
                <a href="" onclick="add(''); return false;" class="btn btn-info mright5 test pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('create_add_new'); ?></a>
                <div class="line-sp"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- data table -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
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

                        <div class="btn-group mbot10" style="width: 100%;">
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left disabled" style="display: block;">
                                    <i class="fa fa-angle-left"></i>
                                </div>
                                <div class="scroller scroller-right arrow-right" style="display: block;">
                                    <i class="fa fa-angle-right"></i>
                                </div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                        <li class="active">
                                            <a class="H_filter" data-id="">
                                                <?= _l('cong_all') ?> <b class="filter_all"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="0">
                                                Chưa duyệt <b class="filter_0"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="1">
                                                Đã duyệt <b class="filter_1"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="2">
                                                Không Duyệt <b class="filter_2"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="4">
                                                Chưa hoàn thành quy trình <b class="filter_4"></b>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="H_filter" data-id="3">
                                                Hoàn thành quy trình <b class="filter_3"></b>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="horizontal-scrollable-tabs">
                                <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                                <div class="horizontal-tabs">
                                    <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                        <li class="active">
                                            <a style="padding: 3px;">
                                                <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="all">
                                                    <?= _l('leads_all') ?>
                                                    <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                    <span class="check-show" style="float: left; margin-right: 5px;">
                                                    </span>
                                                </button>
                                            </a>
                                        </li>
                                        <?php if (!empty($recommended_list)) : ?>
                                            <?php foreach ($recommended_list as $key => $value) : ?>
                                                <li>
                                                    <a style="padding: 3px;">
                                                        <button style=" font-size: 11px;" type="button" id="btndata_all" data-toggle="tab" class="btn btn-info btn-search" data-value="<?= $value['id'] ?>">
                                                            <?= $value['name'] ?>
                                                            <span class="badge menu-badge bg-warning" id="all_status" style="position: absolute;top: 1px; right: -3px; background-color: #ff6f00;color: white;font-size: 14px;font-weight: bold;"></span>
                                                            <span class="check-show" style="float: left; margin-right: 5px;">
                                                            </span>
                                                        </button>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                    <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="filterStatus" id="filterStatus" value="">
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
                                    <!-- <th class="text-center"><?= lang('Quản lý Duyệt') ?></th>
                                    <th class="text-center"><?= lang('BOD Duyệt') ?></th> -->
                                    <th class="text-center"><?= lang('intpro_money') ?></th>
                                    <th class="text-center"><?= lang('intpro_content') ?></th>
                                    <th class="text-center"><?= lang('Công việc') ?></th>
                                    <th class="text-center"><?= lang('action') ?></th>
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
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('Cảnh báo tạo PO'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <?php
                echo form_open(admin_url('internal_proposal/update_submit'), array('id' => 'internal_proposal-form', 'class' => 'internal_proposal-form'));
                ?>
                <input id="id_dxnb" class="hide" id="id_dxnb" name="id_dxnb">
                <div id="table_html"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info"><?= _l('Xác nhận duyệt') ?></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?= _l('close') ?></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="modal"></div>
<?php init_tail(); ?>
<script>
    $('.action-menu').click();
    appValidateForm($('#internal_proposal-form'), {}, manage_internal_proposal);

    // $('#staff_follow_search').change(function() {
    //     if($(this).val()) {
    //         $('#div_status_follow').removeClass('hide');
    //     }
    //     else {
    //         $('#div_status_follow').addClass('hide');
    //     }
    // })
    function manage_internal_proposal(form) {
        var data = $(form).serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            // if (response.success == true) {
            //     oTable.draw();
            //     $('#confirm_warehous').modal('hide');
            // }
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw(false);
            } else {
                alert_float('danger', response.message);
            }
            $('#confirm_warehous').modal('hide');
        })
        return false;
    }

    function handling_status_internal(_internal_proposal_id, _status, type) {
        bootbox.confirm('Bạn có muốn duyệt không?', function(result) {
            if (result) {
                var data = {};
                if (typeof(csrfData) !== 'undefined') {
                    data[csrfData['token_name']] = csrfData['hash'];
                }
                data['internal_proposal_id'] = _internal_proposal_id;
                data['status'] = _status;
                data['type'] = type;

                $.ajax({
                    type: "POST",
                    url: site.base_url + 'admin/internal_proposal/handling_status_internal',
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        if (response.result == 0) {
                            alert_float('danger', response.message);
                        } else {
                            alert_float('success', response.message);
                            oTable.draw(false);
                        }
                    }
                });
            }
        });
    }

    init_editor('textarea[name="content"]');

    $('body').on('click', '.H_filter', function(e) {
        $('.H_filter').parent('li').removeClass('active');
        $(this).parent('li').addClass('active');
        $('input[name="filterStatus"]').val($(this).attr('data-id')).trigger('change');
        // tAPI.draw('page');
    });



    var oTable;

    $(document).on('click', '.status-table li a button', function(event) {
        status_table = $(this).attr('data-value');
        $('#status_table').val(status_table);
        oTable.draw(false);
    });

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

        oTable = tnhInitDataTable('#data-table', '<?= site_url('admin/internal_proposal/table') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            "ajax": {
                "url": '<?= site_url('admin/internal_proposal/table') ?>',
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
                "targets": 21,
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
        cHtml = cData[21];
        return `<div>${cHtml}</div>`;
    }

    function add(id) {
        $('#modal').html('');
        $.get(admin_url + 'internal_proposal/add_modal/' + id).done(function(response) {
            $('#modal').html(response);
            // $('#add_modal select[name="type"]').selectpicker('refresh');
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }

    function deleting(id) {
        var r = confirm("<?php echo _l('confirm_action_prompt'); ?>");
        if (r == false) {
            return false;
        } else {
            $.get(admin_url + 'internal_proposal/delete/' + id, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    oTable.draw(false);
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
        return false;
    };
    $('body').on('click', '#agree_status_staff', function() {
        var id = $(this).data('id');
        $.get(admin_url + 'internal_proposal/approve_status_staff/' + id, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            // if (response.success == 3) {
            //     var html = '<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
            //                 <thead>\
            //                     <tr>\
            //                         <th class="text-center"><?= lang('tnh_items') ?></th>\
            //                         <th class="text-center"><?= lang('tnh_dvt') ?></th>\
            //                         <th class="text-center"><?= lang('Số lượng yêu cầu') ?></th>\
            //                         <th class="text-center"><?= lang('Số lượng đã tạo PO') ?></th>\
            //                         <th class="text-center"><?= lang('Số lượng đề xuất') ?></th>\
            //                         <th class="text-center"><?= lang('Lưu ý') ?></th>\
            //                     </tr>\
            //                 </thead>\
            //                 <tbody>';
            //     html += response.html;
            //     html += '</tbody>\
            //             </table>';
            //     $('#confirm_warehous').modal('show');
            //     $('#table_html').html(html);
            //     $('#id_dxnb').val(id);
            //     return false;
            // }
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw(false);
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })
    $('body').on('click', '#agree', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        $.get(admin_url + 'internal_proposal/approve/' + id + '/' + status, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            if (response.success == 3) {
                var html = '<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                            <thead>\
                                <tr>\
                                    <th class="text-center"><?= lang('tnh_items') ?></th>\
                                    <th class="text-center"><?= lang('tnh_dvt') ?></th>\
                                    <th class="text-center"><?= lang('Số lượng yêu cầu') ?></th>\
                                    <th class="text-center"><?= lang('Số lượng đã tạo PO') ?></th>\
                                    <th class="text-center"><?= lang('Số lượng đề xuất') ?></th>\
                                    <th class="text-center"><?= lang('Lưu ý') ?></th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                html += response.html;
                html += '</tbody>\
                        </table>';
                $('#confirm_warehous').modal('show');
                $('#table_html').html(html);
                $('#id_dxnb').val(id);
                return false;
            }
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw(false);
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })
    $('body').on('click', '#not_agree_status_staff', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var reason = $(this).parents('.not_agree_status_staff').find('.reason').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        data['reason'] = reason;

        $.post(admin_url + 'internal_proposal/not_approve_status_staff', data, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            if (response.success) {
                alert_float('success', response.message);
                oTable.draw(false);
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })
    $('body').on('click', '#not_agree', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var reason = $(this).parents('.not_agree').find('.reason').val();
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;
        data['reason'] = reason;

        $.post(admin_url + 'internal_proposal/not_approve', data, function(response) {
            $('.popover').closest('div.popover').popover('hide');
            if (response.success) {
                alert_float('success', response.message);
                if (response.id_task) {
                    init_task_modal(response.id_task);
                }
                oTable.draw(false);
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
        return false;
    })
    $(document).on('click', '.po-close', function() {
        $('.popover').closest('div.popover').popover('hide');
        return false;
    });

    $('body').on('click', '.c_modal_tasks', function() {
        var url = $(this).attr('href');
        $.get(url, function(result) {
            $('.modal-backdrop.in').remove();
            $("#_task").html(result);
            $("body").find("#_task_modal").modal({
                show: !0,
                backdrop: "static"
            })

        }).error(function(response) {
            alert_float('danger', response.responseText);
        });
        return false;
    })
    $('#task-modal').on('hidden.bs.modal', function() {
        oTable.draw(false);
    });
    $(document).on('change', '.price_suppliers', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('change', '.mainQuantity_suppliers', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });

    $(document).on('change', 'input.suppliers_id', (e) => {
        var currentQuantityInput = $(e.currentTarget);
        var data = {};

        var suppliers_id = currentQuantityInput.val();
        var id_items = currentQuantityInput.parent().parent().find('input#id_items').val();
        var type = currentQuantityInput.parent().parent().find('input#type').val();

        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['suppliers_id'] = suppliers_id;
        data['id_items'] = id_items;
        data['type'] = type;
        $.post(admin_url + 'internal_proposal/GetPirceSuppliers', data, function(data) {
            data = JSON.parse(data);
            currentQuantityInput.parent().parent().find('input.price_suppliers').val(tnhFormatNumber(data)).change();
        });

        $.post(admin_url + 'internal_proposal/getCategorySupplier', data, function(data) {
            data = JSON.parse(data);
            currentQuantityInput.parent().parent().find('.category_supplier').html(data);
        });
    });
    $(document).on('change', 'select.tax', function(e) {
        var currentQuantityInput = $(e.currentTarget);
        var tax_id = currentQuantityInput.val();
        var tax_rate = parseInt(currentQuantityInput.find('option:selected').attr('data-taxrate'));
        var current_row = currentQuantityInput.parents('tr');
        if (isNaN(tax_rate)) tax_rate = 0;
        currentQuantityInput.parent().parent().find('input.tax_rate').val(tax_rate);
        calculateTotal(currentQuantityInput.parent());
    });

    function exportExcel() {
        start_date_search = $("#date_start").val();
        end_date_search = $("#date_end").val();

        $.ajax({
            type: "POST",
            // url: site.base_url + 'admin/internal_proposal/export_excel',
            url: site.base_url + 'admin/internal_proposal_export/export_excel',
            data: {
                csrf_token_name: hash,
                start_date_search: start_date_search,
                end_date_search: end_date_search,
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
    // $('#recommended_list_id_search').change(function() {
    //     var recommended_list_id_search = $(this).val();
    //     var data = {};
    //     if (typeof(csrfData) !== 'undefined') {
    //         data[csrfData['token_name']] = csrfData['hash'];
    //     }
    //     data['id'] = recommended_list_id_search;

    //     $.post(admin_url + 'production_report/getRecommendedListByParentrecommended_new', data, function(data) {
    //         data = JSON.parse(data);
    //         $('#recommended_list_group_id_search').html(`<option></option>`);
    //         $.each(data, function(index, value) {
    //             $('#recommended_list_group_id_search').append(`<option value="${value.id}" data-subtext="${value.name}">${value.code}</option>`);
    //         })
    //         $('#recommended_list_group_id_search').selectpicker('refresh');
    //     });
    // });
</script>