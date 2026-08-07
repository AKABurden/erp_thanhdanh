<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    /*.fixedHeader-floating {*/
    /*    position: fixed !important;*/
    /*}*/

    .progressbar_img {
        text-align: center !important;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .progressbar_img img {
        height: 25px;
        width: 25px;
    }

    ul.progressbar_img li.active_img img {
        border: 2px solid #00ff50;
    }

    ul.progressbar_img li.cancel img {
        border: 2px solid red;
    }

    ul.progressbar_img li.cancel_all img {
        border: 2px solid blue;
    }

    ul.progressbar_img li {
        width: 100px;
        float: left;
    }

    .progressbar:not(.hoang) {
        margin: 0;
        padding: 0;
        counter-reset: step;
    }

    .progressbar li span {
        font-size: 11px;
    }

    .progressbar li:not(.hoang) {
        list-style-type: none;
        width: 87px;
        float: left;
        font-size: 12px;
        position: relative;
        text-align: center;
        /*text-transform: uppercase;*/
        color: #7d7d7d;
        z-index: 0;
    }

    .progressbar li:not(.hoang):before {
        width: 10px;
        height: 10px;
        content: ' ';
        counter-increment: step;
        line-height: 51px;
        border: 5px solid #7d7d7d;
        display: block;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: white;
    }

    .progressbar li:not(.hoang):after {
        width: 100% !important;
        height: 2px !important;
        content: '' !important;
        position: absolute !important;
        background-color: #7d7d7d !important;
        top: 4px !important;
        left: -50% !important;
        z-index: -1 !important;
    }

    .progressbar li:first-child:after {
        content: none;
        display: none;
    }

    .progressbar li.active_ch:before {
        border-color: red;
    }

    .progressbar li.active:not(.hoang) {
        color: green;
    }

    .progressbar li.active:not(.hoang):before {
        border-color: #55b776;
    }

    .progressbar li.cancel:before {
        border-color: red;
    }

    .progressbar li.active+li:after {
        background-color: #55b776 !important;
    }

    /* timeline */

    .timeline-vertical ul,
    .timeline-vertical li {
        list-style: none;
        padding: 0;
    }

    .timeline-vertical .wrapper {
        /* background: #eaf6ff; */
        /* background: #dddddd3b; */
        padding: 1rem;
        border-radius: 15px;
    }

    .timeline-vertical h1 {
        font-size: 1.1rem;
        font-family: sans-serif;
    }

    .timeline-vertical .sessions {
        /* margin-top: 2rem; */
        margin-top: 0.5rem;
        border-radius: 12px;
        position: relative;
    }

    .timeline-vertical li {
        padding-bottom: 1.5rem;
        /* border-left: 1px solid #abaaed;
         */
        border-left: 1px solid #729cdc;
        position: relative;
        padding-left: 20px;
        margin-left: 10px;
    }

    .timeline-vertical li:last-child {
        border: 0px;
        padding-bottom: 0;
    }

    .timeline-vertical li:before {
        content: "";
        width: 15px;
        height: 15px;
        background: white;
        /* border: 1px solid #4e5ed3;
        box-shadow: 3px 3px 0px #bab5f8;
        box-shadow: 3px 3px 0px #bab5f8; */
        border: 1px solid #729cdc;
        box-shadow: unset !important;
        box-shadow: unset !important;
        border-radius: 50%;
        position: absolute;
        left: -10px;
        top: 2px !important;
    }

    .timeline-vertical li.active:before {
        background: green !important;
    }

    .timeline-vertical .time {
        color: #2a2839;
        font-family: "Poppins", sans-serif;
        font-weight: 500;
    }

    .fixed {
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 9999999;
    }

    .timeline-vertical li.again:before {
        background: red !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if ($this->perAddQC): ?>
                <a href="<?= base_url('admin/quality_control/add_check_quality') ?>"
                   class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="content">
        <div class="col-md-3">
            <?= lang('Lọc số đơn hàng', 'order_search') ?>
            <input type="text" name="order_search" id="order_search" style="width: 100%;"
                   data-placeholder="<?= lang('Số đơn hàng') ?>" value="">
        </div>
        <div class="col-md-3">
            <?= lang('Khách hàng', 'customer_search') ?>
            <input type="text" name="customer_search" id="customer_search" style="width: 100%;"
                   data-placeholder="<?= lang('Khách hàng') ?>" value="">
        </div>
        <div class="col-md-3">
            <?= lang('start_date', 'start_date_search') ?>
            <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>"
                   id="start_date_search" autocomplete="off" class="start_date_search datepicker form-control"
                   style="width: 100%;" value="">
        </div>
        <div class="col-md-3">
            <?= lang('end_date', 'end_date_search') ?>
            <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>"
                   id="end_date_search" autocomplete="off" class="end_date_search datepicker form-control" style="width: 100%;"
                   value="">
        </div>
        <?php
        $active = '';
        $activegdx = '';
        $activetp = '';
        $so = '';
        if (is_admin()) {
            $active = 'active';
            $so = 'all';
        } elseif (has_permission('quality_control', '',
                'approve') && ($activetp == '')) {
            $activetp = 'active';
            $so = 'manager_approve';
        } elseif (has_permission('quality_control', '', 'approve_manager') && ($activetp == '')) {
            $activegdx = 'active';
            $so = 'gdx_approve';
        } else {
            $active = 'active';
            $so = 'all';
        }
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs hide">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="<?= $activetp ?>">
                                        <a href="#approved" aria-controls="manager_approve" role="tab"
                                           value="manager_approve" data-toggle="tab"><?= lang('Phòng QC') ?>(<span
                                                    class="count-invoice_status_paid"><?= $manager_approve ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="<?= $activegdx ?>">
                                        <a href="#gdx_approve" aria-controls="approved" role="tab" value="gdx_approve"
                                           data-toggle="tab"><?= lang('Giám đốc xưởng') ?>(<span
                                                    class="count_follow_outsource"><?= $gdx_approve ?></span>)</a>
                                    </li>
                                    <li role="presentation" class="<?= $active ?>">
                                        <a href="#all" aria-controls="all" role="tab" value="all"
                                           data-toggle="tab"><?= lang('all') ?>(<span
                                                    class="count-all"><?= $all ?></span>)</a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table"
                                       class="form-control status_table" value="<?= $so ?>">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table-check-quality"
                                   class="table table-hover table-bordered table-condensed dataTable table-check-quality">
                                <thead>
                                <tr>
                                    <th rowspan="2">
                                        <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="check-quality"><label for="mass_select_all"></label>
                                        </div>
                                    </th>
                                    <th rowspan="2"><?= lang('date') ?></th>
                                    <th rowspan="2"><?= lang('tnh_reference_qc') ?></th>
                                    <th rowspan="2"><?= lang('Số lệnh tổng') ?></th>
                                    <th rowspan="2"><?= lang('Khách hàng') ?></th>
                                    <th rowspan="2"><?= lang('Đơn hàng bán/Kế hoạch BTP') ?></th>
                                    <th class="text-center" colspan="3"><?= lang('Số lượng') ?></th>
                                    <th rowspan="2"><?= lang('Quy trình QC') ?></th>
                                    <th rowspan="2"><?= lang('note') ?></th>
                                    <th rowspan="2"><?= lang('actions') ?></th>
                                </tr>
                                <tr>
                                    <th><?= lang('SL QC') ?></th>
                                    <th><?= lang('SL Lỗi') ?></th>
                                    <th><?= lang('SL Đạt') ?></th>
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
<?php echo form_close(); ?>
<?php init_tail(); ?>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        order_search: '#order_search',
        customer_search: '#customer_search',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
    };
    var oTable = '';

    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        ajaxSelectParams('#order_search', 'admin/quality_control/searchOrders', 0, true, true);
        oTable = tnhDatatable(
            '#table-check-quality', {
                'order': [
                    [1, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // "processing": true,
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                fixedColumns: {
                    leftColumns: 0,
                    rightColumns: 0
                },
                scrollY: height_body,
                // scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/quality_control/getCheckQuality') ?>',
                'fnServerData': function(sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    for (var key in fnserverparams) {
                        aoData.push({
                            "name": key,
                            "value": $(fnserverparams[key]).val()
                        });
                    }
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                },
                "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" class="check_quality_id" name="check_quality_id[]" id="check_quality_id' +
                            data + '" value="' + data + '"><label for="check_quality_id' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '50px',
                    'visible': false
                },
                    {
                        "render": function(data, type, row) {
                            return '<div>' + fld(data) + '</div>';
                        },
                        "targets": 1,
                        "name": 'date',
                        'searchable': false,
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            str = '';
                            create_by = row[9];
                            if (data != null) {
                                data = data.split('__');
                                str = '<div style="min-width: 150px;" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url +
                                    'admin/quality_control/viewQualityControl/' + row[0] +
                                    '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a>\
                            <div style="font-style: italic;">' + data[1] + '</div>\
                            <div style="font-style: italic;">Người tạo: ' + create_by + '</div>\
                            </div><div class="td-reference-no"></div>';
                            } else {
                                str = '';
                            }
                            return str;


                        },
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '90px'
                    },
                    {
                        "targets": 3,
                        "name": 'pod_id',
                        'width': '120px'
                    },
                    {
                        "targets": 4,
                        "name": 'company',
                        'width': '140px',
                        'visible': true
                    },
                    {
                        "targets": 5,
                        "name": 'order_id',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 6,
                        "name": 'quantity_qc',
                        'width': '70px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'qty_item_error',
                        'searchable': false,
                        'width': '70px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'qty_item_success',
                        'searchable': false,
                        'width': '70px'
                    },
                    {
                        "targets": 9,
                        "name": 'created_by',
                        'width': '200px',
                        'visible': false
                    },
                    {
                        "targets": 10,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 11,
                        "name": 'actions',
                        'orderable': false,
                        'searchable': false,
                        'width': '130px',
                    }
                ]
            }
        );

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            quality_id = $(this).attr('quality_id');
            status = $(this).attr('value');
            console.log(status);
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (quality_id) {
                $.ajax({
                    url: site.base_url + 'admin/quality_control/agreeQualityControl',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        quality_id: quality_id,
                        status: status
                    },
                })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    })
                    .fail(function(data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw();
        });
        $(document).on('change', '#order_search, #end_date_search, #start_date_search, #customer_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });
        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
    });

    function var_status_qc(status, id) {
        {
            dataString = {
                id: id,
                status: status,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>quality_control/update_status",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    alert_float(response.alert_type, response.message);
                    oTable.draw('page');
                }
            });
            return false;
        }
    }
    // $(window).scroll(function() {
    //     var sticky = $('.table-items-check'),
    //         scroll = $(window).scrollTop();
    //     console.log(scroll)

    //     if (scroll >= 100) sticky.addClass('fixed');
    //     else sticky.removeClass('fixed');
    // });
    // $('.modal').on('scroll', function() {
    //     var scroll = $(window).scrollTop();
    //     console.log(scroll)
    //     // if ($('#myModal').scrollTop() > threshold) {
    //     //     $('.fixed-header').addClass('affixed');
    //     // } else {
    //     //     $('.fixed-header').removeClass('affixed');
    //     // }
    // });
    $('.modal').on('scroll', function() {
        var sticky = $('table.table-items-check > thead'),
            scroll = $(window).scrollTop();
        var scrollModal = $('#tnhModal').scrollTop();
        if (scrollModal >= (scroll + 200)) {
            sticky.addClass('fixed');
            sticky.css({
                top: (scrollModal - 330),
            });
        } else {
            sticky.removeClass('fixed');
        }
    });
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>