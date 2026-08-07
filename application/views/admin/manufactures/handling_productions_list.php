<?php init_head(); ?>
<link rel="stylesheet" type="text/css" href="<?= css('tnh_core.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= css('tnh.css?vs=1.1') ?>">
<?php echo form_open('admin/productions_list/add', array('id' => 'orders')); ?>
<div id="wrapper">
	<div class="panel_s mbot10 H_scroll" id="">
		<div class="panel-body _buttons">
			<span class="bold uppercase fsize18 H_title"><?= $title ?></span>
			<?= $this->load->view('admin/breadcrumb') ?>
		</div>
	</div>
	<div class="content ae-content">
		<div class="row">
			<div class="col-md-12">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
			<input type="hidden" name="add" id="" class="form-control" value="1">
			<button type="submit" class="btn btn-info add-order">
				<?php echo _l('submit'); ?>
			</button>
		</div>
	</div>
</div>
</div>
<?php echo form_close(); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/bootbox/bootbox.locales.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/jquery.dataTables.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript" src="<?= js('datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script type="text/javascript">
</script>