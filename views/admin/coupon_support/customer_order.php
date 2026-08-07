<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
    /* #table-orders-infomation tr th:nth-child(1) {
        width: 50px !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    #table-orders-infomation tr th:nth-child(2) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #table-orders-infomation tr th:nth-child(3) {
        width: 80px !important;
        min-width: 80px !important;
        max-width: 80px !important;
    }

    #table-orders-infomation tr th:nth-child(4) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(5) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(6) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(7) {
        width: 180px !important;
        min-width: 180px !important;
        max-width: 180px !important;
    }

    #table-orders-infomation tr th:nth-child(8) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(9) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(10) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(11) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(12) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(13) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(14) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(15) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(16) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(17) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    }

    #table-orders-infomation tr th:nth-child(18) {
        width: 100px !important;
        min-width: 100px !important;
        max-width: 100px !important;
    } */
</style>
<!-- <?php echo form_open(); ?> -->
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-2">
                        <?= lang('customers', 'customers') ?>
                        <input type="text" name="customer_search" data-placeholder="<?= lang('customers') ?>" id="customer_search" class="customer_search" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_type_orders', 'type_orders_search') ?>
                        <select name="type_orders_search" id="type_orders_search" data-placeholder="<?= lang('tnh_type_orders') ?>" class="type_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($type_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_status_orders', 'status_orders_search') ?>
                        <select name="status_orders_search" id="status_orders_search" data-placeholder="<?= lang('tnh_status_orders') ?>" class="status_orders" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach ($status_orders as $key => $value) : ?>
                                <option value="<?= $value['id'] ?>"><?= $value['name'] ?>(<?= $value['time'] ?>)</option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('Sản phẩm', 'items_search') ?>
                        <input type="text" name="items_search" id="items_search" class="items_search" style="width: 100%;" data-placeholder="Thành phẩm" value="">
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2 mtop10">
                        <?= lang('tnh_branch', 'branch_search') ?>
                        <select name="branch_search" id="branch_search" data-placeholder="<?= lang('tnh_branch') ?>" class="" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($branch)) : ?>
                                <?php foreach ($branch as $key => $value) : ?>
                                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mtop10">
                        <?php $arrDeliveries = get_table_where('tbl_deliveries', [], '', 'result_array', '', 'id, reference_no') ?>
                        <?= render_select('delivery_search', $arrDeliveries, ['id', 'reference_no'], 'tnh_reference_deliveries') ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div class="">
                            <table id="table-orders-infomation" class="table dt-tnh table-orders-infomation-new" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <?= lang('tnh_numbers') ?>
                                        </th>
                                        <th class="text-center"><?= lang('Mã KH') ?></th>
                                        <th class="text-center"><?= lang('Mã ĐĐH') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_deliveries') ?></th>
                                        <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                                        <th class="text-center"><?= lang('Quy cách') ?></th>
                                        <th class="text-center"><?= lang('ĐVT') ?></th>
                                        <th class="text-center"><?= lang('SL đã giao') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('Đánh giá') ?></th>
                                        <th class="text-center" style="width: 100px;"><?= lang('Người kiểm') ?></th>
                                        <?php foreach ($fail_factor as $key => $value) { ?>
                                            <th class="text-center"><?= $value ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="99"></td>
                                    </tr>
                                </tbody>
                                <!-- <tfoot>
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
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="submit_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('coupon_support/evaluateOrder'), array('id' => 'submit_form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="title"><?php echo _l('Đánh giá'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <input type="text" class="hide" name="order_item_id" id="order_item_id">
                <div id="id_container"></div>
                <table class="tnh-tb table-bordered table-hover">
                    <tbody>
                        <?php foreach ($fail_factor as $key => $value) { ?>
                            <tr class="row" for="<?= $key ?>">
                                <td><label for="<?= $key ?>"><?= $value ?></label></td>
                                <td>
                                    <div class="checkbox-info">
                                        <input type="checkbox" name="fail_factor[<?= $key ?>]" id="<?= $key ?>" class="checkbox_item" value="1">
                                    </div>
                                </td>
                                <!-- <div class="col-md-12">
                                <div id="additional"></div>
                                <?php echo render_input($key, $value); ?>
                            </div> -->
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- <?php echo form_close(); ?> -->
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';

    function evaluateOrder(_this) {
        var value = _this.value;
        var name = _this.name;
        if (value == 1) {
            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
    
            data[name] = value;
            $.post(admin_url + 'coupon_support/evaluateOrder', data, function(data) {
                data = JSON.parse(data);
                if (data.isSuccess) {
                }
                oTable.draw();
                alert_float(data.isSuccess, data.message);
            });
        } else {
            $('#submit_form').trigger("reset");
            $('#id_container').html('<input type="text" class="hide" name="'+name+'" id="order_item_id" value="'+value+'">');
            $('#submit_modal').modal('show');

            var data = {};
            if (typeof(csrfData) !== 'undefined') {
                data[csrfData['token_name']] = csrfData['hash'];
            }
    
            var text = '';
            data[name] = value;
            $.post(admin_url + 'coupon_support/getCustomer_order_detail', data, function(data) {
                data = JSON.parse(data);
                console.log(data);
                $.each(data, (index,value)=>{
                    console.log(value);
                    $('input#'+index).prop('checked', true);
                });
            });
            // console.log(text);

        }
    }

    $("#submit_form").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var actionUrl = form.attr('action');
        var data = form.serialize();
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: data, // serializes the form's elements.
            success: function(data)
            {
                data = JSON.parse(data);
                oTable.draw();
                alert_float(data.isSuccess, data.message);
                // myModal.hide()
                $('#submit_modal').modal('hide');

            }
        });

    });

    $('#submit_modal').on('hidden.bs.modal', function () {
        $('#submit_form').trigger("reset");
        $('#id_container').html('');
        oTable.draw();
    })

    var fnserverparams = {
        customer_search: "#customer_search",
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search',
        type_orders_search: '#type_orders_search',
        status_orders_search: '#status_orders_search',
        items_search: '#items_search',
        branch_search: '#branch_search',
        delivery_search: '#delivery_search',
    };
    var oTable = '';


    $(document).ready(function() {
        ajaxSelectParams('#customer_search', 'admin/clients/searchOnlyCustomers', 0, true, true);
        $('#type_orders_search').select2({
            allowClear: true
        });
        $('#status_orders_search').select2({
            allowClear: true
        });

        oTable = tnhInitDataTable('#table-orders-infomation', '', {
            // 'order': [
            //     [2, 'desc']
            // ],
            'ordering': false,
            'fixedHeader': {
                header: true,
            },
            'responsive': false,
            "ajax": {
                "url": '<?= site_url('admin/coupon_support/getCustomerDelivery') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    // $('#table-orders-infomation tfoot tr td:nth-child(9)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_not_delivery) + '</div>');
                    // $('#table-orders-infomation tfoot tr td:nth-child(10)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_orders) + '</div>');
                    // $('#table-orders-infomation tfoot tr td:nth-child(11)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_delivery) + '</div>');
                    // $('#table-orders-infomation tfoot tr td:nth-child(12)').html('<div class="text-center">' + tnhFormatMoney(json.quantity_rest) + '</div>');

                    setTimeout(() => {
                        oTable.columns.adjust()
                    }, 2000);
                    return json.aaData;
                }
            },
            "columnDefs": [],
            // "createdRow": function(row, data, index) {
            //     if (data[1] === 'group') {
            //         $('td:eq(0)', row).attr('colspan', 99);
            //         $('td:eq(1)', row).css('display', 'none');
            //         $('td:eq(2)', row).css('display', 'none');
            //         $('td:eq(3)', row).css('display', 'none');
            //         $('td:eq(4)', row).css('display', 'none');
            //         $('td:eq(5)', row).css('display', 'none');
            //         $('td:eq(6)', row).css('display', 'none');
            //         $('td:eq(7)', row).css('display', 'none');
            //         $('td:eq(8)', row).css('display', 'none');
            //         $('td:eq(9)', row).css('display', 'none');
            //         $('td:eq(10)', row).css('display', 'none');
            //         $('td:eq(11)', row).css('display', 'none');
            //         $('td:eq(12)', row).css('display', 'none');
            //         $('td:eq(13)', row).css('display', 'none');
            //         $('td:eq(14)', row).css('display', 'none');
            //         $('td:eq(15)', row).css('display', 'none');
            //         $('td:eq(16)', row).css('display', 'none');
            //         $('td:eq(17)', row).css('display', 'none');
            //         $('td:eq(18)', row).css('display', 'none');
            //         $('td:eq(19)', row).css('display', 'none');
            //         $('td:eq(20)', row).css('display', 'none');
            //         $('td:eq(21)', row).css('display', 'none');
            //         $('td:eq(22)', row).css('display', 'none');
            //         $('td:eq(23)', row).css('display', 'none');
            //         $('td:eq(24)', row).css('display', 'none');
            //         // $('td:eq(20)', row).css('display', 'none');
            //         this.api().cell($('td:eq(0)', row)).data(data[3]);
            //         $(row).addClass('bg-group bold');
            //     }
            //     $(row).addClass('shown');
            // },
        });

        $(document).on('change', '#customer_search, #start_date_search, #end_date_search, #type_orders_search, #status_orders_search, #items_search, #branch_search, #delivery_search', function(
            event) {
            event.preventDefault();
            oTable.draw();
        });
    });


    ajaxSelectParams($('#items_search'), 'admin/products/searchProductAndGoodsMaterials', 0, true, true);
    $('#branch_search').select2({
        'allowClear': true
    });
</script>