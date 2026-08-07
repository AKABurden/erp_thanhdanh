<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .tnh-status-sm {
        height: 70px !important;
    }

    #table-productions-orders td:nth-child(7) img {
        width: 50px;
        height: 50px;
    }

    .option_main {
        font-weight: 500;
    }
    .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn){
        width: 100% !important;
    }

    #wrapper .buttons-collection {
        display: none !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=3.1') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if ($this->perAddProductionsOrders) : ?>
                <a href="<?= base_url('admin/manufactures/add_productions_orders') ?>" class="btn btn-info mright5 pull-right H_action_button hide">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>
            <a href="<?= base_url('admin/manufactures/statistical_planning') ?>" class="btn btn-info mright5 pull-right H_action_button hide">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('tnh_statistical_planning'); ?>
            </a>
            <a href="#" onclick="exportExcelPOTotal(); return false;" class="btn btn-info pull-right new H_action_button mleft5">
                <i class="fa fa-download" aria-hidden="true"></i>Xuất excel tổng hợp
            </a>
            <a href="#" onclick="exportExcelPOTotalDetail(); return false;" class="btn btn-info pull-right new H_action_button mleft5">
                <i class="fa fa-download" aria-hidden="true"></i>Xuất excel chi tiết
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row ">
            <div class="col-md-12">
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                        <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_items', 'items_search') ?>
                        <input type="text" name="items_search" data-placeholder="<?= lang('tnh_items') ?>" id="items_search" class="items_search" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_orders_and_business_plan', 'orders_and_business_plan') ?>
                        <input type="text" data-placeholder="<?= lang('ĐHB/Kế hoạch BTP') ?>" name="orders_and_business_plan" id="orders_and_business_plan" style="width: 100%;" data-placeholder="<?= lang('') ?>" value="">
                    </div>
                </div>
                <div class="col-md-2">
                    <?= lang('customers', 'customers') ?>
                    <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>"
                           id="customer_search" class="customer_search" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_type_print', 'type_print_search') ?>
                        <select name="type_print_search" id="type_print_search" data-placeholder="<?= lang('tnh_type_print') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($CategoryStages)) : ?>
                                <?php foreach ($CategoryStages as $key => $value) : ?>
                                    <option class="<?= ($value['main'] == '1' ? 'option_main' : '') ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <?= lang('tnh_period_time', 'period_time') ?>
                        <input type="text" name="period_time" autocomplete="off" placeholder="<?= lang('tnh_period_time') ?>" id="period_time" class="period_time form-control dateranger-custom" style="width: 100%;" value="">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2">
                    <?php $arrStatus = [
                        [
                            'id' => 3,
                            'name' => 'Chưa sản xuất'
                        ],
                        [
                            'id' => 1,
                            'name' => 'Đang sản xuất'
                        ],
                        [
                            'id' => 2,
                            'name' => 'Hoàn thành'
                        ],
                    ] ?>
                    <?= lang('Trạng thái', 'status_search') ?>
                    <br>
                    <select name="status_search" id="status_search" data-none-selected-text="<?= lang('tnh_status') ?>" data-live-search="true" class="selectpicker" style="width: 100%">
                        <option value=""></option>
                        <?php if (!empty($arrStatus)) : ?>
                            <?php foreach ($arrStatus as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2 hide">
                    <?php $arrStatusOrder = [
                        [
                            'id' => 1,
                            'name' => 'Khẩn'
                        ],
                        [
                            'id' => 2,
                            'name' => 'Gấp'
                        ],
                    ] ?>
                    <?= lang('Trạng thái đơn hàng', 'status_search_order') ?>
                    <br>
                    <select name="status_search_order" id="status_search_order" data-none-selected-text="<?= lang('Trạng thái đơn hàng') ?>" data-live-search="true" class="selectpicker" style="width: 100%;">
                        <option value=""></option>
                        <?php if (!empty($arrStatusOrder)) : ?>
                            <?php foreach ($arrStatusOrder as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <?= lang('Chi nhánh', 'branch_search') ?>
                    <select name="branch_search" id="branch_search" class="branch_search"  data-placeholder="<?= lang('Chi nhánh') ?>" style="width: 100%;">
                        <option value=""></option>
                        <?php if (!empty($branch)) { ?>
                            <?php foreach ($branch as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body p-top-0">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div role="tabpanel">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <?php foreach (typeProductionsOrders() as $key => $value) : ?>
                                    <!-- <li role="presentation">
                                    <a href="#<?= $key ?>" aria-controls="<?= $key ?>" role="tab" value="<?= $key ?>" data-toggle="tab"><?= $value ?>(<span><?= $status[$key] ?></span>)</a>
                                </li> -->
                                <?php endforeach ?>
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" id="status_all" role="tab" value="all" data-toggle="tab"><?= lang('Tổng hợp') ?></a>
                                </li>
                                <li role="presentation" class="">
                                    <a href="#detail" aria-controls="detail" role="tab" value="detail" data-toggle="tab"><?= lang('Chi tiết') ?></a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                        </div>
                        <div class="table-all">
                            <table id="table-productions-orders-all" class="table table-hover table-condensed table-productions-orders-all dont-responsive-table dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center"><?= lang('Ngày mở đơn') ?></th>
                                        <th class="text-center"><?= lang('tnh_date_created_manufactures') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th class="text-center"><?= lang('Hình ảnh') ?></th>
                                        <th class="text-center"><?= lang('dt_product_code') ?></th>
                                        <th class="text-center"><?= lang('dt_product_name') ?></th>
                                        <th class="text-center"><?= lang('Ngày DK giao hàng') ?></th>
                                        <th class="text-center"><?= lang('Ngày giữ') ?></th>
                                        <th class="text-center"><?= lang('Các NPL đã xuất') ?></th>
                                        <th class="text-center"><?= lang('Khổ in ngang x dọc = cm') ?></th>
                                        <th class="text-center"><?= lang('Số lượng đặt') ?></th>
                                        <th class="text-center"><?= lang('Số lượng sx') ?></th>
                                        <th class="text-center"><?= lang('Số lượng giữ hàng') ?></th>
                                        <th class="text-center"><?= lang('Số lượng tờ in') ?></th>
                                        <th class="text-center"><?= lang('Số lượng hoàn thành') ?></th>
                                        <th class="text-center"><?= lang('tnh_quantity_errors') ?></th>
                                        <th class="text-center"><?= lang('Trạng thái') ?></th>
                                        <!-- <th class="text-center"><?//= lang('Loại hình in') ?></th> -->
                                        <th class="text-center"><?= lang('Nhóm công đoạn') ?></th>
                                        <th class="text-center"><?= lang('Công đoạn sản phẩm') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('tnh_branch') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bold">
                                        <td></td>
                                        <td class="text-center"><?= lang('tnh_grand_total') ?></td>
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="table-detail hide">
                            <table id="table-productions-orders" class="table table-hover table-condensed table-productions-orders dont-responsive-table dataTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?= lang('tnh_numbers') ?></th>
                                        <th class="text-center"><?= lang('Ngày mở đơn') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th class="text-center"><?= lang('productions_plan_acronym') ?></th>
                                        <th class="text-center"><?= lang('tnh_orders_and_business_plan') ?></th>
                                        <th class="text-center"><?= lang('total_quantity') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('tnh_status') ?></th>
                                        <th class="text-center"><?= lang('tnh_branch') ?></th>
                                        <th class="text-center"><?= lang('Xác nhận bàn giao GĐSX') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                        <th class="text-center"><?= lang('items') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bold">
                                        <td></td>
                                        <td class="text-center"><?= lang('tnh_grand_total') ?></td>
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
<?php echo form_close(); ?>
<?php init_tail(); ?>
<a href="" class="tnh-modal hide" id="clickFini"></a>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
 
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        productions_orders_search: '#productions_orders_search',
        items_search: '#items_search',
        period_time: '#period_time',
        type_print_search: '#type_print_search',
        branch_search: '#branch_search',
        orders_and_business_plan: '#orders_and_business_plan',
        customer_search: '#customer_search',
        status_search: '#status_search',
        status_search_order: '#status_search_order',
    };
    var oTable = '';
    var oTableNew = '';
    var trItem = '';
    var trIndex = '';
</script>
<script type="text/javascript">
    function handlingEndPO(c_productions_orders_id, c_status) {
        bootbox.confirm({
            message: '<?= lang('Bạn có muốn kết thúc lệnh sản xuất tổng này') ?>',
            buttons: {
                confirm: {
                    label: '<?= lang('tnh_update') ?>',
                    className: 'btn-primary'
                },
                cancel: {
                    label: '<?= lang('close') ?>',
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: site.base_url + 'admin/manufactures/handlingEndPO',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            productions_orders_id: c_productions_orders_id,
                            status: c_status,
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.result == 1) {
                                alert_float('success', response.message);
                            } else if (response.result == 0) {
                                alert_float('danger', response.message);
                            }
                            oTable.draw(false);
                        }
                    });
                }
            }
        });
    }

    function createdProductionsDetail(c_productions_orders_id) {
        bootbox.confirm("Bạn có muốn tạo LSX chi tiết", function(result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: site.base_url + 'admin/manufactures/createdProductionsDetail',
                    data: {
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        productions_orders_id: c_productions_orders_id
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            oTable.draw('page');
                        } else {
                            alert_float('danger', data.message);
                            oTable.draw('page');
                        }
                    }
                });
            }

        });
    }

    function loadItemsPO(cData) {
        if (typeof cData === "undefined" || cData == null || !cData) return '';
        productions_orders_items = cData[11];
        cHtml = '';

        // <div style="width: 50px;">
        //     <a href="${site.base_url+'admin/manufactures/print_productions_orders/'+value['productions_orders_id']+'/'+value['id']}" target="_blank"><i class="fa fa-print"></i></a>
        // </div>
        if (productions_orders_items != null && productions_orders_items.length > 0) {
            $.each(productions_orders_items, function(index, value) {
                images = site.base_url + 'assets/images/tnh/no_image.png';
                if (value.images) {
                    images = site.base_url + 'uploads/products/' + value.images;
                }
                var infoStaff = '<div class="mleft30 mbot10"><h4 onclick="addAssigedPromotion(' + value.id + ')" class="task-info-heading font-normal font-medium-xs pointer"><i class="fa fa-user-o" aria-hidden="true"></i> Người được phân công</h4>';
                if (value.staff_promotion) {
                    $.each(value.staff_promotion, function(i, v) {
                        infoStaff += '<span class="mleft5">' + v.avatar + '</span>';
                    })
                }
                infoStaff += '</div>';

                cHtml += `<div class="row mbot5" style="margin-right: 0px; margin-left: 0px;">
                    <div class="col-md-4" style="padding-right: 0;">
                        <div class="flex-center">
                            <div class="td-image mright5" style="width: 50px;">
                                <div class="preview_image" style="width: auto;">
                                    <div class="display-block contract-attachment-wrapper img">
                                        <div style="width:45px;">
                                            <a href="${images}" data-lightbox="customer-profile" class="display-block mbot5">
                                                <div class=""><img src="${images}" style="border-radius: 50%"></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 80%;">
                                <div class="text-bold"><a href="${site.base_url+'admin/manufactures/detail_productions/'+value['pod_id']}" target="_blank">${value.item_code}<br>${value.item_name}</a></div>
                                <div class=""><?= lang('quantity') ?>: ${tnhFormatNumber(value.quantity)}</div>
                                <div class=""><?= lang('Công đoạn') ?>: ${(value.versions_stage)}</div>
                                ${value.price_costing > 0 ? '<div class="text-danger">Giá thành: '+tnhFormatMoney(value.price_costing)+'</div>' : ''}
                                <div class="">${value.reference_no}</div>
                            </div>
                        </div>${infoStaff}
                    </div>
                    <div class="col-md-8" style="padding-right: 0;">
                        ${value.workflow}
                    </div>
                </div>`;
            });
        }
        return `<div class="scrolling-stone pr-3 position-absolute h-100 w-100 overflow-auto max-height">${cHtml}</div>`;
    }

    $(document).ready(function() {
        $("#branch_search").select2({
            'allowClear': true
        })
        loadTable();
        $('#type_print_search').select2({
            'allowClear': true
        });
        init_datepicker();
        ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        // ajaxSelectParams('#orders_search', 'admin/orders/searchOrders', 0, true, true);
        // ajaxSelectParams('#business_plan_search', 'admin/business_plan/searchBusinessPlan', 0, true, true);
        oTable = tnhInitDataTable('#table-productions-orders', '<?= site_url('admin/manufactures/getProductionsOrdersNew') ?>', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            // 'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getProductionsOrdersNew') ?>',
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
                    $('#table-productions-orders tfoot tr td:nth-child(6)').html('<div class="text-center">' + tnhFormatMoney(json.total_quantity) + '</div>');
                    return json.aaData;
                }
            },
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         text: 'Xuất Excel',
            //         customize: function (xlsx) {
            //             const sheet = xlsx.xl.worksheets['sheet1.xml'];
            //             console.log(123);
            //             // Xử lý thay đổi dữ liệu ảnh
            //             $('row c[r]', sheet).each(function () {
            //                 const cell = $(this);
            //                 const text = cell.text();

            //                 // Kiểm tra nội dung và thay thế hình ảnh bằng đường dẫn
            //                 if (text.includes('<img')) {
            //                     const imgSrc = $(text).attr('src');
            //                     cell.text(imgSrc); // Thay hình ảnh bằng đường dẫn
            //                 }
            //             });
            //         }
            //     }
            // ],
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row) {
                        return '<div class="text-center"><a style="font-size: 25px; color: #0e3063;" href="javascript:void(0)" class="rows-child fa fa-caret-right"></a></div>';
                    },
                    'width': '50px'
                },
                {
                    "targets": 1,
                    'width': '100px'
                },
                {
                    "targets": 2,
                    'width': '120px'
                },
                {
                    "targets": 3,
                    'width': '120px',
                    'orderable': false
                },
                {
                    "targets": 4,
                    'width': '180px',
                    'orderable': false
                },
                {
                    "targets": 5,
                    'width': '120px',
                    'orderable': false
                },
                {
                    "targets": 6,
                    'width': '120px'
                },
                {
                    "targets": 7,
                    'width': '100px',
                    'sortable': false,
                },
                {
                    "targets": 8,
                    'width': '100px',
                    'sortable': false,
                },
                {
                    "targets": 9,
                    'width': '100px',
                    'sortable': false,
                },
                {
                    "targets": 10,
                    'width': '100px',
                    'sortable': false,
                },
                {
                    "targets": 11,
                    'width': '0px',
                    'visible': false
                },
            ]
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            productions_orders_id = $(this).attr('productions_orders_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_orders_id) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/agreeProductionsOrders',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            productions_orders_id: productions_orders_id,
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

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            if (status_table == 'all') {
                $(".table-detail").addClass('hide');
                $(".table-all").removeClass('hide');
                loadTable();
                if (oTableNew !== 'undefined' && oTableNew !== '') {
                    oTableNew.draw();
                } else {
                    loadTable();
                }
            } else {
                $(".table-detail").removeClass('hide');
                $(".table-all").addClass('hide');
                oTable.draw();
            }
        });

        $(document).on('change', '#productions_orders_search, #items_search, #start_date_search, #end_date_search, #orders_search, #business_plan_search, #period_time, #orders_and_business_plan, #type_print_search, #customer_search, #branch_search', function() {
            oTable.draw();
            oTableNew.draw();
        });

        $(document).on('change', '#status_search,#status_search_order', function() {
            oTableNew.draw();
        });

        $(document).on('click', '.tnh-warehousing', function(event) {
            event.preventDefault();
            trItem = $(this).closest('tr');
            trIndex = intVal($(this).attr('tr-index'));
        });

        $(document).on('click', '#agree-items', function(event) {
            event.preventDefault();
            index = this;
            productions_orders_details_id = $(this).attr('productions_orders_details_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (productions_orders_details_id) {
                $.ajax({
                        url: site.base_url + 'admin/manufactures/updateStatusDetailProductionsView',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                            productions_orders_details_id: productions_orders_details_id,
                            status: status
                        },
                    })
                    .done(function(data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            $('.txt-finished-' + productions_orders_details_id).html(data.strStatus);
                        } else {
                            alert_float('danger', data.message);
                        }
                    })
                    .fail(function(data) {
                        alert_float('danger', 'errors');
                        $(index).removeAttr('disabled');
                    })
            }
        });

        $('#table-productions-orders tbody').on('click', 'td .rows-child', function() {
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
                row.child(loadItemsPO(row.data())).show();
                tr.addClass('shown');
            }
        });

        $('#table-productions-orders').on('draw.dt', function() {
            $('.rows-child').click();
        });
    });

    function addAssigedPromotion(id) {
        $.get(admin_url + 'manufactures/get_assiged_promotion/' + id, function(data) {
            $('#cong_modal').html(data);
        })
    }

    function loadTable() {
        oTableNew = tnhInitDataTable('#table-productions-orders-all', '<?= site_url('admin/manufactures/getProductionsOrdersAll') ?>', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getProductionsOrdersAll') ?>',
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
                    $('#table-productions-orders-all tfoot tr td:nth-child(12)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity) + '</div>');
                    $('#table-productions-orders-all tfoot tr td:nth-child(13)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_sx) + '</div>');
                    $('#table-productions-orders-all tfoot tr td:nth-child(14)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_hold) + '</div>');
                    $('#table-productions-orders-all tfoot tr td:nth-child(15').html('<div class="text-center">' + (json.total_quantity_new) + '</div>');
                    $('#table-productions-orders-all tfoot tr td:nth-child(16)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_finished) + '</div>');
                    $('#table-productions-orders-all tfoot tr td:nth-child(17)').html('<div class="text-center">' + tnhFormatNumber(json.total_quantity_errors) + '</div>');
                    return json.aaData;
                }
            },
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         text: 'Xuất Excel',
            //         customize: function (xlsx) {
            //             const sheet = xlsx.xl.worksheets['sheet1.xml'];
            //             console.log(123);
            //             // Xử lý thay đổi dữ liệu ảnh
            //             $('row c[r]', sheet).each(function () {
            //                 const cell = $(this);
            //                 const text = cell.text();

            //                 // Kiểm tra nội dung và thay thế hình ảnh bằng đường dẫn
            //                 if (text.includes('<img')) {
            //                     const imgSrc = $(text).attr('src');
            //                     cell.text(imgSrc); // Thay hình ảnh bằng đường dẫn
            //                 }
            //             });
            //         }
            //     }
            // ],
            "columnDefs": [{
                    "targets": 0,
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + data + '</div>';
                    },
                    'width': '50px'
                },
                {
                    "targets": 1,
                    'width': '80px'
                },
                {
                    "targets": 2,
                    'width': '80px'
                },
                {
                    "targets": 3,
                    'width': '120px'
                },
                {
                    "targets": 4,
                    'width': '80px',
                    'className': 'not-export',
                    'visible': true,
                    'sortable': false,
                    'searchable': false,
                },
                {
                    "targets": 5,
                    'width': '120px'
                },
                {
                    "targets": 6,
                    'width': '120px'
                },
                {
                    "targets": 7,
                    'width': '80px',
                },
                {
                    "targets": 8,
                    'width': '80px',
                },
                {
                    "targets": 9,
                    'width': '100px',
                    'sortable': false,
                    'searchable': false,
                },
                {
                    "targets": 10,
                    'width': '80px',
                    'sortable': false,
                    'searchable': false,
                },
                {
                    "targets": 11,
                    'width': '80px'
                },
                {
                    "targets": 12,
                    'width': '120px'
                },
                {
                    "render": function(data, type, row) {
                        return `<div style="width: 220px;">${data}</div>`;
                    },
                    "targets": 19,
                    'width': '220px'
                },
            ]
        });
    }
</script>

<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script>
    function handPause(id, status_orders) {
        var messsage = '';
        if(status_orders == 2) {
            messsage = '<?= lang('Bạn có muốn tạm dừng lệnh sản xuất tổng này') ?>';
        }
        else {
            messsage = '<?= lang('Bạn có muốn tiếp tục thực hiện lệnh sản xuất tổng này') ?>';
        }


        bootbox.confirm({
            message: messsage,
            buttons: {
                confirm: {
                    label: '<?= lang('tnh_update') ?>',
                    className: 'btn-primary'
                },
                cancel: {
                    label: '<?= lang('close') ?>',
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result == true) {
                    var data = {id : id, status_orders : status_orders};
                    if (typeof(csrfData) !== 'undefined') {
                        data[csrfData['token_name']] = csrfData['hash'];
                    }
                    $.post(admin_url + 'manufactures/status_pause', data, function(result) {
                        result = JSON.parse(result);
                        if(result.success) {
                            oTable.draw(false);
                        }
                        alert_float(result.alert_type, result.message);
                    })
                }
            }
        });
    }

    function exportExcelPOTotal() {
        var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];

        $.each(fnserverparams, function (index, value) { 
            dataPOST[index] = $(value).val();
        });

        dataPOST['export_excel'] = 1;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/exportExcelPOTotal',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    function exportExcelPOTotalDetail() {
        var dataPOST = {};
		dataPOST[csrfData['token_name']] = csrfData['hash'];

        $.each(fnserverparams, function (index, value) { 
            dataPOST[index] = $(value).val();
        });

        dataPOST['export_excel'] = 1;
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/manufactures_temp/exportExcelPOTotalDetail',
            data: dataPOST,
            dataType: "json",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                    download(response.filename, response.file);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            po_id = $(this).attr('po_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (po_id) {
                $.ajax({
                    url: site.base_url + 'admin/manufactures/agreePO',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        po_id: po_id,
                        status: status
                    },
                })
                .done(function(data) {
                    if (data.result) {
                        alert_float('success', data.message);
                    } else {
                        alert_float('danger', data.message);
                    }
                    oTable.draw(false);
                })
                .fail(function(data) {
                    alert_float('danger', 'errors');
                    $(index).removeAttr('disabled');
                })
            }
        });
    });
</script>