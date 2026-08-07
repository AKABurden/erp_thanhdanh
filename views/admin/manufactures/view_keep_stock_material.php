<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('tnh_view_keep_stock') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-view-keep-stock-material" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('tnh_reference_no_transfer') ?></th>
                                    <th class="text-center"><?= lang('tnh_created_by') ?></th>
                                    <th class="text-center"><?= lang('status') ?></th>
                                    <th class="text-center" style="width: 80px;"><?= lang('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="view_productions_plan_id" id="view_productions_plan_id" class="form-control" value="<?= $id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
    var oTableKeepStockMaterials = '';
    var fnserverparamsKeepStockMaterials = {
        view_productions_plan_id: "#view_productions_plan_id",
    };

    function rowChildKeepStock(_this, c_transfer_id) {
        var tr = $(_this).closest('tr');
        var row = oTableKeepStockMaterials.row( tr );
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
                    url: site.base_url+'admin/manufactures/loadKeepStockMaterial',
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

    function deleteTransferToPlan(c_transfer_id) {
        bootbox.confirm("Bạn có muốn xóa giữ kho NVL ?", function(result){ 
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: site.base_url+'admin/manufactures/deleteTransferToPlan',
                    data: {
                        transfer_id: c_transfer_id,
                        '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: "json",
                    success: function (data) {
                        if (data.result) {
                            alert_float('success', data.message);
                            if (typeof oTableKeepStockMaterials != 'undefined' && oTableKeepStockMaterials != '') {
                                oTableKeepStockMaterials.draw();
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

    $(document).ready(function() {
        oTableKeepStockMaterials = tnhInitDataTable('#table-view-keep-stock-material', '<?= site_url('admin/manufactures/getKeepStockMaterial') ?>', {
            'order': [
                [1, 'desc']
            ],
            // 'fixedHeader': {
            //     header: true,
            // },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/manufactures/getKeepStockMaterial') ?>',
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
            // "columnDefs": [
            //     {
            //         "targets": 0,
            //         'width': '80px'
            //     },
            // ]
        })
    });
</script>