<?php //print_arrays(get_productions_orders());
?>
<style type="text/css">
    .menu-mobile-dashboard {
        display: none;
    }

    .title-dashboard {
        display: none;
    }

    @media (min-width: 769px) {
        .menu_v2 {
            margin-bottom: 10px;
            width: 100% !important;
            border-right: 0 !important;
            border-left: 0 !important;
        }

        .app-menu-group {
            width: 25% !important;
        }

        .content-menu-v2 {
            position: relative;
            padding-bottom: 10px;
            z-index: 9 !important;
        }

        .app-menu-group:hover:before {
            box-shadow: unset !important;
        }

        .app-menu-group:before,
        .app-menu-group:after {
            content: "";
            position: unset !important;
            transition: unset !important;
        }

        .app-menu-group:hover {
            border: 0 !important;
            border-right: 1px solid #ddd !important;
            ;
        }

        .header-timers {
            pointer-events: none;
        }

        .app-menu-group:not(:last-child) {
            border-left: 0 !important;
        }

        .line-menu {
            margin-left: calc(100% - 150px) !important;
        }
    }

    @media (max-width: 768px) {
        body {
            background: #fff;
        }

        #wrapper {
            display: none;
        }

        #top_search_button {
            display: none;
        }

        .menu-mobile-dashboard {
            display: block;
        }

        .title-dashboard {
            display: none;
        }

        .wrap-img-mobile-dashboard img {
            width: 25px;
        }

        .wrap-img-mobile-dashboard {
            float: left;
        }

        .wap-off-mobile-dashboard {
            padding: 10px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            background: linear-gradient(to right, #6322aa 0%, #226ca9 37%, #3b8293 100%);
            margin: 10px;
        }

        .app-menu-item-mobile-dashboard {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .app-menu-item-mobile-dashboard span {
            color: #fff;
            margin-left: 10px;
        }
    }

    .table-birthday-staff thead {
        background: #267ec5;
    }

    .table-birthday-staff thead tr th {
        color: #fff;
    }

    .table-birthday-staff tbody tr {
        background: #badbeb26;
    }
</style>
<style>
    .dashboard_statistic .row-statistic_card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 16px 15px;
        gap: 12px;
        width: 200px;
        height: 108px;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_1 {
        background: #EBF5FF;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_2 {
        background: #F3F4F6;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_3 {
        background: #EBFEF2;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_4 {
        background: #FEFAEC;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_5 {
        background: #FFEEF0;
        border-radius: 10px;
    }

    .dashboard_statistic .statistic_6 {
        background: #FFEDDD;
        border-radius: 10px;
    }

    .dashboard_statistic .row-statistic {
        min-height: 80px;
        background-color: white;
        margin-top: 10px;
        height: auto;
        border: 1px solid #DDDDE2;
        border-radius: 8px;
    }

    .title-statisti_card {
        height: 16px;
        font-style: normal;
        font-weight: 500;
        font-size: 12px;
        line-height: 16px;
        letter-spacing: 1px;
        text-transform: uppercase;

        color: #371B01;
    }

    .title-statistic {
        padding: 10px;
        font-size: 14px;
        color: #525151;
    }

    .content-statistic {
        text-align: center;
        margin-top: 10px;
    }

    .row-statistic-success {
        color: rgb(63, 134, 0);
        font-size: 16px;
        text-align: left;
    }

    .row-statistic-danger {
        color: rgb(255 19 19);
        font-size: 16px;
        text-align: left;
    }

    .title-child-statistic {
        color: #858585;
    }

    .content-bashboard {
        padding-left: 0px !important;
        padding-right: 5px !important;
        padding-top: 5px !important;
    }

    .chartjs-content {
        padding: 20px;
    }

    .small-success {
        color: green;
        font-size: 10px;
    }

    .small-danger {
        color: red;
        font-size: 10px;
    }

    @media screen and (min-width: 992px) {
        .row-data {
            padding-left: 0px !important;
        }
    }


    table.scroll tbody {
        display: block;
        height: 230px;
        overflow: auto;
    }

    table.scroll thead,
    table.scroll tbody tr {
        display: table;
        table-layout: fixed;
        /* even columns width , fix width of table too*/
        width: 100%;
    }

    .row350 {
        height: 350px !important;
    }

    .statistic_1 .crad-number {
        background: #1760B9;
    }

    .statistic_2 .crad-number {
        background: #9295A4;
    }

    .statistic_3 .crad-number {
        background: #0BAA2E;
    }

    .statistic_4 .crad-number {
        background: #FF8F0D;
    }

    .statistic_5 .crad-number {
        background: #EE1E1E;
    }

    .statistic_6 .crad-number {
        background: #542901;
    }

    .crad-number {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        padding: 10px 16px;
        gap: 10px;
        width: 62px;
        height: 48px;
        border-radius: 8px;
    }

    .title-number {
        width: 30px;
        height: 28px;
        font-style: normal;
        font-weight: 800;
        font-size: 16px;
        line-height: 28px;
        display: flex;
        align-items: flex-end;
        letter-spacing: 0.01em;
        color: #FFFFFF;
    }

    .crad-up {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 0px;
        gap: 4px;
        width: 54px;
        height: 20px;
    }

    .row-statistic-child {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-end;
        padding: 0px;
        gap: 55px;
        width: 150px;
        height: 48px;
    }
</style>
<div class="css_slide_mobile"></div>
<?php
$CI = &get_instance();
$aside_menu_active = json_decode(get_option('aside_menu_active'));
$list_title = array();
if (!empty($aside_menu_active)) {
    foreach ($aside_menu_active as $key => $value) {
        if (!empty($value->type)) {
            $value->object = $key;
            $list_title[1][] = $value;
        }
    }
}
$date_end_after = date('Y-m-d');
$date_start_after = strtotime('-6 month', strtotime($date_end_after));
$date_start_after = date('Y-m-d', $date_start_after);
$date_end_before = $date_start_after;
$date_end_before = strtotime('-1 day', strtotime($date_end_before));
$date_end_before = date('Y-m-d', $date_end_before);
$date_start_before = strtotime('-6 month', strtotime($date_end_before));
$date_start_before = date('Y-m-d', $date_start_before);
// $data_chart_manufactures = get_product_production_top($date_start_after, $date_end_after);
// $data_chart_productions_orders = get_productions_orders($date_start_after, $date_end_after);
?>
<div style="margin-top: 35px;"></div>
</hr>
<div class="wap-container-menu">
    <?php
    $_data_status_after = getNumberStatusProduction($date_start_after, $date_end_after);
    $_data_status_before = getNumberStatusProduction($date_start_before, $date_end_before);
    ?>
    <div class="content content-bashboard">
        <div class="">
            <?php $this->load->view('admin/includes/alerts'); ?>
            <div>
                <div class="dashboard_statistic mbot20">
                    <div class="col-md-2">
                        <div class="row-statistic_card statistic_1">
                            <div class="title-statisti_card">Đang thực hiện</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusProcessing'] >= $_data_status_before['statusProcessing'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusProcessing'] >= $_data_status_before['statusProcessing'] ? '+' : ''; ?>
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusProcessing']) ? formatNumber($_data_status_after['statusProcessing']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= ($_data_status_after['statusProcessing'] - $_data_status_before['statusProcessing']) ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 row-data">
                        <div class="row-statistic_card statistic_2">
                            <div class="title-statisti_card">Chưa thực hiện</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusNotProduction'] >= $_data_status_before['statusNotProduction'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusNotProduction'] >= $_data_status_before['statusNotProduction'] ? '+' : ''; ?>
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusNotProduction']) ? formatNumber($_data_status_after['statusNotProduction']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusNotProduction'] - $_data_status_before['statusNotProduction'] ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 row-data">
                        <div class="row-statistic_card statistic_3">
                            <div class="title-statisti_card">Hoàn thành</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusSuccess'] >= $_data_status_before['statusSuccess'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusSuccess'] >= $_data_status_before['statusSuccess'] ? '+' : ''; ?>
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusSuccess']) ? formatNumber($_data_status_after['statusSuccess']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusSuccess'] - $_data_status_before['statusSuccess'] ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 row-data">
                        <div class="row-statistic_card statistic_4">
                            <div class="title-statisti_card">Tạm dừng</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusPause'] >= $_data_status_before['statusPause'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusPause'] >= $_data_status_before['statusPause'] ? '+' : ''; ?>
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusPause']) ? formatNumber($_data_status_after['statusPause']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusPause'] - $_data_status_before['statusPause'] ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 row-data">
                        <div class="row-statistic_card statistic_5">
                            <div class="title-statisti_card">Hủy bỏ</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusCancel'] >= $_data_status_before['statusCancel'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusCancel'] >= $_data_status_before['statusCancel'] ? '+' : ''; ?>
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusCancel']) ? formatNumber($_data_status_after['statusCancel']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusCancel'] - $_data_status_before['statusCancel'] ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 row-data">
                        <div class="row-statistic_card statistic_6">
                            <div class="title-statisti_card">Tổng đơn</div>
                            <div class="col-md-12 content-statistic">
                                <?php $lableClass = $_data_status_after['statusAll'] >= $_data_status_before['statusAll'] ? 'success' : 'danger'; ?>
                                <?php $Spend = $_data_status_after['statusAll'] >= $_data_status_before['statusAll'] ? '+' : ''; ?>
                                
                                <div class="row-statistic-<?= $lableClass ?> row-statistic-child">
                                    <span class="crad-number">
                                        <span class="title-number">
                                            <?= !empty($_data_status_after['statusAll']) ? formatNumber($_data_status_after['statusAll']) : 0 ?>
                                        </span>
                                    </span>
                                    <span class="crad-up">
                                        <i class="fas <?=($Spend == '+' ? 'fa-arrow-up' : 'fa-arrow-down')?>"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusAll'] - $_data_status_before['statusAll'] ?>)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div style="margin-top: 35px;"></div>
                    <div class="col-md-4 ">
                        <div class="row-statistic row350">
                            <div class="title-statistic">Top sản phẩm sản xuất nhiều nhất</div>
                            <div class="chartjs-content">
                                <canvas id="charjs-manufactures-top" width="400" height="200"></canvas>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 row-data">
                        <div class="row-statistic row350">
                            <div class="title-statistic">Nguyên vật liệu cần mua</div>
                            <?php $nvlNeedBuy = get_nvl_need_buy(); ?>
                            <table class="table dataTable scroll" style="padding: 15px;border-collapse:unset !important;">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">STT</th>
                                        <th style="width: 30%">Mã NVL</th>
                                        <th style="width: 40%">Tên NVL</th>
                                        <th style="width: 20%" class=" text-center">Số lượng</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php $stt = 1; ?>
                                    <?php foreach ($nvlNeedBuy as $key => $value) { ?>
                                        <tr>
                                            <td style="width: 10%"><?= ($stt) ?></td>
                                            <td style="width: 30%"><?= $value['item_code'] ?></td>
                                            <td style="width: 40%"><?= $value['item_name'] ?></td>
                                            <td style="width: 20%" class="text-center"><?= $value['quantity_rest'] > 0 ? number_format_data($value['quantity_rest']) : 0 ?></td>
                                        </tr>
                                    <?php $stt++;
                                    } ?>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="col-md-4 row-data">
                        <div class="row-statistic row350">
                            <div class="title-statistic">Kế hoạch sản xuất</div>
                            <div class="chartjs-content">
                                <canvas id="charjs-productions" width="400" height="200"></canvas>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
    app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>