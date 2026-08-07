<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<a href="#" onclick="new_priority(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_ticket_priority'); ?></a>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<?php if(count($priorities) > 0){ ?>

						<table class="table dt-table scroll-responsive">
							<thead>
								<th><?php echo _l('id'); ?></th>
								<th><?php echo _l('ticket_priority_dt_name'); ?></th>
								<th><?php echo _l('date_warning'); ?></th>
								<th><?php echo _l('cong_color'); ?></th>
								<th><?php echo _l('options'); ?></th>
							</thead>
							<tbody>
								<?php foreach($priorities as $priority){ ?>
								<tr>
									<td><?php echo $priority['priorityid']; ?></td>
									<td><a href="#" onclick="edit_priority(this,<?php echo $priority['priorityid']; ?>);return false;" data-name="<?php echo $priority['name']; ?>" data-date="<?php echo $priority['date']; ?>" data-color="<?php echo $priority['color']; ?>"><?php echo $priority['name']; ?></a></td>
									<td><?php echo $priority['date']; ?></td>
									<td style="background: <?php echo $priority['color']; ?>;"><?php echo $priority['color']; ?></td>
									<td>
										<a href="#" onclick="edit_priority(this,<?php echo $priority['priorityid']; ?>); return false" data-name="<?php echo $priority['name']; ?>" data-date="<?php echo $priority['date']; ?>" data-color="<?php echo $priority['color']; ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
										<a href="<?php echo admin_url('tickets/delete_priority/'.$priority['priorityid']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php } else { ?>
						<p class="no-margin"><?php echo _l('no_ticket_priorities_found'); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="priority" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('tickets/priority'),array('id'=>'form-priority')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="edit-title"><?php echo _l('ticket_priority_edit'); ?></span>
					<span class="add-title"><?php echo _l('ticket_priority_add'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div id="additional"></div>
						<?php echo render_input('name','ticket_priority_add_edit_name'); ?>
						<?php echo render_input('date','date_warning','','number'); ?>
						<label class="bold mbot10 inline-block"><?=_l('ticket_status_add_edit_color')?></label>
                        <div class="input-group mbot15 colorpicker-component colorpicker-element" data-css="background">
                            <input type="text" value="" name="color" id="color" class="form-control colorpicker">
                            <span class="input-group-addon">
                                <i class="i_color" style=""></i>
                            </span>
                        </div>
                        <input type="hidden" name="checkUpdate" id="checkUpdate" value="0">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a class="btn btn-info submit"><?php echo _l('submit'); ?></a>
				<a class="btn btn-info submit_update"><?php echo _l('submit_and_update'); ?></a>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
	$(function(){
		appValidateForm($('form'),{name:'required'},manage_ticket_priorities);
		$('#priority').on('hidden.bs.modal', function(event) {
			$('#additional').html('');
			$('#priority input[name="name"]').val('');
			$('#priority input[name="date"]').val('');
			$('#priority input[name="color"]').val('');
			$('.i_color').css('background-color', '#fff');
			$('.add-title').removeClass('hide');
			$('.edit-title').removeClass('hide');
		});
	});
	function manage_ticket_priorities(form) {
		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function(response) {
			window.location.reload();
		});
		return false;
	}
	function new_priority(){
		$('#color').colorpicker();
		$('#priority').modal('show');
		$('.edit-title').addClass('hide');
	}
	function edit_priority(invoker,id){
		var name = $(invoker).data('name');
		$('#color').colorpicker();
		$('#additional').append(hidden_input('id',id));
		$('#priority input[name="name"]').val(name);
		$('#priority input[name="date"]').val($(invoker).data('date'));
		$('#priority input[name="color"]').val($(invoker).data('color'));
		$('.i_color').css('background-color', $(invoker).data('color'));

		$('#priority').modal('show');
		$('.add-title').addClass('hide');
	}
	$('body').on('click','.colorpicker-with-alpha',function(){
        $.each($('input.colorpicker'), function(i,v){
            $(v).parent('div').find('i:nth-child(1)').css('background-color', $(v).val());
        })
    })
    $(".submit").click(function() {
	  	$('#form-priority').submit();
	});
	$(".submit_update").click(function() {
		$('#checkUpdate').val(1);
	  	$('#form-priority').submit();
	});
</script>
</body>
</html>
