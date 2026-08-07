<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .table-condensed tbody tr td:nth-child(11) {
        white-space: inherit;
        min-width: 160px;
    }

    .table-condensed tbody .dropdown {
        text-align: center;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php if ($this->perAddPurchaseProducts) : ?>
                <a href="<?= base_url('admin/stock/add_purchase_product') ?>" class="btn btn-info pull-right H_action_button">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= lang('tnh_reference_productions_orders', 'productions_orders_search') ?>
                    <input type="text" name="productions_orders_search" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
                </div>
                <div class="col-md-3">
                    <?= lang('Thành phẩm', 'items_search') ?>
                    <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Thành phẩm" value="">
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
                <div class="col-md-2">
                    <?= lang('start_date', 'start_date_search') ?>
                    <input type="text" name="start_date_search" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                </div>
                <div class="col-md-2">
                    <?= lang('end_date', 'end_date_search') ?>
                    <input type="text" name="end_date_search" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="horizontal-scrollable-tabs">
                            <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal status-table" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#all" aria-controls="all" role="tab" value="all" data-toggle="tab"><?= lang('all') ?>(<span><?= $all ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#un_approved" aria-controls="un_approved" role="tab" value="un_approved" data-toggle="tab"><?= lang('Sản xuất dự phòng chưa duyệt kho tồn sẵn') ?>(<span> <?= $un_approve ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#un_approved_chuyen" aria-controls="un_approved_chuyen" role="tab" value="un_approved_chuyen" data-toggle="tab"><?= lang('Sản xuất dự phòng chưa duyệt kho trên chuyền') ?>(<span> <?= $un_approved_chuyen ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tab_err" aria-controls="tab_err" role="tab" value="tab_err" data-toggle="tab"><?= lang('Sản xuất lỗi') ?>(<span> <?= $tab_err ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved_business" aria-controls="approved_business" role="tab" value="approved_business" data-toggle="tab"><?= lang('Sản xuất dự phòng đã duyệt kho tồn sẵn') ?>(<span> <?= $approved_business ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved_chuyen" aria-controls="approved_chuyen" role="tab" value="approved_chuyen" data-toggle="tab"><?= lang('Sản xuất dự phòng đã duyệt kho trên chuyền') ?>(<span> <?= $approved_chuyen ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#approved" aria-controls="approved" role="tab" value="approved" data-toggle="tab"><?= lang('Sản xuất theo đơn hàng đã duyệt kho') ?>(<span><?= $approved ?></span>)</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#is_pass" aria-controls="is_pass" role="tab" value="is_pass" data-toggle="tab"><?= lang('Vượt') ?></a>
                                    </li>
                                </ul>
                                <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="all">
                            </div>
                        </div>
                        <div class="">
                            <table id="table-purchases" class="table dt-tnh table-hover table-condensed table-purchases-new" style="min-width: 100%;">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="purchases-new"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('tnh_reference_purchase_products') ?></th>
                                        <th><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th><?= lang('tnh_lsxct') ?></th>
                                        <th><?= lang('Đơn hàng/ Kế hoạch TP') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('tnh_total_quantity') ?></th>
                                        <th><?= lang('Sl trên truyền') ?></th>
                                        <th><?= lang('Sl nhập tồn') ?></th>
                                        <th><?= lang('tnh_created_by') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('tnh_status') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
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
<div class="modal fade" id="confirm_warehous" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeModal()" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('Duyệt nhập kho thành phẩm'); ?></span>
                </h4>
            </div>
            <?php echo form_open(admin_url('stock/confirm_warehous_purchase_products/'), array('id' => 'warehouse-form')); ?>
            <div class="modal-body">
                <div style="color: red">* Chọn lại vị trí nhập kho (nếu có)</div>
                <input type="text" class="hide" name="id" id="id">
                <input type="text" class="hide" name="warehouseman_id" id="warehouseman_id" value="1">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" id="submit" autocomplete="off"><?= _l('submit') ?></button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()"  data-dismiss="modal"><?= _l('close') ?></button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    // var site = <?= json_encode(array('base_url' => base_url())) ?>;
    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        productions_orders_search: '#productions_orders_search',
        branch_search: '#branch_search',
        items_search: '#items_search'
    };
    var oTable = '';

    $(document).ready(function() {
        $("#branch_search").select2({
            'allowClear': true
        })
        oTable = tnhDatatable(
            '#table-purchases', {
                // 'order': [
                //     [1, 'desc'],
                //     [2, 'desc']
                // ],
                'ordering': false,
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                // scrollY: "450px",
                // "dom": '<"wrapper"flipt>',
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 1
                },
                scrollY: height_body,
                scrollX: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/stock/getPurchaseProducts') ?>',
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
                "rowCallback": function(row, data) {
                    pod = data[4];
                    if (pod) {
                        $(row).find('.tnh-edit').addClass('tnh-disabled');
                    }
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" name="order_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '40px'
                    },
                    {
                        "render": function(data, type, row) {
                            return fld(data);
                        },
                        "targets": 1,
                        "name": 'date',
                        'width': '80px',
                        'searchable': false
                    },
                    {
                        "render": function(data, type, row) {
                            data = data.split('__');
                            branch_name = data[3];
                            strBranch = '';
                            strType = '';
                            if (data[1] == 1){
                                strType = '<div class="label label-primary">Trên chuyền</div>';
                            } else if(data[1] == 0){
                                if (data[2] == 2) {
                                    strType = '<div class="label label-danger">Tồn sẵn</div>';
                                }
                            }
                            if (branch_name){
                                strBranch = `<div style="font-style: italic">${branch_name}</div>`;
                            }
                            return '<div style="" class="">\
                            <a data-tnh="modal" class="tnh-modal" href="' + site.base_url + 'admin/stock/view_purchase_product/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a>\
                            '+strType+strBranch+'</div><div class="td-reference-no"></div>';
                        },
                        "targets": 2,
                        "name": 'reference_no',
                        'width': '120px'
                    },
                    {
                        "targets": 3,
                        "name": 'reference_no_po',
                        'width': '120px'
                    },
                    {
                        "targets": 4,
                        "name": 'reference_pod',
                        'width': '120px',
                        'visible': false
                    },
                    {
                        "targets": 5,
                        "name": 'order',
                        'width': '120px'
                    },
                    {
                        "targets": 6,
                        "name": 'warehouse_name',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 7,
                        "name": 'total_quantity',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'total_quantity1',
                        'width': '100px',
                        'visible': false
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 9,
                        "name": 'total_quantity2',
                        'width': '100px',
                        'visible': false
                    },
                    {
                        "targets": 10,
                        "name": 'created_by',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            staff_name_warehouse = row[12];
                            data_code = row[2].split('__');
                            if (data == "un_approved") {
                                if(data_code[1] == 0 && data_code[2] == 2 && data_code[4] == 1){
                                    return '<a href="javascript:void(0)" onclick="confirm_warehous_purchase_products_new(' + row[0] + '); return;" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                                } else {
                                    return '<a href="javascript:void(0)" onclick="confirm_warehous_purchase_products(' + row[0] + ',1); return;" class=" btn btn-info btn-icon " data-toggle="tooltip" data-loading-text="Xin vui lòng đợi..." data-original-title="Thủ kho duyệt"><i class="fa  fa-square-o"></i> Chưa duyệt kho</a>';
                                }
                            }
                            return '<a href="javascript:void(0)" onclick="confirm_warehous_purchase_products(' + row[0] + ',2); return;" class=" btn btn-info btn-icon "  data-loading-text="Xin vui lòng đợi..." data-toggle="tooltip"  data-original-title="Thủ kho duyệt"><i class="fa  fa-check-square-o"></i>Đã duyệt kho</a><br>' + staff_name_warehouse + '';
                        },
                        "targets": 11,
                        "name": 'status',
                        'width': '100px'
                    },
                    {
                        "targets": 12,
                        "name": 'staff_name_warehouse',
                        'width': '100px',
                        'visible': false
                    },
                    {
                        "targets": 13,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 14,
                        "name": 'actions',
                        'width': '100px',
                        'sortable': false,
                        'searchable': false
                    },
                ],
                "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {
                    var grand_total = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        grand_total += intVal(aaData[i][7]);
                    }
                    var nCells = nRow.getElementsByTagName('th');
                    nCells[6].innerHTML = '<div class="text-center bold">' + tnhFormatNumber(grand_total) + '</div>';
                }
            }
        );

        $('#table-tools_supplies').on('draw.dt', function() {})

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw('page');
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });
        $(document).on('change', '#customer_search, #orders_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search, #items_search, #productions_orders_search, #branch_search', function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    });

    //hau
    function confirm_warehous_purchase_products(id, warehouseman_id) {
        {
            dataString = {
                id: id,
                warehouseman_id: warehouseman_id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>stock/confirm_warehous_purchase_products",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    oTable.draw('page');
                    alert_float(response.alert_type, response.message);
                }
            });
            return false;
        }
    }

    function confirm_warehous_purchase_products_new(id) {
        dataString = {
            id: id,
            [csrfData['token_name']]: csrfData['hash']
        };
        jQuery.ajax({
            type: "post",
            url: "<?= admin_url() ?>stock/get_items_purchase_product",
            data: dataString,
            cache: false,
            success: function(response) {
                response = JSON.parse(response);
                $('#id').val(id);
                var html = '<table id="view-enquiry_ch" class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new" style="width:100%">\
                            <thead>\
                                <tr>\
                                    <th style="width:10%" class="text-center"><?= lang('STT') ?></th>\
                                    <th style="width:25%" class="text-center"><?= lang('Mã TP') ?></th>\
                                    <th style="width:30%" class="text-center"><?= lang('Tên TP') ?></th>\
                                    <th style="width:10%" class="text-center"><?= lang('Số lượng') ?></th>\
                                    <th style="width:25%" class="text-center"><?= lang('Vị trí kho') ?></th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                $.each(response.purchase_product_items, function(key, value) {
                    key_new = key;
                    html += '<tr>\
                                        <td style="width:10%" class="text-center"><input class="hide" type="text" name="items[' + key + '][id]" value="' + value.id + '">'+(++key_new)+'</td>\
                                        <td style="width:25%" class="text-center">' + (value.item_code) + '</td>\
                                        <td style="width:30 class="text-center">' + (value.item_name) + '</td>\
                                        <td style="width:10%" class="text-center">' + tnhFormatNumber(value.quantity) + '</td>\
                                        <td style="width:25%"  class="text-left"><div class="form-group " style="width: 100%">\
                                             <select  required="true" name="items[' + key + '][location_id]" data-placeholder="<?= _l('choose') ?>" id="location_id_' + key + '" class="location_id" style="width: 100%;">' + value.local + '</select>\
                                        </div></td>\
                                    </tr>';

                });

                html += '</tbody>\
                        </table>';
                $('#confirm_warehous').modal('show');
                $('#table_html').html(html);
                $.each(response.purchase_product_items, function(key, value) {
                    $('#location_id_' + key).select2();
                    $('#location_id_' + key).val(value.location_id).change();

                });
                validate_form();
            }
        });
        return false;
    }

    function validate_form() {
        _validate_form($('#warehouse-form'), {}, confirm_warehous_s);
    }

    function confirm_warehous_s(form) {
        var data = $(form).serialize(),
            action = form.action;
        return $.post(action, data).done(function(form) {
            form = JSON.parse(form),
                alert_float(form.alert_type, form.message);
            oTable.draw('page');
            $('#confirm_warehous').modal('hide');

        }), !1
    }

    function closeModal(){
        oTable.draw('page');
    }
    ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0, true, true);
    ajaxSelectParamsCallback('#productions_orders_search', 'admin/manufactures/searchProductionsOrders', 0, false, true);
</script>