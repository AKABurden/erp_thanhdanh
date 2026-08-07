<style>
    #tb-reports tr th:nth-child(1) {
        width: 40px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 80px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 200px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 200px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(6) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(7) {
        width: 70px !important;
    }

    #tb-reports tr th:nth-child(8) {
        width: 200px !important;
    }
</style>
<?php
$stages = $this->site_model->getStages();
$nStages = count($stages) - 1;
?>
<div class="text-center uppercase">
    <h2><?= lang('report_complete_stage') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-3">
        <?= lang('tnh_reference_productions_orders') ?>
        <input type="text" name="productions_orders_search" onchange="loadTableReport()" data-placeholder="<?= lang('tnh_reference_productions_orders') ?>" id="productions_orders_search" class="productions_orders_search" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('items') ?>
        <input type="text" name="products" id="products" onchange="loadTableReport()" style="width: 100%;" data-placeholder="<?= lang('cong_item_code') ?>" value="">
    </div>
    <div class="col-md-3">
        <?php $stage = get_table_where('tbl_stages', ['id !=' => STAGES_MATERIAL]); ?>
        <?= lang('Công đoạn sản phẩm') ?>
        <select name="type_stage[]" multiple="1" id="type_stage" onchange="loadTableReport()" data-placeholder="<?= lang('Công đoạn sản phẩm') ?>" data-live-search="true" data-actions-box="1" class="selectpicker no-margin" style="width: 100%;">
            <?php if (!empty($stage)) : ?>
                <?php foreach ($stage as $key => $value) : ?>
                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-md-3">
        <?php $arrStatus = [
            [
                'id' => 0,
                'name' => 'Đang sản xuất'
            ],
            [
                'id' => 1,
                'name' => 'Hoàn thành'
            ],
        ] ?>
        <?= lang('Trạng thái') ?>
        <select name="status_search" id="status_search" onchange="loadTableReport()" data-none-selected-text="<?= lang('tnh_status') ?>" data-live-search="true" class="selectpicker" style="width: 100%;">
            <option value=""></option>
            <?php if (!empty($arrStatus)) : ?>
                <?php foreach ($arrStatus as $key => $value) : ?>
                    <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="col-md-3">
        <?= lang('start_date') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-3">
        <?= lang('end_date') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('STT') ?></th>
                <th class="text-center"><?= lang('Ngày lập lệnh') ?></th>
                <th class="text-center"><?= lang('Mã lệnh tổng') ?></th>
                <th class="text-center"><?= lang('Mã sản phẩm') ?></th>
                <th class="text-center"><?= lang('Tên sản phẩm') ?></th>
                <th class="text-center"><?= lang('Ngày dự kiến giao hàng') ?></th>
                <th class="text-center"><?= lang('SL sản xuất') ?></th>
                <th class="text-center"><?= lang('SL tờ in') ?></th>
                <th class="text-center"><?= lang('Trạng thái') ?></th>
                <th class="text-center"><?= lang('Công đoạn') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
        "products": "#products",
        "productions_orders_search": "#productions_orders_search",
        type_stage: '#type_stage',
        status_search: '#status_search',
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function() {
        init_selectpicker();
        init_datepicker();
        $('#stages_search').select2({
            'allowClear': true
        });
        ajaxSelectMultipleParams($('#products'), 'admin/products/searchProductsSelect2', 0, false, true);
        ajaxSelectMultipleParams($('#productions_orders_search'), 'admin/manufactures/searchProductionsOrders', 0, false, true);

        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_productions/tableComplete_stage') ?>', {
            'order': [
                [1, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_productions/tableComplete_stage') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();

                        //custom
                        if ($(fnserverparams[key]).data('select2') && $(fnserverparams[key]).val()) {
                            var array_data = $(fnserverparams[key]).select2('data');
                            if (array_data) {
                                array_data.forEach((item, index) => {
                                    if (item.id !== undefined) {
                                        d[key+'_text_'+index] = item.text;
                                    } else {
                                        d[key+'_text'] = $(fnserverparams[key]).select2('data').text;
                                    }
                                });
                            }
                        } else if ($(fnserverparams[key]).hasClass('selectpicker')) {
                            var selectedText = $(fnserverparams[key]).find('option:selected').text();
                            if (selectedText) {
                                d[key+'_text'] = selectedText;
                            }
                        }
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [{
                "targets": 0,
                "name": 'id',
                'width': '45px',
                'className': 'text-center',
                'visible': false
            }, ],
            "btnButtons": 1
        });
    });
</script>