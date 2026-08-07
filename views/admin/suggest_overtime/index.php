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

    #table-suggest-overtime th {
        background: #d9edf7 !important;
        color: #0e5dab !important;
        border: 1px solid #93b4d6 !important;
    }

    #table-suggest-overtime tr td:nth-child(5) {
        min-width: 200px;
        white-space: unset;
    }

    #table-suggest-overtime tr td:nth-child(3) {
        min-width: 300px;
        white-space: unset;
    }

    #table-suggest-overtime tr td:nth-child(7) {
        width: 150px;
        white-space: unset;
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
                                                <option <?= $value == date('m') ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
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
                                                <option <?= $value == date('Y') ? 'selected' : '' ?> value="<?= $value ?>"><?= $value ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <table id="table-suggest-overtime" class="table dt-tnh table-suggest-overtime-new" style="">
                            <thead>
                            <tr>
                                <th></th>
                                <th class="text-center"><?= lang('Tên phiếu') ?></th>
                                <th class="text-center"><?= lang('Nhân viên') ?></th>
                                <th class="text-center"><?= lang('Tháng/Năm') ?></th>
                                <th class="text-center"><?= lang('Ngày') ?></th>
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
<div id="view_add_suggest_overtime"></div>
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
        name_search: '#name_search',
        year_search: '#year_search',
        month_search: '#month_search',
        'staff_search[]': '#staff_search'
    };

    function loadTableNew() {
        oTable = tnhInitDataTable('#table-suggest-overtime',
            '<?= site_url('admin/suggest_overtime/getSuggestOvertime') ?>', {
                'order': [
                    [0, 'desc'],
                ],
                "ajax": {
                    "url": '<?= site_url('admin/suggest_overtime/getSuggestOvertime') ?>',
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
                        "name": 'month',
                        'width': '100px',
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
                        "name": 'created_by',
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

    $('#table-suggest-overtime tbody').on('click', 'td .rows-child', function() {
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

    $(document).on('keyup', '#name_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
    });
    $(document).on('change', '#staff_search,#month_search,#year_search', function(
        event) {
        event.preventDefault();
        oTable.draw();
    });

    $('#table-suggest-overtime').on('draw.dt', function() {
        // var wrap = $('.table_date');
        // var current_height = wrap.height();
        // var your_height = 100;
        // if(current_height > your_height){
        //     wrap.css('height', your_height+'px');
        //     wrap.append(function(){
        //         return '<div class="mdsco_readmore_taxonomy_flatsome mdsco_readmore_taxonomy_flatsome_show"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
        //     });
        //     wrap.append(function(){
        //         return '<div class="mdsco_readmore_taxonomy_flatsome mdsco_readmore_taxonomy_flatsome_less" style="display: none"><a title="Thu gọn" href="javascript:void(0);">Thu gọn</a></div>';
        //     });
        //     $('body').on('click','.mdsco_readmore_taxonomy_flatsome_show', function(){
        //         wrap.removeAttr('style');
        //         $('body .mdsco_readmore_taxonomy_flatsome_show').hide();
        //         $('body .mdsco_readmore_taxonomy_flatsome_less').show();
        //     });
        //     $('body').on('click','.mdsco_readmore_taxonomy_flatsome_less', function(){
        //         wrap.css('height', your_height+'px');
        //         $('body .mdsco_readmore_taxonomy_flatsome_show').show();
        //         $('body .mdsco_readmore_taxonomy_flatsome_less').hide();
        //     });
        // }
    });


    $('body').on('click', '#agree_child', function () {
        var id = $(this).data('id');
        var status = $(this).attr('value');
        var data = {};
        if (typeof (csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['id'] = id;
        data['status'] = status;

        $.post(admin_url + 'suggest_overtime/update_status_child', data, function (result) {
            result = JSON.parse(result);
            if (result.result) {
                oTable.draw('page');
                alert_float('success', result.message);
                $('.popover').closest('div.popover').popover('hide');
                setTimeout(function (){
                    $(`#rows-child-${result.id}`).click();
                },300);
            } else {
                alert_float('danger', result.message);
            }
        })
    })


    function add() {
        $('#view_add_paid_holiday_leave').html('');
        $.get(admin_url + 'suggest_overtime/add_suggest_overtime').done(function (response) {
            $('#view_add_suggest_overtime').html(response);
            $('#suggest_overtime').modal({
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
        if (id) {
            dataString = {
                id: id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>suggest_overtime/checkEdit",
                data: dataString,
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result == false) {
                        alert_float('danger','Có chi tiết phiếu đề xuất đã được duyệt không thể sửa')
                        return;
                    } else {
                        $('#view_add_paid_holiday_leave').html('');
                        if (id){
                            $.get(admin_url + 'suggest_overtime/add_suggest_overtime/'+id).done(function (response) {
                                $('#view_add_suggest_overtime').html(response);
                                $('#suggest_overtime').modal({
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
                }
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
                    url: "<?= admin_url() ?>suggest_overtime/deleteTicket",
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