<div class="modal-dialog modal-lg" style="width: 80%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('view') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<div class="lead-view" id="leadViewWrapper">
						<div class="wap-content firt">
							<span class="text-muted lead-field-heading no-mtop"><?= lang('tnh_versions') ?>: </span>
							<span class="bold font-medium-xs mbot15"><?= $bom['versions'] ?></span>
						</div>
						<div class="wap-content second hide">
							<span class="text-muted lead-field-heading no-mtop bold"><?= lang('date_start') ?>: </span>
							<span class="bold font-medium-xs lead-name"><?= _d($bom['date_start']) ?></span>
						</div>
						<div class="wap-content firt hide">
							<span class="text-muted lead-field-heading no-mtop"><?= lang('date_end') ?>: </span>
							<span class="bold font-medium-xs mbot15"><?= _d($bom['date_end']) ?></span>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<?= $bom_html ?>
				</div>
				<div class="col-md-6 pull-right mtop10">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-user"></i> <?= lang('tnh_created_by') ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div><?= lang('tnh_created_by') ?>: <?= $created_by ?></div>
                                <div><?= lang('tnh_date_creted') ?>: <?= _dt($bom['date_created']) ?></div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($updated_by)): ?>
                                    <div><?= lang('tnh_updated_by') ?>: <?= $updated_by ?></div>
                                    <div><?= lang('tnh_date_updated') ?>: <?= _dt($bom['date_updated']) ?></div>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('.tbbb').DataTable({
			"language": app.lang.datatables,
			"pageLength": app.options.tables_pagination_limit,
			// "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
		});
	});
</script>