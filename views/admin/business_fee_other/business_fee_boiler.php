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

    #table-business-fee-boiler th {
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
            <a onclick="add()" class="btn btn-info test pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('create_add_new'); ?></a>
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
                                        <?= lang('Tên phiếu', 'name_search') ?>
                                        <input type="text" name="name_search"
                                               placeholder="<?= lang('nhập tên phiếu') ?>" id="name_search"
                                               class="name_search form-control" style="width: 100%;"
                                               value="">
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Nhân viên', 'staff_search') ?>
                                        <select class="selectpicker staff_search form-control" name="staff_search[]" id="staff_search"
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
                                        <select class="selectpicker month_search form-control" name="month_search" id="month_search"
                                                data-live-search="true"
                                                title='<?php echo _l('Tháng'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getMonth() as $key => $value){ ?>
                                                <option value="<?= $value ?>"><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <?= lang('Năm', 'year_search') ?>
                                        <select class="selectpicker year_search form-control" name="year_search" id="year_search"
                                                data-live-search="true"
                                                title='<?php echo _l('Năm'); ?>'
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getYear() as $key => $value){ ?>
                                                <option value="<?= $value ?>"><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span class="count-all"></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab" value="un_approved" data-toggle="tab"><?= lang('tnh_un_approved') ?>
                                            (<span class="count-un_approved"></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved" data-toggle="tab"><?= lang('tnh_approved') ?>(<span class="count-approved"></span>)</a>
                                    </li>

                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <table id="table-business-fee-boiler" class="table dt-tnh table-business-fee-boiler-new" style="">
                            <thead>
                            <tr>
                                <th></th>
                                <th class="text-center"><?= lang('Tên phiếu') ?></th>
                                <th class="text-center"><?= lang('Nhân viên') ?></th>
                                <th class="text-center"><?= lang('Tháng/Năm') ?></th>
                                <th class="text-center"><?= lang('Trạng thái') ?></th>
                                <th class="text-center"><?= lang('Người tạo') ?></th>
                                <th class="text-center"><?= lang('Tác vụ') ?></th>
                                <th class="text-center"><?= lang('info') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="99"></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr class="bold">
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
<div id="view_add_business_fee_boiler"></div>
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
        'staff_search[]': '#staff_search'
    };

    function loadTableNew() {
        oTable = tnhInitDataTable('#table-business-fee-boiler',
            '<?= site_url('admin/business_fee_other/getBusinessFeeBoilers') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                "ajax": {
                    "url": '<?= site_url('admin/business_fee_other/getBusinessFeeBoilers') ?>',
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
                        return json.aaData;
                    }
                },
                "createdRow": function (row, data, index) {
                },
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'width': '40px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 1,
                        "name": 'name',
                        'width': '150px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 2,
                        "name": 'staff_id',
                        'width': '250px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 3,
                        "name": 'status',
                        'width': '120px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 4,
                        "name": 'created_by',
                        'width': '100px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 5,
                        "name": 'actions',
                        'width': '120px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 7,
                        "name": 'info',
                        'width': '100px',
                        'visible': false,
                    },
                ],
            });
    }

    $(document).ready(function () {
        loadTableNew();
    });

    function loadInfoData(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        return cData[7];
    }

    $('#table-business-fee-boiler tbody').on('click', 'td .rows-child', function() {
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
            row.child(loadInfoData(row.data())).show();
            tr.addClass('shown');
        }
    });

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });

    $(document).on('keyup', '#name_search, #staff_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
    });
    $(document).on('change', '#staff_search,#month_search,#year_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
    });

    $('#table-business-fee-boiler').on('draw.dt', function() {
        get_total();
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

        $.post(admin_url + 'business_fee_other/update_status', data, function (result) {
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
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>business_fee_other/get_total/",
            data: dataString,
            cache: false,
            success: function(data) {
                data = JSON.parse(data);
                $(".count-un_approved").html(tnhFormatNumber(data.un_approved));
                $(".count-approved").html(tnhFormatNumber(data.approved));
                $(".count-all").html(tnhFormatNumber(data.all));
            }
        });
    }

    function add() {
        $('#view_add_business_fee_boiler').html('');
        $.get(admin_url + 'business_fee_other/add_business_fee_boiler').done(function (response) {
            $('#view_add_business_fee_boiler').html(response);
            $('#business_fee_boiler').modal({
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

    function edit(id,status){
        if(status != 0){
            alert_float('danger','Đã duyệt không thể sửa');
            return;
        }
        $('#view_add_business_fee_boiler').html('');
        if (id){
            $.get(admin_url + 'business_fee_other/add_business_fee_boiler/'+id).done(function (response) {
                $('#view_add_business_fee_boiler').html(response);
                $('#business_fee_boiler').modal({
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

    function deleteTicket(id){
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
                    url: "<?= admin_url() ?>business_fee_other/deleteTicket",
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



</script>