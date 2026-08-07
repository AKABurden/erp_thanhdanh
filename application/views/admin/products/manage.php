<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .td-input-field {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
    }

    .delete_input_field i {
        color: #949494;
        font-size: 1.5em;
    }

    .delete_input_field i:hover {
        color: #000;
        cursor: pointer;
    }

    .panel_box {
        margin: 0;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, .2), 0 2px 2px 0 rgba(0, 0, 0, .14), 0 1px 5px 0 rgba(0, 0, 0, .12);
    }

    .head-setting {
        font-weight: 500;
    }

    .line-head-setting {
        border-bottom: 1px solid #ccc;
    }

    .div-note img {
        width: 100px;
        height: 100px;
    }

    .div-note table {
        width: 100px !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.2') ?>">
<?php echo form_open(); ?>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <div class="dropdown pull-right">
                <button class="btn btn-info pull-right H_action_button dropdown-toggle nav-link" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                    <?= lang('actions') ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu1" style="width: 200px;">
                    <?php if ($this->perExportProducts): ?>
                        <li>
                            <a href="<?= base_url('admin/products/export_excel_products_old') ?>" class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                <?php echo lang('tnh_export_excel'); ?>
                            </a>
                        </li>
                    <?php endif ?>
                    <?php if ($this->perAddProducts): ?>
                        <li>
                            <a href="<?= base_url('admin/products/import_products') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_import_excel'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('admin/products/import_bom') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_import_bom'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('admin/products/import_bom_additional') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_import_bom_additional'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('admin/products/import_stage') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_import_stages'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('admin/products/update_stage') ?>" class="">
                                <i class="fa fa-upload" aria-hidden="true"></i>
                                <?php echo lang('tnh_update_stages'); ?>
                            </a>
                        </li>
                    <?php endif ?>
                    <li>
                        <a class="test btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                    </li>
                    <?php if ($this->perDeleteProducts): ?>
                        <li class="not-outside">
                            <?php echo '<a class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                        <button href=\'' . base_url('admin/products/delete_products_multiple') . '\' class=\'btn btn-danger po-delete-multiple-json\'>' . lang('delete') . '</button>
                        <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                        "><i class="fa fa-remove"></i> ' . lang('delete') . '</button>' ?>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
            <?php if ($this->perAddProducts): ?>
                <a href="<?= base_url('admin/products/add_product') ?>" class="btn btn-info pull-right mright5 H_action_button tnh-modal active-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                    <i class="lnr lnr-plus-circle" aria-hidden="true"></i>
                    <?php echo _l('add'); ?>
                </a>
            <?php endif ?>

            <?php if ($this->perExportProducts): ?>
                <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>

                <a href="<?= base_url('admin/export_data/export_product_stage_bom_view') ?>" class="btn btn-info pull-right mright5 H_action_button">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <?php echo lang('EXPORT CÔNG ĐOẠN - BOM'); ?>
                </a>

                <a href="<?= base_url('admin/export_data/export_product_stage_view') ?>" class="btn btn-info pull-right mright5 H_action_button">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <?php echo lang('EXPORT CÔNG ĐOẠN'); ?>
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
                <div id="search-tnh" class="collapse in" aria-expanded="true" style="">
                    <div class="col-md-3">
                        <?= lang('category', 'category_search') ?>
                        <select name="category_search" id="category_search" data-placeholder="<?= lang('tnh_category_product') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?= recursiveCategoryProducts() ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('products', 'products_search') ?>
                        <input type="text" name="products_search" id="products_search" style="width: 100%;" data-placeholder="<?= lang('products') ?>" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('dt_bom_old', 'code_bom_search') ?>
                        <input type="text" name="code_bom_search" id="code_bom_search" style="width: 100%;" class="form-control code_bom_search" placeholder="<?= lang('dt_bom_old') ?>" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('Ngày tạo từ', 'date_start_search') ?>
                        <input type="text" name="date_start_search" id="date_start_search" class="form-control datepicker" placeholder="<?= lang('Ngày tạo từ') ?>" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('Ngày tạo đến', 'date_end_search') ?>
                        <input type="text" name="date_end_search" id="date_end_search" class="form-control datepicker" placeholder="<?= lang('Ngày tạo đến') ?>" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php echo $this->load->view('admin/alert') ?>
                        <div class="clearfix"></div>
                        <div role="tabpanel">
                            <ul class="nav nav-tabs status-table" role="tablist">
                                <?php foreach (type_products() as $key => $value): ?>
                                    <li role="presentation">
                                        <a href="#<?= $key ?>" aria-controls="<?= $key ?>" role="tab" value="<?= $key ?>" data-toggle="tab"><?= $value ?></a>
                                    </li>
                                <?php endforeach ?>
                                <li role="presentation" class="active">
                                    <a href="#all" aria-controls="all" role="tab" value="" data-toggle="tab"><?= lang('all') ?></a>
                                </li>
                            </ul>
                            <input type="hidden" name="status_table" id="status_table" class="form-control status_table" value="">
                        </div>
                        <div class="">
                            <table id="table-products" class="table table-hover table-products" style="min-width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="products"><label for="mass_select_all"></label></div>
                                        </th>
                                        <th class="text-center"><?= lang('image') ?></th>
                                        <th class="text-center"><?= lang('category') ?></th>
                                        <th class="text-center"><?= lang('tnh_type_products') ?></th>
                                        <th class="text-center"><?= lang('tnh_product_code') ?></th>
                                        <th class="text-center"><?= lang('tnh_product_name') ?></th>
                                        <th class="text-center"><?= lang('unit') ?></th>
                                        <th class="text-center"><?= lang('Không sản xuất tồn') ?></th>
                                        <th class="text-center"><?= lang('tnh_quantity_inventory') ?></th>
                                        <th class="text-center"><?= lang('BOM') ?></th>
                                        <th class="text-center"><?= lang('stages') ?></th>
                                        <th class="text-center"><?= lang('note') ?></th>
                                        <th class="text-center"><?= lang('tnh_versions') ?></th>
                                        <th class="text-center"><?= lang('tnh_versions_stage') ?></th>
                                        <th class="text-center"><?= lang('tnh_detail_quantity') ?></th>
                                        <?= $th ?>
                                        <th><?= lang('id') ?></th>
                                        <th class="text-center"><?= lang('actions') ?></th>
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

<div class="modal fade" id="print_barcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('print_barcode'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/products/pdf', array('id' => 'print_pdf')); ?>
            <div class="modal-body" style="background: #f1f1f1">
                <div class="col-md-8">
                    <div class="panel_s panel_box">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= _l('item_name') ?></th>
                                        <th style="text-align: center;">
                                            <?= _l('item_quantity') ?>
                                            (<span class="checkbox-primary">
                                                <label for="check_change_all" data-toggle="tooltip" data-original-title=""
                                                    title="">
                                                    <?= _l('all') ?>:
                                                </label>
                                                <input type="checkbox" id="check_change_all" name="check_change_all"
                                                    value="1">
                                            </span>)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="content-print">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel_s panel_box">
                        <div class="panel-body">
                            <div class="print-size">
                                <div class="head-setting line-head-setting">
                                    <?= _l('printing_size') ?>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="0" checked>
                                    <label for="type_size">1 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="1">
                                    <label for="type_size">2 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="2">
                                    <label for="type_size">3 <?= _l('stamp') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_size" value="3">
                                    <label for="type_size">100 <?= _l('stamp') ?></label>
                                </div>
                            </div>
                            <div class="print-show">
                                <div class="head-setting line-head-setting">
                                    <?= _l('show') ?>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="0">
                                    <label for="type_show"><?= _l('only_code') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="1">
                                    <label for="type_show"><?= _l('code_and_name') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="2">
                                    <label for="type_show"><?= _l('code_and_amount') ?></label>
                                </div>
                                <div class="radio radio-primary mtop10">
                                    <input type="radio" name="type_show" value="3" checked>
                                    <label for="type_show"><?= _l('full_show') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer" style="background: #f1f1f1">
                <button type="submit" class="btn btn-info" target="_blank"><?php echo _l('print_barcode'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
    var lang_product = <?= json_encode(array('tnh_sequence' => lang('tnh_sequence'), 'tnh_stage' => lang('tnh_stage'), 'tnh_number_date' => lang('tnh_number_date'), 'tnh_number_date' => lang('tnh_number_date'))) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        status_table: '#status_table',
        category_search: '#category_search',
        products_search: '#products_search',
        code_bom_search: '#code_bom_search',
        date_start_search: '#date_start_search',
        date_end_search: '#date_end_search',
    };
    var oTable = '';
    var iDt = 0;
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#category_search').select2({
            'allowClear': true
        });
        ajaxSelectParamsCallback('#products_search', 'admin/products/searchProductsSelect2', $('#products_search').val(), false, true);

        oTable = tnhDatatable(
            '#table-products', {
                'order': [
                    [<?= $targetsId ?>, 'desc']
                ],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "<?= lang('all') ?>"]
                ],
                "processing": true,
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                // scrollY: height_body,
                // scrollX: true,
                // scrollCollapse: true,
                // fixedColumns:   {
                //     leftColumns: 5,
                //     rightColumns: 1
                // },
                // stateSave: true,
                autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/products/getProducts') ?>',
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
                "drawCallback": function(aoData, settings) {
                    $('.sl-bom').selectpicker();
                    $('.sl-stages').selectpicker();
                },
                'fnRowCallback': function(nRow, aData, iDisplayIndex) {
                    type_products = aData[3];
                    if (type_products == 'semi_products_outside') {
                        $(nRow).find('.design_bom').addClass('tnh-disabled');
                        $(nRow).find('.stages').addClass('tnh-disabled');
                    }
                    return nRow;
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function(tfoot, data, start, end, display) {},
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" name="product_id[]" id="check-item' + data + '" value="' + data + '"><label for="check-item' + data + '"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '30px'
                    },
                    {
                        "targets": 1,
                        "name": 'images',
                        'width': '50px',
                        "render": function(data, type, row) {
                            images = (data != null) ? site.base_url + "uploads/products/" + data + '?' : site.base_url + "assets/images/tnh/no_image.png";
                            return '<div class="preview_image" style="width: auto;">\
		                        <div class="display-block contract-attachment-wrapper img">\
		                            <div style="width:30px; margin: auto;">\
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
                        'width': '100px'
                    },
                    {
                        "targets": 3,
                        "name": 'type_products',
                        'width': '130px',
                        "render": function(data, type, row) {
                            str = '';
                            if (data == "products") {
                                str = '<span class="label label-success"><?= lang('products') ?></span>';
                            } else if (data == "semi_products") {
                                str = '<span class="label label-danger"><?= lang('semi_products') ?></span>';
                            } else if (data == "semi_products_outside") {
                                str = '<span class="label label-warning"><?= lang('semi_products_outside') ?></span>';
                            }
                            return '<div class="text-center">' + str + '</div><div class="mtop5">' + row[<?= $targets ?>] + '</div>';
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            if (!data) return '';
                            data = data.split('__');
                            return '<div style=""><a data-tnh="modal" class="tnh-modal" href="' + site.base_url + 'admin/products/view_product/' + row[0] + '" data-toggle="modal" data-target="#myModal">' + data[0] + '</a></div><div class="italic">' + data[1] + '</div>';
                        },
                        "targets": 4,
                        "name": 'code',
                        'width': '150px'
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div style="width: 150px;">' + data + '</div>'
                        },
                        "targets": 5,
                        "name": 'name',
                        'width': '150px'
                    },
                    {
                        "targets": 6,
                        "name": 'unit_name',
                        'width': '80px',
                        'className': 'text-center'
                    },
                    {
                        "render": function(data, type, row) {
                            return `<div class="onoffswitch">
                                <input type="checkbox" data-switch-url="${site.base_url+'admin/products/change_status_products'}" name="onoffswitch" class="onoffswitch-checkbox" id="c_${row[0]}" data-id="${row[0]}" ${data == 1 ? 'checked' : ''}>
                                <label class="onoffswitch-label" for="c_${row[0]}"></label>
                            </div>`;
                        },
                        "targets": 7,
                        "name": 'status',
                        'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            detailQty = row[14];
                            return '<div class="text-center" style="color:red;font-weight: 500;font-size: 20px; cursor: pointer;" data-html="true" data-toggle="tooltip" data-title="' + detailQty + '">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 8,
                        "name": 'quantity_inventory',
                        'width': '120px'
                    },
                    {
                        "targets": 9,
                        "name": 'bm',
                        'width': '100px',
                        "render": function(data, type, row) {
                            sl = '';
                            if (data) {
                                data = data.split(':::');
                                sl += '<div style="width: 100px;"><select name="sl-bom[]" class="form-control sl-bom" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>"><option value=""></option>';
                                $.each(data, function(index, el) {
                                    selected = row[12] == el ? 'selected' : '';
                                    sl += '<option ' + selected + ' value="' + el + '">' + el + '</option>';
                                });
                                sl += '</select></div>';
                            }
                            return sl;
                        }
                    },
                    {
                        "targets": 10,
                        "name": 'st',
                        'width': '100px',
                        "render": function(data, type, row) {
                            sl = '';
                            if (data) {
                                data = data.split(':::');
                                sl += '<div style="width: 100px;"><select name="sl-stages[]" class="form-control sl-stages" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>"><option value=""></option>';
                                $.each(data, function(index, el) {
                                    selected = row[13] == el ? 'selected' : '';
                                    sl += '<option ' + selected + ' value="' + el + '">' + el + '</option>';
                                });
                                sl += '</select></div>';
                            }
                            return sl;
                        }
                    },
                    {
                        "render": function(data, type, row) {
                            return '<div class="div-note">' + (data) + '</div>';
                        },
                        "targets": 11,
                        "name": 'note',
                        'width': '200px'
                    },
                    {
                        "targets": 12,
                        "name": 'versions',
                        'visible': false
                    },
                    {
                        "targets": 13,
                        "name": 'versions_stage',
                        'visible': false
                    },
                    {
                        "targets": 14,
                        "name": 'qty_ws_inventory',
                        'width': '100px',
                        'visible': false,
                        'orderable': false,
                        'searchable': false
                    },
                    <?= $script ?> {
                        "targets": <?= $targetsId ?>,
                        "name": 'id_sort',
                        'visible': false
                    },
                    {
                        "targets": <?= $targets ?>,
                        "name": 'actions',
                        'orderable': false,
                        'searchable': false,
                        'width': '160px',
                        'visible': false
                    }
                ]
            }
        );

        // filterCustom('#table-products thead', oTable, [
        //           {element: '#table-products thead tr:eq(1) th:nth-child(2)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(3)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(4)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(5)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(6)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(7)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(8)', type: "text", data: []},
        //           {element: '#table-products thead tr:eq(1) th:nth-child(9)', type: "text", data: []},
        //       ]);

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw(false);
        });

        $('#table-products').on('draw.dt', function(e, settings) {
            // $('.dataTables_scrollHead tr th:nth-child(15)').addClass('abcd');
            // $(this).removeClass('dataTable');
            // $('.DTFC_LeftBodyLiner .dataTables_wrapper').remove();
            // $('table').removeClass('dataTable');
            // $('.DTFC_LeftBodyLiner table').removeClass('dataTable');
            // setTimeout(function(){ }, 3000);

            // $('.DTFC_LeftBodyLiner table td:nth-child(1)').trigger('click');
            // $('.DTFC_RightBodyLiner table td:nth-child(1)').trigger('click');
            // setTimeout(function(){
            //     $('.DTFC_RightBodyLiner table td:nth-child(1)').trigger('click');
            //     $('.DTFC_LeftBodyLiner table').removeClass('dataTable');
            //     $('.DTFC_RightBodyLiner table').removeClass('dataTable');
            // }, 1000);

            // console.log(settings.fnRecordsTotal());
        })

        $(document).on('change', 'select.sl-bom', function(event) {
            event.preventDefault();
            row = $(this).closest('tr');
            product_id = row.find('input[name="product_id[]"]').val();
            material_bom = $(this).val();
            $.ajax({
                    url: site.base_url + 'admin/products/change_versions',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                        product_id: product_id,
                        material_bom: material_bom,
                    },
                })
                .done(function(data) {
                    alert_float('success', data.message);
                })
                .fail(function() {
                    alert_float('danger', 'fail');
                });
        });

        $(document).on('change', 'select.sl-stages', function(event) {
            event.preventDefault();
            row = $(this).closest('tr');
            product_id = row.find('input[name="product_id[]"]').val();
            vs_stage = $(this).val();
            $.ajax({
                    url: site.base_url + 'admin/products/change_versions_stages',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                        product_id: product_id,
                        vs_stage: vs_stage,
                    },
                })
                .done(function(data) {
                    alert_float('success', data.message);
                })
                .fail(function() {
                    alert_float('danger', 'fail');
                });
        });

        $(document).on('click', '.status-table li a', function(event) {
            status_table = $(this).attr('value');
            $('#status_table').val(status_table);
            oTable.draw();
        });

        $(document).on('change', '#category_search, #products_search, #date_start_search, #date_end_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        $(document).on('change', '#code_bom_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });


        loadAjax();
    });

    //Hoàng CRM bổ xung
    $(document).on('keyup', '.quantity_print', function(e) {
        var current = $(e.currentTarget);
        var checkbox = $('[name="check_change_all"]');
        if (checkbox.is(':checked')) {
            current.parent().parent().parent().parent().find('.quantity_print').val(current.val());
        }
    });
    $(document).on('click', '.delete_input_field', function(e) {
        var current = $(e.currentTarget);
        current.parent().parent().remove();
    });

    $(document).on('click', '.option_barcode', function(e) {
        $('.content-print').html('');
        var arr_id = [];
        var rows = $('.DTFC_LeftBodyWrapper').find('.table-products').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                arr_id.push(checkbox.val());
            }
        });

        if (arr_id.length <= 0) {
            return;
        }

        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['arrID'] = arr_id;
        $.post(admin_url + 'products/getList_items', data).done(function(response) {
            var html = '';
            response = JSON.parse(response);
            $.each(response, function(k, v) {
                var stt = k + 1;
                html += '<tr>\
                            <td>' + stt + '</td>\
                            <td>' + v.name + '</td>\
                            <td class="td-input-field">\
                                <div class="input_infix">\
                                    <input type="number" name="item[' + k + '][quantity_print]" class="quantity_print H_input" value="1">\
                                    <input type="hidden" name="item[' + k + '][id_item]" class="id_item" value="' + v.id + '">\
                                </div>\
                                <div class="delete_input_field">\
                                    <i class="fa fa-times"></i>\
                                </div>\
                            </td>\
                        </tr>';
            });
            $('.content-print').append(html);
        });
    });

    function export_product_stage() {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['product_id'] = $('input[name="product_id[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        var url = "<?= base_url('admin/products/export_product_stage') ?>";
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            }).done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    download(data.filename, data.file);
                    $('.add').removeAttr('disabled', 'disabled');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'errors');
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }

    function export_product_stage_bom() {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['product_id'] = $('input[name="product_id[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        var url = "<?= base_url('admin/products/export_product_stage_bom') ?>";
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
            }).done(function(data) {
                if (data.result) {
                    alert_float('success', data.message);
                    download(data.filename, data.file);
                    $('.add').removeAttr('disabled', 'disabled');
                } else {
                    alert_float('danger', data.message);
                    $('.add').removeAttr('disabled', 'disabled');
                }
            })
            .fail(function() {
                alert_float('danger', 'errors');
                $('.add').removeAttr('disabled', 'disabled');
            });
        return false;
    }

    <?php if ($this->input->get('modal') == true) { ?>
        $(document).ready(function() {
            $('.active-modal')[0].click();
        });
    <?php } ?>
    //end

    function print_qr() {
        var data = {};
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
        data['product_id'] = $('input[name="product_id[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        var url = "<?= base_url('admin/products/print_qr') ?>";
        $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                targets: '_blank'
            }).done(function(data) {
                window.open('<?= base_url('admin/products/print_qr') ?>', '_blank');
            })
            .fail(function() {
                alert_float('danger', 'errors');;
                window.open('<?= base_url('admin/products/print_qr') ?>', '_blank');
            });
        return false;
    }

    function printQRItems() {
        var ids = '';
        var rows = $('.table-products').find('tbody tr');
        var grand_total_seller = 0;
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });

        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn thành phẩm cần in QR');
            return;
        }

        window.open(site.base_url + 'admin/products/print_qr?ids=' + ids, "_blank");
    }

    function exportExcel() {
        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/products/export_excel_products',
            data: {
                csrf_token_name: hash,
                export_excel: 1,
            },
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
<script type="text/javascript" src="<?= js('modal.js?vs=1.1') ?>"></script>
<script type="text/javascript">
    var json = {};
    var vProduct = 1;
    var STAGES_MATERIAL = <?= STAGES_MATERIAL ?>;
</script>
<script type="text/javascript" src="<?= js('design_bom.js?vs=3.7') ?>"></script>