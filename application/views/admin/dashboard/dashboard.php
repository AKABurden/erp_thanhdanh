<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php //print_arrays(get_productions_orders());?>
<?php init_head(); ?>
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
            border-right: 1px solid #ddd !important;;
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
    .dashboard_statistic .row-statistic {
        min-height: 80px;
        background-color: white;
        margin-top: 10px;
        height: auto;
        border: 1px solid #acc5e0;
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
    table.scroll thead, table.scroll tbody tr {
        display: table;
        table-layout: fixed;/* even columns width , fix width of table too*/
        width: 100%;
    }
    .row350 {
        height: 350px!important;
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
$data_chart_manufactures = get_product_production_top($date_start_after, $date_end_after);
$data_chart_productions_orders = get_productions_orders($date_start_after, $date_end_after);
?>
<div class="title-dashboard">Phần mền quản lý</div>
<div class="menu-mobile-dashboard">
    <div class="menu_mobile-dashboard">
        <div class="app-menu-group-mobile-dashboard">
            <div class="content-menu-v2-mobile-dashboard">
				<?php
				if (!empty(!empty($list_title[1]))) {
					foreach ($list_title[1] as $key => $value) { ?>
                        <div class="wap-off-mobile-dashboard <?= empty($value->off) ? (has_permission_parent($value->parent) ? '' : 'no-event') : 'no-event' ?>">
                            <a class="app-menu-item-mobile-dashboard <?= empty($value->off) ? (has_permission_parent($value->parent) ? '' : 'no-event') : 'no-event' ?> <?= empty($value->url) ? 'change_menu_child' : '' ?>" <?= !empty($value->url) ? 'href="' . admin_url($value->url) . '"' : ' object = "' . $value->object . '" ' ?>>
								<?php if (!empty($value->img)) { ?>
                                    <div class="wrap-img-mobile-dashboard">
                                        <img src="<?= empty($value->off) ? (has_permission_parent($value->parent) ? base_url($value->img) : base_url($value->img_black)) : base_url($value->img_black) ?>">
                                    </div>
								<?php } ?>
                                <span><?php echo ucwords(_l($value->name, '', false)); ?></span>
                                <div class="clearfix"></div>
                            </a>
                        </div>
					<?php }
				}
				?>
            </div>
        </div>
    </div>
</div>
<div id="wrapper">
    <div class="wap-container-menu">
		<?php if (is_mobile()) { ?>
        <div class="wap-slide previous">
            << /div>
            <div class="wap-slide next">></div>
			<?php } ?>
			<?php $this->load->view('admin/includes/menu_v2'); ?>
        </div>
        <div class="screen-options-area"></div>
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
                            <div class="row-statistic">
                                <div class="title-statistic">Đang thực hiện</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusProcessing'] >= $_data_status_before['statusProcessing'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusProcessing'] >= $_data_status_before['statusProcessing'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusProcessing']) ? $_data_status_after['statusProcessing'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= ($_data_status_after['statusProcessing'] - $_data_status_before['statusProcessing']) ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row-data">
                            <div class="row-statistic">
                                <div class="title-statistic">Chưa thực hiện</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusNotProduction'] >= $_data_status_before['statusNotProduction'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusNotProduction'] >= $_data_status_before['statusNotProduction'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusNotProduction']) ? $_data_status_after['statusNotProduction'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusNotProduction'] - $_data_status_before['statusNotProduction'] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row-data">
                            <div class="row-statistic">
                                <div class="title-statistic">Hoàn thành</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusSuccess'] >= $_data_status_before['statusSuccess'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusSuccess'] >= $_data_status_before['statusSuccess'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusSuccess']) ? $_data_status_after['statusSuccess'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusSuccess'] - $_data_status_before['statusSuccess'] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row-data">
                            <div class="row-statistic">
                                <div class="title-statistic">Tạm dừng</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusPause'] >= $_data_status_before['statusPause'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusPause'] >= $_data_status_before['statusPause'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusPause']) ? $_data_status_after['statusPause'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusPause'] - $_data_status_before['statusPause'] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row-data">
                            <div class="row-statistic">
                                <div class="title-statistic">Hủy bỏ</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusCancel'] >= $_data_status_before['statusCancel'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusCancel'] >= $_data_status_before['statusCancel'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusCancel']) ? $_data_status_after['statusCancel'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusCancel'] - $_data_status_before['statusCancel'] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 row-data">
                            <div class="row-statistic">
                                <div class="title-statistic">Tổng đơn</div>
                                <div class="col-md-12 content-statistic">
									<?php $lableClass = $_data_status_after['statusAll'] >= $_data_status_before['statusAll'] ? 'success' : 'danger'; ?>
									<?php $Spend = $_data_status_after['statusAll'] >= $_data_status_before['statusAll'] ? '+' : ''; ?>
                                    <div class="row-statistic-<?= $lableClass ?>">
										<?= !empty($_data_status_after['statusAll']) ? $_data_status_after['statusAll'] : 0 ?>
                                        <i class="fas fa-arrow-up"></i>
                                        <span class="small-<?= $lableClass ?>">(<?= $Spend ?><?= $_data_status_after['statusAll'] - $_data_status_before['statusAll'] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
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
                                <table class="table dataTable scroll" style="padding: 15px;">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã NVL</th>
                                        <th>Tên NVL</th>
                                        <th class="text-center">Số lượng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php $stt = 1; ?>
									<?php foreach ($nvlNeedBuy as $key => $value) { ?>
                                        <tr>
                                            <td><?= ($stt) ?></td>
                                            <td><?= $value['item_code'] ?></td>
                                            <td><?= $value['item_name'] ?></td>
                                            <td class="text-center"><?= $value['quantity_rest'] > 0 ? number_format_data($value['quantity_rest']) : 0 ?></td>
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
	<?php init_tail(); ?>
	<?php $this->load->view('admin/utilities/calendar_template'); ?>
	<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
    <script>
        const ctx_manufactures = document.getElementById('charjs-manufactures-top').getContext('2d');
        const Chart_manufactures = new Chart(ctx_manufactures, {
            type: 'doughnut',
            data: <?=!empty($data_chart_manufactures) ? json_encode($data_chart_manufactures) : '[]'?>
        })

        const ctx_productions = document.getElementById('charjs-productions').getContext('2d');
        var dataCharjsTask = <?=!empty($data_chart_productions_orders) ? json_encode($data_chart_productions_orders) : '[]' ?>;
        const Chart_productions = new Chart(ctx_productions, {
            type: 'bar',
            data: dataCharjsTask,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
