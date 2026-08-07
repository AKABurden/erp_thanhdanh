<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
.fixedHeader-floating {
    position: fixed !important;
}
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button"
                    id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <?php if ($this->perExportToolsSupplies): ?>
<!--                    <li>-->
<!--                        <a href="--><?//= base_url('admin/tools_supplies/export_excel_tools_supplies') ?><!--" class="tnh-modal"-->
<!--                            data-tnh="modal" data-toggle="modal" data-target="#myModal">-->
<!--                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>-->
<!--                            --><?php //echo lang('tnh_export_excel'); ?>
<!--                        </a>-->
<!--                    </li>-->
                    <?php endif ?>
                    <?php if ($this->perAddToolsSupplies): ?>
                    <li>
                        <a href="<?= base_url('admin/tools_supplies/import_tools_supplies') ?>" class="">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                            <?php echo lang('tnh_import_excel'); ?>
                        </a>
                    </li>
                    <?php endif ?>
                    <?php if ($this->perDeleteToolsSupplies): ?>
                    <li class="not-outside">
                        <?php echo '<a class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\''.base_url('admin/tools_supplies/delete_tools_supplies_multiple').'\' class=\'btn btn-danger po-delete-multiple-json\'>'.lang('delete').'</button>
                        <button class=\'btn btn-default po-close\'>'.lang('close').'</button>
                        "><i class="fa fa-remove"></i> '.lang('delete').'</button>' ?>
                    </li>
                    <?php endif ?>
                </ul>
            </div>

            <?php if ($this->perAddToolsSupplies): ?>
            <a href="<?= base_url('admin/tools_supplies/add_item') ?>"
                class="btn btn-info mright5 pull-right mright H_action_button tnh-modal" data-tnh="modal"
                data-toggle="modal" data-target="#myModal">
                <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                <?php echo _l('add'); ?>
            </a>
            <?php endif ?>

			<?php if ($this->perExportToolsSupplies): ?>
                    <a href="<?= base_url('admin/tools_supplies/export_excel_tools_supplies') ?>" class="btn btn-info mright5 pull-right mright H_action_button tnh-modal"
                       data-tnh="modal" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
						<?php echo lang('tnh_export_excel'); ?>
                    </a>
			<?php endif ?>
            <a onclick="printQRItems()" href="#" class="btn btn-info pull-right mright5 H_action_button">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                <?php echo lang('IN QR'); ?>
            </a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div role="tabpanel">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <?php foreach (type_tools_supplies() as $key => $value): ?>
                                <li role="presentation">
                                    <a href="#<?= $key ?>" aria-controls="tools" role="<?= $key ?>" value="<?= $key ?>"
                                        data-toggle="tab"><?= $value ?></a>
                                </li>
                                <?php endforeach ?>
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value=""
                                        data-toggle="tab"><?= lang('all') ?></a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table"
                                value="">
                        </div>
                        <div class="table-responsive">
                            <table id="table-tools_supplies"
                                class="table table-hover table-datatable table-bordered table-condensed dataTable table-tools_supplies">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input
                                                    type="checkbox" id="mass_select_all"
                                                    data-to-table="tools_supplies"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('image') ?></th>
                                        <th><?= lang('category') ?></th>
                                        <th><?= lang('type') ?></th>
                                        <th><?= lang('tnh_tool_supplies_code') ?></th>
                                        <th><?= lang('tnh_tool_supplies_name') ?></th>
                                        <th><?= lang('unit') ?></th>
                                        <th><?= lang('tnh_price_import') ?></th>
                                        <th><?= lang('Số lượng tối thiểu') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_quantity_inventory') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('tnh_date_warehousing_nearest') ?></th>
                                        <th><?= lang('tnh_user_warehousing_nearest') ?></th>
                                        <th><?= lang('tnh_reference_warehousing_nearest') ?></th>
                                        <?= $th ?>
                                        <th><?= lang('actions') ?></th>
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

<?php $this->load->view('loader')?>
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
// var site = <?= json_encode(array('base_url' => base_url())) ?>;
var lang_toos =
    <?= json_encode(array('tools' => lang('tools'), 'supplies' => lang('supplies'), 'packaging' => lang('packaging'))) ?>;
var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var fnserverparams = {
    status_table: '#status_table'
};
var oTable = '';
</script>
<script type="text/javascript">
$(document).ready(function() {
    oTable = tnhDatatable(
        '#table-tools_supplies', {
            'order': [
                [2, 'asc']
            ],
            'orderCellsTop': true,
            "language": app.lang.datatables,
            "pageLength": app.options.tables_pagination_limit,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "<?= lang('all') ?>"]
            ],
            scrollY: height_body,
            scrollX: true,
            fixedColumns: {
                leftColumns: 5,
                rightColumns: 1
            },
            "serverSide": true,
            'sAjaxSource': '<?= site_url('admin/tools_supplies/getToolsSupplies') ?>',
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
            "initComplete": function(settings, json) {
                var t = this;
                t.parents('.table-loading').removeClass('table-loading');
                t.removeClass('dt-table-loading');
                mainWrapperHeightFix();
            },
            "columnDefs": [{
                    "render": function(data, type, row) {
                        return '<div class="checkbox"><input type="checkbox" name="tools_supplies_id[]" id="check-item' +
                            data + '" value="' + data + '"><label for="check-item' + data +
                            '"></label></div>';
                    },
                    "targets": 0,
                    "name": 'id',
                    'orderable': false,
                    'width': '40px'
                },
                {
                    "targets": 1,
                    "name": 'images',
                    'width': '60px',
                    "render": function(data, type, row) {
                        images = (data != null) ? site.base_url + "uploads/tools_supplies/" + data +
                            '?' : site.base_url + "assets/images/tnh/no_image.png";
                        return '<div class="preview_image" style="width: auto;">\
		                        <div class="display-block contract-attachment-wrapper img">\
		                            <div style="width:45px; margin: auto;">\
		                                <a href="' + images + '" data-lightbox="customer-profile" class="display-block mbot5">\
		                                    <div class="">\
		                                        <img src="' + images + '" style="border-radius: 50%" />\
		                                    </div>\
		                                </a>\
		                            </div>\
		                        </div>\
		                    </div>';
                    }
                },
                {
                    "targets": 2,
                    "name": 'category_name',
                    'width': '130px'
                },
                {
                    "render": function(data, type, row) {
                        str = '';
                        if (data == "tools") {
                            str = '<span class="label label-success">' + lang_toos[data] +
                                '</span>';
                        } else if (data == "supplies") {
                            str = '<span class="label label-warning">' + lang_toos[data] +
                                '</span>';
                        } else if (data == "packaging") {
                            str = '<span class="label label-danger">' + lang_toos[data] + '</span>';
                        }
                        return str;
                    },
                    "targets": 3,
                    "name": 'type',
                    'width': '50px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div>' + data + '</div>';
                    },
                    "targets": 4,
                    "name": 'code',
                    'width': '130px'
                },
                {
                    "targets": 5,
                    "name": 'name',
                    'width': '180px'
                },
                {
                    "targets": 6,
                    "name": 'unit_name',
                    'width': '80px',
                    'class': 'text-center'
                },
                {
                    "targets": 7,
                    "name": 'price_import',
                    'width': '100px',
                    "render": function(data, type, row) {
                        return '<div class="text-right">' + formatNumberTnh(data) + '</div>';
                    }
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 8,
                    "name": 'quantity_minimum',
                    'width': '100px'
                },
                {
                    "targets": 9,
                    "name": 'note',
                    'width': '100px'
                },
                {
                    "render": function(data, type, row) {
                        return '<div class="text-center">' + tnhFormatNumber(data) + '</div>';
                    },
                    "targets": 10,
                    "name": 'quantity_inventory',
                    'width': '120px'
                },
                {
                    "targets": 11,
                    "name": 'warehouses',
                    'width': '120px'
                },
                {
                    "render": function(data, type, row) {
                        return fsd(data);
                    },
                    "targets": 12,
                    "name": 'date_warehousing_nearest',
                    'width': '120px'
                },
                {
                    "targets": 13,
                    "name": 'user_warehousing_nearest',
                    'width': '120px'
                },
                {
                    "targets": 14,
                    "name": 'reference_warehousing_nearest',
                    'width': '120px'
                },
                <?= $script ?> {
                    "targets": <?= $targets ?>,
                    "name": 'actions',
                    'orderable': false,
                    'searchable': false,
                    'width': '80px'
                }
            ]
        }
    );

    $('#table-tools_supplies').on('draw.dt', function() {})

    $(document).on('click', '.btn-dt-reload', function(event) {
        oTable.draw();
    });

    $(document).on('click', '.status-table li a', function(event) {
        status_table = $(this).attr('value');
        $('#status_table').val(status_table);
        oTable.draw();
    });
});

function printQRItems()
{
    var ids = '';
    var rows = $('#table-tools_supplies').find('tbody tr');
    var grand_total_seller = 0;
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            ids += checkbox.val() + ',';
        }
    });

    ids = ids.slice(0, -1);

    if (!ids) {
        bootbox.alert('Xin vui lòng chọn mặt hàng cần in QR');
        return;
    }

    window.open(site.base_url+'admin/tools_supplies/print_qr?ids='+ids, "_blank");
}
</script>
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>