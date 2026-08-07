<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title"><?= lang('tnh_print_bom') ?></h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div id="view-print" class="col-md-12">
					<style type="text/css">
						.tnh-tb {
							border-collapse: collapse !important;
							width: 100%;
						}

						.tnh-tb tr th {
							font-weight: 600 !important;
						}

						.tnh-tb, .tnh-tb tr th, .tnh-tb tr td {
							border: 1px solid #9e9e9ea3 !important;
							padding: 10px !important;
							vertical-align: middle !important;
						}
					</style>
					<table>
						<tr>
							<td style="vertical-align: middle;"><img style="width: 120px;" src="<?= base_url('uploads/company/').get_option('company_logo') ?>"></td>
							<td style="font-weight: 700; padding: 10px;">
								<div><?= lang('tnh_company') ?>: <?= get_option('invoice_company_name') ?></div>
								<div><?= lang('tnh_address') ?>: <?= get_option('invoice_company_address') ?></div>
								<div><?= lang('tnh_phone') ?>: <?= get_option('invoice_company_phonenumber') ?></div>
								<div><?= lang('tnh_website') ?>: <?= get_option('company_website') ?></div>
							</td>
						</tr>
					</table>
					<h2 style="text-align: center; text-transform: uppercase;"><?= lang('tnh_table') ?> <?= lang('tnh_bom') ?></h2>
					<table class="tnh-tb table-bordered table-hover" style="width: 100%;">
						<thead>
							<tr>
								<th class=""><?= lang('tnh_numbers') ?></th>
								<th><?= lang('Mã NPL/BTP') ?></th>
								<th><?= lang('Tên NPL/BTP') ?></th>
								<th><?= lang('tnh_versions_bom') ?></th>
								<th><?= lang('unit') ?></th>
								<th><?= lang('type') ?></th>
								<th><?= lang('tnh_landscape_print_size') ?></th>
								<th><?= lang('tnh_number_children_size') ?></th>
								<th><?= lang('tnh_exchange_value') ?></th>
								<th><?= lang('tnh_paper_exchange') ?></th>
								<th><?= lang('tnh_quantity_compensation') ?></th>
								<th><?= lang('tnh_stage') ?></th>
							</tr>
						</thead>
						<tbody>
							<?= $html_bom ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
			<button type="button" class="btn btn-primary btn-print" data-dismiss="modal"><?= lang('print') ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		function printData()
		{
			myStyle = '<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">';
			// newWin = window.open("");
			var newWin = window.open('','Print-Window');
			newWin.document.write('<html><head><title><?= lang('tnh_print_bom') ?></title>');
			// newWin.document.write('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" /><meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Expires" content="0" />');
			newWin.document.write('</head><body>');
			// newWin.document.write(myStyle);
			newWin.document.write($('#view-print').html());
			newWin.document.write('</body></html>');

			newWin.print();
			// newWin.close();

		}
		$('.btn-print').click(function(event) {
			printData();
		});
	});
</script>