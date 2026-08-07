<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Xem giữ hàng') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <b><?= lang('tnh_reference_orders') ?>:</b> <span><?= $order['reference_no'] ?></span>
                </div>
                
                
                
                <div class="col-md-12 mtop10">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home-keep" aria-controls="home" role="tab" data-toggle="tab"><?= lang('Giữ hàng sẵn') ?></a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-tt" aria-controls="tab" role="tab" data-toggle="tab"><?= lang('Giữ trên chuyền') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="home-keep">
                                <div class="table-responsive">
                                    <table id="table-view-keep-orders" class="table dataTable" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 80px;"></th>
                                            <th class="text-center"><?= lang('date') ?></th>
                                            <th class="text-center"><?= lang('tnh_reference_no_transfer') ?></th>
                                            <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                            <th class="text-center"><?= lang('status') ?></th>
                                            <th class="text-center"><?= lang('note') ?></th>
                                            <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab-tt">
                                <table id="table-view-keep-orders-tt" class="table dataTable" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 80px;"></th>
                                        <th class="text-center"><?= lang('Ngày giữ') ?></th>
                                        <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                        <th class="text-center"><?= lang('Nguời tạo') ?></th>
                                        <th class="text-center"><?= lang('Ghi chú') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="view_order_id" id="view_order_id" class="form-control" value="<?= $id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTableKeepOrders = '';
    var fnserverparamsKeepStockMaterials = {
        order_id: "#view_order_id",
    };

    var oTableKeepOrdersTT = '';
    var fnserverparamsKeepStockMaterialsTT = {
        order_id: "#view_order_id",
    };

    function rowChildKeepOrders(_this, c_transfer_id) {
        var tr = $(_this).closest('tr');
        var row = oTableKeepOrders.row( tr );
        if ( row.child.isShown() ) {
            // This row is already open - close it
            $(_this).removeClass('fa-caret-down');
            $(_this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            $(_this).addClass('fa-caret-down');
            $(_this).removeClass('fa-caret-right');
            if (c_transfer_id) {
                $.ajax({
                    type: "GET",
                    url: site.base_url+'admin/orders/loadKeepOrders',
                    data: {
                        transfer_id: c_transfer_id,
                        '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: "html",
                    success: function (response) {
                        row.child(response).show();
                        tr.addClass('shown');
                    }
                });
            }
        }
    }

    function deleteTransferToOrders(c_transfer_id) {
        bootbox.confirm("Bạn có muốn xóa giữ kho đơn hàng này ?", function(result){
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: site.base_url+'admin/orders/deleteTransferToOrders',
                    data: {
                        transfer_id: c_transfer_id,
                        '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTableKeepOrders != 'undefined' && oTableKeepOrders != '') {
                                oTableKeepOrders.draw('page');
                            }
                            if (typeof oTable != 'undefined' && oTable != '') {
                                oTable.draw(false);
                            }
                        } else {
                            alert_float('danger', data.message);
                        }
                    }
                });
            }
        });
    }

    function rowChildKeepOrdersTT(_this, c_transfer_id) {
        var tr = $(_this).closest('tr');
        var row = oTableKeepOrdersTT.row( tr );
        if ( row.child.isShown() ) {
            // This row is already open - close it
            $(_this).removeClass('fa-caret-down');
            $(_this).addClass('fa-caret-right');
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            $(_this).addClass('fa-caret-down');
            $(_this).removeClass('fa-caret-right');
            if (c_transfer_id) {
                $.ajax({
                    type: "GET",
                    url: site.base_url+'admin/orders/loadKeepOrdersTT',
                    data: {
                        transfer_id: c_transfer_id,
                        order_id: '<?= $id ?>',
                        '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: "html",
                    success: function (response) {
                        row.child(response).show();
                        tr.addClass('shown');
                    }
                });
            }
        }
    }

    $(document).ready(function() {
        oTableKeepOrders = tnhInitDataTable('#table-view-keep-orders', '<?= site_url('admin/manufactures/getKeepStockMaterial') ?>', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/orders/getKeepOrders') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsKeepStockMaterials) {
                        d[key] = $(fnserverparamsKeepStockMaterials[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
        });

        oTableKeepOrdersTT = tnhInitDataTable('#table-view-keep-orders-tt', '', {
            'order': [
                [1, 'desc']
            ],
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/orders/getKeepOrdersTT') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparamsKeepStockMaterialsTT) {
                        d[key] = $(fnserverparamsKeepStockMaterialsTT[key]).val();
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
        });
    });
</script>