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

    #table-setup-paid-holiday th {
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
                                        <?= lang('Năm', 'year_search') ?>
                                        <select class="selectpicker year_search form-control" name="year_search[]" id="year_search"
                                                data-live-search="true"
                                                title='<?php echo _l('Năm'); ?>'
                                                multiple
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php foreach (getYear() as $key => $value){ ?>
                                                <option value="<?= $key ?>"> <?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <table id="table-setup-paid-holiday" class="table dt-tnh table-setup-paid-holiday-new" style="">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th class="text-center"><?= lang('Năm') ?></th>
                                <th class="text-center"><?= lang('Tổng số nhân viên') ?></th>
                                <th class="text-center"><?= lang('Người tạo') ?></th>
                                <th class="text-center"><?= lang('Tác vụ') ?></th>
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
<div id="view_add_setup_paid_holiday"></div>
<div id="view_setup_paid_holiday"></div>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowGroup.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.rowsGroup.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script>
    var oTable = '';
    var oTableItemsNew = '';
    var fnserverparamsitems = {
        status_table: '#status_table',
        'year_search[]': '#year_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };

    function loadTableNew() {
        oTable = tnhInitDataTable('#table-setup-paid-holiday',
            '<?= site_url('admin/paid_holidays/getSetUpPaidHoliday') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                "ajax": {
                    "url": '<?= site_url('admin/paid_holidays/getSetUpPaidHoliday') ?>',
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
                        "targets": 3,
                        "name": 'created_by',
                        'width': '160px',
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + data +'</div>';
                        },
                        "targets": 4,
                        "name": 'created_by',
                        'width': '120px',
                    },
                ],
            });
    }

    $(document).ready(function () {
        loadTableNew();
    });

    $(document).on('keyup', '#name_search, #staff_search', function(
        event) {
        event.preventDefault();
        // oTable.draw();
    });
    $(document).on('change', '#year_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
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

        $.post(admin_url + 'paid_holidays/update_status', data, function (result) {
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
            url: "<?= admin_url() ?>paid_holidays/get_total/",
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
        $('#view_add_setup_paid_holiday').html('');
        $.get(admin_url + 'paid_holidays/add_setup_paid_holiday').done(function (response) {
            $('#view_add_setup_paid_holiday').html(response);
            $('#setup_paid_holiday').modal({
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

    function edit(id){
        $('#view_add_setup_paid_holiday').html('');
        if (id){
            $.get(admin_url + 'paid_holidays/add_setup_paid_holiday/'+id).done(function (response) {
                $('#view_add_setup_paid_holiday').html(response);
                $('#setup_paid_holiday').modal({
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
                    url: "<?= admin_url() ?>paid_holidays/deleteSetupPaidHoliday",
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

    function viewStaff(id){
        $('#view_setup_paid_holiday').html('');
        $.get(admin_url + 'paid_holidays/view_staff_setup_paid_holiday/'+id).done(function (response) {
            $('#view_setup_paid_holiday').html(response);
            $('#view_staff_setup_paid_holiday').modal({
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

</script>