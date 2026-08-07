<div class="modal-dialog modal-lg" style="width: 70%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('sum_view_business_plan') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="row-modal">
					<div class="row-group">
						<div class="lead-view" id="leadViewWrapper">
							<div class="row-contro">
								<div><?= lang('date') ?>: </div>
								<div class="ml-at t-bold"><?= _dt($business_plan['date']) ?></div>
							</div>
							<div class="row-contro">
								<div><?= lang('tnh_reference_business_plan') ?>: </div>
								<div class="ml-at t-bold"><?= $business_plan['reference_no'] ?></div>
							</div>
							<div class="row-contro">
								<div><?= lang('tnh_status') ?>: </div>
								<div class="ml-at t-bold"><?= lang($business_plan['status']) ?></div>
							</div>
							<div class="row-contro">
								<div><?= lang('tnh_branch') ?>: </div>
								<div class="ml-at t-bold">
									<?php
										$dtBranch = $this->site_model->getBranchById($business_plan['id_branch']);
										echo $dtBranch['name'];
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="row-group">
						<div class="row-contro">
							<div><?= lang('tnh_user_agree') ?>: </div>
							<div class="ml-at t-bold">
								<?php if ($business_plan['status'] == "approved") : ?>
									<?= $user_status ?>
								<?php endif ?>
							</div>
						</div>
						<div class="row-contro">
							<div><?= lang('note') ?>: </div>
							<div class="ml-at t-bold"><?= $business_plan['note'] ?></div>
						</div>
					</div>
				</div>
				<div class="col-md-12 mtop10">
					<div class="tabset">
						<!-- Tab 1 -->
						<input type="radio" name="tabset" id="tab1" aria-controls="view-items" checked>
						<label for="tab1"><?= lang('tnh_items') ?></label>
						<!-- Tab 5 -->
						<input type="radio" name="tabset" id="tab5" aria-controls="view-activity-log">
						<label for="tab5"><?= lang('activity_log_puchases') ?></label>
						<div class="tab-panels">
							<section id="view-items" class="tab-panel">
								<div class="table-responsive">
									<table id="tb-items" class="table table-hover dont-responsive-table" style="width: 100%; min-width: 100%;">
										<thead>
											<tr>
												<th class="text-center"><?= lang('tnh_numbers') ?></th>
												<th><?= lang('tnh_images') ?></th>
												<th><?= lang('tnh_product_code') ?></th>
												<th><?= lang('tnh_product_name') ?></th>
												<th><?= lang('ĐV sản xuất') ?></th>
												<th><?= lang('quantity') ?></th>
												<th><?= lang('date') ?></th>
												<th><?= lang('note') ?></th>
												<th class="hide"></th>
											</tr>
										</thead>
										<tbody>
											<?= $tr_html ?>
										</tbody>
									</table>
								</div>
							</section>
							<section id="view-activity-log" class="tab-panel">
								<div class="activity-container tnh-activity-log" style="max-height: 500px;">
									<?php
									$history = getActivityLogByObjId($business_plan['id'], 'business_plan');
									?>
									<?php if (!empty($history)) : ?>
										<?php foreach ($history as $key => $value) : ?>
											<?php
											echo '<div class="feed-item">
                                                    <div class="activity-text">
                                                        ' . staff_profile_image($value['staff_id'], array('staff-profile-image-small'), 'small') . '' . $value['staff_name'] . '
                                                    </div>
                                                    <div class="activity-time">
                                                        ' . time_ago($value['date']) . '<span class="activity-module">' . _l($value['type_parent_obj']) . '</span>
                                                    </div>
                                                    <div>
                                                        ' . $value['content'] . '
                                                    </div>
                                                </div>';
											?>
										<?php endforeach ?>
									<?php endif ?>
								</div>
							</section>
						</div>
					</div>
				</div>
				<div class="col-md-6 pull-right mtop10">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_user_created') ?></h3>
						</div>
						<div class="panel-body">
							<div class="col-md-6">
								<div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
								<div><?= lang('tnh_date_creted') ?>: <?= _dt($business_plan['date_created']) ?></div>
							</div>
							<div class="col-md-6">
								<?php if (!empty($updated_by)) : ?>
									<div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
									<div><?= lang('tnh_date_updated') ?>: <?= _dt($business_plan['date_updated']) ?></div>
								<?php endif ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
		</div>
	</div>
</div>
<script>
	var dtItems = '';
	$(document).ready(function() {
		dtItems = $('#tb-items').DataTable({
			"language": app.lang.datatables,
			"pageLength": app.options.tables_pagination_limit,
			scrollY: true,
			scrollX: true,
			'fnRowCallback': function(nRow, aData, iDisplayIndex) {},
			"initComplete": function(settings, json) {
				var t = this;
				t.parents('.table-loading').removeClass('table-loading');
				t.removeClass('dt-table-loading');
			},
			"footerCallback": function(row, data, start, end, display) {}
		});

		function format(d) {
			return d[8];
		}

		$('#tb-items').DataTable().rows().every(function() {
			var tr = $(this.node());
			var row = dtItems.row(tr);

			if (row.child.isShown()) {} else {
				row.child(format(row.data())).show();
				tr.addClass('shown');
			}
		});
	});
</script>