<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .tnh-status-sm {
        height: 70px !important;
    }

    #table-manufactures tr th:nth-child(2) {
        width: 80px !important; 
        max-width: 80px !important;
        min-width: 80px !important;
    }

    #table-manufactures tr th:nth-child(3) {
        width: 100px !important; 
        max-width: 100px !important;
        min-width: 100px !important;
    }

    #table-manufactures tr th:nth-child(4) {
        width: 100px !important; 
        max-width: 100px !important;
        min-width: 100px !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('timeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <?php //if ($this->perAddProductionsOrders): 
            ?>
            <a href="<?= base_url('admin/manufacture/add') ?>" class="btn btn-info pull-right H_action_button">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <?php //endif 
            ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="search-tnh" class="collapse in" aria-expanded="true">
                    <div class="col-md-3">
                        <?= lang('start_date', 'start_date_search') ?>
                        <input type="text" name="start_date_search" onchange="loadTable(this)" placeholder="<?= lang('start_date') ?>" id="start_date_search" autocomplete="off" class="start_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                    <div class="col-md-3">
                        <?= lang('end_date', 'end_date_search') ?>
                        <input type="text" name="end_date_search" autocomplete="off" onchange="loadTable(this)" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div role="tabpanel" class="hide">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value="" data-toggle="tab"><?= lang('all') ?></a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="">
                        </div>
                        <div class="">
                            <table id="table-manufactures" class="table dt-tnh table-hover table-condensed table-manufactures dont-responsive-table dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="manufactures"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th class="text-center"><?= lang('date') ?></th>
                                        <th class="text-center"><?= lang('Số phiếu xả') ?></th>
                                        <th class="text-center"><?= lang('tnh_reference_productions_orders') ?></th>
                                        <th class="text-center"><?= lang('tnh_product_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_product_name') ?></th>
                                        <th class="text-center"><?= lang('total_quantity') ?></th>
                                        <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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
                                        <td class="text-center"><?= ''//lang('tnh_grand_total') ?></td>
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
<div class="modal fade" id="confirm_warehous_delete" role="dialog">
    <div class="modal-dialog modal-lm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="book-title"><?php echo _l('ch_export_quantity_missing');?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="table_html"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?=_l('close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/colReorderWithResize.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var fnserverparams = {
        start_date_search: '#start_date_search',
        end_date_search: '#end_date_search'
    };
    var oTable = '';

    function loadTable(_this) {
        oTable.draw();
    }

    $(document).ready(function() {
        oTable = tnhInitDataTable('#table-manufactures', '', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            // 'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufacture/getManufactures') ?>',
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
                    // $('#table-manufactures tfoot tr td:nth-child(5)').html('<div class="text-center">'+tnhFormatMoney(json.total_quantity)+'</div>');
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    'targets': 6,
                    'visible': false,
                }
            ],
        });

        $(document).on('click', '#agree', function(event) {
            event.preventDefault();
            index = this;
            manufactures_id = $(this).attr('manufactures_id');
            status = $(this).attr('value');
            $(index).attr('disabled', 'disabled');
            $('.po').popover('hide');
            if (manufactures_id) {
                $.ajax({
                    url: site.base_url + 'admin/manufacture/agree',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "<?= $this->security->get_csrf_token_name() ?>": "<?= $this->security->get_csrf_hash() ?>",
                        manufactures_id: manufactures_id,
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
    });

    function confirm_warehous(id, warehouseman_id) {
        {
            dataString = {
                id: id,
                warehouseman_id: warehouseman_id,
                [csrfData['token_name']]: csrfData['hash']
            };
            jQuery.ajax({
                type: "post",
                url: "<?= admin_url() ?>manufacture/confirm_warehous",
                data: dataString,
                cache: false,
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.response == 0) {
                        var html = '<table class="table dt-tnh table-hover table-bordered table-condensed table-export-warehouses-new">\
                            <thead>\
                                <tr>\
                                    <th class="text-center"><?= lang('tnh_items') ?></th>\
                                    <th class="text-center"><?= lang('tblwarehouse') ?></th>\
                                    <th class="text-center"><?= lang('ch_quantity_missing') ?></th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                        $.each(response.html, function(key, value) {
                            html += '<tr>\
                                    <td>' + value.detail.name + '(' + value.detail.code + ')</td>\
                                    <td class="text-center">' + value.name_ware + ' - '+value.name_local+'</td>\
                                    <td class="text-center">' + tnhFormatNumber(value.quantity_net) + '</td>\
                                </tr>';
                        });
                        html += '</tbody>\
                        </table>';
                        $('#confirm_warehous_delete').modal('show');
                        $('#table_html').html(html);
                        $('#confirm_warehous_delete').find('.book-title').html('Số lượng không đủ để bỏ duyệt kho');

                    }
                    oTable.draw('page');
                    alert_float(response.alert_type, response.message);
                }
            });
            return false;
        }
    }

    function printPdfManu(id) {
        var dataPOST = {};
        dataPOST[csrfData['token_name']] = csrfData['hash'];
        dataPOST['id'] = id;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/manufacture/printPdfManu',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result == 0) {
                    alert_float('danger', response.message);
                } else {
                    var iframe = document.createElement('iframe');
                    // iframe.id = 'pdfIframe'
                    iframe.className = 'pdfIframe'
                    document.body.appendChild(iframe);
                    iframe.style.display = 'none';
                    iframe.onload = function() {
                        setTimeout(function() {
                            iframe.focus();
                            iframe.contentWindow.print();
                            URL.revokeObjectURL(response.url)
                            // document.body.removeChild(iframe)
                        }, 1);
                    };
                    iframe.src = response.url;
                }
            }
        });
        
    }
</script>