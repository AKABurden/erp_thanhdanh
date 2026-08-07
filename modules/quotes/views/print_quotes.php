<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?= $title ?></title>
	<script type="text/javascript" id="vendor-js" src="<?= base_url() ?>assets/builds/vendor-admin.js?v=2.3.3"></script>
</head>
<style type="text/css">
	body {
		font-family: "Times New Roman", Times, serif;
	}

	.bold {
		font-weight: bold;
	}

	.cred {
		color: #d51a2b;
	}

	.cblue {
		color: #437fc0;
	}

	.fsize-head {
		font-size: 12px;
	}

	.text-underline {
		text-decoration: underline;
	}

	.text-center {
		text-align: center;
	}

	.text-right {
		text-align: right;
	}

	.text-left {
		text-align: left;
	}

	.italic {
		font-style: italic;
	}

	sub {
		vertical-align: bottom;
	}

	table {
		border-collapse: collapse;
		width: 100%;
	}

	.tb-items {
		border-collapse: collapse;
		width: 100%;
	}

	.tb-items, .tb-items th, .tb-items td {
	  	border: 1px solid black;
	  	padding: 5px;
	}

	.tb-items, .tb-items th {
		font-size: 14px;
	}

	.tb-items, .tb-items td {
		font-size: 16px;
	}

	.capitalize {
		text-transform: capitalize;
	}

	.fsize-ft {
		font-size: 16px;
	}

	.info-size-ft {
		font-size: 11px;
	}

	#info-items img {
		max-width: 99% !important;
		/*max-height: 90% !important;*/
		padding: 4px;
	}

	@media print
	{
		* {-webkit-print-color-adjust:exact;}
		/*@page :first {
			margin: 100cm !important;
		}*/
	}

</style>
<!-- onload="window.print()" -->
<body onload="window.print()" style="width: 100%;">
	<div class="bg">
		<img style="width: 100%;" src="<?= module_dir_url(MODULE_NAME, 'assets/images/bg.png') ?>">
	</div>
	<p style="page-break-before: always">
	<div style="width: 100%;">
		<!-- <div style="width: 10%; float: left;">
			<img src="<?= module_dir_url(MODULE_NAME, 'assets/images/logo.png') ?>">
		</div> -->
		<!-- <div style="width: 87%; float: left;"> -->
		<div style="width: 99%">
			<div style="width: 10%; float: left;">
				<img src="<?= module_dir_url(MODULE_NAME, 'assets/images/logo.png') ?>">
			</div>
			<div style="width: 87%; float: left;">
				<div class="bold cred"><?= get_option('invoice_company_name') ?></div>
				<div class="fsize-head">
					<div>Office: <?= get_option('invoice_company_address') ?></div>
					<div>Factory: <?= get_option('factory') ?></div>
					<div>Tel: <?= get_option('invoice_company_phonenumber') ?>   Email: <?= get_option('email') ?></div>
					<div>Web: <?= get_option('company_website') ?>     Fanpage: <?= get_option('fanpage') ?></div>
					<div class="text-underline">Bank information:</div>
					<div>Beneficiary: <?= get_option('beneficiary') ?></div>
					<div>Account No. <?= get_option('account_no') ?>        Swift codes: <?= get_option('swift_codes') ?></div>
					<div>Bank name: <?= get_option('bank_name') ?></div>
					<div>Address: <?= get_option('address_bank') ?></div>
				</div>
			</div>
			<h3 class="text-center" style="width: 100%; float: left;">QUOTATION</h3>
			<div>
				<table style="width: 100%;">
					<tr>
						<td style="width: 50%;">Customer information: </td>
						<td style="width: 30%;">Quotation No: </td>
						<td style="width: 20%;">Date: </td>
					</tr>
					<tr>
						<td><?= ($quote['type_customer'] == "customers") ? $customer['company'] : (!empty($lead) ? $lead['name'] : '') ?></td>
						<td><?= $quote['reference_no'] ?></td>
						<td><?= _dC($quote['date']) ?></td>
					</tr>
					<tr>
						<td><?= ($quote['type_customer'] == "customers") ? $customer['address'] : (!empty($lead) ? $lead['address'] : '') ?></td>
						<td>Pre. Quotation:</td>
						<td>Validity:</td>
					</tr>
					<tr>
						<td>Attention: <?= tnh_html_entity_decode($quote['note']) ?></td>
						<td><?= !empty($quote['pre_quote']) ? $quote['pre_quote'] : '' ?></td>
						<td><?= _dC($quote['validity']) ?></td>
					</tr>
					<tr>
						<td>Email: <?= ($quote['type_customer'] == "customers") ? $customer['email_client'] : (!empty($lead) ? $lead['email'] : '') ?></td>
					</tr>
				</table>
			</div>
			<div style="margin-top: 20px;">
				<div class="bold italic" style="margin-left: 20px;">We have much pleasure to offer you our quotation as follows:</div>
				<table class="tb-items">
					<thead>
						<tr style="background: #67adeb;">
							<th>STT</th>
							<th style="width: 35%;">Description of Goods</th>
							<th>Origin</th>
							<th>Unit</th>
							<th>Quan’</th>
							<th>
								<div>Unit price</div>
								<div>(USD)</div>
							</th>
							<th>
								<div>Total amount</div>
								<div>(USD)</div>
							</th>
							<th>
								<div>Lead time</div>
								<div>(days)</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<?= $body_items ?>
					</tbody>
					<tfoot style="background: #67adeb;">
						<tr class="bold" >
							<th class="text-left" colspan="4">TOTAL EX-WORK</th>
							<th class="text-center"><?= formatNumber($quote['total_quantity']) ?></th>
							<th></th>
							<th class="text-right"><?= formatMoney($quote['total']) ?></th>
							<th></th>
						</tr>
						<?php if (!empty($quote_charges)): ?>
							<?php foreach ($quote_charges as $key => $value): ?>
								<tr class="bold">
									<th class="text-left" colspan="4"><?= $value['name_charge'] ?></th>
									<th class="text-center"><?= formatNumber($value['quantity_charge']) ?></th>
									<th class="text-right"><?= formatMoney($value['price_charge']) ?></th>
									<th class="text-right"><?= formatMoney($value['total_amount_charge']) ?></th>
									<th></th>
								</tr>
							<?php endforeach ?>
						<?php endif ?>
						<tr class="bold">
							<th class="text-left" colspan="4">TOTAL CIF YANGON: <span class="capitalize"><?= $money_words ?></span></th>
							<th class="text-center"></th>
							<th></th>
							<th class="text-right"><?= formatMoney($quote['grand_total']) ?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</div>
			<div style="margin-top: 20px;" id="parts-origin">
				<img style="width: 100%;" src="<?= module_dir_url(MODULE_NAME, 'assets/images/list_of_parts.png') ?>">
				<?= tnh_html_entity_decode($quote['parts_origin']) ?>
			</div>
			<!-- <p style="page-break-before: always"> -->
			<div style="margin-top: 20px;" id="info-items">
				<?php foreach ($items as $key => $value): ?>
					<div><?= tnh_html_entity_decode($value['info']) ?></div>
				<?php endforeach ?>
			</div>
			<p style="page-break-before: always">
			<div style="position: relative;">
				<img style="width: 100%;" src="<?= module_dir_url(MODULE_NAME, 'assets/images/footer1.png') ?>">
				<div style="
				position: absolute;
				top: 16%;
				left: 37%;
				width: 50%;">
					<div class="bold cblue fsize-ft" style="margin-bottom: 5%;">DELIVERY</div>
					<div class="info-size-ft" style="margin-bottom: 17%;"><?= tnh_html_entity_decode($quote['delivery']) ?></div>
					<div class="bold cblue fsize-ft" style="margin-bottom: 11%;">WARANTEE</div>
					<div class="info-size-ft" style="margin-bottom: 3%;">Waranteed against malfunctions due to manufacturing defects for 12 months (with conditions) from the date of loading. Repairs will be made free of charge.</div>
					<div class="info-size-ft" style="margin-bottom: 6%;">Repairs will not be performed free of charge even during the warantee period in the following</div>
					<div class="info-size-ft italic cblue" style="padding: 5px;">(A) Damage occurs due to a natural disaster.</div>
					<div class="info-size-ft italic cblue" style="padding: 5px;">(B) Malfunction occurs due to user's mistake</div>
					<div class="info-size-ft italic cblue" style="padding: 5px;">(C) Malfunction occurs due to self-modifications</div>
					<div class="info-size-ft italic cblue" style="padding: 5px;">(D) Malfunction occurs due to further movement or shipment after installation.</div>
				</div>
			</div>
			<div style="position: relative;">
				<img style="width: 100%;" src="<?= module_dir_url(MODULE_NAME, 'assets/images/footer2.png') ?>">
				<div style="
				position: absolute;
				top: 0%;
				left: 37%;
				width: 50%;
				/*text-align: center;*/
				">
					<div class="bold cblue fsize-ft" style="margin-bottom: 5%;">INSTALLATION COST</div>
					<div class="info-size-ft" style="width: 80%;"><?= tnh_html_entity_decode($quote['installation_cost']) ?></div>
				</div>
				<div style="
				position: absolute;
				/* top: 10%; */
				width: 100%;
				text-align: center;
				bottom: 20%;">
					<div class="bold fsize-ft">TERMS OF PAYMENT</div>
					<?php foreach ($quote_payments as $key => $value): ?>
						<div><?= $value['number'] ?>% <?= $value['name'] ?></div>
					<?php endforeach ?>
					<!-- <div>50% of contract value advance payment by T/T after signing the contract</div>
					<div>45% by T/T before shipment and test run finished at the Seller factory</div>
					<div>5% balance after The Seller set up the machines in the Buyer factory</div> -->
				</div>
			</div>
		</div>
	</div>
</body>

</html>
<script type="text/javascript">
	(function() {
		var beforePrint = function() {
			console.log('Functionality to run before printing.');
		};
		var afterPrint = function() {
			console.log('Functionality to run after printing');
			window.close();
		};

		if (window.matchMedia) {
			var mediaQueryList = window.matchMedia('print');
			mediaQueryList.addListener(function(mql) {
				if (mql.matches) {
					beforePrint();
				} else {
					afterPrint();
				}
			});
		}

		window.onbeforeprint = beforePrint;
		window.onafterprint = afterPrint;
	}());
</script>