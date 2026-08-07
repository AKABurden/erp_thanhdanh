<!DOCTYPE html>
<html>
	<head>
		<link href="<?= base_url('assets/plugins/OrgChart/common/jquery-ui.min.css');?>" rel="stylesheet">
		<script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery.min.js');?>"></script>
		<script type="text/javascript" src="<?= base_url('assets/plugins/OrgChart/common/jquery-ui.min.js');?>"></script>
	</head>

	<body>
		<div style="width: 100%; height: 100%; overflow: auto;">
			<canvas id="diagram">
			</canvas>
		</div>
		<form method="post" action="<?=admin_url('support/yeucau')?>" id="form_yc">
			<div class="users-diagram">
				
			</div>
			<button class="btn btn-info">Xác nhận</button>
		</form>
		<script src="<?= base_url('assets/plugins/OrgChart/MindFusion.Common.js');?>" type="text/javascript"></script>
		<script src="<?= base_url('assets/plugins/OrgChart/MindFusion.Diagramming.js');?>" type="text/javascript"></script>
		<script src="<?= base_url('assets/plugins/OrgChart/OrgChartEditor.js');?>" type="text/javascript"></script>
	</body>
</html>