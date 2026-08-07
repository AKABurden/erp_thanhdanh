<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<style>
.item_error {
    display: flex;
    border: 1px dotted red;
    padding: 5px;
    min-height: 90px
}

.item-content {
    width: 100%;
    margin-left: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.item-content-title {
    font-size: 14px;
    font-weight: bold;
}

.item-content-qty {
    color: red;
}

.title_error {
    text-align: center;
    font-size: 15px;
    color: red;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.item-image {
    display: flex;
    align-items: center;
}

.item-content-warehouse {
    font-size: 12px;
    margin-bottom: 5px;
    margin-top: 5px;
}
</style>
<div class="modal-dialog modal-lg" style="width:70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= lang('Xem Nhập Gia Công') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="html_error">
                </div>
                <div class="col-md-12 mtop10">
                    <div class="table-responsive">
                        <table id="table-view-import-outsource" class="table dataTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;"></th>
                                    <th class="text-center"><?= lang('date') ?></th>
                                    <th class="text-center"><?= lang('Mã phiếu') ?></th>
                                    <th class="text-center"><?= lang('status') ?></th>
                                    <th class="text-center"><?= lang('Trạng thái kho') ?></th>
                                    <th class="text-center"><?= lang('Kho hàng') ?></th>
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
var oTableImport;

var fnserverparams = {
    view_outsource_id: "#view_outsource_id",
};
$(document).ready(function() {
    oTableImport = tnhDatatable(
        '#table-view-import-outsource', {
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
            'sAjaxSource': '<?= site_url('admin/outsource/getImportOutsourceAjax') ?>',
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
                        return `<a class="fa fa-caret-right font-size-20" onclick="rowChildImport(this, ${data})" href="javascript:void(0)"></a>`;
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
                    "render": function(data, type, row) {
                        if (data == 'un_approved') {
                            str =
                                '<span class="label label-warning"><?= lang('Chưa duyệt') ?></span>';
                        } else if (data == 'approved') {
                            str =
                                '<span class="label label-info"><?= lang('Đã duyệt') ?></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 3,
                    "name": 'status',
                    'width': '80px'
                },
                {
                    "render": function(data, type, row) {
                        if (data == 0) {
                            str =
                                '<span class="label label-warning"><?= lang('Chưa duyệt kho') ?></span>';
                        } else if (data != 0) {
                            str =
                                '<span class="label label-danger"><?= lang('Đã duyệt kho') ?></span>';
                        }
                        return `<div class="text-center">${str}</div>`;
                    },
                    "targets": 4,
                    "name": 'warehouseman_id',
                    'width': '100px',
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 5,
                    "name": 'warehouse',
                    'width': '100px',
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-left">' + data + '</div>';
                    },
                    "targets": 6,
                    "name": 'staff_id',
                    'width': '100px',
                },
                {
                    "targets": 7,
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

function rowChildImport(_this, c_import_id) {
    var tr = $(_this).closest('tr');
    var row = oTableImport.row(tr);
    if (row.child.isShown()) {
        $(_this).removeClass('fa-caret-down');
        $(_this).addClass('fa-caret-right');
        row.child.hide();
        tr.removeClass('shown');
    } else {
        $(_this).addClass('fa-caret-down');
        $(_this).removeClass('fa-caret-right');
        if (c_import_id) {
            $.ajax({
                type: "GET",
                url: site.base_url + 'admin/outsource/loadItemsImportOutsource',
                data: {
                    import_outsource: c_import_id,
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

function deleteImport(_this, id) {
    var r = confirm("<?php echo _l('confirm_action_prompt');?>");
    if (r == false) {
        return false;
    } else {
        $.ajax({
            type: "GET",
            url: site.base_url + 'admin/outsource/deleteImportOutsource/' + id,
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: "JSON",
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    console.log(response.items);
                    alert_float('danger', response.message);
                    if (response.items.length > 0) {
                        html = '<div class="title_error">Thành phẩm không đủ</div>';
                        $.each(response.items, function(key, value) {
                            images = value.image;
                            html += `
                            <div class="col-md-4">
                            <div class="item_error">
                                <div class="item-image">
                                    <div class="td-image">
                                        <div class="preview_image" style="width: auto;">
                                            <div class="display-block contract-attachment-wrapper img">
                                                <div style="width:45px;">
                                                    <a href="${images}" data-lightbox="customer-profile"
                                                        class="display-block mbot5">
                                                        <div class="">
                                                            <img src="${images}" style="border-radius: 50%">
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-content">
                                    <div class="item-content-title">${value.item_name}( ${value.item_code})</div>
                                    <div class="item-content-warehouse">Kho hàng: ${value.name_ware} -> ${value.name_location}</div>
                                    <div class="item-content-qty">Số lượng thiếu: ${formatNumberTnh(value.quantity_net)}</div>
                                </div>
                            </div>
                        </div>
                        `;
                        });

                        $(".html_error").html(html);
                    }
                }
                oTableImport.draw('page');
                oTable.draw('page');
            }
        });
    }
    return false;
}
</script>