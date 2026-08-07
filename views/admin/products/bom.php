<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<style>
    .table-loading table thead th {
        opacity: 1 !important;
    }

    .dt-table-loading.table, .table-loading .dataTables_filter, .table-loading .dataTables_length, .table-loading .dt-buttons, .table-loading table tbody tr, .table-loading table thead th {
        opacity: 1 !important;
    }
</style>
<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
    <!-- <div> -->
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?=$title?></span>
            <?php if ($this->perPrintProductsBom): ?>
                <a href="<?= base_url('admin/products/print_bom') ?>" class="btn btn-info pull-right H_action_button tnh-modal-bom" data-tnh="modal" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-print" aria-hidden="true"></i>
                    <?php echo _l('tnh_print_bom'); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <?= lang('materials', 'materials_search') ?>
                        <input type="text" name="materials_search" id="materials_search" style="width: 100%;"
                                data-placeholder="<?= lang('materials') ?>" value="">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="table-bom" class="table dt-tnh table-hover table-bordered table-condensed table-bom" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><div class="checkbox mass_select_all_wrap text-center"><input type="checkbox" id="mass_select_all" data-to-table="bom"><label for="mass_select_all"></label></div></th>
                                        <th></th>
                                        <th><?= lang('tnh_product_code') ?></th>
                                        <th><?= lang('tnh_product_name') ?></th>
                                        <th><?= lang('tnh_versions_bom') ?></th>
                                        <th><?= lang('unit') ?></th>
                                        <th><?= lang('tnh_quantity_productions') ?></th>
                                        <th><?= lang('vs') ?></th>
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
<?php init_tail(); ?>

<!-- <?php $this->load->view('loader')?> -->
<!-- <script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script> -->
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var fnserverparams = {
        materials_search: '#materials_search'
    };
    var oTable = '';
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.selectpicker').selectpicker();

        ajaxSelectParamsCallback('#materials_search', 'admin/items/searchSelect2Materials', $('#materials_search')
        .val(), false, true);

        oTable = tnhDatatable(
            '#table-bom',
            {
                'order': [[2, 'asc']],
                'orderCellsTop': true,
                "language": app.lang.datatables,
                "pageLength": app.options.tables_pagination_limit,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "processing": true,
                // 'fixedHeader': {
                //     header: true,
                //     footer: true
                // },
                scrollY: height_body,
                scrollX: true,
                // scrollCollapse: true,
                // fixedColumns:   {
                //     leftColumns: 1,
                //     rightColumns: 4
                // },
                // autoWidth: true,
                "serverSide": true,
                'sAjaxSource': '<?= site_url('admin/products/getBom') ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
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
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "drawCallback": function( settings ) {
                    $('.vs').selectpicker();
                },
                "initComplete": function(settings, json) {
                    var t = this;
                    t.parents('.table-loading').removeClass('table-loading');
                    t.removeClass('dt-table-loading');
                    mainWrapperHeightFix();
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return '<div class="checkbox"><input type="checkbox" id="check-item'+data+'" class="check-item" value="'+ data +'"><label for="check-item'+data+'"></label></div>';
                        },
                        "targets": 0,
                        "name": 'id',
                        'orderable': false,
                        'width': '50px'
                    },
                    {
                        "render": function (data, type, row) {
                            return '';
                        },
                        "className": 'details-control',
                        "targets": 1,
                        "name": 'records',
                        'orderable': false,
                        'width': '5px'
                    },
                    {
                        "render": function (data, type, row) {
                            return data+'<div class="mtop5"><a href="javascript:void(0)" onclick="editBOM(this)">Sửa BOM</a></div>';
                        },
                        "targets": 2, "name": "code"
                    },
                    {"targets": 3, "name": "name"},
                    {
                        "render": function (data, type, row) {
                            sl = '';
                            if (data) {
                                data = data.split(':::');
                                sl += '<select name="vs" class="form-control vs" id="vs" data-live-search="true" data-none-selected-text="<?= lang('choose') ?>"><option value=""></option>';
                                $.each(data, function(index, el) {
                                    arrEl = el ? el.split('___') : '';
                                    if (arrEl) {
                                        el = arrEl[0];
                                        id_versions = arrEl[1];
                                        selected = row[7] == el ? 'selected' : '';
                                        sl += '<option '+selected+' data-id="'+id_versions+'" value="'+el+'">'+el+'</option>';
                                    } 

                                });
                                sl += '</select>';
                            }
                            return sl;
                            // data = data ? data : '';
                            // return data+'<input type="hidden" name="vs" id="vs" class="form-control vs" value="'+row[7]+'">';
                        },
                        "targets": 4, "name": "bm"
                    },
                    {"targets": 5, "name": "unit_name"},
                    {
                        "render": function (data, type, row) {
                            return data;
                            // return  '<div class="view-quantity" style="cursor: pointer;" data-toggle="tooltip" data-placement="left" title="Click 2 lần để sửa">'+data+'</div>'+
                            //         '<div class="input-number" style="display: none;">'+
                            //             '<input type="number" name="number_production" id="number_production" class="form-control number_production" value="'+data+'">'+
                            //             '<button class="btn btn-primary btn-edit" style="position: absolute; right: 15px; padding: 5px 15px 10px 15px;"><i class="fa fa-pencil"></i></button>'+
                            //         '</div>';
                        },
                        'className': 'text-center th-quantity',
                        "targets": 6, "name": "quantity_productions",
                        'visible': false
                    },
                    {"targets": 7, "name": "vs", 'visible': false},
                ]
            }
        );

        $(document).on('change', '#category_search, #materials_search', function(event) {
            event.preventDefault();
            oTable.draw();
        });

        $(document).on('click', '.btn-dt-reload', function(event) {
            // oTable.draw();
        });

        function ajaxShowBom(product_id, vs, quantity, row, tr) {
            $.ajax({
                url: site.base_url+'admin/products/show_bom',
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>',
                    product_id: product_id,
                    vs: vs,
                    quantity: quantity
                },
            })
            .done(function(data) {
                row.child(data).show();
                tr.addClass('shown');
            })
            .fail(function() {
                console.log("error");
            });
        }

        $('#table-bom tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = oTable.row( tr );
            product_id = tr.find('.check-item').val();
            vs = tr.find('select.vs').val();
            quantity = tr.find('.number_production').val();
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                ajaxShowBom(product_id, vs, quantity, row, tr);
            }
        });

        $(document).on('dblclick', '.th-quantity', function () {
            var tr_click = $(this).closest('tr');
            tr_click.find('.view-quantity').hide();
            tr_click.find('.input-number').show();
        });

        $(document).on('click', '.btn-edit', function () {
            var tr_click = $(this).closest('tr');
            var row = oTable.row( tr_click );
            quantity_lick = tr_click.find('.number_production').val();
            tr_click.find('.view-quantity').show();
            tr_click.find('.input-number').hide();
            tr_click.find('.view-quantity').html(quantity_lick);

            product_id = tr_click.find('.check-item').val();
            vs = tr_click.find('select.vs').val();
            quantity = tr_click.find('.number_production').val();
            ajaxShowBom(product_id, vs, quantity, row, tr_click);
        })

        $(document).on('change', 'select.vs', function(event) {
            event.preventDefault();
            var tr_click = $(this).closest('tr');
            var row = oTable.row( tr_click );
            quantity_lick = tr_click.find('.number_production').val();
            tr_click.find('.view-quantity').show();
            tr_click.find('.input-number').hide();
            tr_click.find('.view-quantity').html(quantity_lick);

            product_id = tr_click.find('.check-item').val();
            vs = tr_click.find('select.vs').val();
            quantity = tr_click.find('.number_production').val();
            ajaxShowBom(product_id, vs, quantity, row, tr_click);
        });

        $(document).on('click', '.tnh-modal-bom', function(event) {
            event.preventDefault();
            this.blur();
            link = this.href;
            arr_id = [];
            arr_vs = [];
            arr_quantity = [];
            for (i = 0; i < $('.check-item').length; i++) {
                ck = $('.check-item')[i];
                if ($(ck).is(":checked")) {
                    tr_ = $(ck).closest('tr');
                    product_id = $(ck).val();
                    vs = tr_.find('select.vs').val();
                    if (!vs) continue;
                    quantity = tr_.find('.number_production').val();
                    arr_id.push(product_id);
                    arr_vs.push(vs);
                    arr_quantity.push(quantity);
                }
            }
            $.ajax({
                url: link,
                type: 'POST',
                dataType: 'html',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash() ?>',
                    arr_id: arr_id,
                    arr_vs: arr_vs,
                    arr_quantity: arr_quantity,
                },
            })
            .done(function(data) {
                $('#tnhModal').html(data);
            })
            .fail(function() {
                console.log("error");
            });
            $('#tnhModal').modal('show');
        });
    });
</script>
<script type="text/javascript">
    var json = {};
    var vProduct = 1;
    var STAGES_MATERIAL = <?= STAGES_MATERIAL ?>;
</script>
<script type="text/javascript" src="<?= js('design_bom.js?vs=3.6') ?>"></script>

<script>
    function editBOM(_this) {
        var tr = $(_this).closest('tr');
        product_id = tr.find('.check-item').val();
        vs_id = $('option:selected', tr.find('select.vs')).attr("data-id");
        $.ajax({
            url: site.base_url+'admin/products/design_bom/'+product_id+'/'+vs_id+'/edit',
            type: 'GET',
            dataType: 'html',
            data: {
                token: hash
            },
        })
        .done(function(data) {
            $('#tnhModal').html(data);
        })
        .fail(function() {
            console.log("error");
        });
        $('#tnhModal').modal({backdrop: 'static', keyboard: true});
    }
</script>
