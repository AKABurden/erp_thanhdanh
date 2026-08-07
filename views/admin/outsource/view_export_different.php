<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<div class="modal-dialog modal-lg" style="width:70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Xem Xuất Gia Công') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-view-export-outsource" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                    <th class="text-center"><?= lang('Nhà cung cấp') ?></th>
                                    <th class="text-center"><?= lang('Nhà cung cấp') ?></th>
                                    <th class="text-center"><?= lang('status') ?></th>
                                    <th class="text-center"><?= lang('Trạng thái kho') ?></th>
                                    <th class="text-center"><?= lang('Người tạo') ?></th>
                                    <th class="text-center"><?= lang('Người tạo') ?></th>
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
            <input type="hidden" name="view_outsource_id" id="view_outsource_id" class="form-control"
                value="<?= $id ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
        </div>
    </div>
</div>
<script>
var oTableMaterial;

// function deleteTransferToPlan(c_transfer_id) {
//     bootbox.confirm("Bạn có muốn xóa giữ kho NVL ?", function(result) {
//         if (result == true) {
//             $.ajax({
//                 type: "POST",
//                 url: site.base_url + 'admin/manufactures/deleteTransferToPlan',
//                 data: {
//                     transfer_id: c_transfer_id,
//                     '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
//                 },
//                 dataType: "json",
//                 success: function(data) {
//                     if (data.result) {
//                         alert_float('success', data.message);
//                         if (typeof oTableKeepStockMaterials != 'undefined' &&
//                             oTableKeepStockMaterials != '') {
//                             oTableKeepStockMaterials.draw('page');
//                         }
//                         if (typeof oTable != 'undefined' && oTable != '') {
//                             oTable.draw(false);
//                         }
//                     } else {
//                         alert_float('danger', data.message);
//                     }
//                 }
//             });
//         }
//     });
// }

var fnserverparams = {
    view_outsource_id: "#view_outsource_id",
};
$(document).ready(function() {
    oTableMaterial = tnhDatatable(
        '#table-view-export-outsource', {
            'order': [
                [2, 'desc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            // scrollY: height_body,
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/outsource/getExportOutsource') ?>',
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
            "rowCallback": function(row, data) {},
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return `<a class="fa fa-caret-right font-size-20" onclick="rowChildExport(this, ${data})" href="javascript:void(0)"></a>`;
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "render": function(data, type, row) {
                        return (data);
                    },
                    "targets": 1,
                    "name": 'date',
                    'width': '100px',
                    'searchable': false
                },
                {
                    "render": function(data, type, row) {
                        return data;
                    },
                    "targets": 2,
                    "name": 'code',
                    'width': '80px'
                },
                {
                    "targets": 3,
                    "name": 'object',
                    'width': '140px',
                    'visible': false
                },
                {
                    "targets": 4,
                    "name": 'id_object',
                    'width': '150px',
                },
                {
                    "render": function(data, type, row) {
                        if (data == 0) {
                            str =
                                '<span class="label label-warning"><?= lang('Chưa duyệt') ?></span>';
                        } else if (data == 1) {
                            str =
                                '<span class="label label-info"><?= lang('Đã duyệt') ?></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 5,
                    "name": 'status',
                    'width': '80px',
                },
                {
                    "render": function(data, type, row) {
                        if (data == 0) {
                            str =
                                '<span class="label label-warning"><?= lang('Chưa duyệt kho') ?></span>';
                        } else if (data != 0) {
                            str =
                                '<span class="label label-info"><?= lang('Đã duyệt kho') ?></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 6,
                    "name": 'warehouseman_id',
                    'width': '100px',
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 7,
                    "name": 'staff_id',
                    'width': '100px',
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 8,
                    "name": 'object_text',
                    'width': '100px',
                    'visible': false
                },
                {
                    "targets": 9,
                    "name": 'actions',
                    'sortable': false,
                    'searchable': false,
                    'width': '130px',

                },
            ],
            "fnFooterCallback": function(nRow, aaData, iStart, iEnd, aiDisplay) {

            }
        }
    );

});

function rowChildExport(_this, c_export_id) {
    var tr = $(_this).closest('tr');
    var row = oTableMaterial.row(tr);
    if (row.child.isShown()) {
        $(_this).removeClass('fa-caret-down');
        $(_this).addClass('fa-caret-right');
        row.child.hide();
        tr.removeClass('shown');
    } else {
        $(_this).addClass('fa-caret-down');
        $(_this).removeClass('fa-caret-right');
        if (c_export_id) {
            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/outsource/loadItemsExportOutsource',
                data: {
                    export_outsource: c_export_id,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: "html",
                success: function(response) {
                    row.child(response).show();
                    tr.addClass('shown');
                }
            });
        }
    }
}

function deleteExportOutsource(_this, id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/export_different/delete/' + id,
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: "JSON",
            success: function(response) {
                alert_float(response.alert_type, response.message);
                oTableMaterial.draw('page');
                oTable.draw('page');
            }
        });
    }
    return false;
}
</script>