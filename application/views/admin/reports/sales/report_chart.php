  <link href="" rel="stylesheet">
  <!-- <link href="<?= base_url('assets/css_ch/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet"> -->
  <!-- Custom fonts for this template-->

  <!-- Page level plugin CSS-->
  <style type="text/css">
  	.row {
  		display: -ms-flexbox;
  		-ms-flex-wrap: wrap;
  		flex-wrap: wrap;
  		margin-right: -15px;
  		margin-left: -15px;
  		margin-bottom: 25px;
  	}

  	*,
  	::after,
  	::before {
  		box-sizing: border-box;
  	}

  	.fa-fw_ch {
  		width: 4.285714em !important;
  		text-align: center;
  	}

  	.card-body-icon {
  		position: absolute;
  		z-index: 0;
  		top: -17px;
  		right: -89px;
  		font-size: 5rem;
  		-webkit-transform: rotate(15deg);
  		-ms-transform: rotate(15deg);
  		transform: rotate(15deg);
  	}

  	.card-body {
  		-ms-flex: 1 1 auto;
  		flex: 1 1 auto;
  		padding: 2.25rem;
  	}

  	.card-footer:last-child {
  		border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
  	}

  	.z-1 {
  		z-index: 1;
  	}

  	.text-white {
  		color: #fff !important;
  	}

  	.card-footer {
  		padding: .75rem 1.25rem;
  		background-color: rgba(0, 0, 0, .03);
  		border-top: 1px solid rgba(0, 0, 0, .125);
  	}

  	.small,
  	small {
  		font-size: 80%;
  		font-weight: 400;
  	}

  	.mb-3,
  	.my-3 {
  		margin-bottom: 1rem !important;
  	}

  	.o-hidden {
  		overflow: hidden !important;
  	}

  	.text-white {
  		color: #fff !important;
  	}

  	.h-100 {
  		height: 100% !important;
  	}

  	.bg-primary {
  		background: linear-gradient(to right, #84d9d2, #07cdae) !important;
  	}

  	.bg-warning {
  		background: linear-gradient(to right, #ffbf96, #fe7096) !important;
  	}

  	.bg-success {
  		background: linear-gradient(to right, #90caf9, #047edf 99%) !important;
  	}

  	.card {
  		position: relative;
  		display: -ms-flexbox;
  		display: flex;
  		-ms-flex-direction: column;
  		flex-direction: column;
  		min-width: 0;
  		word-wrap: break-word;
  		background-color: #fff;
  		background-clip: border-box;
  		border: 1px solid rgba(0, 0, 0, .125);
  		border-radius: .25rem;
  	}

  	.card_ch {
  		padding-top: 10px;
  	}

  	.card-img-absolute {
  		position: absolute;
  		top: 0;
  		right: 0;
  		height: 100%;
  	}

  	.wrap_box {
  		height: 380px;
  		background-color: #fff;
  		border: 1px solid #dfdfdf;
  		border-radius: 7px;
  	}

  	.wrap_container {
  		display: flex;
  		align-items: flex-end;
  		padding: 0 10px;
  		font-size: 15px
  	}

  	.wrap_line:not(:last-child) {
  		margin: 15px 10px;
  		height: 3px;
  		background: linear-gradient(to right, #33a3ff, #14b900 99%);
  	}

  	.top_staff .wrap_container:nth-child(1) .wrap_number,
  	.top_client .wrap_container:nth-child(1) .wrap_number,
  	.top_items .wrap_container:nth-child(1) .wrap_number {
  		font-size: 20px;
  		font-weight: bold;
  		color: #f00;
  	}

  	.top_staff .wrap_container:nth-child(3) .wrap_number,
  	.top_client .wrap_container:nth-child(3) .wrap_number,
  	.top_items .wrap_container:nth-child(3) .wrap_number {
  		font-size: 20px;
  		font-weight: bold;
  		color: #ffd459;
  	}

  	.top_staff .wrap_container:nth-child(5) .wrap_number,
  	.top_client .wrap_container:nth-child(5) .wrap_number,
  	.top_items .wrap_container:nth-child(5) .wrap_number {
  		font-size: 20px;
  		font-weight: bold;
  		color: #5984ff;
  	}

  	.top_staff .wrap_container:nth-child(7) .wrap_number,
  	.top_client .wrap_container:nth-child(7) .wrap_number,
  	.top_items .wrap_container:nth-child(7) .wrap_number {
  		font-size: 20px;
  		font-weight: bold;
  		color: #59d1ff;
  	}

  	.top_staff .wrap_container:nth-child(9) .wrap_number,
  	.top_client .wrap_container:nth-child(9) .wrap_number,
  	.top_items .wrap_container:nth-child(9) .wrap_number {
  		font-size: 20px;
  		font-weight: bold;
  		color: #ff8759;
  	}

  	.card-body {
  		-ms-flex: 1 1 auto;
  		flex: 1 1 auto;
  		padding: 1.25rem;
  	}
  </style>
  <script src="<?= base_url('assets/css_ch/Chart.min.js') ?>"></script>
  <div class="content-wrapper">
  	<div class="container-fluid">
  		<!-- Breadcrumbs-->
  		<!-- <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">My Dashboard</li>
      </ol> -->
  		<!-- Icon Cards-->
  		<div class="row">
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-warning o-hidden h-100">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Doanh Số Bán (VND)<br><span id="subtotal-doanh_so_ban">0</span></h3>
  					</div>
  				</div>
  			</div>
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-warning o-hidden h-100">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Doanh Số Bán (USD)<br><span id="subtotal-doanh_so_ban_usd">0</span></h3>
  					</div>
  				</div>
  			</div>
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-warning o-hidden h-100">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Doanh Thu <br><span id="subtotal-doanh_thu">0</span></h3>
  					</div>
  				</div>
  			</div>
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-success o-hidden h-100">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Doanh số mua <br><span id="subtotal-doanh_so_mua">0</span></h3>
  						<h5 class="mr-5"> Đã thanh toán: <span id="subtotal-doanh_so_mua_dtt">0</span></h3>
  							<h5 class="mr-5"> Chưa thanh toán: <span id="subtotal-doanh_so_mua_ctt">0</span></h3>
  					</div>
  				</div>
  			</div>
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-success o-hidden h-100">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Chi phí <br><span id="subtotal-chi_phi">0</span></h3>
  					</div>
  				</div>
  			</div>
  			<div style="height: 150px;" class="col-md-2">
  				<div class="card text-white bg-primary o-hidden h-100" style="background-color: #ff4015;">
  					<div class="card-body">
  						<img src="<?= base_url('assets/images/circle.svg') ?>" class="card-img-absolute" alt="circle-image" />
  						<h3 class="mr-5"> Lợi nhuận <br><span id="subtotal-loi_nhuan">0</span></h3>
  						<h5 class="mr-5"> Danh thu bán - Doanh số mua - Chi phí </h3>
  					</div>
  				</div>
  			</div>
  		</div>
  		<div class="clearfix"></div>
  		<div class="row">
  			<div class="col-md-3">
  				<div class="bg-light-gray border-radius-4">
  					<div class="p8">
  						<div class="form-group " id="report-time">
  							<label for="months-report"><?php echo _l('Lọc theo ngày'); ?></label><br />
  							<select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
  								<!-- <option value=""><?php //echo _l('report_sales_months_all_time'); 
														?></option> -->
  								<option value="day"><?php echo _l('Hôm nay'); ?></option>
  								<option value="week"><?php echo _l('Tuần này'); ?></option>
  								<option value="this_month"><?php echo _l('this_month'); ?></option>
  								<option value="1"><?php echo _l('last_month'); ?></option>
  								<option selected value="this_year"><?php echo _l('Năm nay'); ?></option>
  								<option value="last_year"><?php echo _l('Năm trước'); ?></option>
  								<option value="custom"><?php echo _l('period_datepicker'); ?></option>
  							</select>
  						</div>
  					</div>
  				</div>
  				<div id="date-range" class="hide mbot15">
  					<div class="row">
  						<div class="col-md-6">
  							<label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
  							<div class="input-group date">
  								<input type="text" class="form-control datepicker" id="report-from" name="report-from">
  								<div class="input-group-addon">
  									<i class="fa fa-calendar calendar-icon"></i>
  								</div>
  							</div>
  						</div>
  						<div class="col-md-6">
  							<label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
  							<div class="input-group date">
  								<input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
  								<div class="input-group-addon">
  									<i class="fa fa-calendar calendar-icon"></i>
  								</div>
  							</div>
  						</div>
  					</div>
  				</div>
  				<div class="bg-light-gray border-radius-4">
  					<div class="p8">
  						<div class="form-group ">
  							<?php echo render_select('search_id_staff[]', $dataStaff, array('staffid', 'name'), 'Nhân viên', '', array('data-actions-box' => 1, 'multiple' => true), array(), '', '', false); ?>
  						</div>
  					</div>
  				</div>
  				<div class="bg-light-gray border-radius-4">
  					<div class="p8">
  						<div class="form-group ">
  							<?= lang('customers') ?>
  							<input type="text" name="customers_ch" id="customers_ch" style="width: 100%;" data-placeholder="<?= lang('customers') ?>" value="">
  						</div>
  					</div>
  				</div>
  			</div>
  			<div class="col-lg-9">
  				<!-- Example Pie Chart Card-->
  				<div class="card mb-3">
  					<div class="card-body">
  						<canvas id="myBarChart" width="1200" height="400"></canvas>
  					</div>
  					<div class="card-footer small Updated text-muted">Updated <?= _dt(date('Y-m-d H:i:s')) ?></div>
  				</div>
  			</div>
  			<div class="col-md-3">
  				<div class="border-radius-4 wrap_box">
  					<strong>
  						<h4 class="text-center">Top 5 nhân viên bán tốt</h4>
  					</strong>
  					<hr />
  					<div>
  						<div style="float: left; padding: 0 0 0 10px;">
  							<span style="font-size: 10px;"><?= _l('als_staff') ?></span>
  						</div>
  						<div style="float: right; padding: 0 10px 0 0;">
  							<span style="font-size: 10px;"><?= _l('als_sales') ?></span>
  						</div>
  						<div class="clearfix"></div>
  					</div>
  					<div class="top_staff"></div>
  					<br />
  				</div>
  			</div>
  			<div class="col-md-3">
  				<div class="border-radius-4 wrap_box">
  					<strong>
  						<h4 class="text-center">Top 5 khách hàng mua nhiều</h4>
  					</strong>
  					<hr />
  					<div>
  						<div style="float: left; padding: 0 0 0 10px;">
  							<span style="font-size: 10px;"><?= _l('client') ?></span>
  						</div>
  						<div style="float: right; padding: 0 10px 0 0;">
  							<span style="font-size: 10px;"><?= _l('als_sales') ?></span>
  						</div>
  						<div class="clearfix"></div>
  					</div>
  					<div class="top_client"></div>
  					<br />
  				</div>
  			</div>
  			<div class="col-md-3">
  				<div class="border-radius-4 wrap_box">
  					<strong>
  						<h4 class="text-center">Top 5 hàng hóa bán chạy</h4>
  					</strong>
  					<hr />
  					<div>
  						<div style="float: left; padding: 0 0 0 10px;">
  							<span style="font-size: 10px;"><?= _l('cong_item_name') ?></span>
  						</div>
  						<div style="float: right; padding: 0 10px 0 0;">
  							<span style="font-size: 10px;"><?= _l('cong_quantity') ?></span>
  						</div>
  						<div class="clearfix"></div>
  					</div>
  					<div class="top_items"></div>
  					<br />
  					<!-- <hr/>
                <div style="padding: 0 10px;">1 Huỳnh Công Hậu <span style="float: right;">1000000</span></div>
                <hr/>
                <br/> -->
  				</div>
  			</div>
  			<div class="col-md-3">
  				<div class="border-radius-4 wrap_box">
  					<strong>
  						<h4 class="text-center">Chi phí</h4>
  					</strong>
  					<hr />
  					<div>
  						<div style="float: left; padding: 0 0 0 10px;">
  							<span style="font-size: 10px;"><?= _l('Tên chi phí') ?></span>
  						</div>
  						<div style="float: right; padding: 0 10px 0 0;">
  							<span style="font-size: 10px;"><?= _l('Số tiền') ?></span>
  						</div>
  						<div class="clearfix"></div>
  					</div>
  					<div class="top_chiphi" style="max-height: 285px;overflow-y: auto;overflow-x: hidden;"></div>
  					<br />
  				</div>
  			</div>
  		</div>
  		<div>
  			<!-- SECTION: LIFECYCLE + ALERTS -->
  			<div style="display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:28px;" class="anim anim-d4">

  				<!-- Lifecycle Funnel -->
  				<div class="panel">
  					<div style="margin-bottom:22px;">
  						<div class="panel-title"><i class="fa fa-code-fork" style="font-size:15px;"></i> Chu trình &amp; Vòng đời công việc</div>
  						<div class="panel-subtitle">Trạng thái luân chuyển công việc trong hệ thống</div>
  					</div>
  					<div style="display:flex;align-items:center;justify-content:space-between;gap:4px;padding:10px 0;">

  						<div class="lifecycle-step">
  							<div class="lifecycle-bubble lc-gray"><?= $tasks_no_check + $tasks_in_progress ?></div>
  							<div class="lifecycle-label">Tạo mới</div>
  						</div>
  						<div style="flex-shrink:0;color:#c7d2fe;margin-bottom:20px;">
  							<i class="fa fa-chevron-right" style="font-size:18px;"></i>
  						</div>



  						<div class="lifecycle-step">
  							<div class="lifecycle-bubble lc-blue"><?= $tasks_in_progress ?></div>
  							<div class="lifecycle-label">Đang thực hiện</div>
  						</div>
  						<div style="flex-shrink:0;color:#c7d2fe;margin-bottom:20px;">
  							<i class="fa fa-chevron-right" style="font-size:18px;"></i>
  						</div>



  						<div class="lifecycle-step">
  							<div class="lifecycle-bubble lc-green"><?= $tasks_completed_process ?></div>
  							<div class="lifecycle-label">CLOSED</div>
  						</div>

  					</div>

  					<!-- Mini Progress Bar -->
  					<div style="margin-top:18px;padding:14px 16px;background:#f8faff;border-radius:12px;border:1px solid #e0e7ff;">
  						<div style="display:flex;justify-content:space-between;margin-bottom:8px;">
  							<span style="font-size:12px;font-weight:600;color:#475569;">Tổng tiến độ hoàn thành</span>
  							<?php $total_tasks = $tasks_no_check + $tasks_in_progress + $tasks_completed_process;
								$pct = $total_tasks > 0 ? round(($tasks_completed_process / $total_tasks) * 100, 2) : 0; ?>
  							<span style="font-size:12px;font-weight:700;color:#4f46e5;"><?= $pct ?>%</span>
  						</div>
  						<div style="height:8px;background:#e0e7ff;border-radius:99px;overflow:hidden;">
  							<div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,#6366f1,#2563eb);border-radius:99px;"></div>
  						</div>
  					</div>
  				</div>

  				<!-- Alerts Panel -->
  				<div class="panel">
  					<div style="margin-bottom:18px;">
  						<div class="panel-title">
  							<i class="fa fa-shield" style="font-size:15px;color:#ef4444;"></i>
  							Vi phạm &amp; Cảnh báo KSNB
  						</div>
  						<div class="panel-subtitle">Tháng <?= date('m/Y') ?></div>
  					</div>

  					<?php
						$hasAlert = $p3_type2_count > 0 || $p3_type1_count > 0 || $p3_type3_count > 0 || $p3_type4_count > 0;
						?>

  					<?php if ($p3_type2_count > 0): ?>
  						<div class="alert-item alert-red">
  							<div class="alert-icon-wrap">
  								<i class="fa fa-exclamation-triangle" style="font-size:14px;"></i>
  							</div>
  							<div style="flex:1;">
  								<div class="alert-title">Phiếu vi phạm</div>
  							</div>
  							<div style="background:#ef4444;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
  								<?= $p3_type2_count ?>
  							</div>
  						</div>
  					<?php endif; ?>

  					<?php if ($p3_type1_count > 0): ?>
  						<div class="alert-item alert-orange">
  							<div class="alert-icon-wrap">
  								<i class="fa fa-file-text-o" style="font-size:14px;"></i>
  							</div>
  							<div style="flex:1;">
  								<div class="alert-title">BCKPH chưa hoàn thành</div>
  							</div>
  							<div style="background:#f97316;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
  								<?= $p3_type1_count ?>
  							</div>
  						</div>
  					<?php endif; ?>

  					<?php if ($p3_type3_count > 0): ?>
  						<div class="alert-item alert-blue">
  							<div class="alert-icon-wrap">
  								<i class="fa fa-clock-o" style="font-size:14px;"></i>
  							</div>
  							<div style="flex:1;">
  								<div class="alert-title">Công việc chưa hoàn thành</div>
  							</div>
  							<div style="background:#3b82f6;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
  								<?= $p3_type3_count ?>
  							</div>
  						</div>
  					<?php endif; ?>

  					<?php if ($p3_type4_count > 0): ?>
  						<div class="alert-item" style="background:#fffbeb;border-color:#fde68a;">
  							<div class="alert-icon-wrap" style="background:#fef3c7;">
  								<i class="fa fa-clipboard" style="font-size:14px;color:#d97706;"></i>
  							</div>
  							<div style="flex:1;">
  								<div class="alert-title">Audit fail</div>
  							</div>
  							<div style="background:#d97706;color:#fff;font-size:13px;font-weight:700;min-width:28px;height:28px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 8px;flex-shrink:0;">
  								<?= $p3_type4_count ?>
  							</div>
  						</div>
  					<?php endif; ?>

  					<?php if (!$hasAlert): ?>
  						<div style="text-align:center;padding:32px 16px;color:#94a3b8;">
  							<i class="fa fa-check-circle" style="font-size:36px;color:#22c55e;margin-bottom:10px;display:block;text-align:center;"></i>
  							<div style="font-size:13px;font-weight:600;color:#475569;">Không có cảnh báo</div>
  							<div style="font-size:12px;margin-top:4px;">Mọi chỉ số trong tháng đều ổn định</div>
  						</div>
  					<?php endif; ?>

  				</div>

  			</div>

  			<!-- SECTION: HR TABLE -->
  			<div class="section-label anim anim-d5"><i class="fa fa-users" style="font-size:13px;"></i> Lộ trình sự nghiệp &amp; Đánh giá định kỳ</div>
  			<div class="panel anim anim-d5" style="margin-bottom:28px;">
  				<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
  					<div>
  						<div class="panel-title"><i class="fa fa-user-circle-o" style="font-size:15px;"></i> Danh sách đánh giá nhân sự</div>
  						<div class="panel-subtitle">Hệ thống tự động sinh Phiếu YC đánh giá nâng bậc dựa trên thời gian làm việc</div>
  					</div>
  				</div>
  				<div style="overflow-x:auto;">
  					<table class="styled-table">
  						<thead>
  							<tr>
  								<th>Nhân sự</th>
  								<th>Vị trí</th>
  								<th>Cột mốc</th>
  								<th>Điểm KPI</th>
  								<th>Trạng thái</th>
  							</tr>
  						</thead>
  						<tbody>
  							<?php
								$av_colors = ['av-blue', 'av-purple', 'av-green'];
								$av_bar_gradients = [
									'av-blue'   => 'linear-gradient(90deg,#6366f1,#4f46e5)',
									'av-purple' => 'linear-gradient(90deg,#a855f7,#7c3aed)',
									'av-green'  => 'linear-gradient(90deg,#22c55e,#16a34a)',
								];
								if (!empty($eval_list)):
									foreach ($eval_list as $idx => $ev):
										$name      = trim($ev['staff_name']);
										// Tạo chữ viết tắt từ 2 từ đầu tiên
										$words     = explode(' ', $name);
										$abbr      = '';
										foreach (array_slice($words, 0, 2) as $w) {
											$abbr .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8');
										}
										$av_class  = $av_colors[$idx % count($av_colors)];
										$gradient  = $av_bar_gradients[$av_class];
										$milestone = (int)$ev['milestone_month'];
										$point     = (float)$ev['point'];
										$has_point = !empty($ev['rating_list']) && $point > 0;
										$bar_width = $has_point ? (min(5, $point) / 5) * 100 : 0;

								?>
  									<tr>
  										<td>
  											<span class="user-avatar <?= $av_class ?>"><?= $abbr ?></span>
  											<span class="name">
  												<a href="<?= base_url('admin/personnel_assessment/view/' . $ev['id']) ?>" style="color:inherit;text-decoration:none;">
  													<?= htmlspecialchars($name) ?>
  												</a>
  											</span>
  										</td>
  										<td><?= htmlspecialchars($ev['name_role'] ?? '—') ?></td>
  										<td>
  											<span class="milestone-badge">
  												<i class="fa fa-clock-o" style="font-size:10px;"></i>
  												Đánh giá <?= $milestone ?> tháng
  											</span>
  										</td>
  										<td>
  											<div class="kpi-bar-wrap">
  												<div class="kpi-bar-bg">
  													<div class="kpi-bar-fill" style="width:<?= $bar_width ?>%;background:<?= $gradient ?>;"></div>
  												</div>
  												<?php if ($has_point): ?>
  													<span class="kpi-score"><?= number_format($point, 1) ?>/5</span>
  												<?php else: ?>
  													<span class="kpi-score" style="color:#94a3b8;">--</span>
  												<?php endif; ?>
  											</div>
  										</td>
  										<td>
  											<?php if ($has_point): ?>
  												<span class="status-badge status-green">Hoàn thành</span>
  											<?php else: ?>
  												<span class="status-badge status-blue">Chưa đánh giá</span>
  											<?php endif; ?>
  										</td>
  									</tr>
  								<?php endforeach; ?>
  							<?php else: ?>
  								<tr>
  									<td colspan="5" style="text-align:center;padding:32px 16px;color:#94a3b8;">
  										<i class="fa fa-inbox" style="font-size:32px;display:block;text-align:center;margin-bottom:10px;color:#c7d2fe;"></i>
  										<div style="font-size:13px;font-weight:600;color:#475569;">Không có dữ liệu đánh giá</div>
  										<div style="font-size:12px;margin-top:4px;">Chưa có phiếu đánh giá nhân viên nào trong năm <?= date('Y') ?></div>
  									</td>
  								</tr>
  							<?php endif; ?>
  						</tbody>
  					</table>
  				</div>
  			</div>
  			<!-- SECTION: ĐÁNH GIÁ RỦI RO -->
  			<div class="section-label anim anim-d5"><i data-lucide="shield-alert" style="width:14px;height:14px;"></i> Cần đánh giá rủi ro &mdash; <span style="font-weight:500;color:#ef4444;"><?= $filter_label ?></span></div>
  			<div class="panel anim anim-d5" style="margin-bottom:28px;border-left:4px solid #ef4444;">
  				<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
  					<div>
  						<div class="panel-title"><i data-lucide="alert-triangle" style="width:16px;height:16px;color:#ef4444;"></i> Phiếu có rủi ro cao chưa đánh giá</div>
  						<div class="panel-subtitle">Các phiếu trong <strong><?= $filter_label ?></strong></div>
  					</div>
  					<?php if (!empty($big_risk_list)): ?>
  						<div style="background:#fee2e2;color:#ef4444;font-size:12px;font-weight:700;padding:5px 14px;border-radius:99px;border:1px solid #fecaca;">
  							<?= count($big_risk_list) ?> phiếu cần xử lý
  						</div>
  					<?php endif; ?>
  				</div>
  				<div style="overflow-x:auto;">
  					<?php if (!empty($big_risk_list)): ?>
  						<table class="styled-table">
  							<thead>
  								<tr>
  									<th style="width:36px;">#</th>
  									<th>Nhân sự</th>
  									<th>Vị trí</th>
  									<th>Cấp bậc</th>
  									<th>Mã phiếu</th>
  									<th>Ngày tạo</th>
  									<th>Mức rủi ro</th>
  									<th></th>
  								</tr>
  							</thead>
  							<tbody>
  								<?php foreach ($big_risk_list as $ri => $risk): ?>
  									<?php
										$risk_val  = (int)$risk['big_risk'];
										$risk_color = $risk_val >= 3 ? '#dc2626' : ($risk_val == 2 ? '#f97316' : '#eab308');
										$risk_bg    = $risk_val >= 3 ? '#fee2e2' : ($risk_val == 2 ? '#ffedd5' : '#fef9c3');
										$risk_label = $risk_val >= 3 ? 'Rất cao' : ($risk_val == 2 ? 'Cao' : 'Trung bình');
										?>
  									<tr>
  										<td style="color:#94a3b8;font-size:12px;"><?= $ri + 1 ?></td>
  										<td class="name">
  											<a href="<?= base_url('admin/personnel_assessment/view/' . $risk['id']) ?>" style="color:inherit;text-decoration:none;">
  												<?= htmlspecialchars($risk['staff_name'] ?: '—') ?>
  											</a>
  										</td>
  										<td><?= htmlspecialchars($risk['name_role'] ?? '—') ?></td>
  										<td><?= htmlspecialchars($risk['code_role_level'] ?? '—') ?></td>
  										<td>
  											<a href="<?= base_url('admin/personnel_assessment/view/' . $risk['id']) ?>" style="font-weight:600;color:#6366f1;font-size:12px;">
  												<?= htmlspecialchars($risk['code']) ?>
  											</a>
  										</td>
  										<td style="font-size:12px;color:#94a3b8;"><?= date('d/m/Y', strtotime($risk['date'])) ?></td>
  										<td>
  											<span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;background:<?= $risk_bg ?>;color:<?= $risk_color ?>;border:1px solid <?= $risk_color ?>30;">
  												<span style="width:6px;height:6px;border-radius:50%;background:<?= $risk_color ?>;display:inline-block;"></span>
  												<?= $risk_label ?>
  											</span>
  										</td>
  										<td>
  											<a href="<?= base_url('admin/personnel_assessment/process_evaluate/' . $risk['id']) ?>" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;background:#fef2f2;color:#ef4444;font-size:12px;font-weight:600;border:1px solid #fecaca;text-decoration:none;white-space:nowrap;">
  												<i data-lucide="clipboard-check" style="width:13px;height:13px;"></i> Đánh giá
  											</a>
  										</td>
  									</tr>
  								<?php endforeach; ?>
  							</tbody>
  						</table>
  					<?php else: ?>
  						<div style="text-align:center;padding:32px 16px;color:#94a3b8;">
  							<i data-lucide="shield-check" style="width:36px;height:36px;color:#22c55e;display:block;margin:0 auto 10px;"></i>
  							<div style="font-size:13px;font-weight:600;color:#475569;">Không có phiếu rủi ro cần xử lý</div>
  							<div style="font-size:12px;margin-top:4px;">Tất cả phiếu trong <?= $filter_label ?> đã được đánh giá hoặc không có rủi ro</div>
  						</div>
  					<?php endif; ?>
  				</div>
  			</div>
  			<!-- SECTION: TOP 5 NHÂN VIÊN -->
  			<div class="section-label anim anim-d6"><i class="fa fa-trophy" style="font-size:13px;"></i> Top 5 nhân viên theo từng chỉ số vi phạm</div>
  			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-bottom:28px;" class="anim anim-d6">

  				<!-- Top 5: Phiếu vi phạm -->
  				<div class="panel">
  					<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
  						<div style="width:34px;height:34px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
  							<i class="fa fa-exclamation-triangle" style="font-size:15px;color:#ef4444;"></i>
  						</div>
  						<div>
  							<div class="panel-title" style="color:#ef4444;">Phiếu vi phạm</div>
  							<div class="panel-subtitle">Tháng <?= date('m/Y') ?></div>
  						</div>
  					</div>
  					<?php if (!empty($top5_type2)): ?>
  						<div style="display:flex;flex-direction:column;gap:8px;">
  							<?php foreach ($top5_type2 as $rank => $row): ?>
  								<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fff1f2' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fecdd3' : '#f1f5f9' ?>;">
  									<div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#ef4444' : ($rank === 1 ? '#f87171' : '#fca5a5') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
  									<div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
  									<div style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
  								</div>
  							<?php endforeach; ?>
  						</div>
  					<?php else: ?>
  						<div style="text-align:center;padding:24px 12px;color:#94a3b8;">
  							<i class="fa fa-check-circle" style="font-size:28px;color:#22c55e;display:block;text-align:center;margin-bottom:8px;"></i>
  							<div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
  						</div>
  					<?php endif; ?>
  				</div>

  				<!-- Top 5: BCKPH chưa hoàn thành -->
  				<div class="panel">
  					<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
  						<div style="width:34px;height:34px;border-radius:10px;background:#ffedd5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
  							<i class="fa fa-file-text-o" style="font-size:15px;color:#f97316;"></i>
  						</div>
  						<div>
  							<div class="panel-title" style="color:#f97316;">BCKPH chưa hoàn thành</div>
  							<div class="panel-subtitle">Tháng <?= date('m/Y') ?></div>
  						</div>
  					</div>
  					<?php if (!empty($top5_type1)): ?>
  						<div style="display:flex;flex-direction:column;gap:8px;">
  							<?php foreach ($top5_type1 as $rank => $row): ?>
  								<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fff7ed' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fed7aa' : '#f1f5f9' ?>;">
  									<div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#f97316' : ($rank === 1 ? '#fb923c' : '#fdba74') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
  									<div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
  									<div style="background:#f97316;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
  								</div>
  							<?php endforeach; ?>
  						</div>
  					<?php else: ?>
  						<div style="text-align:center;padding:24px 12px;color:#94a3b8;">
  							<i class="fa fa-check-circle" style="font-size:28px;color:#22c55e;display:block;text-align:center;margin-bottom:8px;"></i>
  							<div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
  						</div>
  					<?php endif; ?>
  				</div>

  				<!-- Top 5: Công việc chưa hoàn thành -->
  				<div class="panel">
  					<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
  						<div style="width:34px;height:34px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
  							<i class="fa fa-clock-o" style="font-size:15px;color:#3b82f6;"></i>
  						</div>
  						<div>
  							<div class="panel-title" style="color:#3b82f6;">Công việc chưa hoàn thành</div>
  							<div class="panel-subtitle">Tháng <?= date('m/Y') ?></div>
  						</div>
  					</div>
  					<?php if (!empty($top5_type3)): ?>
  						<div style="display:flex;flex-direction:column;gap:8px;">
  							<?php foreach ($top5_type3 as $rank => $row): ?>
  								<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#eff6ff' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#bfdbfe' : '#f1f5f9' ?>;">
  									<div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#3b82f6' : ($rank === 1 ? '#60a5fa' : '#93c5fd') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
  									<div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
  									<div style="background:#3b82f6;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
  								</div>
  							<?php endforeach; ?>
  						</div>
  					<?php else: ?>
  						<div style="text-align:center;padding:24px 12px;color:#94a3b8;">
  							<i class="fa fa-check-circle" style="font-size:28px;color:#22c55e;display:block;text-align:center;margin-bottom:8px;"></i>
  							<div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
  						</div>
  					<?php endif; ?>
  				</div>

  				<!-- Top 5: Audit fail -->
  				<div class="panel">
  					<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
  						<div style="width:34px;height:34px;border-radius:10px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
  							<i class="fa fa-clipboard" style="font-size:15px;color:#d97706;"></i>
  						</div>
  						<div>
  							<div class="panel-title" style="color:#d97706;">Audit fail</div>
  							<div class="panel-subtitle">Tháng <?= date('m/Y') ?></div>
  						</div>
  					</div>
  					<?php if (!empty($top5_type4)): ?>
  						<div style="display:flex;flex-direction:column;gap:8px;">
  							<?php foreach ($top5_type4 as $rank => $row): ?>
  								<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;background:<?= $rank === 0 ? '#fffbeb' : '#f8faff' ?>;border:1px solid <?= $rank === 0 ? '#fde68a' : '#f1f5f9' ?>;">
  									<div style="width:24px;height:24px;border-radius:50%;background:<?= $rank === 0 ? '#d97706' : ($rank === 1 ? '#f59e0b' : '#fcd34d') ?>;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $rank + 1 ?></div>
  									<div style="flex:1;font-size:13px;font-weight:600;color:#1e293b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($row['staff_name'] ?: '—') ?></div>
  									<div style="background:#d97706;color:#fff;font-size:11px;font-weight:700;min-width:24px;height:24px;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 6px;flex-shrink:0;"><?= $row['total'] ?></div>
  								</div>
  							<?php endforeach; ?>
  						</div>
  					<?php else: ?>
  						<div style="text-align:center;padding:24px 12px;color:#94a3b8;">
  							<i class="fa fa-check-circle" style="font-size:28px;color:#22c55e;display:block;text-align:center;margin-bottom:8px;"></i>
  							<div style="font-size:12px;font-weight:600;color:#475569;">Không có dữ liệu</div>
  						</div>
  					<?php endif; ?>
  				</div>

  			</div>
  		</div>
  	</div>
  </div>
  <script type="text/javascript">
  	$('select[name="months-report"]').on('change', function() {
  		var val = $(this).val();
  		$('input[name="report-to"]').val('');
  		$('input[name="report-from"]').val('');
  		if (val == 'custom') {
  			$('#date-range').addClass('fadeIn').removeClass('hide');
  			return;
  		} else {
  			if (!$('#date-range').hasClass('hide')) {
  				$('#date-range').removeClass('fadeIn').addClass('hide');
  			}
  		}
  		init_dashboard_report();
  		get_total_limit();
  		top_staff();
  		top_client();
  		top_items();
  		top_chiphi();
  	});

  	function top_staff() {
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		jQuery.ajax({
  			type: "post",
  			url: "<?= admin_url() ?>reports/top_staff/",
  			data: dataString,
  			cache: false,
  			success: function(data) {
  				data = JSON.parse(data);
  				$('.top_staff').html(data);
  			}
  		});
  	}

  	function top_client() {
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		jQuery.ajax({
  			type: "post",
  			url: "<?= admin_url() ?>reports/top_client/",
  			data: dataString,
  			cache: false,
  			success: function(data) {
  				data = JSON.parse(data);
  				$('.top_client').html(data);
  			}
  		});
  	}

  	function top_items() {
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		jQuery.ajax({
  			type: "post",
  			url: "<?= admin_url() ?>reports/top_items/",
  			data: dataString,
  			cache: false,
  			success: function(data) {
  				data = JSON.parse(data);
  				$('.top_items').html(data);
  			}
  		});
  	}

  	function get_total_limit() {
  		// top_staff();
  		// top_client();
  		// top_items();
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		jQuery.ajax({
  			type: "post",
  			url: "<?= admin_url() ?>reports/count_all/",
  			data: dataString,
  			cache: false,
  			success: function(data) {
  				data = JSON.parse(data);
  				$('#subtotal-doanh_so_ban').html(tnhFormatZero(data.doanh_so_ban, 0));
  				$('#subtotal-doanh_thu').html(tnhFormatZero(data.doanh_thu, 0));
  				$('#subtotal-doanh_so_mua').html(tnhFormatZero(data.doanh_so_mua, 0));
  				$('#subtotal-doanh_so_mua_dtt').html(tnhFormatZero(data.doanh_so_mua_dtt, 0));
  				$('#subtotal-doanh_so_mua_ctt').html(tnhFormatZero(data.doanh_so_mua_ctt, 0));
  				$('#subtotal-chi_phi').html(tnhFormatZero(data.chi_phi, 0));
  				$('#subtotal-loi_nhuan').html(tnhFormatZero(data.loi_nhuan, 0));
  				$('#subtotal-doanh_so_ban_usd').html(tnhFormatZero(data.doanh_so_ban_usd, 0));
  			}
  		});
  	}
  	var myLineChart;

  	function number_format(number, decimals, dec_point, thousands_sep) {
  		// *     example: number_format(1234.56, 2, ',', ' ');
  		// *     return: '1 234,56'
  		number = (number + '').replace(',', '').replace(' ', '');
  		var n = !isFinite(+number) ? 0 : +number,
  			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
  			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
  			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
  			s = '',
  			toFixedFix = function(n, prec) {
  				var k = Math.pow(10, prec);
  				return '' + Math.round(n * k) / k;
  			};
  		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
  		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  		if (s[0].length > 3) {
  			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  		}
  		if ((s[1] || '').length < prec) {
  			s[1] = s[1] || '';
  			s[1] += new Array(prec - s[1].length + 1).join('0');
  		}
  		return s.join(dec);
  	}
  	// line chart data
  	function init_dashboard_report() {
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		$.post(admin_url + 'reports/dashboard_report/', dataString, function(response) {
  			var response = JSON.parse(response);
  			if (typeof(myLineChart) !== 'undefined') {
  				myLineChart.destroy();
  			}
  			labels_cot = [];
  			data_doanh_so_ban = [];
  			data_doanh_thu = [];
  			data_doanh_so_mua = [];
  			data_chi_phi = [];
  			data_loi_nhuan = [];
  			$.each(response.labels, function(key, value) {
  				labels_cot.push(value);
  			});

  			$.each(response.data_doanh_so_ban, function(key, value) {
  				data_doanh_so_ban.push(value);
  			});

  			$.each(response.data_doanh_thu, function(key, value) {
  				data_doanh_thu.push(value);
  			});

  			$.each(response.data_doanh_so_mua, function(key, value) {
  				data_doanh_so_mua.push(value);
  			});

  			$.each(response.data_chi_phi, function(key, value) {
  				data_chi_phi.push(value);
  			});

  			$.each(response.data_loi_nhuan, function(key, value) {
  				data_loi_nhuan.push(value);
  			});

  			var ctx = document.getElementById("myBarChart");
  			myLineChart = new Chart(ctx, {
  				type: 'line',
  				data: {
  					labels: labels_cot,
  					datasets: [{
  							data: data_doanh_so_ban,
  							label: "Doanh số bán",
  							borderColor: "#009688",
  							fill: false,
  							lineTension: 0
  						},
  						{
  							data: data_doanh_thu,
  							label: "Doanh thu",
  							borderColor: "#3e95cd",
  							fill: false,
  							lineTension: 0
  						},
  						{
  							data: data_doanh_so_mua,
  							label: "Doanh số mua",
  							borderColor: "#8bc34a",
  							fill: false,
  							lineTension: 0
  						},
  						{
  							data: data_chi_phi,
  							label: "Chi phí",
  							borderColor: "#8e5ea2",
  							fill: false,
  							lineTension: 0
  						},
  						{
  							data: data_loi_nhuan,
  							label: "Lợi nhuận",
  							borderColor: "#c45850",
  							fill: false,
  							lineTension: 0
  						}
  					]
  				},
  				options: {
  					title: {
  						display: true,
  						text: 'Báo cáo doanh thu'
  					},
  					tooltips: {
  						callbacks: {
  							label: function(tooltipItem, data) {
  								return Number(tooltipItem.yLabel).toFixed(0).replace(/./g, function(c, i, a) {
  									return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
  								});
  							}
  						}
  					},
  					scales: {
  						yAxes: [{
  							ticks: {
  								beginAtZero: true,
  								callback: function(value, index, values) {
  									return number_format(value);
  								}
  							},
  							gridLines: {
  								display: !0
  							}
  						}]
  					},
  				}
  			});
  		});
  	}

  	function top_chiphi() {
  		var months_report = $('select[name="months-report"]').val();
  		var report_to = $('input[name="report-to"]').val();
  		var report_from = $('input[name="report-from"]').val();
  		var customers_ch = $('[name="customers_ch"]').val();
  		var search_id_staff = $('select[name="search_id_staff[]"]').val();
  		dataString = {
  			[csrfData['token_name']]: csrfData['hash'],
  			months_report: months_report,
  			report_from: report_from,
  			report_to: report_to,
  			search_id_staff: search_id_staff,
  			customers_ch: customers_ch
  		};
  		jQuery.ajax({
  			type: "post",
  			url: "<?= admin_url() ?>reports/top_chiphi/",
  			data: dataString,
  			cache: false,
  			success: function(data) {
  				data = JSON.parse(data);
  				$('.top_chiphi').html(data);
  			}
  		});
  	}

  	$(document).ready(function() {
  		get_total_limit();
  		init_dashboard_report();
  		top_staff();
  		top_client();
  		top_items();
  		top_chiphi();
  	});
  </script>