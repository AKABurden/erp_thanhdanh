<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style type="text/css">
    .fixedHeader-floating {
        position: fixed !important;
    }

    .hide_btn_options .buttons-collection.btn-default-dt-options {
        display: block !important;
    }
</style>
<?php echo form_open(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.2') ?>">
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
                    <?php if ($this->perExportItems) : ?>
                        <li>
                            <!--                        <a href="--><? //= base_url('admin/items/export_excel_items') 
                                                                    ?><!--" class="tnh-modal" data-tnh="modal"-->
                            <!--                            data-toggle="modal" data-target="#myModal">-->
                            <!--                            <i class="fa fa-file-text-o"></i>-->
                            <!--                            --><?php //echo lang('tnh_export_excel'); 
                                                                ?>
                            <!--                        </a>-->
                        </li>
                    <?php endif ?>
                    <?php if ($this->perAddItems) : ?>
                        <li>
                            <a href="<?= base_url('admin/items/import_items') ?>" class="">
                                <i class="fa fa-upload"></i>
                                <?php echo lang('tnh_import_excel'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('admin/items/import_items_suppliers') ?>" class="">
                                <i class="fa fa-upload"></i>
                                <?php echo lang('tnh_import_excel_suppliers'); ?>
                            </a>
                        </li>
                    <?php endif ?>
                    <li>
                        <a class="test btn-search-tnh" data-toggle="collapse" data-target="#search-tnh" aria-expanded="true"><i class="fa fa-filter"></i> <?= lang('tnh_seach_statistical') ?></a>
                    </li>
                    <?php if ($this->perDeleteItems) : ?>
                        <li class="not-outside">
                            <?php echo '<a class="po" data-container="body" data-html="true" data-toggle="popover" data-placement="left" data-content="
                            <button href=\'' . base_url('admin/items/delete_material_multiple') . '\' class=\'btn btn-danger po-delete-multiple-json\'>' . lang('delete') . '</button>
                            <button class=\'btn btn-default po-close\'>' . lang('close') . '</button>
                            "><i class="fa fa-remove"></i> ' . lang('delete') . '</a>' ?>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
            <?php if ($this->perAddItems) : ?>
                <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/items/add_item') ?>" class="btn btn-info mright5 H_action_button tnh-modal active-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                        <?php echo _l('add'); ?>
                    </a>
                </div>
            <?php endif ?>
            <?php if ($this->perExportItems) : ?>
                <!-- <div class="pull-right mright5 H_border">
                    <a href="<?= base_url('admin/items/export_excel_items') ?>" class="btn btn-info mright5 H_action_button tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-file-text-o"></i>
                        <?php echo lang('tnh_export_excel'); ?>
                    </a>
                </div> -->
                <div class="pull-right mright5 H_border">
                    <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)">Xuất Excel</a>
                </div>
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
                        <?= lang('Nhóm NPL', 'category_search') ?>
                        <select name="category_search" id="category_search" data-placeholder="<?= lang('tnh_item_materials_category') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?= recursiveCategoryItems() ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <?= lang('materials', 'materials_search') ?>
                        <input type="text" name="materials_search" id="materials_search" style="width: 100%;" data-placeholder="<?= lang('materials') ?>" value="">
                    </div>
                    <div class="col-md-2">
                        <?= lang('tnh_species', 'species_search') ?>
                        <select name="species_search" id="species_search" data-placeholder="<?= lang('tnh_species') ?>" class="modal-select2" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($species)) : ?>
                                <?php foreach ($species as $key => $value) : ?>
                                    <option data-code="<?= $value['code'] ?>" value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
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
                        <div class="alert alert-danger alert-dismissible show-alert" style="display: none;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="right: 0;">&times;</a>
                            <div class="show-errors">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="hide_btn_options">
                            <table id="table-materials" class="table table-hover dataTable table-materials">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="materials"><label for="mass_select_all"></label>
                                            </div>
                                        </th>
                                        <th><?= lang('image') ?></th>
                                        <th><?= lang('Nhóm NPL') ?></th>
                                        <th><?= lang('tnh_material_code') ?></th>
                                        <th><?= lang('tnh_material_name') ?></th>
                                        <th><?= lang('tnh_species') ?></th>
                                        <th><?= lang('tnh_paper') ?></th>
                                        <th><?= lang('tnh_quantitative') ?></th>
                                        <th><?= lang('tnh_standard_unit') ?></th>
                                        <th><?= lang('Tồn kho') ?></th>
                                        <th><?= lang('tnh_price_import') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th><?= lang('tnh_single_use') ?></th>
                                        <th><?= lang('tnh_is_zinc') ?></th>
                                        <th><?= lang('note') ?></th>
                                        <th><?= lang('tnh_warehouses') ?></th>
                                        <th><?= lang('tnh_date_warehousing_nearest') ?></th>
                                        <th><?= lang('tnh_detail_quantity') ?></th>
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
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
    var lang_material =
        <?= json_encode(array('tnh_sequence' => lang('tnh_sequence'), 'tnh_stage' => lang('tnh_stage'), 'tnh_number_date' => lang('tnh_number_date'), 'tnh_number_date' => lang('tnh_number_date'))) ?>;
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        category_search: '#category_search',
        materials_search: '#materials_search',
        species_search: '#species_search',
        date_start_search: '#date_start_search',
        date_end_search: '#date_end_search',
    };
    var oTable = '';
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#category_search').select2({
            'allowClear': true
        });
        $('#species_search').select2({
            'allowClear': true
        });

        ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search')
            .val(), false, true);

        oTable = tnhDatatable(
            '#table-materials', {
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
                // scrollY: height_body,
                // scrollX: true,
                // fixedColumns: {
                //     leftColumns: 0,
                //     rightColumns: 1
                // },
                // dom: "<'row'><'row'<'col-md-7'lB><'col-md-5'f>>rt<'row'<'col-md-4'i>><'row'<'#colvis'><'.dt-page-jump'>p>",
                // stateSave: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/items/getMaterials') ?>',
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
                "columnDefs": [{
                        "render": function(data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" name="material_id[]" id="check-item' +
                                data + '" value="' + data + '"><label for="check-item' + data +
                                '"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '30px'
                    },
                    {
                        "targets": 1,
                        "name": 'images',
                        'width': '60px',
                        'searchable': false,
                        "render": function(data, type, row) {
                            images = (data != null) ? site.base_url + "uploads/materials/" + data +
                                '?' : site.base_url + "assets/images/tnh/no_image.png";
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
                        'width': '80px'
                    },
                    {
                        "render": function(data, type, row) {
                            data = data.split('__');
                            branch_name = data[1];
                            strBranch = '';
                            if (branch_name) {
                                strBranch = `<div style="font-style: italic;font-size: 12px">${branch_name}</div>`;
                            }
                            return '<div><a class="tnh-modal" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' +
                                site.base_url + 'admin/items/view_item/' + row[0] + '">' + data[0] +
                                '</a></div>' + strBranch;
                        },
                        "targets": 3,
                        "name": 'code',
                        'width': '100px'
                    },
                    {
                        "targets": 4,
                        "name": 'name',
                        'width': '180px'
                    },
                    {
                        "targets": 5,
                        "name": 'name_species',
                        'width': '100px'
                    },
                    {
                        "targets": 6,
                        "name": 'paper',
                        'width': '100px'
                    },
                    {
                        "targets": 7,
                        "name": 'quantitative',
                        'width': '100px'
                    },
                    {
                        "targets": 8,
                        "name": 'unit_name',
                        'width': '100px'
                    },
                    {
                        "render": function(data, type, row) {
                            detailQty = row[17];
                            return '<div class="text-center" style="color:red;font-weight: 500;font-size: 20px; cursor: pointer;" data-html="true" data-toggle="tooltip" data-title="' +
                                detailQty + '">' + tnhFormatNumber(data) + '</div>';
                        },
                        "targets": 9,
                        "name": 'quantity_inventory',
                        'width': '80px',
                        'searchable': false,
                    },
                    {
                        "targets": 10,
                        "name": 'price_import',
                        'width': '80px',
                        'visible': false,
                        "render": function(data, type, row) {
                            return '<div class="text-right">' + tnhFormatMoney(data) + '</div>';
                        }
                    },
                    {
                        "targets": 11,
                        "name": 'status',
                        'width': '80px',
                        "render": function(data, type, row) {

                            return `<div class="onoffswitch">
                            <input type="checkbox" data-switch-url="${site.base_url+'admin/items/change_status_material'}" name="onoffswitch" class="onoffswitch-checkbox" id="c_${row[0]}" data-id="${row[0]}" ${data == 1 ? 'checked' : ''}>
                            <label class="onoffswitch-label" for="c_${row[0]}"></label>
                        </div>`;
                        }
                    },
                    {
                        "targets": 12,
                        "name": 'is_single_use',
                        'width': '80px',
                        "render": function(data, type, row) {

                            return `<div class="onoffswitch">
                            <input type="checkbox" data-switch-url="${site.base_url+'admin/items/change_is_single_use'}" name="onoffswitch" class="onoffswitch-checkbox" id="c_single_use_${row[0]}" data-id="${row[0]}" ${data == 1 ? 'checked' : ''}>
                            <label class="onoffswitch-label" for="c_single_use_${row[0]}"></label>
                        </div>`;
                        }
                    },
                    {
                        "targets": 13,
                        "name": 'is_zinc',
                        'width': '80px',
                        "render": function(data, type, row) {

                            return `<div class="onoffswitch">
                            <input type="checkbox" data-switch-url="${site.base_url+'admin/items/change_is_zinc'}" name="onoffswitch" class="onoffswitch-checkbox" id="c_is_zinc_${row[0]}" data-id="${row[0]}" ${data == 1 ? 'checked' : ''}>
                            <label class="onoffswitch-label" for="c_is_zinc_${row[0]}"></label>
                        </div>`;
                        }
                    },
                    {
                        "targets": 14,
                        "name": 'note',
                        'width': '100px'
                    },
                    {
                        "targets": 15,
                        "name": 'warehouses',
                        'width': '150px',
                        'visible': false,
                        'searchable': false,
                    },
                    {
                        "render": function(data, type, row) {
                            return fsd(data);
                        },
                        "targets": 16,
                        "name": 'date_warehousing_nearest',
                        'width': '150px',
                        'visible': false,
                        'searchable': false,
                    },
                    {
                        "targets": 17,
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
                        'width': '160px'
                    }
                ]
            }
        );

        // filterCustom('#table-materials thead', oTable, [
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(2)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(3)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(4)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(5)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(6)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(7)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(8)', type: "text", data: []},
        //           {element: '#table-materials thead tr:eq(1) th:nth-child(9)', type: "text", data: []},
        //       ]);

        $(document).on('click', '.btn-dt-reload', function(event) {
            oTable.draw();
        });

        $(document).on('change', '#category_search, #materials_search, #species_search, #date_start_search, #date_end_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        loadAjax();

    });

    function printQRItems() {
        var ids = '';
        var rows = $('.table-materials').find('tbody tr');
        var grand_total_seller = 0;
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                ids += checkbox.val() + ',';
            }
        });

        ids = ids.slice(0, -1);

        if (!ids) {
            bootbox.alert('Xin vui lòng chọn nguyên vật liệu cần in QR');
            return;
        }

        window.open(site.base_url + 'admin/items/print_qr?ids=' + ids, "_blank");
    }
    <?php if ($this->input->get('modal') == true) { ?>
        $(document).ready(function() {
            $('.active-modal')[0].click();
        });
    <?php } ?>

    function exportExcel() {
        // groups_ch = $('[name="groups_ch"]').val();
        var listDataId = [];
        var rows = $('.table-materials').find('tbody tr');
        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') == true) {
                listDataId.push(checkbox.val());
            }
        });

        $.ajax({
            type: "POST",
            url: site.base_url + 'admin/items/exportExcelItems',
            data: {
                csrf_token_name: hash,
                // groups_ch: groups_ch,
                listDataId: listDataId,
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