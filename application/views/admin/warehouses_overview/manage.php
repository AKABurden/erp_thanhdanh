<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style type="text/css">
    .table-warehouses_overview_deal_nvl tbody tr td {
        vertical-align: middle;
    }
</style>

<div id="wrapper">
    <div class="panel_s mbot10 H_scroll" id="H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
            <a onclick="exportExcel()" class="btn btn-info H_action_button pull-right" href="javascript:void(0)"><i class="fa fa-file-excel-o"></i> Xuất Excel</a>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <p class="bold"><?php echo _l('filter_by'); ?></p>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="warehouse_id" class="control-label"><?php echo _l('warehouse'); ?></label>
                                <select class="selectpicker" multiple data-width="100%" data-actions-box="true" data-live-search="true" id="warehouse_id" name="warehouse_id[]" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach ($warehouses as $wh) { ?>
                                        <option value="<?= $wh['id'] ?>"><?= $wh['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="type_items" class="control-label">Loại hàng</label>
                                <select class="selectpicker" data-width="100%" id="type_items" name="type_items">
                                    <option value="">-- Tất cả --</option>
                                    <option value="nvl" selected>Nguyên phụ liệu</option>
                                    <option value="product">Thành phẩm</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 select_custom_item_select">
                            <div class="form-group">
                                <label for="custom_item_select" class="control-label"><?php echo _l('item_name'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="custom_item_select" name="custom_item_select" class="custom_item_select" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-md-2 select_custom_item_select">
                            <div class="form-group">
                                <label for="category_id" class="control-label"><?php echo _l('tnh_category_id'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="category_id" name="category_id" class="category_id" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-md-2 select_warehouse_localtion">
                            <div class="form-group">
                                <label for="warehouse_localtion" class="control-label"><?php echo _l('warehouse_localtion'); ?></label>
                                <input data-placeholder="<?= _l('dropdown_non_selected_tex') ?>" id="warehouse_localtion" name="warehouse_localtion" class="warehouse_localtion" type-id="" data-id="" style="width: 100%">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <?= render_input('lot_code', 'Lot') ?>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="table-responsive">
                            <?php render_datatable_tfoot_ch(array(
                                _l('warehouse'),
                                _l('item_code'),
                                _l('item_name'),
                                _l('tnh_dvt'),
                                _l('Lot'),
                                _l('ch_items_specification'),
                                _l('warehouse_localtion'),
                                _l('quantity_detal'),
                                _l('ch_date_of_manufacture'),
                                _l('ch_items_dateed'),
                                _l('ch_items_date_use'),
                                _l('ch_price_warehouse'),
                            ), 'warehouses_overview_deal_nvl'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    var OverviewServerParams = {
        'warehouse_id': '[name="warehouse_id[]"]',
        'type_items': '[name="type_items"]',
        'category_id': '[name="category_id"]',
        'custom_item_select': '[name="custom_item_select"]',
        'localtion': '[name="warehouse_localtion"]',
        'lot_code': '[name="lot_code"]',
    };

    var tAPIOverview;

    $(function() {
        ajaxSelectCallBack($('#custom_item_select'), "<?= admin_url('purchases/SearchItems') ?>", 0);
        ajaxSelectCallBack($('#category_id'), "<?= admin_url('warehouse/SearchCategory') ?>", 0);
        ajaxSelectCallBack($('#warehouse_localtion'), "<?= admin_url('warehouse/SearchLocaltion') ?>", 0);

        function ajaxSelectCallBack(element, url, id, types = '') {
            $(element).select2({
                width: 'resolve',
                allowClear: true,
                ajax: {
                    url: url + '/' + $(element).val(),
                    dataType: 'json',
                    quietMillis: 15,
                    data: function(term, page) {
                        var selectedType = $('#type_items').val();
                        return {
                            type: selectedType ? selectedType : 'nvl',
                            types: types,
                            term: term,
                            limit: 50
                        };
                    },
                    results: function(data, page) {
                        if (data.results != null) {
                            return { results: data.results };
                        } else {
                            return { results: [{ id: '', text: 'No Match Found' }] };
                        }
                    }
                },
                formatResult: repoFormatSelection,
                formatSelection: repoFormatSelection,
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) { return m; }
            });
        }

        function repoFormatSelection(state) {
            if (!state.id) return state.text;
            if (state.code == undefined) {
                return state.text;
            }
            return state.text + ' - (' + state.code + ')';
        }

        tAPIOverview = initDataTable('.table-warehouses_overview_deal_nvl', admin_url + 'warehouses_overview/table_warehouses_overview_nvl', [0], [0], OverviewServerParams, [0, 'asc']);
    });

    $('#type_items').on('change', function() {
        $('#custom_item_select').select2('val', '');
        $('#category_id').select2('val', '');
    });

    $.each(OverviewServerParams, function(filterIndex, filterItem) {
        $('' + filterItem).on('change', function() {
            if ($('.table-warehouses_overview_deal_nvl').hasClass('dataTable')) {
                tAPIOverview.draw('page');
            }
        });
    });

    $('.table-warehouses_overview_deal_nvl').on('draw.dt', function() {
        var dt = $(this).DataTable();
        var json = dt.ajax.json();
        var sums = json ? json.sums : null;
        if (sums) {
            $(this).find('tfoot').addClass('bold');
            $(this).find('tfoot td').eq(0).html("<?php echo _l('Tổng cộng'); ?>");
            $(this).find('tfoot td').eq(7).html('<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' + sums.slt + '</div>');
            $(this).find('tfoot td').eq(11).html('<div class="text-right" style="font-size:16px;font-weight: bold">' + sums.gtt + '</div>');
        }
    });

    function exportExcel() {
        var warehouse_id = $('#warehouse_id').val();
        var type_items = $('#type_items').val();
        var category_id = $('#category_id').val();
        var custom_item_select = $('#custom_item_select').val();
        var localtion = $('#warehouse_localtion').val();
        var lot_code = $('#lot_code').val();

        var params = [];
        if (warehouse_id) {
            if ($.isArray(warehouse_id)) {
                $.each(warehouse_id, function(i, val) {
                    params.push('warehouse_id[]=' + encodeURIComponent(val));
                });
            } else {
                params.push('warehouse_id=' + encodeURIComponent(warehouse_id));
            }
        }
        if (type_items) params.push('type_items=' + encodeURIComponent(type_items));
        if (category_id) params.push('category_id=' + encodeURIComponent(category_id));
        if (custom_item_select) params.push('custom_item_select=' + encodeURIComponent(custom_item_select));
        if (localtion) params.push('localtion=' + encodeURIComponent(localtion));
        if (lot_code) params.push('lot_code=' + encodeURIComponent(lot_code));

        var url = admin_url + 'warehouses_overview/export_excel?' + params.join('&');
        window.open(url, '_blank');
    }
</script>
</body>
</html>
