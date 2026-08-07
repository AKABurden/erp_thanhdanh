<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .bg-group {
        background: #daeaf9;
    }

    .group {
        background-color: #f9f9f9;
    }

    .staff-profile-image-small {
        width: 20px !important;
        height: 20px !important;
    }

    #table-business-fee-boiler-calculate{
        width: 100%;
    }

    .table-business-fee-boiler-calculate-new thead tr th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    #table-suggest-payslip tr td:nth-child(5) {
        min-width: 120px;
        white-space: unset;
        text-align: left;
    }

    .tag-cs-red {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid red;
        color: red;
    }

    .tag-cs-color {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #2886e7;
        color: #2886e7;
    }

    .staff-profile-image-small {
        width: 25px;
        height: 25px;
        margin-top: 5px;
    }

    .tag-cs-danger {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #fb101b;
        color: #fb101b;
    }

    .tag-cs-primary {
        font-size: 11px;
        font-style: italic;
        font-weight: 400;
        padding: 0.3em 0.7em 0.3em;
        background: 0 0;
        border: 1px solid #17adf1;
        color: #17adf1;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll" style="margin-bottom: unset">
        <div class="panel-body ">
            <div class="_buttons">
                <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            </div>
            <a href="<?= admin_url('business_fee_other/add_business_fee_boiler_calculate') ?>"
               class="btn btn-info pull-right test H_action_button">
                <?php echo _l('create_add_new'); ?></a>
            <div class="pull-right mright5 H_border">
                <a href="<?= base_url('admin/business_fee_other/load_view_edit_chose') ?>"
                   class="tnh-modal btn btn-info test H_action_button" data-tnh="modal" data-toggle="modal"
                   data-target="#myModal"><i
                            class="fa fa-edit width-icon-actions"></i><?php echo _l('Sửa bảng tính'); ?></a>
            </div>
            <div class="pull-right mright5 H_border">
                <a class="btn btn-info test H_action_button" onclick="deletePayroll(); return false;"> <i
                            class="fa fa-remove width-icon-actions"></i><?php echo _l('Xoá bảng tính'); ?></a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row col-md-12">
                            <div class="row" style="margin-bottom:5px">
                                <div id="search-tnh" class="collapse in" aria-expanded="true">
                                    <div class="col-md-3">
                                        <?= lang('Nhân viên', 'staff_search') ?>
                                        <select class="selectpicker staff_search form-control" name="staff_search[]"
                                                id="staff_search"
                                                data-live-search="true"
                                                multiple
                                                title='<?php echo _l('Nhân viên'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if (!empty($staff)) { ?>
                                                <?php foreach ($staff as $key => $value) { ?>
                                                    <optgroup label="<?= $value['name'] ?>">
                                                        <?php if (!empty($value['staffs'])) : ?>
                                                            <?php foreach ($value['staffs'] as $k => $v) : ?>
                                                                <option data-subtext="<?= $v['name_roles'] ?>" <?= (!empty($arrSelect) && in_array($v['staffid'],
                                                                        $arrSelect)) ? 'selected' : '' ?>
                                                                        value="<?= $v['staffid'] ?>"><?= $v['staff_name'] ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </optgroup>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Tháng', 'month_search') ?>
                                        <select class="selectpicker month_search form-control" name="month_search"
                                                id="month_search"
                                                data-live-search="true"
                                                title='<?php echo _l('Tháng'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getMonth() as $key => $value) { ?>
                                                <option <?= $value == date('m') ? 'selected' : '' ?>
                                                        value="<?= $value ?>"><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Năm', 'year_search') ?>
                                        <select class="selectpicker year_search form-control" name="year_search"
                                                id="year_search"
                                                data-live-search="true"
                                                title='<?php echo _l('Năm'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getYear() as $key => $value) { ?>
                                                <option <?= $value == date('Y') ? 'selected' : '' ?>
                                                        value="<?= $value ?>"><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Chi nhánh', 'branch') ?>
                                        <select class="selectpicker branch form-control" name="branch_search" id="branch_search" data-live-search="true" title='<?php echo _l('Chi nhánh'); ?>' data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <option></option>
                                            <?php if (!empty($branch)) { ?>
                                                <?php foreach ($branch as $key => $value) { ?>
                                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="table-business-fee-boiler-calculate"
                                   class="table dt-tnh table-business-fee-boiler-calculate-new" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="text-center"><div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="business-fee-boiler-calculate-new"><label
                                                    for="mass_select_all"></label>
                                        </div></th>
                                    <th class="text-center"><?= lang('Mã NV') ?></th>
                                    <th class="text-center"><?= lang('Họ tên') ?></th>
                                    <th class="text-center"><?= lang('Chức vụ') ?></th>
                                    <th class="text-center"><?= lang('Tổng lương') ?></th>
                                    <th class="text-center"><?= lang('Lương vị trí(LCB) ') ?></th>
                                    <th class="text-center"><?= lang('Kiêm nhiệm') ?></th>
                                    <th class="text-center"><?= lang('Doanh số') ?></th>
                                    <th class="text-center"><?= lang('Thâm niên') ?></th>
                                    <th class="text-center"><?= lang('TC chủ nhật(H)') ?></th>
                                    <th class="text-center"><?= lang('LCB/26/8*2*H') ?></th>
                                    <th class="text-center"><?= lang('Tăng ca lễ (H)') ?></th>
                                    <th class="text-center"><?= lang('LCB/26/8*3*H') ?></th>
                                    <th class="text-center"><?= lang('TC thường (H)') ?></th>
                                    <th class="text-center"><?= lang('LCB/26/8*1.5*H') ?></th>
                                    <th class="text-center"><?= lang('TC đêm thường (H)') ?></th>
                                    <th class="text-center"><?= lang('LCB/26/8*'.get_option('coefficient_default_night').'*H') ?></th>
                                    <th class="text-center"><?= lang('TC đêm chủ nhật (H)') ?></th>
                                    <th class="text-center"><?= lang('LCB/26/8*'.get_option('coefficient_sunday_night').'*H') ?></th>
                                    <th class="text-center"><?= lang('Tổng') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="99"></td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="bold">
                                    <td colspan="4" style="text-align: center;text-transform: uppercase;font-weight: bold">Tổng cộng</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="view_add_business_fee_boiler_overtime"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    var oTable = '';
    var oTableItemsNew = '';
    var fnserverparamsitems = {
        name_search: '#name_search',
        status_table: '#status_table',
        month_search: '#month_search',
        year_search: '#year_search',
        branch_search: '#branch_search',
        'staff_search[]': '#staff_search'
    };

    function loadTableNew() {
        oTable = tnhInitDataTable('#table-business-fee-boiler-calculate',
            '<?= site_url('admin/business_fee_other/getBusinessFeeBoilerCalculate') ?>', {
                'order': [
                    [2, 'asc'],
                ],
                scrollX: true,
                "ajax": {
                    "url": '<?= site_url('admin/business_fee_other/getBusinessFeeBoilerCalculate') ?>',
                    "type": "POST",
                    "data": function (d) {
                        if (typeof (csrfData) !== 'undefined') {
                            d[csrfData['token_name']] = csrfData['hash'];
                        }
                        for (var key in fnserverparamsitems) {
                            d[key] = $(fnserverparamsitems[key]).val();
                        }
                        if (table.attr('data-last-order-identifier')) {
                            d['last_order_identifier'] = table.attr('data-last-order-identifier');
                        }
                    },
                    "dataSrc": function (json) {
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(7)').html(`<div class="text-center">${tnhFormatMoney(json.total_sunday)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(8)').html(`<div class="text-right">${tnhFormatMoney(json.total_sunday_money)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(9)').html(`<div class="text-center">${tnhFormatMoney(json.total_holiday)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(10)').html(`<div class="text-right">${tnhFormatMoney(json.total_holiday_money)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(11)').html(`<div class="text-center">${tnhFormatMoney(json.total_weekday)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(12)').html(`<div class="text-right">${tnhFormatMoney(json.total_weekday_money)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(13)').html(`<div class="text-center">${tnhFormatMoney(json.total_weekday_night)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(14)').html(`<div class="text-right">${tnhFormatMoney(json.total_weekday_night_money)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(15)').html(`<div class="text-center">${tnhFormatMoney(json.total_sunday_night)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(16)').html(`<div class="text-right">${tnhFormatMoney(json.total_sunday_night_money)}</div>`);
                        $('.table-business-fee-boiler-calculate-new tfoot tr td:nth-child(17)').html(`<div class="text-right">${tnhFormatMoney(json.total)}</div>`);
                        return json.aaData;
                    }
                },
                "createdRow": function (row, data, index) {
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:center">${data}</div>`;
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 1,
                        "width": '80px',
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 2,
                        "width": '100px',
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:left">${data}</div>`;
                        },
                        "targets": 3,
                        "width": '100px',
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div style="text-align:right">${data}</div>`;
                        },
                        "targets": 4,
                        "width": '100px',
                    },
                ],
            });
    }

    $(document).ready(function () {
        loadTableNew();
    });


    $(document).on('click', '.status-table li a', function (event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });

    $(document).on('change', '#staff_search, #month_search, #year_search, #branch_search', function (
        event) {
        event.preventDefault();
        oTable.draw();
    });

    $('#table-business-fee-boiler-overtime').on('draw.dt', function () {
    });

    $('body').on('click', '#agree', function () {
        var id = $(this).data('id');
        var status = $(this).attr('value');

        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;

        $.post(admin_url + 'business_fee_other/update_status_overtime', data, function (result) {
            result = JSON.parse(result);
            if (result.result) {
                oTable.draw('page');
                alert_float('success', result.message);
                $('.popover').closest('div.popover').popover('hide');
            } else {
                alert_float('danger', result.message);
            }
        })
    })

    function get_total() {

        dataString = {
            [csrfData['token_name']]: csrfData['hash'],
            'name_search': $("#name_search").val(),
            'start_date_search': $("#start_date_search").val(),
            'end_date_search': $("#end_date_search").val(),
            "staff_search[]": $('#staff_search').val(),
            "month_search": $('#month_search').val(),
            "year_search": $('#year_search').val(),
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>business_fee_other/get_total_overtime/",
            data: dataString,
            cache: false,
            success: function (data) {
                data = JSON.parse(data);
                $(".count-un_approved").html(tnhFormatNumber(data.un_approved));
                $(".count-approved").html(tnhFormatNumber(data.approved));
                $(".count-all").html(tnhFormatNumber(data.all));
            }
        });
    }

    function add() {
        $('#view_add_business_fee_boiler_overtime').html('');
        $.get(admin_url + 'business_fee_other/add_business_fee_boiler_overtime').done(function (response) {
            $('#view_add_business_fee_boiler_overtime').html(response);
            $('#business_fee_boiler_overtime').modal({
                backdrop: 'static',
                keyboard: false
            });
            init_editor();
            init_selectpicker();
            init_datepicker();
        }).fail(function (error) {
            var response = JSON.parse(error.responseText);
            alert_float('danger', response.message);
        });
    }

    function edit(id, status) {
        if (status != 0) {
            alert_float('danger', 'Đã duyệt không thể sửa');
            return;
        }
        $('#view_add_business_fee_boiler_overtime').html('');
        if (id) {
            $.get(admin_url + 'business_fee_other/add_business_fee_boiler_overtime/' + id).done(function (response) {
                $('#view_add_business_fee_boiler_overtime').html(response);
                $('#business_fee_boiler_overtime').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                init_editor();
                init_selectpicker();
                init_datepicker();
            }).fail(function (error) {
                var response = JSON.parse(error.responseText);
                alert_float('danger', response.message);
            });
        }
    }

    function deleteTicket(id) {
        var r = confirm(
            "<?php echo _l('Xoá phiếu. Bạn có muốn tiếp tục');?>");
        if (r == false) {
            oTable.draw('page');
            return false;
        } else {
            if (id) {
                dataString = {
                    id: id,
                    [csrfData['token_name']]: csrfData['hash']
                };
                jQuery.ajax({
                    type: "post",
                    url: "<?= admin_url() ?>business_fee_other/deleteTicketOvertime",
                    data: dataString,
                    cache: false,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.result == true) {
                            oTable.draw('page');
                            alert_float('success', response.message);
                        } else {
                            oTable.draw('page');
                            alert_float('danger', response.message);
                        }
                    }
                });
                return false;
            }
        }
    }

    function deletePayroll(){
        var ids = '';
        var rows = $('.table-business-fee-boiler-calculate-new').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });
        if (!ids) {
            bootbox.alert('Xin vui lòng chọn bảng tính lương cần xoá');
            return;
        }
        if (ids) {
            $.ajax({
                url: site.base_url + 'admin/business_fee_other/deletePayroll',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                    ids: ids,
                },
            })
            .done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    oTable.draw();
                } else {
                    alert_float('danger', data.message);
                    oTable.draw('false');
                }
            })
            .fail(function(data) {
                alert_float('danger', 'errors');
            })
        }
    }
</script>