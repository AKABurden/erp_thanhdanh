<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style class="">
    table tr td {
        vertical-align: middle !important;
    }

    .progressbar_img li.active>img {
        color: green;
        border: 1px solid green;
    }

    .active_poin {
        font-size: smaller !important;
        font-style: italic !important;
        color: #da8227 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('ctimeline.css') ?>">
<?php echo form_open(); ?>
<div id="wrapper" class="tnh-height">
    <div class="panel_s mbot10 H_scroll">
        <div class="panel-body _buttons">
            <span class="bold uppercase fsize18 H_title"><?= $title ?></span>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-2">
                <?= lang('year', 'year') ?>
                <select name="year" id="year" data-placeholder="<?= lang('year') ?>" style="width: 100%;">
                    <?php
                    $data = date('Y');
                    for ($i = $data - 5; $i <= $data + 5; $i++) {
                    ?>
                        <option value="<?= $i ?>" <?= ($i == $data) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="show-table-price-list">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>

<?php $this->load->view('loader') ?>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript">
    var token = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    function getPriceList() {
        var dataPOST = {};
        dataPOST[token] = hash;
        year = $('#year').val(); 
        dataPOST['year'] = year;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/price_list/getPriceList',
            data: dataPOST,
            dataType: "html",
            success: function (response) {
                $('.show-table-price-list').html(response);
            }
        });
    }

    function changePriceList(_this, category_products_id , customers_groups_id) {
        var dataPOST = {};
        price = intVal($(_this).val());
        year = $('#year').val(); 

        dataPOST[token] = hash;
        dataPOST['year'] = year;
        dataPOST['category_products_id'] = category_products_id;
        dataPOST['customers_groups_id'] = customers_groups_id;
        dataPOST['price'] = price;

        $.ajax({
            type: "POST",
            url: site.base_url+'admin/price_list/changePriceList',
            data: dataPOST,
            dataType: "json",
            success: function (response) {
                if (response.result) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
            }
        });
    }

    $(document).ready(function() {
        $('#year').select2();
        getPriceList();

        $('#year').change(function (e) { 
            e.preventDefault();
            getPriceList();
        });
    });
</script>