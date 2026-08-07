<style>
    #tb-reports tr th:nth-child(1) {
        width: 40px !important;
        max-width: 40px !important;
    }

    #tb-reports tr th:nth-child(2) {
        width: 50px !important;
        max-width: 50px !important;
    }

    #tb-reports tr th:nth-child(3) {
        width: 130px !important;
    }

    #tb-reports tr th:nth-child(4) {
        width: 200px !important;
    }

    #tb-reports tr th:nth-child(5) {
        width: 80px !important;
    }

    #tb-reports tr th:nth-child(6) {
        width: 170px !important;
    }

    #tb-reports tr th:nth-child(7) {
        width: 100px !important;
    }

    #tb-reports tr th:nth-child(8) {
        width: 100px !important;
    }
</style>
<?php
    $stages = $this->site_model->getStages();
    $nStages = count($stages) - 1;
?>
<div class="text-center uppercase">
    <h2><?= lang('report_productions_orders_detail') ?></h2>
</div>
<hr>
<div class="row mbot10">
    <div class="col-md-4 hide">
        <?= lang('stages', 'stages_search') ?>
        <select name="stages_search" id="stages_search" onchange="loadTableReport()" style="width: 100%;" class="" style="width: 100%;" data-placeholder="<?= lang('stages') ?>" required="required">
            <option value=""></option>
            <?php if(!empty($stages)): ?>
                <?php foreach($stages as $key => $value): ?>
                    <option <?= ($nStages == $key ? 'selected' : '') ?> value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-md-4">
        <?= lang('start_date', 'start_date_search') ?>
        <input type="text" name="start_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('start_date') ?>" id="start_date_search" class="start_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
    <div class="col-md-4">
        <?= lang('end_date', 'end_date_search') ?>
        <input type="text" name="end_date_search" onchange="loadTableReport()" autocomplete="off" placeholder="<?= lang('end_date') ?>" id="end_date_search" class="end_date_search datepicker form-control" style="width: 100%;" value="">
    </div>
</div>
<div class="table-responsive">
    <table id="tb-reports" class="table" style="width: 100%;">
        <thead>
            <tr>
                <th class="text-center"><?= lang('id') ?></th>
                <th class="text-center"><?= lang('tnh_images') ?></th>
                <th class="text-center"><?= lang('tnh_product_code') ?></th>
                <th class="text-center"><?= lang('tnh_product_name') ?></th>
                <th class="text-center"><?= lang('tnh_time_average') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var fnserverparams = {
        'stages_search': '#stages_search',
        'start_date_search': '#start_date_search',
        'end_date_search': '#end_date_search',
    };
    var oTable = '';

    function loadTableReport() {
        oTable.draw();
    };

    $(document).ready(function () {
        init_datepicker();
        $('#stages_search').select2({'allowClear': true});
        ajaxSelectParams('#orders_and_business_plan', 'admin/manufactures/searchOrdersAndBusinessPlan', 0, true, true);
        ajaxSelectParamsCallback('#items_search', 'admin/products/searchProductsSelect2', 0, false, true);
        oTable = tnhInitDataTable('#tb-reports', '<?= site_url('admin/reports_tnh/getProductionsOrdersDetail') ?>', {
            'order': [
                [2, 'desc']
            ],
            'fixedHeader': {
                header: true,
            },
            'responsive': true,
            "ajax": {
                "url": '<?= site_url('admin/reports_tnh/getProductionsProducts') ?>',
                "type": "POST",
                "data": function(d) {
                    if (typeof(csrfData) !== 'undefined') {
                        d[csrfData['token_name']] = csrfData['hash'];
                    }
                    for (var key in fnserverparams) {
                        d[key] = $(fnserverparams[key]).val();
                    }
                    if (table.attr('data-last-order-identifier')) {
                        d['last_order_identifier'] = table.attr('data-last-order-identifier');
                    }
                },
                "dataSrc": function(json) {
                    return json.aaData;
                }
            },
            "columnDefs": [
                {
                    "targets": 0,
                    "name": 'id',
                    'width': '45px',
                    'className': 'text-center',
                    'visible': false
                },
                {
                    "targets": 1,
                    'width': '45px',
                    'className': 'text-center',
                    'visible': false
                },
            ]
        });
    });
</script>